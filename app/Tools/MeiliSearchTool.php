<?php

namespace App\Tools;

use Vizra\VizraADK\Contracts\ToolInterface;
use Vizra\VizraADK\Memory\AgentMemory;
use Vizra\VizraADK\System\AgentContext;
use App\Agents\MeiliSearchAgent;

class MeiliSearchTool implements ToolInterface
{
    public function definition(): array
    {
        return [
            'name' => 'meili_search',
            'description' => 'Búsqueda semántica de información sobre trámites o documentos de movilidad.',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'query' => [
                        'type' => 'string',
                        'description' => 'Término de búsqueda o pregunta',
                    ],                    
                    'namespace' => [
                        'type' => 'string',
                        'description' => 'Namespace donde buscar',
                        'default' => 'test_meili_namespace',
                    ],
                ],
                'required' => ['query'],
            ],
        ];
    }

    public function execute(array $arguments, AgentContext $context, AgentMemory $memory): string
    {
        $namespace = 'test_meili_namespace';
        $agent = new MeiliSearchAgent();

        $results = $agent->rag()->search([
            'query' => $arguments['query'],
            'namespace' => $namespace,
            'limit' => 5,
            'threshold' => 0.5,
            'filter' => 'source = "mobility"'
        ]);

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
