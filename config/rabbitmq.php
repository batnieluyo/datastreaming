<?php

return [
    'host'          => env('RABBITMQ_HOST', 'localhost'),
    'port'          => env('RABBITMQ_PORT', 5672),
    'user'          => env('RABBITMQ_USER', 'guest'),
    'password'      => env('RABBITMQ_PASSWORD', 'guest'),
    'vhost'         => env('RABBITMQ_VHOST', '/'),

    'queue'         => env('RABBITMQ_QUEUE', 'test_queue'),
    'exchange'      => env('RABBITMQ_EXCHANGE', 'app.events'),
    'exchange_type' => env('RABBITMQ_EXCHANGE_TYPE', 'direct'),
    'routing_key'   => env('RABBITMQ_ROUTING_KEY', 'test.key'),

    'stream_queue' => env('RABBITMQ_STREAM_QUEUE', 'stream.testing'),

];