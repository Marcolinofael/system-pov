<?php

return [

    'before' => 'O campo :attribute deve ser uma data anterior a :date.',
    'boolean' => 'O campo :attribute deve ser verdadeiro ou falso.',
    'confirmed' => 'A confirmação de :attribute não confere.',
    'date' => 'O campo :attribute deve ser uma data válida.',
    'digits' => 'O campo :attribute deve ter :digits dígitos.',
    'email' => 'O campo :attribute deve ser um endereço de e-mail válido.',
    'in' => 'O valor selecionado para :attribute é inválido.',
    'max' => [
        'numeric' => 'O campo :attribute não pode ser maior que :max.',
        'string' => 'O campo :attribute não pode ter mais que :max caracteres.',
    ],
    'min' => [
        'numeric' => 'O campo :attribute deve ser no mínimo :min.',
        'string' => 'O campo :attribute deve ter no mínimo :min caracteres.',
    ],
    'password' => [
        'letters' => 'O campo :attribute deve conter ao menos uma letra.',
        'mixed' => 'O campo :attribute deve conter letras maiúsculas e minúsculas.',
        'numbers' => 'O campo :attribute deve conter ao menos um número.',
        'symbols' => 'O campo :attribute deve conter ao menos um símbolo.',
        'uncompromised' => 'O :attribute informado apareceu em um vazamento de dados. Escolha outro.',
    ],
    'required' => 'O campo :attribute é obrigatório.',
    'string' => 'O campo :attribute deve ser um texto.',
    'unique' => 'Este :attribute já está cadastrado.',

    'attributes' => [
        'name' => 'nome',
        'nome' => 'nome',
        'email' => 'e-mail',
        'password' => 'senha',
        'role' => 'perfil',
        'cpf' => 'CPF',
        'cep' => 'CEP',
        'uf' => 'UF',
        'telefone' => 'telefone',
        'data_nascimento' => 'data de nascimento',
        'endereco' => 'logradouro',
        'numero' => 'número',
        'observacoes' => 'observações',
    ],

];
