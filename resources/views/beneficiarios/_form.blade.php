@use('App\Models\Beneficiario', 'B')

@csrf

@php
    $b = $beneficiario;
    $moeda = fn ($v) => $v === null || $v === '' ? '' : number_format((float) $v, 2, ',', '.');

    $familiares = old('familiares', $b->familiares->map(fn ($f) => [
        'nome' => $f->nome,
        'parentesco' => $f->parentesco,
        'data_nascimento' => $f->data_nascimento?->format('Y-m-d'),
        'renda' => $moeda($f->renda),
        'observacao' => $f->observacao,
    ])->all());

    $beneficiosMarcados = old('beneficios', $b->beneficios ?? []);
    $necessidadesMarcadas = old('necessidades', $b->necessidades ?? []);
@endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <i class="fas fa-exclamation-triangle"></i>
        Confira os campos destacados em vermelho ({{ $errors->count() }} {{ $errors->count() > 1 ? 'problemas' : 'problema' }}).
    </div>
@endif

{{-- 1. Identificação --}}
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title"><span class="pov-section-icon"><i class="fas fa-id-card"></i></span>Identificação do responsável</h3>
    </div>
    <div class="card-body">
      <div class="row">
        {{-- Foto de cadastro --}}
        <div class="col-lg-3 mb-3">
            <div class="pov-foto-campo">
                <img id="foto-preview" class="pov-foto-preview" alt="Foto de cadastro"
                     src="{{ $b->foto_url ?? asset('img/sem-foto.svg') }}" data-vazia="{{ asset('img/sem-foto.svg') }}">

                <input type="file" id="foto" name="foto" accept="image/*" class="d-none">
                <input type="hidden" id="remover_foto" name="remover_foto" value="0">

                <div class="btn-group btn-group-sm">
                    <label for="foto" class="btn btn-primary mb-0"><i class="fas fa-camera"></i> {{ $b->foto ? 'Trocar foto' : 'Tirar / escolher foto' }}</label>
                    <button type="button" id="remover-foto" class="btn btn-outline-danger {{ $b->foto ? '' : 'd-none' }}" title="Remover foto">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
                <small class="text-muted text-center">No celular, abre a câmera.</small>
                @error('foto') <span class="text-danger small text-center">{{ $message }}</span> @enderror
                @if ($errors->any() && ! $errors->has('foto'))
                    <small class="text-warning text-center">Se tinha escolhido uma foto, selecione de novo.</small>
                @endif
            </div>
        </div>

        <div class="col-lg-9">
        <div class="form-row">
            @include('partials.campo', ['nome' => 'nome', 'rotulo' => 'Nome completo', 'valor' => $b->nome, 'col' => 'col-md-6', 'max' => 150, 'obrigatorio' => true])
            @include('partials.campo', ['nome' => 'nome_social', 'rotulo' => 'Nome social', 'valor' => $b->nome_social, 'col' => 'col-md-6', 'max' => 150, 'ajuda' => 'Como a pessoa prefere ser chamada, se diferente do registro.'])
        </div>
        <div class="form-row">
            @include('partials.campo', ['nome' => 'cpf', 'rotulo' => 'CPF', 'valor' => $b->cpf_formatado, 'col' => 'col-md-3', 'max' => 14, 'placeholder' => '000.000.000-00', 'inputmode' => 'numeric'])
            @include('partials.campo', ['nome' => 'rg', 'rotulo' => 'RG', 'valor' => $b->rg, 'col' => 'col-md-3', 'max' => 20])
            @include('partials.campo', ['nome' => 'nis', 'rotulo' => 'NIS / PIS (CadÚnico)', 'valor' => $b->nis, 'col' => 'col-md-3', 'max' => 11, 'inputmode' => 'numeric'])
            @include('partials.campo', ['nome' => 'data_nascimento', 'rotulo' => 'Data de nascimento', 'tipo' => 'date', 'valor' => $b->data_nascimento?->format('Y-m-d'), 'col' => 'col-md-3'])
        </div>
        <div class="form-row">
            @include('partials.selecao', ['nome' => 'sexo', 'rotulo' => 'Sexo', 'opcoes' => B::SEXOS, 'valor' => $b->sexo, 'col' => 'col-md-3'])
            @include('partials.selecao', ['nome' => 'estado_civil', 'rotulo' => 'Estado civil', 'opcoes' => B::ESTADOS_CIVIS, 'valor' => $b->estado_civil, 'col' => 'col-md-3'])
            @include('partials.selecao', ['nome' => 'cor_raca', 'rotulo' => 'Cor / raça', 'opcoes' => B::CORES_RACAS, 'valor' => $b->cor_raca, 'col' => 'col-md-3'])
            @include('partials.selecao', ['nome' => 'escolaridade', 'rotulo' => 'Escolaridade', 'opcoes' => B::ESCOLARIDADES, 'valor' => $b->escolaridade, 'col' => 'col-md-3'])
        </div>
        </div>
      </div>
    </div>
