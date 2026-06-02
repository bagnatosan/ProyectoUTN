//mostrar formulario update
const editButtons = document.querySelectorAll<HTMLButtonElement>('.edit-category-btn');

editButtons.forEach(button => {
    button.addEventListener('click' , () => {
        const row = button.closest('.justify-between');

        if(row)
        {
            const form = row.querySelector('.category-edit-form');
            const view = row.querySelector('.category-view-container');
            const actions = row.querySelector('.category-actions-container');


            if(form && view && actions)
            {
                form.classList.remove('hidden');
                view.classList.add('hidden');
                actions.classList.add('hidden');
            }


        }
    });
});

//cruz ocultar form
const cancelButtons = document.querySelectorAll('.cancel-edit-btn');
cancelButtons.forEach(btn => {
    btn.addEventListener('click', () => {
        const row = btn.closest('.justify-between');
        if (row) {
            const form = row.querySelector('.category-edit-form');
            const view = row.querySelector('.category-view-container');
            const actions = row.querySelector('.category-actions-container');
            if (form && view && actions) {
                form.classList.add('hidden');       // Oculta el formulario
                view.classList.remove('hidden');    // Muestra el texto
                actions.classList.remove('hidden'); // Muestra los botones
            }
        }
    });
});
//update

const formUpdate = document.querySelectorAll<HTMLFormElement>('.category-edit-form');

formUpdate.forEach(form => {
    form.addEventListener('submit', async(e) => {
        e.preventDefault(); //evitar recargar pagina

        const url = form.action;

        const formData = new FormData(form);

        const response = await fetch(url , {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        });

        if(response.ok)
        {
            const input = form.querySelector('input[name = "name"]') as HTMLInputElement;
            const newName = input.value;

            const row = form.closest('.justify-between');

            if(row)
            {
                const textName = row.querySelector('.category-view-container p');

                if(textName)
                {
                    textName.textContent = newName;
                }

                const view = row.querySelector('.category-view-container');
                const actions = row.querySelector('.category-actions-container');

                if(view && actions)
                {
                    form.classList.add('hidden');
                    view.classList.remove('hidden');
                    actions.classList.remove('hidden');
                }
            }

        }

        else
        {
            const errorData = await response.json();
            alert(`Error: ${errorData.message}`)
        }


    });
});
