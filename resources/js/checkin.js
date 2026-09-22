import { Html5Qrcode } from 'html5-qrcode';

document.addEventListener('DOMContentLoaded', () => {
    const app = document.getElementById('checkin-app');
    if (!app) return;

    const scanUrl = app.dataset.scanUrl;
    const csrfToken = app.dataset.csrf;
    const resultBox = document.getElementById('qr-result');

    let busy = false;
    const scanner = new Html5Qrcode('qr-reader');

    const showResult = (state, mensaje, extra = '') => {
        const styles = {
            ok: 'bg-emerald-500/10 border-emerald-500/30 text-emerald-400',
            warn: 'bg-amber-500/10 border-amber-500/30 text-amber-400',
            error: 'bg-red-500/10 border-red-500/30 text-red-400',
        };
        resultBox.className = `rounded-2xl border p-5 text-center transition-colors ${styles[state]}`;
        resultBox.innerHTML = `<p class="text-lg font-bold">${mensaje}</p>${extra ? `<p class="text-sm mt-1 opacity-80">${extra}</p>` : ''}`;
    };

    const onScan = async (decodedText) => {
        if (busy) return;
        busy = true;

        try {
            const response = await fetch(scanUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                },
                body: JSON.stringify({ qr_token: decodedText }),
            });

            const data = await response.json();
            const extra = data.asistente ? `${data.asistente.nombres}${data.asistente.empresa ? ' · ' + data.asistente.empresa : ''} (${data.asistente.codigo})` : '';

            if (data.ok) {
                showResult('ok', data.mensaje, extra);
            } else if (data.ya_asistio) {
                showResult('warn', data.mensaje, extra ? `${extra} · ingresó a las ${data.checked_in_at}` : '');
            } else {
                showResult('error', data.mensaje, extra);
            }
        } catch (e) {
            showResult('error', 'No se pudo verificar el QR. Revisa tu conexión.');
        }

        setTimeout(() => { busy = false; }, 2000);
    };

    scanner
        .start(
            { facingMode: 'environment' },
            { fps: 10, qrbox: { width: 250, height: 250 } },
            onScan,
        )
        .catch(() => {
            showResult('error', 'No se pudo acceder a la cámara.', 'Verifica los permisos del navegador.');
        });
});
