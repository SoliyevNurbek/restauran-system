import './bootstrap';

window.addEventListener('load', () => {
    if (window.lucide) {
        window.lucide.createIcons();
    }

    document.querySelectorAll('[data-dismiss-parent]').forEach((button) => {
        button.addEventListener('click', () => {
            button.closest('[data-dismissible]')?.remove();
        });
    });

    const sidebarToggle = document.querySelector('[data-sidebar-toggle]');
    const sidebar = document.querySelector('[data-sidebar]');
    const sidebarBackdrop = document.querySelector('[data-sidebar-backdrop]');

    const toggleSidebar = () => {
        sidebar?.classList.toggle('-translate-x-full');
        sidebarBackdrop?.classList.toggle('hidden');
    };

    sidebarToggle?.addEventListener('click', toggleSidebar);
    sidebarBackdrop?.addEventListener('click', toggleSidebar);

    const dangerModal = document.querySelector('[data-danger-modal]');
    const dangerTitle = dangerModal?.querySelector('[data-danger-modal-title]');
    const dangerMessage = dangerModal?.querySelector('[data-danger-modal-message]');
    const dangerWarning = dangerModal?.querySelector('[data-danger-modal-warning]');
    const dangerAccept = dangerModal?.querySelector('[data-danger-accept]');
    const dangerCancel = dangerModal?.querySelector('[data-danger-cancel]');
    let activeDangerForm = null;

    const closeDangerModal = () => {
        dangerModal?.classList.add('hidden');
        dangerModal?.classList.remove('flex');
        activeDangerForm = null;
    };

    document.querySelectorAll('[data-danger-confirm]').forEach((button) => {
        button.addEventListener('click', () => {
            activeDangerForm = button.closest('form');

            if (!activeDangerForm || !dangerModal) {
                return;
            }

            if (dangerTitle) {
                dangerTitle.textContent = button.dataset.confirmTitle || "Amalni tasdiqlang";
            }

            if (dangerMessage) {
                dangerMessage.textContent = button.dataset.confirmMessage || "Bu amal qaytarilmaydi.";
            }

            if (dangerWarning) {
                dangerWarning.textContent = button.dataset.confirmWarning || "Davom etsangiz yozuv bazadan butunlay o'chadi.";
            }

            dangerModal.classList.remove('hidden');
            dangerModal.classList.add('flex');
        });
    });

    dangerCancel?.addEventListener('click', closeDangerModal);
    dangerModal?.addEventListener('click', (event) => {
        if (event.target === dangerModal) {
            closeDangerModal();
        }
    });

    dangerAccept?.addEventListener('click', () => {
        if (!activeDangerForm) {
            closeDangerModal();
            return;
        }

        activeDangerForm.submit();
    });
});
