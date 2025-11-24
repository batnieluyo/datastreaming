<?php

namespace App\Services;

use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use PhpAmqpLib\Wire\AMQPTable;

class RabbitMQStreamService
{
    protected AMQPStreamConnection $connection;

    public function __construct()
    {
        $this->connection = new AMQPStreamConnection(
            config('rabbitmq.host'),
            config('rabbitmq.port'),
            config('rabbitmq.user'),
            config('rabbitmq.password'),
            config('rabbitmq.vhost'),
        );
    }

    /**
     * Publica un mensaje en un RabbitMQ Stream (cola tipo stream).
     *
     * @param array|string $payload  Datos a enviar (array se convierte a JSON).
     */
    public function publish($payload): void
    {
        $channel = $this->connection->channel();

        $streamQueue = config('rabbitmq.stream_queue');

        // Declarar la queue como STREAM usando x-queue-type=stream
        // Si ya existe como stream, esto es idempotente.
        $arguments = new AMQPTable([
            'x-queue-type' => 'stream',
            // Opcionales: retención por tamaño/tiempo, etc.
            // 'x-max-age'          => '1D',        // mantén 1 día
            // 'x-max-length-bytes' => 1000000000, // 1GB, por ejemplo
        ]);

        $channel->queue_declare(
            queue: $streamQueue,
            passive: false,
            durable: true,
            exclusive: false,
            auto_delete: false,
            nowait: false,
            arguments: $arguments,
        );

        // Preparar el cuerpo del mensaje
        if (is_array($payload)) {
            $body = json_encode($payload);
            $contentType = 'application/json';
        } else {
            $body = (string) $payload;
            $contentType = 'text/plain';
        }

        $msg = new AMQPMessage(
            $body,
            [
                'content_type'  => $contentType,
                'delivery_mode' => AMQPMessage::DELIVERY_MODE_PERSISTENT,
            ]
        );

        /**
         * Para publicar a una queue directamente, usamos el "default exchange" ('')
         * y como routing_key el nombre de la queue (que en este caso es el stream).
         */
        $channel->basic_publish(
            msg: $msg,
            exchange: '',
            routing_key: $streamQueue
        );

        $channel->close();
        $this->connection->close();
    }
}