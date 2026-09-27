<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pessoa extends Model
{
    use HasFactory;

    public const UFS = [
        'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA',
        'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO',
    ];

    protected $fillable = [
        'nome',
        'cpf',
        'email',
        'telefone',
        'data_nascimento',
        'cep',
        'endereco',
        'numero',
        'bairro',
        'cidade',
        'uf',
        'observacoes',
        'ativo',
    ];

    protected function casts(): array
    {
        return [
            'data_nascimento' => 'date',
            'ativo' => 'boolean',
        ];
    }

    public function scopeBusca(Builder $query, ?string $termo): Builder
    {
        if (blank($termo)) {
            return $query;
        }

        $digitos = preg_replace('/\D/', '', $termo);

        return $query->where(function (Builder $q) use ($termo, $digitos) {
            $q->where('nome', 'like', "%{$termo}%")
                ->orWhere('email', 'like', "%{$termo}%");

            if ($digitos !== '') {
                $q->orWhere('cpf', 'like', "%{$digitos}%");
            }
        });
    }

    public function getCpfFormatadoAttribute(): ?string
    {
        if (! $this->cpf) {
            return null;
        }

        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $this->cpf);
    }

    public function getCepFormatadoAttribute(): ?string
    {
        if (! $this->cep) {
            return null;
        }

        return preg_replace('/(\d{5})(\d{3})/', '$1-$2', $this->cep);
    }
}
