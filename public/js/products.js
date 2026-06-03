"use strict";
const formToggle = document.querySelectorAll('.form-toggle');
formToggle.forEach(formElement => {
    formElement.addEventListener('submit', async (e) => {
        e.preventDefault(); //
        const eTarget = e.target;
        // Token CSRF desde el meta tag de la página
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        const csrfToken = csrfMeta ? csrfMeta.content : '';
        try {
            const response = await fetch(eTarget.action, {
                method: 'PATCH',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                }
            });
            const data = await response.json();
            if (data.success) {
                const button = eTarget.querySelector('button');
                const span = button.querySelector('span');
                const label = eTarget.nextElementSibling;
                if (data.state) {
                    button.classList.replace('bg-slate-800', 'bg-indigo-600');
                    span.classList.replace('translate-x-0', 'translate-x-4');
                    if (label) {
                        label.textContent = 'Activo';
                        label.className = 'ml-2 text-xs font-semibold text-emerald-450';
                    }
                }
                else {
                    button.classList.replace('bg-indigo-600', 'bg-slate-800');
                    span.classList.replace('translate-x-4', 'translate-x-0');
                    if (label) {
                        label.textContent = 'Oculto';
                        label.className = 'ml-2 text-xs font-semibold text-slate-500';
                    }
                }
            }
        }
        catch (error) {
            console.error('Error al cambiar el estado:', error);
        }
    });
});
