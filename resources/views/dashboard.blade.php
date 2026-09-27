@extends('layouts.app')

@section('title', 'Painel')

@section('content_header')
@stop

@section('content')
    @include('partials.alerts')

    <div class="pov-hero">
        <h2>Olá, {{ strtok(auth()->user()->name, ' ') }}!</h2>
        <p>
            Cadastro e acompanhamento das famílias atendidas pelo Projeto Oberland Jr. Vive.
            @if ($novosNoMes)
                <strong>{{ $novosNoMes }} {{ $novosNoMes > 1 ? 'famílias cadastradas' : 'família cadastrada' }} este mês.</strong>
            @endif
        </p>
        <a href="{{ route('beneficiarios.create') }}" class="btn btn-pov-orange mt-3">
            <i class="fas fa-user-plus"></i> Novo cadastro
        </a>
        <a href="{{ route('atendimentos.create') }}" class="btn btn-light mt-3 ml-1">
            <i class="fas fa-hand-holding-heart"></i> Registrar atendimento
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
                    <h3>{{ $atendimentosNoMes }}</h3>
                    <p>Atendimentos no mês</p>
                </div>
                <div class="icon"><i class="fas fa-hand-holding-heart"></i></div>
                <a href="{{ route('atendimentos.index', ['de' => today()->startOfMonth()->format('Y-m-d')]) }}" class="small-box-footer">
                    Ver atendimentos <i class="fas fa-arrow-circle-right"></i>
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
        <div class="col-lg-4">
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

        <div class="col-lg-4">
            <div class="card card-info card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-bell mr-1"></i> Sem atendimento há 60+ dias</h3>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse ($semAtendimento as $b)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    <a href="{{ route('beneficiarios.show', $b) }}#atendimentos">{{ $b->nome_exibicao }}</a>
                                    <br><small class="text-muted">
                                        @if ($b->atendimentos_max_data)
                                            Último: {{ \Illuminate\Support\Carbon::parse($b->atendimentos_max_data)->diffForHumans() }}
                                        @else
                                            Nunca atendida
                                        @endif
                                    </small>
                                </span>
                                <a href="{{ route('atendimentos.create', ['beneficiario' => $b->id]) }}" class="btn btn-sm btn-outline-primary" title="Registrar atendimento">
                                    <i class="fas fa-plus"></i>
                                </a>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">Todas as famílias ativas foram atendidas recentemente. 💙</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card card-primary card-outline">
                <div class="card-header">
                    <h3 class="card-title"><i class="fas fa-clock mr-1"></i> Últimos atendimentos</h3>
                    <div class="card-tools">
                        <a href="{{ route('atendimentos.index') }}" class="btn btn-tool">Ver todos</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <ul class="list-group list-group-flush">
                        @forelse ($ultimosAtendimentos as $a)
                            <li class="list-group-item">
                                <i class="{{ $a->icone }} text-primary mr-1"></i>
                                <strong>{{ $a->tipo_label }}</strong>
                                <br><small class="text-muted">
                                    {{ $a->data->format('d/m/Y') }} ·
                                    <a href="{{ route('beneficiarios.show', $a->beneficiario_id) }}#atendimentos">{{ $a->beneficiario->nome_exibicao }}</a>
                                </small>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">Nenhum atendimento registrado ainda.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>
    </div>
@stop
