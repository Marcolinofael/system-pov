<?php

namespace Database\Factories;

use App\Models\Pessoa;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Pessoa>
 */
class PessoaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'cpf' => fake()->unique()->cpf(false),
            'email' => fake()->unique()->safeEmail(),
            'telefone' => fake()->cellphoneNumber(),
            'data_nascimento' => fake()->dateTimeBetween('-70 years', '-18 years'),
            'cep' => preg_replace('/\D/', '', fake()->postcode()),
            'endereco' => fake()->streetName(),
            'numero' => (string) fake()->buildingNumber(),
            'bairro' => fake()->words(2, true),
            'cidade' => fake()->city(),
            'uf' => fake()->randomElement(Pessoa::UFS),
            'ativo' => fake()->boolean(85),
        ];
    }
}
