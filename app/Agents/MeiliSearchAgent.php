<?php

namespace App\Agents;

use Vizra\VizraADK\Agents\BaseLlmAgent;
use App\Tools\MeiliSearchTool;
use App\Tools\MeiliSearchApiTool;
use Vizra\VizraADK\System\AgentContext;

class MeiliSearchAgent extends BaseLlmAgent
{
    protected string $name = 'meili_search_agent';
    protected string $description = 'Un agente especializado en búsquedas y recuperación de información utilizando MeiliSearch como motor de búsqueda.';
    protected string $instructions = 'You are Meili Search Agent. See resources/prompts/meili_search_agent/default.blade.php for full instructions.';

    protected string $model = 'gpt-4o';
    protected ?float $temperature = 0.7;
    protected ?int $maxTokens = 1000;

    protected array $tools = [
        MeiliSearchApiTool::class,
    ];

    public function beforeLlmCall(array $inputMessages, AgentContext $context): array
    {
        $context->setState('namespace', 'test_meili_namespace');
        return parent::beforeLlmCall($inputMessages, $context);
    }
}
