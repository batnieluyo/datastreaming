<?php

namespace App\Tools;

use Illuminate\Support\Facades\Log;
use Vizra\VizraADK\Contracts\ToolInterface;
use Vizra\VizraADK\Memory\AgentMemory;
use Vizra\VizraADK\System\AgentContext;

class SearchDocument implements ToolInterface
{
    /**
     * Get the tool's definition for the LLM.
     * This structure should be JSON schema compatible.
     */
    public function definition(): array
    {
        return [
            'name' => 'search',
            'description' => 'Buscar informacion de tramites',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'query' => [
                        'type' => 'string',
                        'description' => 'The search query'
                    ],
                    'namespace' => [
                        'type' => 'string',
                        'description' => 'Knowledge namespace to search',
                        'default' => 'documents'
                    ],
                    // Define your parameters here
                    // 'example_param' => [
                    //     'type' => 'string',
                    //     'description' => 'An example parameter.'
                    // ],
                ],
                'required' => ['query'],
            ],
        ];
    }

    /**
     * Execute the tool's logic.
     *
     * @param array $arguments Arguments provided by the LLM, matching the parameters defined above.
     * @param AgentContext $context The current agent context, providing access to session state etc.
     * @return string JSON string representation of the tool's result.
     */
    public function execute(array $arguments, AgentContext $context, AgentMemory $memory): string
    {

        $agentClass = $context->getState('agent_class');
        $agent = new $agentClass();

        $results = $agent->rag()->search($arguments['query'], 2);

        return json_encode([
            'success' => true,
            'results' => $results->map(fn($r) => [
                'content' => $r->content,
                'similarity' => round($r->similarity, 3),
                'metadata' => $r->metadata,
                'source' => $r->source
            ])->toArray(),
            'total_found' => $results->count()
        ]);
    }
}
