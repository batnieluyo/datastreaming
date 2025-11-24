<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use PhpAmqpLib\Connection\AMQPStreamConnection;

class RabbitMQConsumeServiceA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'rabbitmq:consume-service-a';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consume messages from q.service-A';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $host     = config('rabbitmq.host');
        $port     = config('rabbitmq.port');
        $user     = config('rabbitmq.user');
        $password = config('rabbitmq.password');
        $vhost    = config('rabbitmq.vhost');

        $queue    = 'q.general';

        $connection = new AMQPStreamConnection($host, $port, $user, $password, $vhost);
        $channel    = $connection->channel();

        // Declarar la cola (idempotente)
        $channel->queue_declare($queue, false, true, false, false);

        // Prefetch para no saturar el consumer
        $channel->basic_qos(null, 1, null);

        $this->info(" [*] Esperando mensajes en {$queue}. Ctrl+C para salir");

        $callback = function ($msg) {
            $this->info(" [x] Recibido en Service A: " . $msg->body);

            // Aquí va tu lógica de negocio:
            // json_decode($msg->body, true) etc...

            // Simular procesamiento
            // sleep(1);

            // ACK
            $msg->ack();
        };

        $channel->basic_consume($queue, '', false, false, false, false, $callback);

        // Loop infinito de consumo
        while ($channel->is_consuming()) {
            $channel->wait();
        }

        $channel->close();
        $connection->close();

        return 0;
    }
}
