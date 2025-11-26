<?php

namespace App\Agents;

use Prism\Prism\Text\PendingRequest;
use Vizra\VizraADK\Agents\BaseLlmAgent;
use Vizra\VizraADK\Contracts\ToolInterface;
use Vizra\VizraADK\System\AgentContext;
// use App\Tools\YourTool; // Example: Import your tool
use App\Agents\MobilitySupportAgent;
use App\Agents\ProcedureAgent;
use Prism\Prism\Enums\Provider;

class RootAgent extends BaseLlmAgent
{
    protected string $name = 'root_agent';
    protected string $description = 'Eres el agente orquestador que gestiona y delega tareas a otros agentes especializados según sea necesario.';

    /**
     * Agent instructions hierarchy (first found wins):
     * 1. Runtime: $agent->setPromptOverride('...')
     * 2. Database: agent_prompt_versions table (if enabled)
     * 3. File: resources/prompts/root_agent/default.blade.php
     * 4. Fallback: This property
     * 
     * The prompt file has been created for you at:
     * resources/prompts/root_agent/default.blade.php
     */
    protected string $instructions = 'You are Root Agent. See resources/prompts/root_agent/default.blade.php for full instructions.';
    protected string $model = 'gpt-4o-mini';

    protected array $tools = [
        // Example: YourTool::class,
    ];

    protected array $subAgents = [
        MobilitySupportAgent::class,
        ProcedureAgent::class,
    ];

    /*

    Optional hook methods to override:

    public function beforeLlmCall(array $inputMessages, AgentContext $context): array
    {
        // $context->setState('custom_data_for_llm', 'some_value');
        // $inputMessages[] = ['role' => 'system', 'content' => 'Additional system note for this call.'];
        return parent::beforeLlmCall($inputMessages, $context);
    }

    public function afterLlmResponse(mixed $response, AgentContext $context, ?PendingRequest $request = null): mixed {

         return parent::afterLlmResponse($response, $context, $request);

    }

    public function beforeToolCall(string $toolName, array $arguments, AgentContext $context): array {

        return parent::beforeToolCall($toolName, $arguments, $context);

    }

    public function afterToolResult(string $toolName, string $result, AgentContext $context): string {

        return parent::afterToolResult($toolName, $result, $context);

    } */
}
