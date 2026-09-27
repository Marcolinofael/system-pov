<?php

namespace App\Http\Requests;

use App\Models\Pessoa;
use App\Rules\Cpf;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PessoaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'cpf' => $this->filled('cpf') ? preg_replace('/\D/', '', $this->input('cpf')) : null,
            'cep' => $this->filled('cep') ? preg_replace('/\D/', '', $this->input('cep')) : null,
            'uf' => $this->filled('uf') ? strtoupper($this->input('uf')) : null,
            'ativo' => $this->boolean('ativo'),
        ]);
    }

    public function rules(): array
    {
        return [
            'nome' => ['required', 'string', 'max:150'],
            'cpf' => ['nullable', new Cpf, Rule::unique('pessoas', 'cpf')->ignore($this->route('pessoa'))],
            'email' => ['nullable', 'email', 'max:150'],
            'telefone' => ['nullable', 'string', 'max:20'],
            'data_nascimento' => ['nullable', 'date', 'before:today'],
            'cep' => ['nullable', 'digits:8'],
            'endereco' => ['nullable', 'string', 'max:150'],
            'numero' => ['nullable', 'string', 'max:20'],
            'bairro' => ['nullable', 'string', 'max:100'],
            'cidade' => ['nullable', 'string', 'max:100'],
            'uf' => ['nullable', Rule::in(Pessoa::UFS)],
            'observacoes' => ['nullable', 'string', 'max:2000'],
            'ativo' => ['boolean'],
        ];
    }
}
