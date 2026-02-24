<?php

namespace Database\Seeders;

use App\Models\School;
use Illuminate\Database\Seeder;

class SchoolSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        School::create([
            'name' => 'Escola Básica Central',
            'address' => 'Rua das Flores, 123, Centro',
            'phone' => '(11) 1234-5678',
            'email' => 'contato@escolacentral.edu.br',
            'director_name' => 'Maria Silva',
        ]);

        School::create([
            'name' => 'Colégio São José',
            'address' => 'Av. Principal, 456, Bairro Novo',
            'phone' => '(11) 9876-5432',
            'email' => 'secretaria@saojose.edu.br',
            'director_name' => 'João Santos',
        ]);

        School::create([
            'name' => 'Instituto Educacional Monte Verde',
            'address' => 'Rua Verde, 789, Vila Esperança',
            'phone' => '(11) 5555-1234',
            'email' => 'info@monteverde.edu.br',
            'director_name' => 'Ana Costa',
        ]);
    }
}