@extends('layouts.app')

@use('App\Models\Beneficiario', 'B')

@section('title', $beneficiario->nome_exibicao)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1 class="mb-2">{{ $beneficiario->nome_exibicao }}</h1>
        <div class="mb-2">
            <div class="btn-group">
                <a href="{{ route('beneficiarios.pdf', $beneficiario) }}" target="_blank" class="btn btn-pov-orange">
                    <i class="fas fa-file-pdf"></i> Gerar PDF
                </a>
                <button type="button" class="btn btn-pov-orange dropdown-toggle dropdown-toggle-split" data-toggle="dropdown" aria-label="Mais opções de PDF"></button>
                <div class="dropdown-menu dropdown-menu-right">
                    <a class="dropdown-item" href="{{ route('beneficiarios.pdf', $beneficiario) }}" target="_blank">
                        <i class="fas fa-file-alt mr-1"></i> Ficha e histórico
                    </a>
                    <a class="dropdown-item" href="{{ route('beneficiarios.pdf', [$beneficiario, 'fotos' => 1]) }}" target="_blank">
                        <i class="fas fa-images mr-1"></i> Ficha e histórico com fotos
                    </a>
                </div>
            </div>
            <a href="{{ route('beneficiarios.edit', $beneficiario) }}" class="btn btn-primary"><i class="fas fa-edit"></i> Editar</a>
            <a href="{{ route('beneficiarios.index') }}" class="btn btn-default">Voltar</a>
        </div>
    </div>
@stop

