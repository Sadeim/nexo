<?php

namespace Database\Seeders;

use App\Models\Client;
use App\Models\Faq;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Client::create([
            'name' => 'TechCorp Inc.',
            'logo' => 'clients/techcorp.png',
            'link' => 'https://www.techcorp.com',
        ]);

        Client::create([
            'name' => 'Global Solutions',
            'logo' => 'clients/globalsolutions.png',
            'link' => 'https://www.globalsolutions.com',
        ]);

        Client::create([
            'name' => 'Smart Innovations',
            'logo' => 'clients/smartinnovations.png',
            'link' => 'https://www.smartinnovations.io',
        ]);
    }
}
