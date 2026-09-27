<?php

namespace App\Http\Requests;

use App\Models\Atendimento;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
            'fotos' => ['nullable', 'array', 'max:'.Atendimento::MAX_FOTOS],
            'fotos.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:8192'],
            'remover_fotos' => ['nullable', 'array'],
            'remover_fotos.*' => ['integer'],
        ];
    }

    /** Limite de fotos somando as que já existem no atendimento (edição). */
    public function after(): array
    {
        return [
            function (Validator $validator) {
                $atendimento = $this->route('atendimento');
                $existentes = $atendimento
                    ? $atendimento->fotos()->whereNotIn('id', $this->input('remover_fotos', []))->count()
                    : 0;

                if ($existentes + count($this->file('fotos', [])) > Atendimento::MAX_FOTOS) {
                    $validator->errors()->add('fotos', 'Cada atendimento pode ter no máximo '.Atendimento::MAX_FOTOS.' fotos.');
                }
            },
        ];
    }

    public function attributes(): array
    {
        return ['fotos.*' => 'foto'];
    }
}
