<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Client;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Cliente 1: Persona Moral (Empresa)
        Client::create([
            'razon_social' => 'Tecnología y Sistemas S.A. de C.V.',
            'contacto'     => 'Ing. Roberto Martínez',
            'rfc'          => 'TSI010101ABC',
            'tel'          => '3312345678',
            'email'        => 'contacto@tecnosistemas.mx',
            'csf'          => 'storage/app/clients/docs/csf_tec.pdf',
            'opinion_cumplimiento' => 'storage/app/clients/docs/opinion_tec.pdf',
            'fiel'         => 'storage/app/clients/keys/fiel_tec.cer',
            'fiel_vigencia'=> '2027-05-20',
            'csd'          => 'storage/app/clients/keys/csd_tec.cer',
            'csd_vigencia' => '2027-10-15',
        ]);

        // Cliente 2: Persona Física (Freelance o Consultor)
        Client::create([
            'razon_social' => 'Juan Pérez García',
            'contacto'     => 'Juan Pérez',
            'rfc'          => 'PEGJ800101H12',
            'tel'          => '5598765432',
            'email'        => 'juan.perez@email.com',
            'csf'          => 'storage/app/clients/docs/csf_juan.pdf',
            'opinion_cumplimiento' => null, // Campo opcional
            'fiel'         => 'storage/app/clients/keys/fiel_juan.cer',
            'fiel_vigencia'=> '2026-12-31',
            'csd'          => null, // Puede que no tenga CSD si no factura directo
            'csd_vigencia' => null,
        ]);
    }
}
