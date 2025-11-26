<?php

namespace Database\Seeders;

use App\Agents\ProcedureAgent;
use Illuminate\Database\Seeder;
use Vizra\VizraADK\Agents\Agent;

class ProcedureSeeder extends Seeder
{
    public function run(): void
    {
        $path = storage_path('documents/registro_cedula_profesional.txt');

        $content = file_get_contents($path);

        $agent = new ProcedureAgent(); # Cambiar al agente que se le quiere dar el conocimiento del doc

        $agent->vector()->addDocument([
            'content' => $content,
            'metadata' => [
                'source' => basename($path),
                'type' => 'documentation'
            ],
            'namespace' => 'procedures', # Cambiar el namespace para guardar en otro corpus(store)
            'source' => 'procedure',
        ]);
    }
}
