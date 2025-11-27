<?php

namespace App\Agents;

use Vizra\VizraADK\Agents\BaseLlmAgent;
use Vizra\VizraADK\System\AgentContext;
use App\Tools\MobilityVectorSearchTool;
use App\Tools\DocumentIndexTool;
use Illuminate\Support\Collection;

class MobilitySupportAgent extends BaseLlmAgent
{
    protected string $name = 'mobility_support_agent';
    protected string $description = 'Agente especializado en ayudar a los usuarios con información acerca de trámites de Movilidad y Transporte.';
    protected string $instructions = 'You are Mobility Agent. See resources/prompts/mobility_agent/default.blade.php for full instructions.';

    protected string $model = 'gpt-4o-mini';
    protected ?float $temperature = 0.7;
    protected ?int $maxTokens = 1000;

    protected array $tools = [
        MobilityVectorSearchTool::class,
    ];

    public function beforeLlmCall(array $inputMessages, AgentContext $context): array
    {
        return $inputMessages;
    }

    /**
     * Realiza una búsqueda simple
     */
    public function search(string $query): string
    {
        return $this->run($query);
    }    /**
     * Realiza una búsqueda semántica en los documentos indexados
     */
    public function semanticSearch(
        string $query,
        int $limit = 5,
        float $threshold = 0.4,
        string $namespace = 'documents'
    ): Collection {
        return $this->rag()->search([
            'query' => $query,
            'limit' => $limit,
            'threshold' => $threshold,
            'namespace' => $namespace,
        ]);
    }    /**
     * Obtiene estadísticas del índice vectorial
     */
    public function getIndexStats(string $namespace = 'documents'): array
    {
        return $this->vector()->getStatistics($namespace);
    }

    /**
     * Limpia todos los documentos en un namespace
     */
    public function clearNamespace(string $namespace = 'documents'): int
    {
        return $this->vector()->deleteMemories($namespace);
    }

    /**
     * Elimina documentos de una fuente específica
     */
    public function removeSource(string $source, string $namespace = 'documents'): int
    {
        return $this->vector()->deleteMemoriesBySource($source, $namespace);
    }

    /**
     * Busca por similitud con opciones avanzadas
     */
    public function findSimilar(string $query, array $options = []): array
    {
        $limit = $options['limit'] ?? 5;
        $threshold = $options['threshold'] ?? 0.4;
        $namespace = $options['namespace'] ?? 'documents';

        $results = $this->semanticSearch($query, $limit, $threshold, $namespace);

        return $results->map(function ($result) {
            return [
                'content' => $result->content,
                'similarity' => round($result->similarity * 100, 2) . '%',
                'metadata' => $result->metadata ?? [],
                'source' => $result->source ?? 'unknown',
            ];
        })->toArray();
    }

    /**
     * Busca y formatea resultados de forma legible
     */
    public function formatResults(
        string $query,
        int $limit = 5,
        float $threshold = 0.4,
        string $namespace = 'documents'
    ): array {
        $results = $this->semanticSearch($query, $limit, $threshold, $namespace);

        return [
            'query' => $query,
            'total_results' => $results->count(),
            'results' => $results->map(function ($result) {
                return [
                    'content' => $result->content,
                    'similarity' => round($result->similarity, 4),
                    'similarity_percent' => round($result->similarity * 100, 2),
                    'metadata' => $result->metadata ?? [],
                    'source' => $result->source ?? 'unknown',
                ];
            })->toArray(),
        ];
    }
}
