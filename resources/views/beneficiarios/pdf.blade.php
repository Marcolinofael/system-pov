@use('App\Models\Beneficiario', 'B')
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <title>Ficha — {{ $b->nome_exibicao }}</title>
    <style>
        @page { margin: 28mm 14mm 20mm 14mm; }
        * { font-family: 'DejaVu Sans', sans-serif; }
        body { font-size: 8.5pt; color: #1f2a44; }

        header { position: fixed; top: -21mm; left: 0; right: 0; height: 17mm; border-bottom: 2px solid #f58549; }
        header img { height: 15mm; float: left; }
        header .titulo { float: right; text-align: right; }
        header .titulo h1 { margin: 1mm 0 0; font-size: 13pt; color: #061c62; }
        header .titulo p { margin: 1mm 0 0; font-size: 7pt; color: #6b7a99; }

        footer { position: fixed; bottom: -13mm; left: 0; right: 0; height: 9mm; border-top: 1px solid #d6e6f2;
                 font-size: 6.5pt; color: #6b7a99; padding-top: 1.5mm; }

        h2 { font-size: 10pt; color: #fff; background: #061c62; padding: 1.5mm 2.5mm; margin: 5mm 0 2mm; }
        h2 small { font-weight: normal; color: #bdeeff; font-size: 7.5pt; }

        table { width: 100%; border-collapse: collapse; }
        .dados td { padding: 1.2mm 2mm; border-bottom: 1px solid #e3eef7; vertical-align: top; }
        .dados td.r { width: 26%; color: #17407e; font-weight: bold; }
        .lista th { background: #eaf8ff; color: #17407e; text-align: left; font-size: 7.5pt; padding: 1.5mm 2mm; }
        .lista td { padding: 1.5mm 2mm; border-bottom: 1px solid #e3eef7; vertical-align: top; }
        .lista tr.titular td { background: #f6fbfe; font-weight: bold; }

        .resumo { margin-top: 1mm; }
        .resumo td { vertical-align: top; }
        .foto { width: 30mm; border-radius: 3mm; border: 2px solid #47bcef; }
        .sem-foto { width: 30mm; height: 30mm; border-radius: 15mm; background: #eaf8ff; color: #087eb5;
                    font-size: 26pt; font-weight: bold; text-align: center; line-height: 30mm; }
        .nome { font-size: 14pt; font-weight: bold; color: #061c62; margin: 0; }
        .sub { color: #6b7a99; margin: 1mm 0 2mm; }
        .kpi { display: inline-block; margin: 0 4mm 1.5mm 0; padding: 1.2mm 2.5mm; background: #eaf8ff; color: #17407e; border-radius: 2mm; }
        .kpi b { color: #061c62; }

        .tag { display: inline-block; padding: .6mm 2mm; margin: 0 1mm 1mm 0; border-radius: 3mm; background: #fde8dc; color: #9a3f10; font-size: 7.5pt; }
        .tag.azul { background: #eaf8ff; color: #17407e; }
        .status { display: inline-block; padding: .6mm 2mm; border-radius: 3mm; font-size: 7.5pt; color: #fff; }
        .ativo { background: #28a745; } .inativo { background: #6c757d; }
        .alerta { margin-top: 3mm; padding: 2mm 3mm; background: #fff4e5; border-left: 3px solid #f58549; color: #7a4a12; }
        .texto { padding: 1.5mm 2mm; line-height: 1.45; }
        .muted { color: #8391ad; }

        .atendimento { page-break-inside: avoid; border-bottom: 1px solid #e3eef7; padding: 2mm 1mm; }
        .atendimento .data { color: #087eb5; font-weight: bold; width: 20mm; }
        .fotos img { width: 42mm; margin: 1.5mm 1.5mm 0 0; border: 1px solid #d6e6f2; vertical-align: top; }
    </style>
</head>
<body>
    <header>
        <img src="{{ $logo }}" alt="Projeto Oberland Jr. Vive">
        <div class="titulo">
            <h1>Ficha do beneficiário</h1>
            <p>Emitida em {{ now()->format('d/m/Y') }} às {{ now()->format('H:i') }} por {{ $geradoPor }}</p>
        </div>
    </header>

    <footer>
        <strong>Documento confidencial</strong> — contém dados pessoais protegidos pela LGPD (Lei 13.709/2018).
        Uso restrito ao Projeto Oberland Jr. Vive; não compartilhe nem deixe exposto.
    </footer>

    {{-- Resumo --}}
    <table class="resumo">
        <tr>
            <td style="width: 34mm">
                @if ($foto)
                    <img src="{{ $foto }}" class="foto" alt="">
                @else
                    <div class="sem-foto">{{ mb_strtoupper(mb_substr($b->nome_exibicao, 0, 1)) }}</div>
                @endif
            </td>
            <td>
                <p class="nome">{{ $b->nome_exibicao }}</p>
                <p class="sub">
                    @if ($b->nome_social) Nome de registro: {{ $b->nome }} · @endif
                    Cadastro nº {{ $b->id }} desde {{ ($b->data_cadastro ?? $b->created_at)->format('d/m/Y') }}
                    · <span class="status {{ $b->ativo ? 'ativo' : 'inativo' }}">{{ $b->ativo ? 'Em acompanhamento' : 'Inativo' }}</span>
                </p>
                <span class="kpi">Pessoas na casa: <b>{{ $b->total_moradores }}</b></span>
                <span class="kpi">Renda familiar: <b>{{ B::moeda($b->renda_familiar) }}</b></span>
                <span class="kpi">Per capita: <b>{{ B::moeda($b->renda_per_capita) }}</b></span>
                <span class="kpi">Atendimentos: <b>{{ $b->atendimentos->count() }}</b></span>
                @unless ($b->consentimento_lgpd)
                    <div class="alerta">Termo de autorização de uso de dados (LGPD) ainda não assinado.</div>
                @endunless
            </td>
        </tr>
    </table>

    {{-- Identificação --}}
    <h2>Identificação</h2>
    <table class="dados">
        <tr><td class="r">CPF</td><td>{{ $b->cpf_formatado ?? '—' }}</td><td class="r">RG</td><td>{{ $b->rg ?? '—' }}</td></tr>
        <tr><td class="r">NIS / PIS</td><td>{{ $b->nis ?? '—' }}</td>
            <td class="r">Nascimento</td><td>{{ $b->data_nascimento ? $b->data_nascimento->format('d/m/Y').' ('.$b->idade.' anos)' : '—' }}</td></tr>
        <tr><td class="r">Sexo</td><td>{{ $b->rotulo('sexo') ?? '—' }}</td><td class="r">Estado civil</td><td>{{ $b->rotulo('estado_civil') ?? '—' }}</td></tr>
        <tr><td class="r">Cor / raça</td><td>{{ $b->rotulo('cor_raca') ?? '—' }}</td><td class="r">Escolaridade</td><td>{{ $b->rotulo('escolaridade') ?? '—' }}</td></tr>
    </table>

    {{-- Contato e endereço --}}
    <h2>Contato e endereço</h2>
    <table class="dados">
        <tr><td class="r">Telefone</td><td>{{ $b->telefone ?? '—' }}</td><td class="r">Recado</td><td>{{ $b->telefone_recado ?? '—' }}</td></tr>
        <tr><td class="r">E-mail</td><td colspan="3">{{ $b->email ?? '—' }}</td></tr>
        <tr>
            <td class="r">Endereço</td>
            <td colspan="3">
                @if ($b->endereco || $b->bairro)
                    {{ collect([$b->endereco, $b->numero, $b->complemento])->filter()->join(', ') }}
                    — {{ collect([$b->bairro, $b->cidade])->filter()->join(', ') }}{{ $b->uf ? '/'.$b->uf : '' }}
                    @if ($b->cep) · CEP {{ $b->cep_formatado }} @endif
                @else
                    —
                @endif
            </td>
        </tr>
        <tr><td class="r">Ponto de referência</td><td colspan="3">{{ $b->ponto_referencia ?? '—' }}</td></tr>
    </table>

    {{-- Composição familiar --}}
    <h2>Composição familiar <small>({{ $b->total_moradores }} {{ $b->total_moradores > 1 ? 'pessoas' : 'pessoa' }})</small></h2>
    <table class="lista">
        <thead><tr><th>Nome</th><th>Parentesco</th><th>Idade</th><th>Renda</th><th>Observação</th></tr></thead>
        <tbody>
            <tr class="titular">
                <td>{{ $b->nome_exibicao }}</td><td>Responsável</td>
                <td>{{ $b->idade !== null ? $b->idade.' anos' : '—' }}</td><td>—</td><td></td>
            </tr>
            @foreach ($b->familiares as $f)
                <tr>
                    <td>{{ $f->nome }}</td>
                    <td>{{ $f->parentesco_label }}</td>
                    <td>{{ $f->data_nascimento ? $f->data_nascimento->age.' anos' : '—' }}</td>
                    <td>{{ $f->renda !== null ? B::moeda($f->renda) : '—' }}</td>
                    <td>{{ $f->observacao }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    {{-- Socioeconômico --}}
    <h2>Situação socioeconômica</h2>
    <table class="dados">
        <tr><td class="r">Moradia</td><td>{{ $b->rotulo('situacao_moradia') ?? '—' }}</td><td class="r">Trabalho</td><td>{{ $b->rotulo('situacao_trabalho') ?? '—' }}</td></tr>
        <tr>
            <td class="r">Benefícios</td>
            <td colspan="3">
                @forelse ($b->rotulos('beneficios') as $beneficio)
                    <span class="tag azul">{{ $beneficio }}</span>
                @empty
                    Nenhum
                @endforelse
            </td>
        </tr>
        <tr><td class="r">Pessoa com deficiência</td><td colspan="3">{{ $b->possui_deficiencia ? 'Sim' : 'Não' }}</td></tr>
        <tr><td class="r">Saúde</td><td colspan="3">{!! $b->saude ? nl2br(e($b->saude)) : '—' !!}</td></tr>
        <tr>
            <td class="r">Necessidades</td>
            <td colspan="3">
                @forelse ($b->rotulos('necessidades') as $n)
                    <span class="tag">{{ $n }}</span>
                @empty
                    Nenhuma informada
                @endforelse
            </td>
        </tr>
    </table>

    @if ($b->observacoes)
        <h2>Observações do cadastro</h2>
        <div class="texto">{!! nl2br(e($b->observacoes)) !!}</div>
    @endif

    {{-- Histórico de atendimentos --}}
    <h2>Histórico de atendimentos <small>({{ $b->atendimentos->count() }})</small></h2>
    @forelse ($b->atendimentos as $a)
        <table class="atendimento">
            <tr>
                <td class="data">{{ $a->data->format('d/m/Y') }}</td>
                <td>
                    <strong>{{ $a->tipo_label }}</strong>{{ $a->quantidade ? ' · '.$a->quantidade.' '.($a->quantidade > 1 ? 'unidades' : 'unidade') : '' }}
                    <span class="muted"> — por {{ $a->responsavel?->name ?? 'usuário removido' }}</span>
                    @if ($a->descricao)
                        <div style="margin-top: 1mm">{!! nl2br(e($a->descricao)) !!}</div>
                    @endif
                    @if (($fotosAtendimentos[$a->id] ?? collect())->isNotEmpty())
                        <div class="fotos">
                            @foreach ($fotosAtendimentos[$a->id] as $imagem)
                                <img src="{{ $imagem }}" alt="">
                            @endforeach
                        </div>
                    @elseif ($a->fotos->isNotEmpty())
                        <div class="muted" style="margin-top: 1mm">{{ $a->fotos->count() }} {{ $a->fotos->count() > 1 ? 'fotos registradas' : 'foto registrada' }} no sistema.</div>
                    @endif
                </td>
            </tr>
        </table>
    @empty
        <p class="muted texto">Nenhum atendimento registrado.</p>
    @endforelse
</body>
</html>
