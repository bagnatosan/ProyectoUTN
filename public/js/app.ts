document.addEventListener('DOMContentLoaded', () => {
    
    const triggerButton = document.getElementById('nav-user-display-btn') as HTMLButtonElement | null;
    const dropdownMenu = document.getElementById('user-dropdown-menu') as HTMLDivElement | null;
    const arrowIcon = document.getElementById('nav-user-arrow') as SVGElement | null;

    if (triggerButton && dropdownMenu) {
        triggerButton.addEventListener('click', (event: MouseEvent) => {
            event.stopPropagation(); 

            const isOpen = dropdownMenu.classList.contains('opacity-100');

            if (isOpen) {
                dropdownMenu.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');
                dropdownMenu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
                
                if (arrowIcon) {
                    arrowIcon.classList.remove('rotate-180');
                }
            } else {
                dropdownMenu.classList.remove('opacity-0', 'scale-95', 'pointer-events-none');
                dropdownMenu.classList.add('opacity-100', 'scale-100', 'pointer-events-auto');
                
                if (arrowIcon) {
                    arrowIcon.classList.add('rotate-180');
                }
            }
        });

        document.addEventListener('click', (event: MouseEvent) => {
            const target = event.target as Node;
            if (!triggerButton.contains(target) && !dropdownMenu.contains(target)) {
                dropdownMenu.classList.remove('opacity-100', 'scale-100', 'pointer-events-auto');
                dropdownMenu.classList.add('opacity-0', 'scale-95', 'pointer-events-none');
                if (arrowIcon) {
                    arrowIcon.classList.remove('rotate-180');
                }
            }
        });
    }
});
