<?php

namespace Database\Seeders;

use App\Agents\CustomerSupportAgent;
use App\Agents\MagiAgent;
use App\Models\Document;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Vizra\VizraADK\Services\VectorMemoryManager;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $vectorManager = app(VectorMemoryManager::class);

        $content = 'Alta de nueva vehiculo';
        $metadata = [
            'category' => 'movilidad-y-transporte',
            'description' => 'El trámite se efectuará a petición del interesado.El trámite deberá realizarlo el propietario. En caso de hacerlo otra persona deberá exhibir carta poder debidamente requisitada anexando copia de las identificaciones de las personas que en ella intervienen.',
            'url' => 'https://citas.morelos.gob.mx/tramite/2'
        ];
        $namespace = 'default';

        $vectorManager->addDocument(
            MagiAgent::class,
            $content,
            $metadata,
            $namespace
        );

        echo 'Document added to Meilisearch';
    }
}
