<?php

namespace Database\Seeders;

use App\Agents\MobilitySupportAgent;
use Illuminate\Database\Seeder;

class DeleteSeeder extends Seeder
{
    public function run(): void
    {
        $agent = new MobilitySupportAgent(); # Cambiar al agente que se le quiere eliminar el conocimiento

        $agent->vector()->deleteMemoriesBySource([
        'source' => 'documentation',  // Sin source = elimina todo
        'namespace' => 'test_meili_namespace'
       ]);
    }
}
