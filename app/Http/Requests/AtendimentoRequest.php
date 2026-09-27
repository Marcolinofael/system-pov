<?php

namespace App\Http\Requests;

use App\Models\Atendimento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AtendimentoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'beneficiario_id' => ['required', Rule::exists('beneficiarios', 'id')],
            'data' => ['required', 'date', 'before_or_equal:today'],
            'tipo' => ['required', Rule::in(array_keys(Atendimento::TIPOS))],
            'quantidade' => ['nullable', 'integer', 'min:1', 'max:999'],
            'descricao' => ['nullable', 'string', 'max:2000'],
        ];
    }
}
