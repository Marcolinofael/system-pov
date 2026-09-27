{{-- Campo de texto: @include('partials.campo', ['nome' => 'rg', 'rotulo' => 'RG', 'valor' => $x->rg, 'col' => 'col-md-4']) --}}
<div class="form-group {{ $col ?? 'col-md-6' }}">
    <label for="{{ $id ?? $nome }}">{{ $rotulo }} @if (!empty($obrigatorio)) <span class="text-danger">*</span> @endif
    </label>
    <input type="{{ $tipo ?? 'text' }}" id="{{ $id ?? $nome }}" name="{{ $nome }}" value="{{ old($nome, $valor ?? null) }}"
           class="form-control {{ $classe ?? '' }} @error($nome) is-invalid @enderror"
           @isset($max) maxlength="{{ $max }}" @endisset
           @isset($placeholder) placeholder="{{ $placeholder }}" @endisset
           @isset($inputmode) inputmode="{{ $inputmode }}" @endisset
           @if (!empty($obrigatorio)) required @endif>
    @error($nome) <span class="invalid-feedback">{{ $message }}</span> @enderror
    @isset($ajuda) <small class="form-text text-muted">{{ $ajuda }}</small> @endisset
</div>
