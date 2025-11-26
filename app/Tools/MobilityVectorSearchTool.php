<?php

namespace App\Tools;

use Vizra\VizraADK\Contracts\ToolInterface;
use Vizra\VizraADK\System\AgentContext;
use Vizra\VizraADK\Memory\AgentMemory;
use App\Agents\MobilitySupportAgent;

class MobilityVectorSearchTool implements ToolInterface
{
    protected ?MobilitySupportAgent $agent = null;

    public function definition(): array
    {
        return [
            'name' => 'mobility_vector_search',
            'description' => 'Busca en la base de conocimiento usando embeddings vectoriales. Encuentra contenido relevante por significado. Ideal para búsquedas semánticas.',
            'parameters' => [
                'type' => 'object',
                'properties' => [
                    'query' => [
                        'type' => 'string',
                        'description' => 'Término de búsqueda o pregunta',
                    ],                    
                    'namespace' => [
                        'type' => 'string',
                        'description' => 'Namespace donde buscar (default: "mobility")',
                        'default' => 'mobility',
                    ],
                ],
                'required' => ['query'],
            ],
        ];
    }

    public function execute(array $arguments, AgentContext $context, AgentMemory $memory): string 
    {
        $namespace = 'mobility';
        $agent = new MobilitySupportAgent();
        $memory->addLearning("Usuario buscó: {$arguments['query']} en el namespace {$namespace}");

        $results = $agent->vector()->search([
            'query' => $arguments['query'],
            'namespace' => $namespace,
            'limit' => 5,
            'threshold' => 0.5,
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
