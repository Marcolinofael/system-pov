@extends('layouts.app')

@section('title', 'Beneficiários')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Beneficiários</h1>
        <a href="{{ route('beneficiarios.create') }}" class="btn btn-pov-orange">
            <i class="fas fa-user-plus"></i> Novo cadastro
        </a>
    </div>
@stop

@section('content')
    @include('partials.alerts')

    <div class="card card-primary card-outline">
        <div class="card-header">
            <form method="GET" action="{{ route('beneficiarios.index') }}" class="form-row">
                <div class="col-md-5 mb-2 mb-md-0">
                    <input type="search" name="q" value="{{ request('q') }}" class="form-control"
                           placeholder="Buscar por nome, CPF, NIS ou bairro">
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <select name="necessidade" class="form-control">
                        <option value="">Todas as necessidades</option>
                        @foreach (\App\Models\Beneficiario::NECESSIDADES as $valor => $rotulo)
                            <option value="{{ $valor }}" @selected(request('necessidade') === $valor)>{{ $rotulo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 mb-2 mb-md-0">
                    <select name="status" class="form-control">
                        <option value="">Todos</option>
                        <option value="ativo" @selected(request('status') === 'ativo')>Ativos</option>
                        <option value="inativo" @selected(request('status') === 'inativo')>Inativos</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button class="btn btn-primary btn-block"><i class="fas fa-search"></i> Filtrar</button>
                </div>
            </form>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>CPF / NIS</th>
                        <th>Telefone</th>
                        <th>Bairro</th>
                        <th class="text-center">Família</th>
                        <th>Último atendimento</th>
                        <th>Status</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($beneficiarios as $b)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center">
                                    @include('partials.avatar', ['b' => $b, 'tamanho' => 38, 'classe' => 'mr-2'])
                                    <div>
                                        <a href="{{ route('beneficiarios.show', $b) }}" class="font-weight-bold">{{ $b->nome_exibicao }}</a>
                                        @if ($b->nome_social)
                                            <br><small class="text-muted">Registro: {{ $b->nome }}</small>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td>
                                {{ $b->cpf_formatado ?? '—' }}
                                @if ($b->nis)
                                    <br><small class="text-muted">NIS {{ $b->nis }}</small>
                                @endif
                            </td>
                            <td>{{ $b->telefone ?? '—' }}</td>
                            <td>{{ $b->bairro ?? '—' }}</td>
                            <td class="text-center">
                                <span class="badge badge-light" title="Titular + familiares">
                                    <i class="fas fa-users"></i> {{ $b->familiares_count + 1 }}
                                </span>
                            </td>
                            <td>
                                @if ($b->atendimentos_max_data)
                                    @php($ultimo = \Illuminate\Support\Carbon::parse($b->atendimentos_max_data))
                                    {{ $ultimo->format('d/m/Y') }}
                                    <br><small class="{{ $ultimo->lt(now()->subDays(60)) ? 'text-danger' : 'text-muted' }}">{{ $ultimo->diffForHumans() }}</small>
                                @else
                                    <span class="text-muted">Nunca</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-{{ $b->ativo ? 'success' : 'secondary' }}">
                                    {{ $b->ativo ? 'Ativo' : 'Inativo' }}
                                </span>
                                @unless ($b->consentimento_lgpd)
                                    <span class="badge badge-warning" title="Termo LGPD não assinado"><i class="fas fa-file-signature"></i></span>
                                @endunless
                            </td>
                            <td class="text-right text-nowrap">
                                <a href="{{ route('beneficiarios.pdf', $b) }}" target="_blank" class="btn btn-sm btn-warning" title="Gerar PDF">
                                    <i class="fas fa-file-pdf"></i>
                                </a>
                                <a href="{{ route('beneficiarios.edit', $b) }}" class="btn btn-sm btn-info" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('beneficiarios.destroy', $b) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Excluir o cadastro de {{ addslashes($b->nome) }} e sua composição familiar?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" title="Excluir"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-5">
                                <i class="fas fa-hands-helping fa-2x mb-2 d-block"></i>
                                Nenhum beneficiário encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($beneficiarios->hasPages())
            <div class="card-footer clearfix">
                <div class="float-right">{{ $beneficiarios->links() }}</div>
            </div>
        @endif
    </div>
@stop
