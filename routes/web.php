<?php

use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::get('/rabbitmq-queue', function () {
    $rabbitmq = new \App\Services\RabbitMQService();

    $queue = 'q.general';

    $payload = [
        'event' => 'user.created',
        'data'  => [
            'id'   => random_int(1, 999),
            'name' => \Illuminate\Support\Str::random(25),
        ],
        'sent_at' => now()->toIso8601String(),
    ];

    $rabbitmq->publish(json_encode($payload), $queue);

    return response()->json(['status' => 'ok', 'sent' => $payload, 'queue' => $queue]);
});

Route::get('/rabbitmq-stream', function () {
    $rabbitmq = new \App\Services\RabbitMQStreamService();

    $payload = [
        'event' => 'user.created',
        'data'  => [
            'id'   => random_int(1, 999),
            'name' => \Illuminate\Support\Str::random(25),
        ],
        'sent_at' => now()->toIso8601String(),
    ];

    $rabbitmq->publish($payload);

    return response()->json([
        'status'  => 'ok',
        'sent' => $payload,
        'message' => 'Evento publicado en stream',
    ]);
});


Route::get('/redpanda', function () {
    $kafka = new \App\Services\KafkaService();
    $kafka->publish('test-topic', $payload = [
        'version' => '1.0',
        'event' => 'user.created',
        'object' => [
            'id' => 123,
            'name' => 'Daniel',
        ],
        'timestamp' => now()->timestamp,
    ]);

    return response()->json($payload);
});

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('profile.edit');
    Volt::route('settings/password', 'settings.password')->name('user-password.edit');
    Volt::route('settings/appearance', 'settings.appearance')->name('appearance.edit');

    Volt::route('settings/two-factor', 'settings.two-factor')
        ->middleware(
            when(
                Features::canManageTwoFactorAuthentication()
                    && Features::optionEnabled(Features::twoFactorAuthentication(), 'confirmPassword'),
                ['password.confirm'],
                [],
            ),
        )
        ->name('two-factor.show');
});
