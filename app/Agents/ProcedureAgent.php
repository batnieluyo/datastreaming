<?php

namespace App\Agents;

use Prism\Prism\Text\PendingRequest;
use Vizra\VizraADK\Agents\BaseLlmAgent;
use Vizra\VizraADK\Contracts\ToolInterface;
use Vizra\VizraADK\System\AgentContext;
use App\Tools\ProcedureSemanticSearchTool;

class ProcedureAgent extends BaseLlmAgent
{
    protected string $name = 'procedure_agent';
    protected string $description = 'Agente especializado en ayudar a los usuarios con trámites gubernamentales de todo tipo, a excepción de los relacionados con movilidad y transporte.';
    protected string $instructions = 'You are Procedure Agent. See resources/prompts/procedure_agent/default.blade.php for full instructions.';
    
    protected string $model = 'gpt-4o-mini';
    protected ?float $temperature = 0.7;
    protected ?int $maxTokens = 1000;

    protected array $tools = [
        ProcedureSemanticSearchTool::class,
    ];
}
