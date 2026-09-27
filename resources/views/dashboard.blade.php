@extends('layouts.app')

@section('title', 'Painel')

@section('content_header')
@stop

@section('content')
    @include('partials.alerts')

    <div class="pov-hero">
        <h2>Olá, {{ strtok(auth()->user()->name, ' ') }}!</h2>
        <p>Cadastro e acompanhamento das famílias atendidas pelo Projeto Oberland Jr. Vive.</p>
        <a href="{{ route('beneficiarios.create') }}" class="btn btn-pov-orange mt-3">
            <i class="fas fa-user-plus"></i> Novo cadastro
        </a>
        <img src="{{ asset('img/povlogo.png') }}" alt="">
    </div>

    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-pov-blue">
                <div class="inner">
                    <h3>{{ $totalAtivos }}</h3>
                    <p>Famílias ativas</p>
                </div>
                <div class="icon"><i class="fas fa-home"></i></div>
                <a href="{{ route('beneficiarios.index', ['status' => 'ativo']) }}" class="small-box-footer">
                    Ver cadastros <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-pov-navy">
                <div class="inner">
                    <h3>{{ $pessoasAtendidas }}</h3>
                    <p>Pessoas alcançadas</p>
                </div>
                <div class="icon"><i class="fas fa-users"></i></div>
                <span class="small-box-footer">Titulares + familiares</span>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-pov-orange">
                <div class="inner">
                    <h3>{{ $novosNoMes }}</h3>
                    <p>Novos cadastros no mês</p>
                </div>
                <div class="icon"><i class="fas fa-user-plus"></i></div>
                <a href="{{ route('beneficiarios.create') }}" class="small-box-footer">
                    Cadastrar <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-pov-sky">
                <div class="inner">
                    <h3>{{ $semConsentimento }}</h3>
                    <p>Sem autorização LGPD</p>
                </div>
                <div class="icon"><i class="fas fa-file-signature"></i></div>
                <span class="small-box-footer">Colher assinatura do termo</span>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-5">
            <div class="card card-secondary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-hand-holding-heart mr-1"></i> O que as famílias precisam</h3>
                </div>
                <div class="card-body">
                    @forelse ($necessidades as $n)
                        <a href="{{ route('beneficiarios.index', ['necessidade' => $n['chave'], 'status' => 'ativo']) }}" class="d-block text-reset mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span>{{ $n['rotulo'] }}</span>
                                <strong>{{ $n['total'] }}</strong>
                            </div>
                            <div class="pov-bar"><span style="width: {{ round($n['total'] / max($totalAtivos, 1) * 100) }}%"></span></div>
                        </a>
                    @empty
                        <p class="text-muted mb-0">As necessidades aparecem aqui conforme os cadastros forem preenchidos.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-clock mr-1"></i> Últimos cadastros</h3>
                    <div class="card-tools">
                        <a href="{{ route('beneficiarios.index') }}" class="btn btn-tool">Ver todos</a>
                    </div>
                </div>
                <div class="card-body p-0 table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Bairro</th>
                                <th class="text-center">Família</th>
                                <th>Cadastro</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($ultimos as $b)
                                <tr>
                                    <td><a href="{{ route('beneficiarios.show', $b) }}">{{ $b->nome_exibicao }}</a></td>
                                    <td>{{ $b->bairro ?? '—' }}</td>
                                    <td class="text-center">{{ $b->familiares_count + 1 }}</td>
                                    <td>{{ ($b->data_cadastro ?? $b->created_at)->format('d/m/Y') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Nenhum beneficiário cadastrado ainda.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@stop
