@use('App\Models\Atendimento')

@csrf

@php($a = $atendimento)

{{-- Quando aberto a partir da ficha da família, volta para ela ao salvar --}}
@if (request('origem', old('origem')) === 'beneficiario')
    <input type="hidden" name="origem" value="beneficiario">
@endif

<div class="card card-primary card-outline">
    <div class="card-body">
        <div class="form-group">
            <label for="beneficiario_id">Família atendida <span class="text-danger">*</span></label>
            <select id="beneficiario_id" name="beneficiario_id" required
                    class="form-control @error('beneficiario_id') is-invalid @enderror">
                <option value="">Selecione o responsável…</option>
                @foreach ($beneficiarios as $b)
                    <option value="{{ $b->id }}" @selected((string) old('beneficiario_id', $a->beneficiario_id) === (string) $b->id)>
                        {{ $b->nome_exibicao }}{{ $b->bairro ? ' — '.$b->bairro : '' }}{{ $b->cpf ? ' — CPF '.$b->cpf_formatado : '' }}
                    </option>
                @endforeach
            </select>
            @error('beneficiario_id') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>

        <div class="form-row">
            @include('partials.campo', ['nome' => 'data', 'rotulo' => 'Data', 'tipo' => 'date', 'valor' => $a->data?->format('Y-m-d'), 'col' => 'col-md-3', 'obrigatorio' => true])
            <div class="form-group col-md-6">
                <label for="tipo">Tipo de atendimento <span class="text-danger">*</span></label>
                <select id="tipo" name="tipo" required class="form-control @error('tipo') is-invalid @enderror">
                    <option value="">Selecione…</option>
                    @foreach (Atendimento::TIPOS as $valor => $rotulo)
                        <option value="{{ $valor }}" @selected(old('tipo', $a->tipo) === $valor)>{{ $rotulo }}</option>
                    @endforeach
                </select>
                @error('tipo') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            @include('partials.campo', ['nome' => 'quantidade', 'rotulo' => 'Quantidade', 'tipo' => 'number', 'valor' => $a->quantidade, 'col' => 'col-md-3', 'ajuda' => 'Ex.: nº de cestas ou peças.'])
        </div>

        <div class="form-group mb-0">
            <label for="descricao">Descrição</label>
            <textarea id="descricao" name="descricao" rows="4" class="form-control @error('descricao') is-invalid @enderror"
                      placeholder="O que foi entregue ou combinado, observações da visita…">{{ old('descricao', $a->descricao) }}</textarea>
            @error('descricao') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between">
        <a href="{{ url()->previous() === url()->current() ? route('atendimentos.index') : url()->previous() }}" class="btn btn-default">Cancelar</a>
        <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save"></i> Salvar atendimento</button>
    </div>
</div>
