<?php

namespace App\Console\Commands;

use App\Kafka\Handlers\RedpandaHandler;
use Illuminate\Console\Command;
use Junges\Kafka\Facades\Kafka;
use Junges\Kafka\Contracts\{ConsumerMessage, MessageConsumer};

class KafkaConsumeServiceB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'kafka:consume-service-b';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Consume messages from kafka';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $topic = 'test-topic'; # $this->argument('topic');

        $this->info("Starting Kafka consumer B on topic: {$topic}");

        $consumer = Kafka::consumer([$topic])
            ->withBrokers(config('kafka.brokers'))
            ->withConsumerGroupId('digital-morelos-b')
            ->withManualCommit()
            ->withHandler(function (ConsumerMessage $message, MessageConsumer $consumer) {
                $this->info(time() . " - new message received");
                (new RedpandaHandler)->handle(message: $message,  consumer: $consumer);
            })
            ->build();

        // loop bloqueante
        $consumer->consume();

        return self::SUCCESS;
    }
}