@section('content')
    @include('partials.alerts')

    @php($b = $beneficiario)

    @unless ($b->consentimento_lgpd)
        <div class="alert alert-warning">
            <i class="fas fa-file-signature"></i>
            O termo de autorização de uso de dados (LGPD) ainda <strong>não foi assinado</strong> por este responsável.
        </div>
    @endunless

    <div class="row">
        {{-- Resumo --}}
        <div class="col-lg-4">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile text-center">
                    <div class="mb-3">
                        @if ($b->foto)
                            <a href="{{ $b->foto_url }}" target="_blank" title="Ver foto ampliada"
                               data-galeria="cadastro" data-legenda="{{ $b->nome_exibicao }}">
                                @include('partials.avatar', ['b' => $b, 'tamanho' => 130])
                            </a>
                        @else
                            @include('partials.avatar', ['b' => $b, 'tamanho' => 110])
                            <div><a href="{{ route('beneficiarios.edit', $b) }}" class="small"><i class="fas fa-camera"></i> Adicionar foto</a></div>
                        @endif
                    </div>
                    <h3 class="profile-username mb-0">{{ $b->nome_exibicao }}</h3>
                    @if ($b->nome_social)
                        <p class="text-muted small mb-1">Nome de registro: {{ $b->nome }}</p>
                    @endif
                    <span class="badge badge-{{ $b->ativo ? 'success' : 'secondary' }}">{{ $b->ativo ? 'Em acompanhamento' : 'Inativo' }}</span>

                    <ul class="list-group list-group-unbordered mt-3 text-left">
                        <li class="list-group-item"><b>Pessoas na casa</b> <span class="float-right">{{ $b->total_moradores }}</span></li>
                        <li class="list-group-item"><b>Renda familiar</b> <span class="float-right">{{ B::moeda($b->renda_familiar) }}</span></li>
                        <li class="list-group-item"><b>Renda per capita</b> <span class="float-right">{{ B::moeda($b->renda_per_capita) }}</span></li>
                        <li class="list-group-item"><b>Cadastrado em</b>
                            <span class="float-right">{{ ($b->data_cadastro ?? $b->created_at)->format('d/m/Y') }}</span>
                        </li>
                        @if ($b->cadastradoPor)
                            <li class="list-group-item"><b>Por</b> <span class="float-right">{{ $b->cadastradoPor->name }}</span></li>
                        @endif
                    </ul>
                </div>
            </div>

            <div class="card card-secondary card-outline">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-hand-holding-heart mr-1"></i> Necessidades</h3></div>
                <div class="card-body">
                    @forelse ($b->rotulos('necessidades') as $n)
                        <span class="pov-tag orange">{{ $n }}</span>
                    @empty
                        <span class="text-muted">Nenhuma informada.</span>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            {{-- Atendimentos --}}
            <div class="card card-secondary card-outline" id="atendimentos">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-hand-holding-heart mr-1"></i> Atendimentos ({{ $b->atendimentos->count() }})</h3>
                    <div class="card-tools">
                        <button type="button" class="btn btn-sm btn-pov-orange" data-toggle="collapse" data-target="#novo-atendimento">
                            <i class="fas fa-plus"></i> Registrar
                        </button>
                    </div>
                </div>

                <div class="collapse {{ $errors->hasAny(['data', 'tipo', 'quantidade', 'descricao', 'fotos', 'fotos.*']) ? 'show' : '' }}" id="novo-atendimento">
                    <form method="POST" action="{{ route('atendimentos.store') }}" enctype="multipart/form-data"
                          class="card-body border-bottom" style="background: var(--pov-ice)">
                        @csrf
                        <input type="hidden" name="beneficiario_id" value="{{ $b->id }}">
                        <input type="hidden" name="origem" value="beneficiario">
                        <div class="form-row">
                            @include('partials.campo', ['nome' => 'data', 'rotulo' => 'Data', 'tipo' => 'date', 'valor' => today()->format('Y-m-d'), 'col' => 'col-md-3', 'obrigatorio' => true])
                            <div class="form-group col-md-6">
                                <label for="tipo">Tipo <span class="text-danger">*</span></label>
                                <select id="tipo" name="tipo" required class="form-control @error('tipo') is-invalid @enderror">
                                    <option value="">Selecione…</option>
                                    @foreach (\App\Models\Atendimento::TIPOS as $valor => $rotulo)
                                        <option value="{{ $valor }}" @selected(old('tipo') === $valor)>{{ $rotulo }}</option>
                                    @endforeach
                                </select>
                                @error('tipo') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            @include('partials.campo', ['nome' => 'quantidade', 'rotulo' => 'Quantidade', 'tipo' => 'number', 'col' => 'col-md-3'])
                        </div>
                        <div class="form-group">
                            <textarea name="descricao" rows="2" class="form-control @error('descricao') is-invalid @enderror"
                                      placeholder="O que foi entregue ou combinado (opcional)">{{ old('descricao') }}</textarea>
                            @error('descricao') <span class="invalid-feedback">{{ $message }}</span> @enderror
                        </div>
                        @include('atendimentos._fotos_campo', ['id' => 'fotos-rapido'])
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar atendimento</button>
                    </form>
                </div>

                <div class="card-body">
                    @if ($b->atendimentos->isEmpty())
                        <p class="text-muted mb-0 text-center py-2">Nenhum atendimento registrado para esta família ainda.</p>
                    @else
                        <div class="timeline timeline-inverse mb-0">
                            @foreach ($b->atendimentos->groupBy(fn ($a) => $a->data->format('Y-m-d')) as $dia => $doDia)
                                <div class="time-label">
                                    <span class="bg-primary">{{ \Illuminate\Support\Carbon::parse($dia)->format('d/m/Y') }}</span>
                                </div>
                                @foreach ($doDia as $a)
                                    <div>
                                        <i class="{{ $a->icone }} bg-warning"></i>
                                        <div class="timeline-item">
                                            <span class="time">
                                                @if ($a->podeSerAlteradoPor(auth()->user()))
                                                    <a href="{{ route('atendimentos.edit', [$a, 'origem' => 'beneficiario']) }}" title="Editar"><i class="fas fa-edit"></i></a>
                                                    <form action="{{ route('atendimentos.destroy', $a) }}" method="POST" class="d-inline"
                                                          onsubmit="return confirm('Excluir este atendimento?')">
                                                        @csrf
                                                        @method('DELETE')
                                                        <input type="hidden" name="origem" value="beneficiario">
                                                        <button class="btn btn-link btn-sm p-0 ml-2 text-danger" title="Excluir"><i class="fas fa-trash"></i></button>
                                                    </form>
                                                @endif
                                            </span>
                                            <h3 class="timeline-header">
                                                <strong>{{ $a->tipo_label }}</strong>{{ $a->quantidade ? ' · '.$a->quantidade.' '.($a->quantidade > 1 ? 'unidades' : 'unidade') : '' }}
                                                <small class="text-muted d-block">por {{ $a->responsavel?->name ?? 'usuário removido' }}</small>
                                            </h3>
                                            @if ($a->descricao || $a->fotos->isNotEmpty())
                                                <div class="timeline-body">
                                                    @if ($a->descricao)
                                                        <div class="mb-2">{!! nl2br(e($a->descricao)) !!}</div>
                                                    @endif
                                                    @foreach ($a->fotos as $foto)
                                                        <a href="{{ $foto->url }}" target="_blank" title="Ver fotos"
                                                           data-galeria="atendimento-{{ $a->id }}"
                                                           data-legenda="{{ $a->tipo_label }} — {{ $a->data->format('d/m/Y') }}">
                                                            <img src="{{ $foto->url }}" class="pov-thumb" alt="Foto do atendimento" loading="lazy">
                                                        </a>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            @endforeach
                            <div><i class="fas fa-flag bg-gray"></i></div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Identificação --}}
            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-id-card mr-1"></i> Identificação</h3></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">CPF</dt><dd class="col-sm-8">{{ $b->cpf_formatado ?? '—' }}</dd>
                        <dt class="col-sm-4">RG</dt><dd class="col-sm-8">{{ $b->rg ?? '—' }}</dd>
                        <dt class="col-sm-4">NIS / PIS</dt><dd class="col-sm-8">{{ $b->nis ?? '—' }}</dd>
                        <dt class="col-sm-4">Nascimento</dt>
                        <dd class="col-sm-8">{{ $b->data_nascimento ? $b->data_nascimento->format('d/m/Y').' ('.$b->idade.' anos)' : '—' }}</dd>
                        <dt class="col-sm-4">Sexo</dt><dd class="col-sm-8">{{ $b->rotulo('sexo') ?? '—' }}</dd>
                        <dt class="col-sm-4">Estado civil</dt><dd class="col-sm-8">{{ $b->rotulo('estado_civil') ?? '—' }}</dd>
                        <dt class="col-sm-4">Cor / raça</dt><dd class="col-sm-8">{{ $b->rotulo('cor_raca') ?? '—' }}</dd>
                        <dt class="col-sm-4">Escolaridade</dt><dd class="col-sm-8">{{ $b->rotulo('escolaridade') ?? '—' }}</dd>
                    </dl>
                </div>
            </div>

            {{-- Contato --}}
            <div class="card">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-map-marker-alt mr-1"></i> Contato e endereço</h3></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Telefone</dt>
                        <dd class="col-sm-8">
                            @if ($b->telefone)
                                {{ $b->telefone }}
                                <a href="https://wa.me/55{{ preg_replace('/\D/', '', $b->telefone) }}" target="_blank" rel="noopener"
                                   class="ml-1 text-success" title="Abrir no WhatsApp"><i class="fab fa-whatsapp"></i></a>
                            @else
                                —
                            @endif
                        </dd>
                        <dt class="col-sm-4">Recado</dt><dd class="col-sm-8">{{ $b->telefone_recado ?? '—' }}</dd>
                        <dt class="col-sm-4">E-mail</dt><dd class="col-sm-8">{{ $b->email ?? '—' }}</dd>
                        <dt class="col-sm-4">Endereço</dt>
                        <dd class="col-sm-8">
                            @if ($b->endereco || $b->bairro)
                                {{ collect([$b->endereco, $b->numero, $b->complemento])->filter()->join(', ') }}<br>
                                {{ collect([$b->bairro, $b->cidade])->filter()->join(' — ') }}{{ $b->uf ? '/'.$b->uf : '' }}
                                @if ($b->cep) &middot; CEP {{ $b->cep_formatado }} @endif
                            @else
                                —
                            @endif
                        </dd>
                        <dt class="col-sm-4">Referência</dt><dd class="col-sm-8">{{ $b->ponto_referencia ?? '—' }}</dd>
                    </dl>
                </div>
            </div>

            {{-- Família --}}
            <div class="card card-info card-outline">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-users mr-1"></i> Composição familiar ({{ $b->total_moradores }} {{ $b->total_moradores > 1 ? 'pessoas' : 'pessoa' }})</h3></div>
                <div class="card-body p-0 table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr><th>Nome</th><th>Parentesco</th><th>Idade</th><th>Renda</th><th>Observação</th></tr>
                        </thead>
                        <tbody>
                            <tr class="table-light">
                                <td><strong>{{ $b->nome_exibicao }}</strong></td>
                                <td>Responsável</td>
                                <td>{{ $b->idade !== null ? $b->idade.' anos' : '—' }}</td>
                                <td>—</td>
                                <td></td>
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
                </div>
            </div>

            {{-- Socioeconômico --}}
            <div class="card card-secondary card-outline">
                <div class="card-header"><h3 class="card-title"><i class="fas fa-home mr-1"></i> Situação socioeconômica</h3></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-4">Moradia</dt><dd class="col-sm-8">{{ $b->rotulo('situacao_moradia') ?? '—' }}</dd>
                        <dt class="col-sm-4">Trabalho</dt><dd class="col-sm-8">{{ $b->rotulo('situacao_trabalho') ?? '—' }}</dd>
                        <dt class="col-sm-4">Benefícios</dt>
                        <dd class="col-sm-8">
                            @forelse ($b->rotulos('beneficios') as $beneficio)
                                <span class="pov-tag">{{ $beneficio }}</span>
                            @empty
                                Nenhum
                            @endforelse
                        </dd>
                        <dt class="col-sm-4">Pessoa com deficiência</dt><dd class="col-sm-8">{{ $b->possui_deficiencia ? 'Sim' : 'Não' }}</dd>
                        <dt class="col-sm-4">Saúde</dt><dd class="col-sm-8">{!! $b->saude ? nl2br(e($b->saude)) : '—' !!}</dd>
                    </dl>
                </div>
            </div>

            @if ($b->observacoes)
                <div class="card">
                    <div class="card-header"><h3 class="card-title"><i class="fas fa-sticky-note mr-1"></i> Observações do atendimento</h3></div>
                    <div class="card-body">{!! nl2br(e($b->observacoes)) !!}</div>
                </div>
            @endif
        </div>
    </div>
@stop
