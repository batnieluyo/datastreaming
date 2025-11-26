<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Agents\MeiliSearchAgent;
use Vizra\VizraADK\Services\VectorMemoryManager;

class DocumentMeiliSeeder extends Seeder
{
    public function run(): void
    {
        $documento = "
            Renovación de Licencia de Conducir (Licencia Vencida)
            Puede realizarlo
            Ciudadanos
            Descripción
            Debe presentarse el interesado, cuando requiera renovar la licencia del Estado de Morelos para conducir un vehículo automotor, cuando se requiera renovar la licencia de conducir por vencimiento de la misma.
            Requisitos
            Se informa a la ciudadanía que el subsidio del 50% de descuento en licencias de conducir estará disponible únicamente hasta el sábado 29 de noviembre.
            A partir del 1 de diciembre, el descuento ya no podrá aplicarse y el trámite se realizará con el costo regular.
            Se invita a las y los interesados a realizar su trámite dentro del periodo establecido para acceder al beneficio.
            Identificación oficial.
            Licencia vencida.
            Comprobante de pago de Hacienda.
            Para completar tu trámite es indispensable que:

            Te presentes puntual a tu cita, ya que no hay tolerancia (10 minutos antes).
            Nota: Para realizar su trámite, es indispensable presentar toda la documentación en original y copia.

            Costo del trámite
            Para realizar el tramite es necesario realizar el pago correspondiente.

            Dónde estamos
            Jiutepec
            Delegación Jiutepec
            Calle Centenario #96, esq. Av. de los 50 Metros, Local K, Civac, 62578, Jiutepec, Mor.
            Jojutla
            Delegación Jojutla
            Blvd. Lázaro Cárdenas #307, Cuauhtémoc, 62900, Jojutla de Juárez, Mor.
            Xochitepec
            Delegación Xochitepec
            República de Costa Rica esq. Calle Haití, Col. Centro, 62790, Xochitepec, Mor.
            Yautepec
            Delegación Yautepec
            Paseo Tlahuica esq. Calle Ganado, Barrio Rancho Nuevo S/N, 62730, Yautepec, Morelos."
        ;

        $vectorManager = app(VectorMemoryManager::class);

        $vectorManager->addDocument(
            MeiliSearchAgent::class,
            [
                'content' => $documento,
                'metadata' => [
                    'source' => 'manual_v1.pdf',
                    'topic' => 'tech'
                ],
                'namespace' => 'test_meili_namespace',
                'source' => 'documentation'
            ]
        );
    }
}
