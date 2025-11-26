<?php

namespace Database\Seeders;

use App\Agents\MobilitySupportAgent;
use Illuminate\Database\Seeder;

class MobilitySeeder extends Seeder
{
    public function run(): void
    {
        $path = storage_path('documents/documento_canje_de_placas.txt');

        $content = file_get_contents($path);

        $agent = new MobilitySupportAgent(); # Cambiar al agente que se le quiere dar el conocimiento del doc

        $agent->rag()->addDocument([
            'content' => $content,
            'metadata' => [
                'source' => basename($path),
                'type' => 'documentation'
            ],
            'namespace' => 'mobility',
            'source' => 'documentation',
        ]);
    }
}
