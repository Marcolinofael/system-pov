<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Atendimento extends Model
{
    public const TIPOS = [
        'cesta_basica' => 'Entrega de cesta básica',
        'roupas' => 'Doação de roupas e calçados',
        'higiene' => 'Kit de higiene e limpeza',
        'medicamentos' => 'Medicamentos',
        'material_escolar' => 'Material escolar',
        'moveis' => 'Móveis e utensílios',
        'visita' => 'Visita domiciliar',
        'encaminhamento' => 'Encaminhamento (CRAS, saúde, documentação…)',
        'orientacao' => 'Orientação / escuta',
        'outro' => 'Outro',
    ];

    public const ICONES = [
        'cesta_basica' => 'fas fa-shopping-basket',
        'roupas' => 'fas fa-tshirt',
        'higiene' => 'fas fa-pump-soap',
        'medicamentos' => 'fas fa-pills',
        'material_escolar' => 'fas fa-pencil-alt',
        'moveis' => 'fas fa-couch',
        'visita' => 'fas fa-home',
        'encaminhamento' => 'fas fa-share',
        'orientacao' => 'fas fa-comments',
        'outro' => 'fas fa-hand-holding-heart',
    ];

    /** Tipos que representam entrega de itens (usam o campo quantidade). */
    public const ENTREGAS = ['cesta_basica', 'roupas', 'higiene', 'medicamentos', 'material_escolar', 'moveis'];

    public const MAX_FOTOS = 5;

    protected $fillable = ['beneficiario_id', 'data', 'tipo', 'quantidade', 'descricao'];

    protected function casts(): array
    {
        return [
            'data' => 'date',
            'quantidade' => 'integer',
        ];
    }

    public function beneficiario(): BelongsTo
    {
        return $this->belongsTo(Beneficiario::class);
    }

    public function fotos(): HasMany
    {
        return $this->hasMany(AtendimentoFoto::class)->orderBy('id');
    }

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function getTipoLabelAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }

    public function getIconeAttribute(): string
    {
        return self::ICONES[$this->tipo] ?? self::ICONES['outro'];
    }

    public function podeSerAlteradoPor(User $user): bool
    {
        return $user->isAdmin() || $this->user_id === $user->id;
    }
}
