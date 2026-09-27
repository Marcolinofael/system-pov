<?php

namespace Database\Seeders;

use App\Models\Pessoa;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Em produção o admin só é criado se a senha vier das variáveis de ambiente
        if (app()->isProduction() && blank(env('ADMIN_PASSWORD'))) {
            $this->command?->warn('ADMIN_PASSWORD não definida: administrador inicial não foi criado.');

            return;
        }

        // Administrador inicial (credenciais definidas no .env)
        User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL', 'admin@oberland.test')],
            [
                'name' => 'Administrador',
                'password' => env('ADMIN_PASSWORD', 'admin12345'),
                'role' => 'admin',
                'ativo' => true,
            ],
        );

        // Dados de exemplo apenas em ambiente local
        if (app()->environment('local') && Pessoa::doesntExist()) {
            Pessoa::factory(30)->create();
        }
    }
}
