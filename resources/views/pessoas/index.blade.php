@extends('adminlte::page')

@section('title', 'Pessoas')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Pessoas</h1>
        <a href="{{ route('pessoas.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Nova pessoa
        </a>
    </div>
@stop

@section('content')
    @include('partials.alerts')

    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route('pessoas.index') }}" class="form-row">
                <div class="col-md-6 mb-2 mb-md-0">
                    <input type="search" name="q" value="{{ request('q') }}" class="form-control"
                           placeholder="Buscar por nome, e-mail ou CPF">
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <select name="status" class="form-control">
                        <option value="">Todos os status</option>
                        <option value="ativo" @selected(request('status') === 'ativo')>Ativos</option>
                        <option value="inativo" @selected(request('status') === 'inativo')>Inativos</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-default btn-block"><i class="fas fa-search"></i> Filtrar</button>
                </div>
            </form>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>Telefone</th>
                        <th>Cidade/UF</th>
                        <th>Status</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($pessoas as $pessoa)
                        <tr>
                            <td><a href="{{ route('pessoas.show', $pessoa) }}">{{ $pessoa->nome }}</a></td>
                            <td>{{ $pessoa->cpf_formatado ?? '—' }}</td>
                            <td>{{ $pessoa->telefone ?? '—' }}</td>
                            <td>{{ collect([$pessoa->cidade, $pessoa->uf])->filter()->join('/') ?: '—' }}</td>
                            <td>
                                <span class="badge badge-{{ $pessoa->ativo ? 'success' : 'secondary' }}">
                                    {{ $pessoa->ativo ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="text-right text-nowrap">
                                <a href="{{ route('pessoas.edit', $pessoa) }}" class="btn btn-sm btn-info" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('pessoas.destroy', $pessoa) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Excluir {{ addslashes($pessoa->nome) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger" title="Excluir"><i class="fas fa-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">Nenhuma pessoa encontrada.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($pessoas->hasPages())
            <div class="card-footer clearfix">
                <div class="float-right">{{ $pessoas->links() }}</div>
            </div>
        @endif
    </div>
@stop
