<?php

namespace App\Kafka\Handlers;

use Junges\Kafka\Contracts\{ConsumerMessage, MessageConsumer};

class RedpandaHandler
{
    public function handle(ConsumerMessage $message, MessageConsumer $consumer): void
    {
        $payload = $message->getBody();

        logger()->channel('stream')->info(time() . " - new message received", [
            'topic' => $message->getTopicName(),
            'partition' => $message->getPartition(),
            'offset' => $message->getOffset(),
            'payload' => $payload,
        ]);

        // 👉 Aquí va tu lógica de negocio:
        // - guardar en DB
        // - lanzar jobs
        // - actualizar estados, etc.
        // ...
        $consumer->commit(); // read
        // try {} catch (\Exception $exception) {}
    }
}