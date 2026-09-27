@extends('adminlte::page')

@section('title', $pessoa->nome)

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>{{ $pessoa->nome }}</h1>
        <div>
            <a href="{{ route('pessoas.edit', $pessoa) }}" class="btn btn-info"><i class="fas fa-edit"></i> Editar</a>
            <a href="{{ route('pessoas.index') }}" class="btn btn-default">Voltar</a>
        </div>
    </div>
@stop

@section('content')
    @include('partials.alerts')

    <div class="row">
        <div class="col-md-4">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile text-center">
                    <i class="fas fa-user-circle fa-5x text-muted mb-3"></i>
                    <h3 class="profile-username">{{ $pessoa->nome }}</h3>
                    <span class="badge badge-{{ $pessoa->ativo ? 'success' : 'secondary' }}">
                        {{ $pessoa->ativo ? 'Ativo' : 'Inativo' }}
                    </span>
                    <ul class="list-group list-group-unbordered mt-3 text-left">
                        <li class="list-group-item"><b>CPF</b> <span class="float-right">{{ $pessoa->cpf_formatado ?? '—' }}</span></li>
                        <li class="list-group-item"><b>Nascimento</b>
                            <span class="float-right">
                                @if ($pessoa->data_nascimento)
                                    {{ $pessoa->data_nascimento->format('d/m/Y') }} ({{ $pessoa->data_nascimento->age }} anos)
                                @else
                                    —
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item"><b>Cadastrado em</b> <span class="float-right">{{ $pessoa->created_at->format('d/m/Y') }}</span></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header"><h3 class="card-title">Contato</h3></div>
                <div class="card-body">
                    <dl class="row mb-0">
                        <dt class="col-sm-3">E-mail</dt>
                        <dd class="col-sm-9">{{ $pessoa->email ?? '—' }}</dd>
                        <dt class="col-sm-3">Telefone</dt>
                        <dd class="col-sm-9">{{ $pessoa->telefone ?? '—' }}</dd>
                    </dl>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h3 class="card-title">Endereço</h3></div>
                <div class="card-body">
                    @if ($pessoa->endereco || $pessoa->cidade)
                        {{ collect([$pessoa->endereco, $pessoa->numero])->filter()->join(', ') }}<br>
                        {{ collect([$pessoa->bairro, $pessoa->cidade])->filter()->join(' — ') }}{{ $pessoa->uf ? '/'.$pessoa->uf : '' }}<br>
                        @if ($pessoa->cep) CEP {{ $pessoa->cep_formatado }} @endif
                    @else
                        <span class="text-muted">Não informado.</span>
                    @endif
                </div>
            </div>

            @if ($pessoa->observacoes)
                <div class="card">
                    <div class="card-header"><h3 class="card-title">Observações</h3></div>
                    <div class="card-body">{!! nl2br(e($pessoa->observacoes)) !!}</div>
                </div>
            @endif
        </div>
    </div>
@stop
