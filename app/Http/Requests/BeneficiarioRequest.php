<?php

namespace App\Http\Requests;

use App\Models\Beneficiario;
use App\Models\Familiar;
use App\Rules\Cpf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BeneficiarioRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $digitos = fn (string $campo) => $this->filled($campo) ? preg_replace('/\D/', '', $this->input($campo)) : null;

        // Descarta linhas de familiares deixadas em branco (mantendo os índices,
        // para os erros de validação apontarem para a linha certa do formulário)
        $familiares = collect($this->input('familiares', []))
            ->filter(fn ($f) => filled($f['nome'] ?? null))
            ->map(fn ($f) => [...$f, 'renda' => self::valor($f['renda'] ?? null)])
            ->all();

        $this->merge([
            'cpf' => $digitos('cpf'),
            'nis' => $digitos('nis'),
            'cep' => $digitos('cep'),
            'uf' => $this->filled('uf') ? strtoupper($this->input('uf')) : null,
            'renda_familiar' => self::valor($this->input('renda_familiar')),
            'beneficios' => array_values($this->input('beneficios', [])),
            'necessidades' => array_values($this->input('necessidades', [])),
            'possui_deficiencia' => $this->boolean('possui_deficiencia'),
            'consentimento_lgpd' => $this->boolean('consentimento_lgpd'),
            'remover_foto' => $this->boolean('remover_foto'),
            'ativo' => $this->boolean('ativo'),
            'familiares' => $familiares,
        ]);
    }

    /** Converte "1.234,56" em "1234.56". */
    private static function valor(?string $valor): ?string
    {
        if (blank($valor)) {
            return null;
        }

        return str_replace(',', '.', str_replace('.', '', preg_replace('/[^\d,.]/', '', $valor)));
    }

    public function rules(): array
    {
        $opcao = fn (array $lista) => ['nullable', Rule::in(array_keys($lista))];
        $id = $this->route('beneficiario');

        return [
            'nome' => ['required', 'string', 'max:150'],
            'nome_social' => ['nullable', 'string', 'max:150'],
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'remover_foto' => ['boolean'],
            'cpf' => ['nullable', new Cpf, Rule::unique('beneficiarios', 'cpf')->ignore($id)],
            'rg' => ['nullable', 'string', 'max:20'],
            'nis' => ['nullable', 'digits:11', Rule::unique('beneficiarios', 'nis')->ignore($id)],
            'data_nascimento' => ['nullable', 'date', 'before_or_equal:today'],
            'sexo' => $opcao(Beneficiario::SEXOS),
            'estado_civil' => $opcao(Beneficiario::ESTADOS_CIVIS),
            'cor_raca' => $opcao(Beneficiario::CORES_RACAS),
            'escolaridade' => $opcao(Beneficiario::ESCOLARIDADES),

            'email' => ['nullable', 'email', 'max:150'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'telefone_recado' => ['nullable', 'string', 'max:20'],

            'cep' => ['nullable', 'digits:8'],
            'endereco' => ['nullable', 'string', 'max:150'],
            'numero' => ['nullable', 'string', 'max:20'],
            'complemento' => ['nullable', 'string', 'max:60'],
            'bairro' => ['nullable', 'string', 'max:100'],
            'cidade' => ['nullable', 'string', 'max:100'],
            'uf' => ['nullable', Rule::in(Beneficiario::UFS)],
            'ponto_referencia' => ['nullable', 'string', 'max:150'],

            'situacao_moradia' => $opcao(Beneficiario::MORADIAS),
            'situacao_trabalho' => $opcao(Beneficiario::TRABALHO),
            'renda_familiar' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'beneficios' => ['array'],
            'beneficios.*' => [Rule::in(array_keys(Beneficiario::BENEFICIOS))],
            'possui_deficiencia' => ['boolean'],
            'saude' => ['nullable', 'string', 'max:2000'],

            'necessidades' => ['array'],
            'necessidades.*' => [Rule::in(array_keys(Beneficiario::NECESSIDADES))],
            'data_cadastro' => ['nullable', 'date', 'before_or_equal:today'],
            'consentimento_lgpd' => ['boolean'],
            'observacoes' => ['nullable', 'string', 'max:2000'],
            'ativo' => ['boolean'],

            'familiares' => ['array', 'max:30'],
            'familiares.*.nome' => ['required', 'string', 'max:150'],
            'familiares.*.parentesco' => ['required', Rule::in(array_keys(Familiar::PARENTESCOS))],
            'familiares.*.data_nascimento' => ['nullable', 'date', 'before_or_equal:today'],
            'familiares.*.renda' => ['nullable', 'numeric', 'min:0', 'max:99999999'],
            'familiares.*.observacao' => ['nullable', 'string', 'max:150'],
        ];
    }

    public function attributes(): array
    {
        return [
            'familiares.*.nome' => 'nome do familiar',
            'familiares.*.parentesco' => 'parentesco',
            'familiares.*.data_nascimento' => 'nascimento do familiar',
            'familiares.*.renda' => 'renda do familiar',
        ];
    }
}
