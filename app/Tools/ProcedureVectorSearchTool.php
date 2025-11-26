<?php

namespace App\Tools;

use Vizra\VizraADK\Contracts\ToolInterface;
use Vizra\VizraADK\System\AgentContext;
use Vizra\VizraADK\Memory\AgentMemory;
use App\Agents\ProcedureAgent;

class ProcedureVectorSearchTool implements ToolInterface
{
    protected ?ProcedureAgent $agent = null;

    public function definition(): array
    {
        return [
            'name' => 'procedure_vector_search',
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
                        'description' => 'Namespace donde buscar (default: "procedures")',
                        'default' => 'procedures',
                    ],
                    'limit' => [
                        'type' => 'integer',
                        'description' => 'Número máximo de resultados (default: 5)',
                        'default' => 5,
                        'minimum' => 1,
                        'maximum' => 20,
                    ],
                    'threshold' => [
                        'type' => 'number',
                        'description' => 'Umbral de similitud (0.0-1.0, default: 0.25)',
                        'default' => 0.25,
                        'minimum' => 0,
                        'maximum' => 1,
                    ],
                ],
                'required' => ['query'],
            ],
        ];
    }

    public function execute(array $arguments, AgentContext $context, AgentMemory $memory): string 
    {
        try {
            $query = trim($arguments['query'] ?? '');
            
            if (empty($query)) {
                return json_encode([
                    'status' => 'error',
                    'message' => 'La consulta no puede estar vacía',
                ]);
            }

            $namespace = $arguments['namespace'] ?? 'procedures';
            $limit = min(max(1, (int)($arguments['limit'] ?? 5)), 20);
            $threshold = max(0, min(1, (float)($arguments['threshold'] ?? 0.25)));

            $memory->addLearning("Usuario buscó: {$query}");

            if (!$this->agent) {
                $this->agent = new ProcedureAgent();
            }

            $results = $this->agent->vector()->search([
                'query' => (string) $query,
                'namespace' => $namespace,
                'threshold' => $threshold,
                'limit' => $limit
            ]);

            $formattedResults = [];
            if (!empty($results) && is_iterable($results)) {
                foreach ($results as $result) {
                    $formattedResults[] = [
                        'content' => substr($result->content ?? '', 0, 500),
                        'similarity' => round($result->similarity ?? 0, 4),
                        'similarity_percent' => round(($result->similarity ?? 0) * 100, 2) . '%',
                        'metadata' => $result->metadata ?? [],
                        'source' => $result->source ?? 'procedure',
                    ];
                }
            }

            $count = count($formattedResults);
            $memory->addFact("Búsqueda encontró {$count} resultados", 0.9);

            return json_encode([
                'status' => 'success',
                'query' => $query,
                'results_found' => $count,
                'results' => $formattedResults,
            ]);

        } catch (\Exception $e) {
            return json_encode([
                'status' => 'error',
                'message' => 'Error en búsqueda: ' . $e->getMessage(),
            ]);
        }
    }
}
