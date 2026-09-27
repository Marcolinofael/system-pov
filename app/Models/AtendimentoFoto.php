<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AtendimentoFoto extends Model
{
    protected $table = 'atendimento_fotos';

    protected $fillable = ['caminho'];

    public function atendimento(): BelongsTo
    {
        return $this->belongsTo(Atendimento::class);
    }

    public function getUrlAttribute(): string
    {
        return route('atendimentos.foto', $this);
    }
}
