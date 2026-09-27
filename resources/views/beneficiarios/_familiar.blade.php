{{-- Linha da composição familiar. $i = índice (ou "__I__" no modelo usado pelo JavaScript) --}}
<tr>
    <td>
        <input type="text" name="familiares[{{ $i }}][nome]" value="{{ $f['nome'] ?? '' }}" maxlength="150"
               class="form-control form-control-sm @error("familiares.$i.nome") is-invalid @enderror">
        @error("familiares.$i.nome") <span class="invalid-feedback">{{ $message }}</span> @enderror
    </td>
    <td>
        <select name="familiares[{{ $i }}][parentesco]" class="form-control form-control-sm @error("familiares.$i.parentesco") is-invalid @enderror">
            <option value="">Selecione…</option>
            @foreach (\App\Models\Familiar::PARENTESCOS as $valor => $rotulo)
                <option value="{{ $valor }}" @selected(($f['parentesco'] ?? '') === $valor)>{{ $rotulo }}</option>
            @endforeach
        </select>
        @error("familiares.$i.parentesco") <span class="invalid-feedback">{{ $message }}</span> @enderror
    </td>
    <td>
        <input type="date" name="familiares[{{ $i }}][data_nascimento]" value="{{ $f['data_nascimento'] ?? '' }}"
               class="form-control form-control-sm @error("familiares.$i.data_nascimento") is-invalid @enderror">
        @error("familiares.$i.data_nascimento") <span class="invalid-feedback">{{ $message }}</span> @enderror
    </td>
    <td>
        <input type="text" name="familiares[{{ $i }}][renda]" value="{{ $f['renda'] ?? '' }}" inputmode="numeric" placeholder="0,00"
               class="form-control form-control-sm mask-moeda @error("familiares.$i.renda") is-invalid @enderror">
        @error("familiares.$i.renda") <span class="invalid-feedback">{{ $message }}</span> @enderror
    </td>
    <td>
        <input type="text" name="familiares[{{ $i }}][observacao]" value="{{ $f['observacao'] ?? '' }}" maxlength="150"
               class="form-control form-control-sm" placeholder="Ex.: estuda, gestante…">
    </td>
    <td class="text-right">
        <button type="button" class="btn btn-sm btn-outline-danger remover-familiar" title="Remover"><i class="fas fa-times"></i></button>
    </td>
</tr>
