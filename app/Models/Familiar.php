<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Familiar extends Model
{
    protected $table = 'familiares';

    public const PARENTESCOS = [
        'conjuge' => 'Cônjuge / companheiro(a)',
        'filho' => 'Filho(a)',
        'enteado' => 'Enteado(a)',
        'pai_mae' => 'Pai / mãe',
        'irmao' => 'Irmão / irmã',
        'neto' => 'Neto(a)',
        'avo' => 'Avô / avó',
        'outro_parente' => 'Outro parente',
        'nao_parente' => 'Não parente',
    ];

    protected $fillable = ['nome', 'parentesco', 'data_nascimento', 'renda', 'observacao'];

    protected function casts(): array
    {
        return [
            'data_nascimento' => 'date',
            'renda' => 'decimal:2',
        ];
    }

    public function beneficiario(): BelongsTo
    {
        return $this->belongsTo(Beneficiario::class);
    }

    public function getParentescoLabelAttribute(): string
    {
        return self::PARENTESCOS[$this->parentesco] ?? $this->parentesco;
    }
}
