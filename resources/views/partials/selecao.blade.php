{{-- Lista de opções: @include('partials.selecao', ['nome' => 'sexo', 'rotulo' => 'Sexo', 'opcoes' => [...], 'valor' => $x->sexo]) --}}
<div class="form-group {{ $col ?? 'col-md-4' }}">
    <label for="{{ $nome }}">{{ $rotulo }}</label>
    <select id="{{ $nome }}" name="{{ $nome }}" class="form-control @error($nome) is-invalid @enderror">
        <option value="">Selecione…</option>
        @foreach ($opcoes as $chave => $texto)
            <option value="{{ $chave }}" @selected((string) old($nome, $valor ?? '') === (string) $chave)>{{ $texto }}</option>
        @endforeach
    </select>
    @error($nome) <span class="invalid-feedback">{{ $message }}</span> @enderror
</div>
