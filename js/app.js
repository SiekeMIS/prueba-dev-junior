// Confirmación de eliminación desde JS
function confirmarEliminacion(nombreBodega) {
    const msg = nombreBodega
        ? `¿Seguro que quieres eliminar la bodega "${nombreBodega}"?\nEsta acción no se puede deshacer.`
        : `¿Seguro que quieres eliminar esta bodega?\nEsta acción no se puede deshacer.`;
    return confirm(msg);
}

// Validación básica en formularios de crear/editar
document.addEventListener('DOMContentLoaded', () => {
    const forms = document.querySelectorAll('form.crud-form');

    forms.forEach(form => {
        form.addEventListener('submit', (e) => {
            const codigo = form.querySelector('input[name="codigo"]');
            const nombre = form.querySelector('input[name="nombre"]');

            if (codigo && codigo.value.length > 5) {
                alert('El código no puede tener más de 5 caracteres.');
                e.preventDefault();
                return;
            }

            if (nombre && nombre.value.length > 100) {
                alert('El nombre no puede tener más de 100 caracteres.');
                e.preventDefault();
                return;
            }
        });
    });
});