</div>

{{-- 2. Contato e endereço --}}
<div class="card card-primary card-outline">
    <div class="card-header">
        <h3 class="card-title"><span class="pov-section-icon"><i class="fas fa-map-marker-alt"></i></span>Contato e endereço</h3>
    </div>
    <div class="card-body">
        <div class="form-row">
            @include('partials.campo', ['nome' => 'telefone', 'rotulo' => 'Telefone / WhatsApp', 'valor' => $b->telefone, 'col' => 'col-md-4', 'max' => 20, 'placeholder' => '(22) 00000-0000', 'classe' => 'mask-telefone', 'inputmode' => 'tel'])
            @include('partials.campo', ['nome' => 'telefone_recado', 'rotulo' => 'Telefone para recado', 'valor' => $b->telefone_recado, 'col' => 'col-md-4', 'max' => 20, 'classe' => 'mask-telefone', 'inputmode' => 'tel'])
            @include('partials.campo', ['nome' => 'email', 'rotulo' => 'E-mail', 'tipo' => 'email', 'valor' => $b->email, 'col' => 'col-md-4', 'max' => 150])
        </div>
        <div class="form-row">
            @include('partials.campo', ['nome' => 'cep', 'rotulo' => 'CEP', 'valor' => $b->cep_formatado, 'col' => 'col-md-2', 'max' => 9, 'placeholder' => '00000-000', 'inputmode' => 'numeric', 'ajuda' => 'Preenche o endereço.'])
            @include('partials.campo', ['nome' => 'endereco', 'rotulo' => 'Logradouro', 'valor' => $b->endereco, 'col' => 'col-md-6', 'max' => 150])
            @include('partials.campo', ['nome' => 'numero', 'rotulo' => 'Número', 'valor' => $b->numero, 'col' => 'col-md-2', 'max' => 20])
            @include('partials.campo', ['nome' => 'complemento', 'rotulo' => 'Complemento', 'valor' => $b->complemento, 'col' => 'col-md-2', 'max' => 60])
        </div>
        <div class="form-row">
            @include('partials.campo', ['nome' => 'bairro', 'rotulo' => 'Bairro', 'valor' => $b->bairro, 'col' => 'col-md-4', 'max' => 100])
            @include('partials.campo', ['nome' => 'cidade', 'rotulo' => 'Cidade', 'valor' => $b->cidade, 'col' => 'col-md-4', 'max' => 100])
            @include('partials.selecao', ['nome' => 'uf', 'rotulo' => 'UF', 'opcoes' => array_combine(B::UFS, B::UFS), 'valor' => $b->uf, 'col' => 'col-md-1'])
            @include('partials.campo', ['nome' => 'ponto_referencia', 'rotulo' => 'Ponto de referência', 'valor' => $b->ponto_referencia, 'col' => 'col-md-3', 'max' => 150])
        </div>
    </div>
</div>

{{-- 3. Composição familiar --}}
<div class="card card-info card-outline">
    <div class="card-header">
        <h3 class="card-title"><span class="pov-section-icon"><i class="fas fa-users"></i></span>Composição familiar</h3>
        <div class="card-tools">
            <button type="button" class="btn btn-sm btn-primary" id="add-familiar"><i class="fas fa-plus"></i> Adicionar pessoa</button>
        </div>
    </div>
    <div class="card-body">
        <p class="text-muted small mb-3">Pessoas que moram com o responsável. O responsável já é contado automaticamente.</p>

        <div class="table-responsive">
            <table class="table table-sm mb-0" id="tabela-familiares">
                <thead>
                    <tr>
                        <th style="min-width: 200px">Nome *</th>
                        <th style="min-width: 170px">Parentesco *</th>
                        <th style="min-width: 150px">Nascimento</th>
                        <th style="min-width: 120px">Renda (R$)</th>
                        <th style="min-width: 160px">Observação</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($familiares as $i => $f)
                        @include('beneficiarios._familiar', ['i' => $i, 'f' => $f])
                    @endforeach
                </tbody>
            </table>
        </div>
        <p class="text-muted text-center py-2 mb-0 {{ count($familiares) ? 'd-none' : '' }}" id="sem-familiares">
            Nenhuma pessoa adicionada — o responsável mora sozinho(a).
        </p>

        <template id="modelo-familiar">
            @include('beneficiarios._familiar', ['i' => '__I__', 'f' => []])
        </template>
    </div>
</div>

