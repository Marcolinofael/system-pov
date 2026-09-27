<?php

namespace Database\Factories;

use App\Models\Beneficiario;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Beneficiario>
 */
class BeneficiarioFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nome' => fake()->name(),
            'cpf' => fake()->unique()->cpf(false),
            'nis' => fake()->unique()->numerify('###########'),
            'data_nascimento' => fake()->dateTimeBetween('-80 years', '-18 years'),
            'sexo' => fake()->randomElement(['feminino', 'masculino']),
            'estado_civil' => fake()->randomElement(array_keys(Beneficiario::ESTADOS_CIVIS)),
            'escolaridade' => fake()->randomElement(array_keys(Beneficiario::ESCOLARIDADES)),
            'telefone' => fake()->cellphoneNumber(),
            'cep' => preg_replace('/\D/', '', fake()->postcode()),
            'endereco' => fake()->streetName(),
            'numero' => (string) fake()->buildingNumber(),
            'bairro' => fake()->randomElement(['Aeroporto', 'Barra', 'Cajueiros', 'Lagomar', 'Malvinas', 'Visconde']),
            'cidade' => 'Macaé',
            'uf' => 'RJ',
            'situacao_moradia' => fake()->randomElement(array_keys(Beneficiario::MORADIAS)),
            'situacao_trabalho' => fake()->randomElement(array_keys(Beneficiario::TRABALHO)),
            'renda_familiar' => fake()->randomFloat(2, 0, 2500),
            'beneficios' => fake()->randomElements(array_keys(Beneficiario::BENEFICIOS), fake()->numberBetween(0, 2)),
            'necessidades' => fake()->randomElements(array_keys(Beneficiario::NECESSIDADES), fake()->numberBetween(1, 3)),
            'data_cadastro' => fake()->dateTimeBetween('-6 months'),
            'consentimento_lgpd' => fake()->boolean(80),
            'ativo' => fake()->boolean(90),
        ];
    }
}
