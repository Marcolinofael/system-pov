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

    window.Pov = { reduzirImagem };
})();