{{-- 4. Situação socioeconômica --}}
<div class="card card-secondary card-outline">
    <div class="card-header">
        <h3 class="card-title"><span class="pov-section-icon"><i class="fas fa-home"></i></span>Situação socioeconômica</h3>
    </div>
    <div class="card-body">
        <div class="form-row">
            @include('partials.selecao', ['nome' => 'situacao_moradia', 'rotulo' => 'Moradia', 'opcoes' => B::MORADIAS, 'valor' => $b->situacao_moradia, 'col' => 'col-md-4'])
            @include('partials.selecao', ['nome' => 'situacao_trabalho', 'rotulo' => 'Trabalho do responsável', 'opcoes' => B::TRABALHO, 'valor' => $b->situacao_trabalho, 'col' => 'col-md-4'])
            @include('partials.campo', ['nome' => 'renda_familiar', 'rotulo' => 'Renda familiar mensal (R$)', 'valor' => $moeda($b->renda_familiar), 'col' => 'col-md-4', 'classe' => 'mask-moeda', 'inputmode' => 'numeric', 'placeholder' => '0,00', 'ajuda' => 'Soma de todas as rendas da casa, incluindo benefícios.'])
        </div>

        <div class="form-row">
            <div class="form-group col-md-6">
                <label>Benefícios que a família recebe</label>
                <div class="pov-checks">
                    @foreach (B::BENEFICIOS as $valor => $rotulo)
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="beneficio-{{ $valor }}" name="beneficios[]" value="{{ $valor }}"
                                   @checked(in_array($valor, $beneficiosMarcados))>
                            <label class="custom-control-label font-weight-normal" for="beneficio-{{ $valor }}">{{ $rotulo }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="form-group col-md-6">
                <label>Saúde</label>
                <div class="custom-control custom-switch mb-2">
                    <input type="checkbox" class="custom-control-input" id="possui_deficiencia" name="possui_deficiencia" value="1"
                           @checked(old('possui_deficiencia', $b->possui_deficiencia))>
                    <label class="custom-control-label font-weight-normal" for="possui_deficiencia">Há pessoa com deficiência na família</label>
                </div>
                <textarea id="saude" name="saude" rows="4" class="form-control @error('saude') is-invalid @enderror"
                          placeholder="Doenças crônicas, deficiências, medicamentos de uso contínuo, gestantes…">{{ old('saude', $b->saude) }}</textarea>
                @error('saude') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
        </div>
    </div>
</div>

{{-- 5. Atendimento --}}
<div class="card card-secondary card-outline">
    <div class="card-header">
        <h3 class="card-title"><span class="pov-section-icon"><i class="fas fa-hand-holding-heart"></i></span>Necessidades e atendimento</h3>
    </div>
    <div class="card-body">
        <div class="form-group">
            <label>Do que a família precisa?</label>
            <div class="row pov-checks">
                @foreach (B::NECESSIDADES as $valor => $rotulo)
                    <div class="col-md-4">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="necessidade-{{ $valor }}" name="necessidades[]" value="{{ $valor }}"
                                   @checked(in_array($valor, $necessidadesMarcadas))>
                            <label class="custom-control-label font-weight-normal" for="necessidade-{{ $valor }}">{{ $rotulo }}</label>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="form-row">
            @include('partials.campo', ['nome' => 'data_cadastro', 'rotulo' => 'Data do cadastro', 'tipo' => 'date', 'valor' => $b->data_cadastro?->format('Y-m-d'), 'col' => 'col-md-3'])
            <div class="form-group col-md-9">
                <label for="observacoes">Observações do atendimento</label>
                <textarea id="observacoes" name="observacoes" rows="3" class="form-control @error('observacoes') is-invalid @enderror"
                          placeholder="Relato da visita, encaminhamentos, combinados com a família…">{{ old('observacoes', $b->observacoes) }}</textarea>
                @error('observacoes') <span class="invalid-feedback">{{ $message }}</span> @enderror
            </div>
        </div>

        <div class="custom-control custom-checkbox mb-2">
            <input type="checkbox" class="custom-control-input" id="consentimento_lgpd" name="consentimento_lgpd" value="1"
                   @checked(old('consentimento_lgpd', $b->consentimento_lgpd))>
            <label class="custom-control-label font-weight-normal" for="consentimento_lgpd">
                O responsável <strong>autorizou</strong> o uso dos seus dados pelo projeto, conforme a LGPD (termo assinado).
            </label>
        </div>
        <div class="custom-control custom-switch">
            <input type="checkbox" class="custom-control-input" id="ativo" name="ativo" value="1" @checked(old('ativo', $b->ativo))>
            <label class="custom-control-label font-weight-normal" for="ativo">Cadastro ativo (família em acompanhamento)</label>
        </div>
    </div>
    <div class="card-footer d-flex justify-content-between">
        <a href="{{ $b->exists ? route('beneficiarios.show', $b) : route('beneficiarios.index') }}" class="btn btn-default">Cancelar</a>
        <button type="submit" class="btn btn-primary px-4"><i class="fas fa-save"></i> Salvar cadastro</button>
    </div>
</div>

@section('js')
    <script>
        (function () {
            const soDigitos = v => v.replace(/\D/g, '');
            const mascaras = {
                cpf: v => soDigitos(v).slice(0, 11)
                    .replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d)/, '$1.$2').replace(/(\d{3})(\d{1,2})$/, '$1-$2'),
                cep: v => soDigitos(v).slice(0, 8).replace(/(\d{5})(\d)/, '$1-$2'),
                nis: v => soDigitos(v).slice(0, 11),
            };
            Object.keys(mascaras).forEach(id => {
                const el = document.getElementById(id);
                el.addEventListener('input', () => el.value = mascaras[id](el.value));
            });

            const telefone = v => {
                const d = soDigitos(v).slice(0, 11);
                return d.length > 10
                    ? d.replace(/(\d{2})(\d{5})(\d{0,4})/, '($1) $2-$3')
                    : d.replace(/(\d{2})(\d{4})(\d{0,4})/, '($1) $2-$3').replace(/-$/, '');
            };
            const moeda = v => {
                const d = soDigitos(v);
                return d ? (parseInt(d, 10) / 100).toLocaleString('pt-BR', { minimumFractionDigits: 2 }) : '';
            };

            // Delegação: vale também para as linhas de familiares criadas depois
            document.addEventListener('input', e => {
                if (e.target.classList.contains('mask-telefone')) e.target.value = telefone(e.target.value);
                if (e.target.classList.contains('mask-moeda')) e.target.value = moeda(e.target.value);
            });

            // Endereço pelo CEP (ViaCEP)
            document.getElementById('cep').addEventListener('blur', async function () {
                const cep = soDigitos(this.value);
                if (cep.length !== 8) return;
                try {
                    const d = await (await fetch(`https://viacep.com.br/ws/${cep}/json/`)).json();
                    if (d.erro) return;
                    document.getElementById('endereco').value = d.logradouro || '';
                    document.getElementById('bairro').value = d.bairro || '';
                    document.getElementById('cidade').value = d.localidade || '';
                    document.getElementById('uf').value = d.uf || '';
                    document.getElementById('numero').focus();
                } catch (e) { /* sem conexão: preenchimento manual */ }
            });

            // Foto: reduz no navegador (máx. 800px, JPEG) antes de enviar — upload leve até no 3G
            const campoFoto = document.getElementById('foto');
            const preview = document.getElementById('foto-preview');
            const remover = document.getElementById('remover-foto');
            const flagRemover = document.getElementById('remover_foto');

            campoFoto.addEventListener('change', async () => {
                const original = campoFoto.files[0];
                if (!original) return;
                flagRemover.value = '0';
                remover.classList.remove('d-none');
                preview.src = URL.createObjectURL(original);

                try {
                    const bitmap = await createImageBitmap(original, { imageOrientation: 'from-image' });
                    const escala = Math.min(1, 800 / Math.max(bitmap.width, bitmap.height));
                    const canvas = document.createElement('canvas');
                    canvas.width = Math.round(bitmap.width * escala);
                    canvas.height = Math.round(bitmap.height * escala);
                    canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height);
                    const blob = await new Promise(ok => canvas.toBlob(ok, 'image/jpeg', 0.85));
                    if (!blob || blob.size >= original.size) return;
                    const dt = new DataTransfer();
                    dt.items.add(new File([blob], 'foto.jpg', { type: 'image/jpeg' }));
                    campoFoto.files = dt.files;
                } catch (e) { /* navegador sem suporte: envia o arquivo original */ }
            });

            remover.addEventListener('click', () => {
                campoFoto.value = '';
                flagRemover.value = '1';
                preview.src = preview.dataset.vazia;
                remover.classList.add('d-none');
            });

            // Composição familiar
            const corpo = document.querySelector('#tabela-familiares tbody');
            const vazio = document.getElementById('sem-familiares');
            const modelo = document.getElementById('modelo-familiar').innerHTML;
            let proximo = {{ $familiares ? max(array_keys($familiares)) + 1 : 0 }};

            const atualizar = () => vazio.classList.toggle('d-none', corpo.children.length > 0);

            document.getElementById('add-familiar').addEventListener('click', () => {
                corpo.insertAdjacentHTML('beforeend', modelo.replaceAll('__I__', proximo++));
                corpo.lastElementChild.querySelector('input').focus();
                atualizar();
            });

            corpo.addEventListener('click', e => {
                const botao = e.target.closest('.remover-familiar');
                if (!botao) return;
                botao.closest('tr').remove();
                atualizar();
            });
        })();
    </script>
@stop
