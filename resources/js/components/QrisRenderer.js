import QRCode from 'qrcode';

/**
 * Cari semua elemen dengan atribut data-qris-code lalu render payload
 * QRIS (EMVCo) menjadi gambar QR menggunakan canvas/lib qrcode.
 */
export function initQrisRenderer() {
    const nodes = document.querySelectorAll('[data-qris-code]');
    if (!nodes.length) {
        return;
    }

    nodes.forEach((node) => {
        const code = (node.getAttribute('data-qris-code') || '').trim();
        if (!code) {
            return;
        }

        const wrapper = node.closest('[data-qris-wrapper]');
        const placeholder = wrapper ? wrapper.querySelector('[data-qris-placeholder]') : null;

        QRCode.toDataURL(code, {
            errorCorrectionLevel: 'M',
            margin: 2,
            width: 640,
            color: { dark: '#000000', light: '#ffffff' },
        })
            .then((url) => {
                node.setAttribute('src', url);
                node.classList.remove('hidden');
                node.setAttribute('data-qris-ready', '1');
                if (placeholder) {
                    placeholder.classList.add('hidden');
                }
            })
            .catch(() => {
                if (placeholder) {
                    placeholder.textContent = 'QRIS gagal dimuat';
                }
            });
    });
}
