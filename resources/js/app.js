import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();

if (document.querySelector('[data-chart]')) {
    import('./charts').then(({ renderCharts }) => renderCharts());
}

// Confirmaciones del ERP mediante el modal centrado del layout.
document.addEventListener('submit', (event) => {
    const form = event.target;

    if (!(form instanceof HTMLFormElement) || !form.dataset.confirm) {
        return;
    }

    if (form.dataset.confirmed === 'true') {
        return;
    }

    event.preventDefault();

    window.dispatchEvent(new CustomEvent('sge-confirm', {
        detail: {
            form,
            message: form.dataset.confirm,
        },
    }));
}, true);
