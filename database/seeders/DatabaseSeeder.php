<?php

namespace Database\Seeders;

use App\Models\Atendimento;
use App\Models\Beneficiario;
use App\Models\Familiar;
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
        if (app()->environment('local') && Beneficiario::doesntExist()) {
            Beneficiario::factory(30)->create()->each(function (Beneficiario $b) {
                foreach (range(1, fake()->numberBetween(0, 4)) as $i) {
                    $b->familiares()->create([
                        'nome' => fake()->name(),
                        'parentesco' => fake()->randomElement(array_keys(Familiar::PARENTESCOS)),
                        'data_nascimento' => fake()->dateTimeBetween('-60 years'),
                    ]);
                }

                foreach (range(1, fake()->numberBetween(0, 5)) as $i) {
                    $b->atendimentos()->create([
                        'data' => fake()->dateTimeBetween('-5 months'),
                        'tipo' => fake()->randomElement(array_keys(Atendimento::TIPOS)),
                        'quantidade' => fake()->optional()->numberBetween(1, 3),
                        'descricao' => fake()->optional()->sentence(),
                    ]);
                }
            });
        }
    }
}
