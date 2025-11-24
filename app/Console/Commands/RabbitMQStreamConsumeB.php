<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Wire\AMQPTable;

class RabbitMQStreamConsumeB extends Command
{
    protected $signature = 'rabbitmq:stream-consume-b';
    protected $description = 'Consume mensajes desde RabbitMQ Stream (Consumer B)';

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

        $arguments = new AMQPTable([
            'x-queue-type' => 'stream',
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

        $channel->basic_qos(null, 10, null);

        $this->info(" [*] Consumer B escuchando stream: {$streamQueue}. Ctrl+C para salir.");

        $callback = function ($msg) {
            $this->info(" [B] Mensaje recibido: " . $msg->body);

            // Lógica de negocio para B
            // $data = json_decode($msg->body, true);

            $msg->ack();
        };

        $channel->basic_consume(
            queue: $streamQueue,
            consumer_tag: 'consumer-b',
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
