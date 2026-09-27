@extends('layouts.app')

@use('App\Models\Atendimento')

@section('title', 'Atendimentos')

@section('content_header')
    <div class="d-flex justify-content-between align-items-center">
        <h1>Atendimentos</h1>
        <a href="{{ route('atendimentos.create') }}" class="btn btn-pov-orange">
            <i class="fas fa-plus"></i> Registrar atendimento
        </a>
    </div>
@stop

@section('content')
    @include('partials.alerts')

    <div class="card card-primary card-outline">
        <div class="card-header">
            <form method="GET" action="{{ route('atendimentos.index') }}" class="form-row">
                <div class="col-md-4 mb-2 mb-md-0">
                    <input type="search" name="q" value="{{ request('q') }}" class="form-control" placeholder="Família: nome, CPF, NIS ou bairro">
                </div>
                <div class="col-md-3 mb-2 mb-md-0">
                    <select name="tipo" class="form-control">
                        <option value="">Todos os tipos</option>
                        @foreach (Atendimento::TIPOS as $valor => $rotulo)
                            <option value="{{ $valor }}" @selected(request('tipo') === $valor)>{{ $rotulo }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 col-6 mb-2 mb-md-0">
                    <input type="date" name="de" value="{{ request('de') }}" class="form-control" title="De">
                </div>
                <div class="col-md-2 col-6 mb-2 mb-md-0">
                    <input type="date" name="ate" value="{{ request('ate') }}" class="form-control" title="Até">
                </div>
                <div class="col-md-1">
                    <button class="btn btn-primary btn-block" title="Filtrar"><i class="fas fa-search"></i></button>
                </div>
            </form>
        </div>

        @if ($resumo->isNotEmpty())
            <div class="card-body border-bottom pb-2">
                @foreach ($resumo as $r)
                    <span class="pov-tag mb-2">
                        <i class="{{ Atendimento::ICONES[$r->tipo] ?? '' }} mr-1"></i>
                        {{ Atendimento::TIPOS[$r->tipo] ?? $r->tipo }}:
                        <strong>{{ $r->total }}</strong> ({{ $r->familias }} {{ $r->familias > 1 ? 'famílias' : 'família' }}{{ $r->itens ? ', '.$r->itens.' itens' : '' }})
                    </span>
                @endforeach
            </div>
        @endif

        <div class="card-body p-0 table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Data</th>
                        <th>Família</th>
                        <th>Atendimento</th>
                        <th class="text-center">Qtd.</th>
                        <th>Responsável</th>
                        <th class="text-right">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($atendimentos as $a)
                        <tr>
                            <td class="text-nowrap">{{ $a->data->format('d/m/Y') }}</td>
                            <td><a href="{{ route('beneficiarios.show', $a->beneficiario_id) }}#atendimentos">{{ $a->beneficiario->nome_exibicao }}</a></td>
                            <td>
                                <i class="{{ $a->icone }} text-primary mr-1"></i> {{ $a->tipo_label }}
                                @if ($a->descricao)
                                    <br><small class="text-muted">{{ \Illuminate\Support\Str::limit($a->descricao, 90) }}</small>
                                @endif
                                @if ($a->fotos->isNotEmpty())
                                    <div class="mt-1">
                                        @foreach ($a->fotos as $foto)
                                            <a href="{{ $foto->url }}" target="_blank" title="Ver foto ampliada">
                                                <img src="{{ $foto->url }}" class="pov-thumb" style="width:44px;height:44px" alt="Foto" loading="lazy">
                                            </a>
                                        @endforeach
                                    </div>
                                @endif
                            </td>
                            <td class="text-center">{{ $a->quantidade ?? '—' }}</td>
                            <td>{{ $a->responsavel?->name ?? '—' }}</td>
                            <td class="text-right text-nowrap">
                                @if ($a->podeSerAlteradoPor(auth()->user()))
                                    <a href="{{ route('atendimentos.edit', $a) }}" class="btn btn-sm btn-info" title="Editar"><i class="fas fa-edit"></i></a>
                                    <form action="{{ route('atendimentos.destroy', $a) }}" method="POST" class="d-inline"
                                          onsubmit="return confirm('Excluir este atendimento?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger" title="Excluir"><i class="fas fa-trash"></i></button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="fas fa-hand-holding-heart fa-2x mb-2 d-block"></i>
                                Nenhum atendimento encontrado.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($atendimentos->hasPages())
            <div class="card-footer clearfix">
                <div class="float-right">{{ $atendimentos->links() }}</div>
            </div>
        @endif
    </div>
@stop
