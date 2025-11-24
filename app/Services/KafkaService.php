<?php

namespace App\Services;

use Junges\Kafka\Facades\Kafka;
use Illuminate\Support\Str;

class KafkaService
{
    public function publish(string $topic = 'test-topic', array $message = [])
    {
        return Kafka::publish()
            ->withKafkaKey((string) Str::uuid7())
            ->onTopic($topic)
            ->withBody($message)
            ->send();
    }
}