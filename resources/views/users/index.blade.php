@extends('layouts.app')

@section('title', 'Usuários')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Usuários</h1>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo usuário
        </a>
    </div>
@stop

@section('content')
    @include('partials.alerts')

    <div class="card">
        <div class="card-header">
            <form method="GET" action="{{ route('users.index') }}" class="input-group">
                <input type="search" name="q" value="{{ request('q') }}" class="form-control"
                       placeholder="Buscar por nome ou e-mail">
                <div class="input-group-append">
                    <button class="btn btn-default"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Perfil</th>
                        <th>Status</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>
                                {{ $user->name }}
                                @if ($user->is(auth()->user()))
                                    <small class="text-muted">(você)</small>
                                @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                <span class="badge badge-{{ $user->isAdmin() ? 'primary' : 'info' }}">{{ $user->role_label }}</span>
                            </td>
                            <td>
                                <span class="badge badge-{{ $user->ativo ? 'success' : 'secondary' }}">
                                    {{ $user->ativo ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="text-right text-nowrap">
                                <a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-info" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                @unless ($user->is(auth()->user()))
                                    <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Excluir o usuário {{ addslashes($user->name) }}?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" title="Excluir"><i class="fas fa-trash"></i></button>
                                    </form>
                                @endunless
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">Nenhum usuário encontrado.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($users->hasPages())
            <div class="card-footer clearfix">
                <div class="float-right">{{ $users->links() }}</div>
            </div>
        @endif
    </div>
@stop
