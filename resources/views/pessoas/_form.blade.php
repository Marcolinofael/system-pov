@csrf

<div class="card card-primary card-outline">
    <div class="card-header"><h3 class="card-title">Dados pessoais</h3></div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group col-md-8">
                <label for="nome">Nome *</label>
                <input type="text" id="nome" name="nome" value="{{ old('nome', $pessoa->nome) }}"
                       class="form-control @error('nome') is-invalid @enderror" required maxlength="150">
                @error('nome') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group col-md-4">
                <label for="cpf">CPF</label>
                <input type="text" id="cpf" name="cpf" value="{{ old('cpf', $pessoa->cpf_formatado) }}"
                       class="form-control @error('cpf') is-invalid @enderror" placeholder="000.000.000-00" maxlength="14">
                @error('cpf') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-5">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" value="{{ old('email', $pessoa->email) }}"
                       class="form-control @error('email') is-invalid @enderror">
                @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group col-md-4">
                <label for="telefone">Telefone</label>
                <input type="text" id="telefone" name="telefone" value="{{ old('telefone', $pessoa->telefone) }}"
                       class="form-control @error('telefone') is-invalid @enderror" placeholder="(00) 00000-0000" maxlength="20">
                @error('telefone') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group col-md-3">
                <label for="data_nascimento">Data de nascimento</label>
                <input type="date" id="data_nascimento" name="data_nascimento"
                       value="{{ old('data_nascimento', $pessoa->data_nascimento?->format('Y-m-d')) }}"
                       class="form-control @error('data_nascimento') is-invalid @enderror">
                @error('data_nascimento') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
</div>

<div class="card card-secondary card-outline">
    <div class="card-header"><h3 class="card-title">Endereço</h3></div>
    <div class="card-body">
        <div class="form-row">
            <div class="form-group col-md-3">
                <label for="cep">CEP</label>
                <input type="text" id="cep" name="cep" value="{{ old('cep', $pessoa->cep_formatado) }}"
                       class="form-control @error('cep') is-invalid @enderror" placeholder="00000-000" maxlength="9">
                @error('cep') <span class="invalid-feedback">{{ $message }}</span> @enderror
                <small class="form-text text-muted">O endereço é preenchido automaticamente.</small>
            </div>
            <div class="form-group col-md-7">
                <label for="endereco">Logradouro</label>
                <input type="text" id="endereco" name="endereco" value="{{ old('endereco', $pessoa->endereco) }}"
                       class="form-control @error('endereco') is-invalid @enderror" maxlength="150">
                @error('endereco') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group col-md-2">
                <label for="numero">Número</label>
                <input type="text" id="numero" name="numero" value="{{ old('numero', $pessoa->numero) }}"
                       class="form-control @error('numero') is-invalid @enderror" maxlength="20">
                @error('numero') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
        </div>
        <div class="form-row">
            <div class="form-group col-md-5">
                <label for="bairro">Bairro</label>
                <input type="text" id="bairro" name="bairro" value="{{ old('bairro', $pessoa->bairro) }}"
                       class="form-control @error('bairro') is-invalid @enderror" maxlength="100">
                @error('bairro') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group col-md-5">
                <label for="cidade">Cidade</label>
                <input type="text" id="cidade" name="cidade" value="{{ old('cidade', $pessoa->cidade) }}"
                       class="form-control @error('cidade') is-invalid @enderror" maxlength="100">
                @error('cidade') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
            <div class="form-group col-md-2">
                <label for="uf">UF</label>
                <select id="uf" name="uf" class="form-control @error('uf') is-invalid @enderror">
                    <option value="">—</option>
                    @foreach (\App\Models\Pessoa::UFS as $uf)
                        <option value="{{ $uf }}" @selected(old('uf', $pessoa->uf) === $uf)>{{ $uf }}</option>
                    @endforeach
                </select>
                @error('uf') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
</div>

<div class="card card-secondary card-outline">
    <div class="card-body">
        <div class="form-group">
            <label for="observacoes">Observações</label>
            <textarea id="observacoes" name="observacoes" rows="3"
                      class="form-control @error('observacoes') is-invalid @enderror">{{ old('observacoes', $pessoa->observacoes) }}</textarea>
            @error('observacoes') <span class="invalid-feedback">{{ $message }}</span> @enderror
        </div>
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="ativo" name="ativo" value="1"
                   @checked(old('ativo', $pessoa->ativo))>
            <label class="custom-control-label" for="ativo">Cadastro ativo</label>
        </div>
    </div>
    <div class="card-footer">
        <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Salvar</button>
        <a href="{{ url()->previous() === url()->current() ? route('pessoas.index') : url()->previous() }}"
           class="btn btn-default">Cancelar</a>
    </div>
</div>

@section('js')
    <script>
        // Máscaras simples
        const mascaras = {
            cpf: v => v.replace(/\D/g, '').slice(0, 11)
                .replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d{1,2})$/, '$1-$2'),
            cep: v => v.replace(/\D/g, '').slice(0, 8).replace(/(\d{5})(\d)/, '$1-$2'),
            telefone: v => {
                const d = v.replace(/\D/g, '').slice(0, 11);
                return d.length > 10
                    ? d.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3')
                    : d.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3').replace(/-$/, '');
            },
        };
        Object.keys(mascaras).forEach(id => {
            const el = document.getElementById(id);
            el.addEventListener('input', () => el.value = mascaras[id](el.value));
        });

        // Busca de endereço pelo CEP (ViaCEP)
        document.getElementById('cep').addEventListener('blur', async function () {
            const cep = this.value.replace(/\D/g, '');
            if (cep.length !== 8) return;
            try {
                const r = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                const d = await r.json();
                if (d.erro) return;
                document.getElementById('endereco').value = d.logradouro || '';
                document.getElementById('bairro').value = d.bairro || '';
                document.getElementById('cidade').value = d.localidade || '';
                document.getElementById('uf').value = d.uf || '';
                document.getElementById('numero').focus();
            } catch (e) { /* sem conexão: preenchimento manual */ }
        });
    </script>
@stop
