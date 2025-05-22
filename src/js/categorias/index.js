import Swal from "sweetalert2";

// ELEMENTOS DEL DOM
const formulario = document.getElementById('FormCategorias');
const btnGuardar = document.getElementById('BtnGuardar');
const btnLimpiar = document.getElementById('BtnLimpiar');

// INICIALIZACIÓN
document.addEventListener('DOMContentLoaded', function() {
    configurarEventos();
});

// CONFIGURAR EVENTOS
function configurarEventos() {
    formulario.addEventListener('submit', function(e) {
        e.preventDefault();
        guardarCategoria();
    });
    
    btnLimpiar.addEventListener('click', limpiarFormulario);
}

// GUARDAR CATEGORÍA
async function guardarCategoria() {
    // Validar formulario
    if (!validarFormulario()) {
        return;
    }
    
    const datos = new FormData(formulario);
    
    try {
        const respuesta = await fetch('/app01_jmp/categorias/guardarAPI', {
            method: 'POST',
            body: datos
        });
        
        const resultado = await respuesta.json();
        
        if (resultado.resultado) {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: resultado.mensaje
            }).then(() => {
                limpiarFormulario();
                window.location.reload(); // Recargar para ver los cambios
            });
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: resultado.mensaje
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al procesar la solicitud'
        });
    }
}

// VALIDAR FORMULARIO
function validarFormulario() {
    const nombre = document.getElementById('cat_nombre').value.trim();
    
    if (nombre === '') {
        Swal.fire('Error', 'El nombre de la categoría es obligatorio', 'error');
        return false;
    }
    
    return true;
}

// EDITAR CATEGORÍA (FUNCIÓN GLOBAL)
window.editarCategoria = function(id, nombre) {
    document.getElementById('cat_id').value = id;
    document.getElementById('cat_nombre').value = nombre;
    
    // Cambiar aspecto del botón y foco en el formulario
    btnGuardar.innerHTML = '<i class="bi bi-save me-1"></i>Actualizar';
    btnGuardar.classList.remove('btn-primary');
    btnGuardar.classList.add('btn-warning');
    
    // Scroll al formulario
    formulario.scrollIntoView({ behavior: 'smooth' });
    document.getElementById('cat_nombre').focus();
}

// ELIMINAR CATEGORÍA (FUNCIÓN GLOBAL)
window.eliminarCategoria = async function(id) {
    const confirmacion = await Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });
    
    if (confirmacion.isConfirmed) {
        try {
            const datos = new FormData();
            datos.append('cat_id', id);
            
            const respuesta = await fetch('/app01_jmp/categorias/eliminarAPI', {
                method: 'POST',
                body: datos
            });
            
            const resultado = await respuesta.json();
            
            if (resultado.resultado) {
                Swal.fire({
                    icon: 'success',
                    title: 'Eliminado',
                    text: resultado.mensaje
                }).then(() => {
                    window.location.reload(); // Recargar para ver los cambios
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: resultado.mensaje
                });
            }
        } catch (error) {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Ocurrió un error al eliminar la categoría'
            });
        }
    }
}

// LIMPIAR FORMULARIO
function limpiarFormulario() {
    formulario.reset();
    document.getElementById('cat_id').value = '';
    
    // Restaurar aspecto del botón
    btnGuardar.innerHTML = '<i class="bi bi-save me-1"></i>Guardar';
    btnGuardar.classList.remove('btn-warning');
    btnGuardar.classList.add('btn-primary');
}