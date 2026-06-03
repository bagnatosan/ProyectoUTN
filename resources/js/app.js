"use strict";
const editButtons = document.querySelectorAll('.edit-category-btn');
editButtons.forEach(button => {
    button.addEventListener('click', () => {
        const row = button.closest('.justify-between');
        if (row) {
            const form = row.querySelector('.category-edit-form');
            const view = row.querySelector('.category-view-container');
            const actions = row.querySelector('.category-actions-container');
            if (form && view && actions) {
                form.classList.remove('hidden');
                view.classList.add('hidden');
                actions.classList.add('hidden');
            }
        }
    });
});
