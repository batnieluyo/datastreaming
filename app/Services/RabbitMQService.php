<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;

class RabbitMQService
{
    protected AMQPStreamConnection $connection;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            config('rabbitmq.host'),
            config('rabbitmq.port'),
            config('rabbitmq.user'),
            config('rabbitmq.password'),
            config('rabbitmq.vhost')
        );
    }

    public function publish(string $messageBody, string $queue = null, string $routingKey = null): void
    {
        $channel = $this->connection->channel();

        $exchange = config('rabbitmq.exchange');
        $exchangeType = config('rabbitmq.exchange_type', 'direct');
        $queue = $queue ?? config('rabbitmq.queue');
        $routingKey = $routingKey ?? config('rabbitmq.routing_key', '');

        // Declarar exchange y cola (idempotente: si ya existen no pasa nada)
        $channel->exchange_declare($exchange, $exchangeType, false, true, false);
        $channel->queue_declare($queue, false, true, false, false);
        $channel->queue_bind($queue, $exchange, $routingKey);

        // Crear mensaje
        $msg = new AMQPMessage(
            $messageBody,
            [
                'content_type'  => 'application/json',
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            ]
        );

        // Publicar
        $channel->basic_publish($msg, $exchange, $routingKey);

        $channel->close();
        $this->connection->close();
    }
}