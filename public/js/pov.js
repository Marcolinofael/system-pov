/*
 * Scripts comuns do sistema.
 *
 * Campos de várias fotos: <input type="file" multiple data-fotos data-max="5" data-preview="#alvo">
 * As imagens são reduzidas no navegador (máx. 1280px, JPEG) antes do envio e ganham prévia.
 */
(function () {
    async function reduzirImagem(arquivo, max = 1280, qualidade = 0.82) {
        try {
            const bitmap = await createImageBitmap(arquivo, { imageOrientation: 'from-image' });
            const escala = Math.min(1, max / Math.max(bitmap.width, bitmap.height));
            const canvas = document.createElement('canvas');
            canvas.width = Math.round(bitmap.width * escala);
            canvas.height = Math.round(bitmap.height * escala);
            canvas.getContext('2d').drawImage(bitmap, 0, 0, canvas.width, canvas.height);
            const blob = await new Promise(ok => canvas.toBlob(ok, 'image/jpeg', qualidade));
            if (!blob || blob.size >= arquivo.size) return arquivo;
            return new File([blob], arquivo.name.replace(/\.\w+$/, '') + '.jpg', { type: 'image/jpeg' });
        } catch (e) {
            return arquivo; // navegador sem suporte: envia o original
        }
    }

    document.addEventListener('change', async function (e) {
        const campo = e.target;
        if (!campo.matches('input[type=file][data-fotos]')) return;

        const max = parseInt(campo.dataset.max || '5', 10);
        let arquivos = Array.from(campo.files);
        if (arquivos.length > max) {
            alert('Você pode anexar no máximo ' + max + (max > 1 ? ' fotos' : ' foto') + ' aqui.');
            arquivos = arquivos.slice(0, max);
        }

        const alvo = document.querySelector(campo.dataset.preview);
        if (alvo) alvo.innerHTML = '<small class="text-muted"><i class="fas fa-spinner fa-spin"></i> Preparando fotos…</small>';

        const reduzidas = await Promise.all(arquivos.map(a => reduzirImagem(a)));
        const dt = new DataTransfer();
        reduzidas.forEach(a => dt.items.add(a));
        campo.files = dt.files;

        if (alvo) {
            alvo.innerHTML = '';
            reduzidas.forEach(a => {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(a);
                img.className = 'pov-thumb';
                img.alt = 'Prévia';
                alvo.appendChild(img);
            });
        }
    });

    /*
     * Galeria em carrossel: links com o mesmo data-galeria abrem juntos num modal.
     * <a href="foto.jpg" data-galeria="atendimento-12" data-legenda="Cesta básica — 10/09/2026">…</a>
     * Sem JavaScript, o link continua abrindo a foto normalmente.
     */
    let modal = null;

    function criarModal() {
        const div = document.createElement('div');
        div.className = 'modal fade pov-galeria';
        div.tabIndex = -1;
        div.setAttribute('aria-hidden', 'true');
        div.innerHTML = `
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title"></h5>
                        <span class="pov-galeria-contador ml-auto mr-3"></span>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">&times;</button>
                    </div>
                    <div class="modal-body p-0">
                        <div class="carousel slide" data-interval="false" data-keyboard="true" data-touch="true">
                            <div class="carousel-inner"></div>
                            <a class="carousel-control-prev" href="#" role="button" data-slide="prev" aria-label="Anterior">
                                <span class="carousel-control-prev-icon"></span>
                            </a>
                            <a class="carousel-control-next" href="#" role="button" data-slide="next" aria-label="Próxima">
                                <span class="carousel-control-next-icon"></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>`;
        document.body.appendChild(div);

        const $carrossel = window.jQuery(div.querySelector('.carousel'));
        // Os controles apontam para o próprio carrossel (evita depender de id)
        div.querySelectorAll('[data-slide]').forEach(controle => {
            controle.addEventListener('click', ev => {
                ev.preventDefault();
                $carrossel.carousel(controle.dataset.slide);
            });
        });
        $carrossel.on('slid.bs.carousel', () => atualizarContador(div));

        return div;
    }

    function atualizarContador(div) {
        const itens = div.querySelectorAll('.carousel-item');
        const ativo = Array.from(itens).findIndex(i => i.classList.contains('active'));
        div.querySelector('.pov-galeria-contador').textContent = itens.length > 1 ? `${ativo + 1} / ${itens.length}` : '';
    }

    document.addEventListener('click', function (e) {
        const link = e.target.closest('a[data-galeria]');
        if (!link || !window.jQuery || !window.jQuery.fn.carousel) return;
        e.preventDefault();

        modal = modal || criarModal();
        const grupo = Array.from(document.querySelectorAll(`a[data-galeria="${link.dataset.galeria}"]`));
        const inicio = grupo.indexOf(link);

        modal.querySelector('.modal-title').textContent = link.dataset.legenda || 'Fotos';
        modal.querySelector('.carousel-inner').innerHTML = grupo.map((a, i) => `
            <div class="carousel-item ${i === inicio ? 'active' : ''}">
                <img src="${a.href}" class="d-block mx-auto" alt="Foto ${i + 1}">
            </div>`).join('');
        modal.querySelectorAll('[data-slide]').forEach(c => c.classList.toggle('d-none', grupo.length < 2));
        atualizarContador(modal);

        window.jQuery(modal).modal('show');
    });

    window.Pov = { reduzirImagem };
})();
