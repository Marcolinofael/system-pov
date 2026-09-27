{{-- Campo de fotos do atendimento. $existentes = fotos já salvas (edição); $id = prefixo único do campo --}}
@use('App\Models\Atendimento')
@php($existentes = $existentes ?? collect())
@php($id = $id ?? 'fotos')

<div class="form-group">
    <label class="d-block">Fotos <small class="text-muted">(até {{ Atendimento::MAX_FOTOS }} — ex.: a entrega, o comprovante assinado)</small></label>

    @if ($existentes->isNotEmpty())
        <div class="d-flex flex-wrap mb-2">
            @foreach ($existentes as $foto)
                <label class="pov-thumb-remover mr-2 mb-2" title="Marque para remover">
                    <img src="{{ $foto->url }}" class="pov-thumb" alt="Foto do atendimento">
                    <span class="custom-control custom-checkbox mt-1">
                        <input type="checkbox" class="custom-control-input" id="{{ $id }}-remover-{{ $foto->id }}" name="remover_fotos[]" value="{{ $foto->id }}">
                        <span class="custom-control-label small">Remover</span>
                    </span>
                </label>
            @endforeach
        </div>
    @endif

    @php($restantes = Atendimento::MAX_FOTOS - $existentes->count())
    @if ($restantes > 0)
        <label for="{{ $id }}" class="btn btn-sm btn-outline-primary mb-0">
            <i class="fas fa-camera"></i> Tirar / anexar fotos
        </label>
        <input type="file" id="{{ $id }}" name="fotos[]" accept="image/*" multiple class="d-none"
               data-fotos data-max="{{ $restantes }}" data-preview="#{{ $id }}-preview">
        <div id="{{ $id }}-preview" class="d-flex flex-wrap mt-2"></div>
    @else
        <small class="text-muted d-block">Limite de fotos atingido. Para trocar, marque uma para remover e salve; depois anexe a nova.</small>
    @endif

    @error('fotos') <span class="text-danger small d-block">{{ $message }}</span> @enderror
    @error('fotos.*') <span class="text-danger small d-block">{{ $message }}</span> @enderror
    @if ($errors->any() && ! $errors->has('fotos') && ! $errors->has('fotos.*'))
        <small class="text-warning d-block">Se tinha escolhido fotos, selecione de novo.</small>
    @endif
</div>
