@extends('adminlte::page')

@section('title', 'Dashboard')

@section('content_header')
    <h1>Dashboard</h1>
@stop

@section('content')
    @include('partials.alerts')

    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $totalPessoas }}</h3>
                    <p>Pessoas cadastradas</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
                <a href="{{ route('pessoas.index') }}" class="small-box-footer">
                    Ver todas <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ $pessoasAtivas }}</h3>
                    <p>Pessoas ativas</p>
                </div>
                <div class="icon"><i class="fas fa-user-check"></i></div>
                <a href="{{ route('pessoas.index', ['status' => 'ativo']) }}" class="small-box-footer">
                    Ver ativas <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $novasNoMes }}</h3>
                    <p>Novos cadastros no mês</p>
                </div>
                <div class="icon"><i class="fas fa-user-plus"></i></div>
                <a href="{{ route('pessoas.create') }}" class="small-box-footer">
                    Cadastrar <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-secondary">
                <div class="inner">
                    <h3>{{ $totalUsuarios }}</h3>
                    <p>Usuários do sistema</p>
                </div>
                <div class="icon"><i class="fas fa-user-shield"></i></div>
                @can('admin')
                    <a href="{{ route('users.index') }}" class="small-box-footer">
                        Gerenciar <i class="fas fa-arrow-circle-right"></i>
                    </a>
                @else
                    <span class="small-box-footer">&nbsp;</span>
                @endcan
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Últimos cadastros</h3>
        </div>
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Cidade/UF</th>
                        <th>Cadastrado em</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($ultimasPessoas as $pessoa)
                        <tr>
                            <td><a href="{{ route('pessoas.show', $pessoa) }}">{{ $pessoa->nome }}</a></td>
                            <td>{{ collect([$pessoa->cidade, $pessoa->uf])->filter()->join('/') ?: '—' }}</td>
                            <td>{{ $pessoa->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted">Nenhuma pessoa cadastrada ainda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@stop
