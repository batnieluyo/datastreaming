<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Wire\AMQPTable;

class RabbitMQStreamConsumeA extends Command
{
    protected $signature = 'rabbitmq:stream-consume-a';
    protected $description = 'Consume mensajes desde RabbitMQ Stream (Consumer A)';

    public function handle()
    {
        $host     = config('rabbitmq.host');
        $port     = config('rabbitmq.port');
        $user     = config('rabbitmq.user');
        $password = config('rabbitmq.password');
        $vhost    = config('rabbitmq.vhost');

        $streamQueue = config('rabbitmq.stream_queue');

        $connection = new AMQPStreamConnection($host, $port, $user, $password, $vhost);
        $channel    = $connection->channel();

        // Declarar el stream como una "queue" de tipo stream
        $arguments = new AMQPTable([
            'x-queue-type' => 'stream',
            // Opcional: políticas de retención
            // 'x-max-age'          => '1D',
            // 'x-max-length-bytes' => 1000000000,
        ]);

        $channel->queue_declare(
            queue: $streamQueue,
            passive: false,
            durable: true,
            exclusive: false,
            auto_delete: false,
            nowait: false,
            arguments: $arguments
        );

        // Prefetch: cuántos mensajes puede tener sin ACK
        $channel->basic_qos(null, 10, null);

        $this->info(" [*] Consumer A escuchando stream: {$streamQueue}. Ctrl+C para salir.");

        $callback = function ($msg) {
            $this->info(" [A] Mensaje recibido: " . $msg->body);

            // Aquí tu lógica de negocio para A
            // $data = json_decode($msg->body, true);

            // Procesamiento...

            // ACK: con AMQP clásico esto borra el mensaje del stream
            $msg->ack();
        };

        // Consumir desde la cola tipo stream
        $channel->basic_consume(
            queue: $streamQueue,
            consumer_tag: 'consumer-a',
            no_local: false,
            no_ack: false,
            exclusive: false,
            nowait: false,
            callback: $callback
        );

        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();

        return 0;
    }
}
