"use strict";

document.addEventListener('DOMContentLoaded', function () {
    var triggerButton = document.getElementById('nav-user-display-btn');
    var dropdownMenu = document.getElementById('user-dropdown-menu');
    var arrowIcon = document.getElementById('nav-user-arrow');

    if (!triggerButton || !dropdownMenu) {
        return;
    }

    function closeDropdown() {
        dropdownMenu.classList.remove('is-open');
        if (arrowIcon) {
            arrowIcon.classList.remove('is-open');
        }
    }

    function openDropdown() {
        dropdownMenu.classList.add('is-open');
        if (arrowIcon) {
            arrowIcon.classList.add('is-open');
        }
    }

    triggerButton.addEventListener('click', function (event) {
        event.preventDefault();
        event.stopPropagation();

        if (dropdownMenu.classList.contains('is-open')) {
            closeDropdown();
        } else {
            openDropdown();
        }
    });

    document.addEventListener('click', function (event) {
        var target = event.target;
        if (!triggerButton.contains(target) && !dropdownMenu.contains(target)) {
            closeDropdown();
        }
    });

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape') {
            closeDropdown();
        }
    });
});
