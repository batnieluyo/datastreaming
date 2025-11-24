<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');


Route::get('/demo', function () {
    $data = collect(json_decode('{ "taxpayer": { "name": "EMMANUEL", "last_name": "GUADARRAMA", "second_last_name": "VILLA" }, "vehicle": { "brand": "Honda de México, S.A. de C.V.", "model": 2019, "vin": "1HGRW1843KL901577", "plate": "PXE719E", "color": "ROJO" }, "metadata": [ {"key": "registered_at", "label":"ALTA DE VEHICULO NUEVO", "value": "2024-05-17"}, {"key": "vehicle_invoice_date", "label": "Fecha factura del vehículo", "value": "2024-05-17"}, {"key": "last_endorsement_date", "label": "Último refrendo realizado", "value": "2024-05-17"}, {"key": "last_endorsement_voucher_number", "label": "Línea de captura del último refrendo realizado", "value": 93001543937045447217} ] }', true));



    $meta = collect($data->get('meta'));

    $a = $meta->get('last_endorsement_date', null); // -->
    $meta->get('registered_at', null);

});