<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class UserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['ativo' => $this->boolean('ativo')]);
    }

    public function rules(): array
    {
        $criando = $this->isMethod('post');

        return [
            'name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($this->route('user'))],
            'role' => ['required', Rule::in(array_keys(User::ROLES))],
            'password' => [$criando ? 'required' : 'nullable', 'confirmed', Password::min(8)],
            'ativo' => ['boolean'],
        ];
    }
}
