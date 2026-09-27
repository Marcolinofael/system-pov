<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Beneficiario extends Model
{
    use HasFactory;

    public const UFS = [
        'AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA',
        'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO',
    ];

    public const SEXOS = [
        'feminino' => 'Feminino',
        'masculino' => 'Masculino',
        'outro' => 'Outro',
        'nao_informado' => 'Prefere não informar',
    ];

    public const ESTADOS_CIVIS = [
        'solteiro' => 'Solteiro(a)',
        'casado' => 'Casado(a)',
        'uniao_estavel' => 'União estável',
        'separado' => 'Separado(a) / Divorciado(a)',
        'viuvo' => 'Viúvo(a)',
    ];

    public const CORES_RACAS = [
        'branca' => 'Branca',
        'preta' => 'Preta',
        'parda' => 'Parda',
        'amarela' => 'Amarela',
        'indigena' => 'Indígena',
        'nao_informado' => 'Prefere não informar',
    ];

    public const ESCOLARIDADES = [
        'sem_escolaridade' => 'Sem escolaridade',
        'fundamental_incompleto' => 'Fundamental incompleto',
        'fundamental_completo' => 'Fundamental completo',
        'medio_incompleto' => 'Médio incompleto',
        'medio_completo' => 'Médio completo',
        'superior_incompleto' => 'Superior incompleto',
        'superior_completo' => 'Superior completo',
    ];

    public const MORADIAS = [
        'propria' => 'Própria',
        'alugada' => 'Alugada',
        'cedida' => 'Cedida / emprestada',
        'ocupacao' => 'Ocupação',
        'abrigo' => 'Abrigo / acolhimento',
        'situacao_rua' => 'Em situação de rua',
        'outra' => 'Outra',
    ];

    public const TRABALHO = [
        'formal' => 'Trabalho formal (carteira assinada)',
        'informal' => 'Trabalho informal / bicos',
        'desempregado' => 'Desempregado(a)',
        'aposentado' => 'Aposentado(a) / pensionista',
        'do_lar' => 'Do lar',
        'estudante' => 'Estudante',
        'outro' => 'Outro',
    ];

    public const BENEFICIOS = [
        'bolsa_familia' => 'Bolsa Família',
        'bpc' => 'BPC / LOAS',
        'auxilio_gas' => 'Auxílio Gás',
        'aposentadoria' => 'Aposentadoria / pensão',
        'beneficio_municipal' => 'Benefício municipal / estadual',
        'outro' => 'Outro',
    ];

    public const NECESSIDADES = [
        'alimentacao' => 'Alimentação / cesta básica',
        'roupas' => 'Roupas e calçados',
        'higiene' => 'Higiene e limpeza',
        'medicamentos' => 'Medicamentos',
        'material_escolar' => 'Material escolar',
        'moveis' => 'Móveis e utensílios',
        'documentacao' => 'Orientação / documentação',
        'emprego' => 'Emprego e renda',
        'outro' => 'Outra',
    ];

    protected $fillable = [
        'nome', 'nome_social', 'cpf', 'rg', 'nis', 'data_nascimento', 'sexo', 'estado_civil',
        'cor_raca', 'escolaridade',
        'email', 'telefone', 'telefone_recado',
        'cep', 'endereco', 'numero', 'complemento', 'bairro', 'cidade', 'uf', 'ponto_referencia',
        'situacao_moradia', 'situacao_trabalho', 'renda_familiar', 'beneficios',
        'possui_deficiencia', 'saude',
        'necessidades', 'data_cadastro', 'consentimento_lgpd', 'observacoes', 'ativo',
    ];

    protected function casts(): array
    {
        return [
            'data_nascimento' => 'date',
            'data_cadastro' => 'date',
            'renda_familiar' => 'decimal:2',
            'beneficios' => 'array',
            'necessidades' => 'array',
            'possui_deficiencia' => 'boolean',
            'consentimento_lgpd' => 'boolean',
            'ativo' => 'boolean',
        ];
    }

    public function familiares(): HasMany
    {
        return $this->hasMany(Familiar::class)->orderBy('id');
    }

    public function cadastradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeBusca(Builder $query, ?string $termo): Builder
    {
        if (blank($termo)) {
            return $query;
        }

        $digitos = preg_replace('/\D/', '', $termo);

        return $query->where(function (Builder $q) use ($termo, $digitos) {
            $q->where('nome', 'like', "%{$termo}%")
                ->orWhere('nome_social', 'like', "%{$termo}%")
                ->orWhere('bairro', 'like', "%{$termo}%");

            if ($digitos !== '') {
                $q->orWhere('cpf', 'like', "%{$digitos}%")
                    ->orWhere('nis', 'like', "%{$digitos}%");
            }
        });
    }

    public function getNomeExibicaoAttribute(): string
    {
        return $this->nome_social ?: $this->nome;
    }

    public function getCpfFormatadoAttribute(): ?string
    {
        return $this->cpf ? preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $this->cpf) : null;
    }

    public function getCepFormatadoAttribute(): ?string
    {
        return $this->cep ? preg_replace('/(\d{5})(\d{3})/', '$1-$2', $this->cep) : null;
    }

    public function getIdadeAttribute(): ?int
    {
        return $this->data_nascimento?->age;
    }

    public function getTotalMoradoresAttribute(): int
    {
        return 1 + $this->familiares->count();
    }

    public function getRendaPerCapitaAttribute(): ?float
    {
        return $this->renda_familiar === null ? null : (float) $this->renda_familiar / $this->total_moradores;
    }

    /** Rótulo legível de um campo de opções (ex.: rotulo('sexo')). */
    public function rotulo(string $campo): ?string
    {
        $mapa = [
            'sexo' => self::SEXOS,
            'estado_civil' => self::ESTADOS_CIVIS,
            'cor_raca' => self::CORES_RACAS,
            'escolaridade' => self::ESCOLARIDADES,
            'situacao_moradia' => self::MORADIAS,
            'situacao_trabalho' => self::TRABALHO,
        ][$campo];

        return $mapa[$this->{$campo}] ?? null;
    }

    /** Rótulos das opções marcadas em um campo de múltipla escolha. */
    public function rotulos(string $campo): array
    {
        $mapa = $campo === 'beneficios' ? self::BENEFICIOS : self::NECESSIDADES;

        return array_values(array_intersect_key($mapa, array_flip($this->{$campo} ?? [])));
    }

    public static function moeda(?float $valor): string
    {
        return $valor === null ? '—' : 'R$ '.number_format($valor, 2, ',', '.');
    }
}
