<?php

namespace App\Livewire\Webapp;

use App\Agents\CustomerSupportAgent;
use App\Agents\RootAgent;
use Illuminate\Support\Str;
use Livewire\Attributes\On;
use Livewire\Component;
use Prism\Prism\Streaming\Events\TextDeltaEvent;
use Vizra\VizraADK\Models\AgentSession;
use Vizra\VizraADK\Services\MemoryManager;

class Chat extends Component
{
    public array $messages = [];

    // TODO: Validate min and max string length
    public string $body = '';

    protected $rules = [
        'body' => 'required|string|max:1000',
    ];

    public $start = 3;

    public $thinking = false;

    public ?string $sessionId = null;

    public function send(): void
    {
        if (is_null($this->sessionId)) {
            $this->sessionId = (string)Str::uuid7();
        }

        $this->validate();
        // Empujar el mensaje al array local (para que el emisor lo vea instantáneo)
        $this->messages[] = ['body' => $this->body, 'role' => 'user'];
        $this->body = '';

        $this->thinking = true;

        $this->dispatch('question-created');
    }

    #[On('talk-with-magi')]
    public function testing(): void
    {
        $buffer = '';

        /*$stream = CustomerSupportAgent::run(array_last($this->messages)['body'])
            ->forUser(auth()->user())
            ->withSession($this->sessionId)
            ->streaming()
            ->go();*/
        $stream = RootAgent::run(array_last($this->messages)['body'])
            ->forUser(auth()->user())
            ->withSession($this->sessionId)
            ->streaming()
            ->go();

        // $session = AgentSession::where('session_id', $this->sessionId)->first();

        foreach ($stream as $chunk) {
            if (!$chunk instanceof TextDeltaEvent) {
                continue;
            }

            $buffer .= $chunk->delta;
            $this->stream(to: 'streaming', content: $buffer, replace: $chunk->delta);
        }

        $this->messages[] = ['body' => $buffer, 'role' => 'assistant'];

        $this->thinking = false;
    }

    public function render()
    {
        return view('livewire.webapp.chat');
    }
}
