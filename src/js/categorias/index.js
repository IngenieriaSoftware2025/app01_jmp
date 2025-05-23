import Swal from "sweetalert2";

// ELEMENTOS DEL DOM
const formulario = document.getElementById('FormCategorias');
const btnGuardar = document.getElementById('BtnGuardar');
const btnLimpiar = document.getElementById('BtnLimpiar');
let modoEdicion = false;

// INICIALIZACIÓN
document.addEventListener('DOMContentLoaded', function() {
    cargarCategorias();
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

// CARGAR CATEGORÍAS
async function cargarCategorias() {
    try {
        const respuesta = await fetch('/app01_jmp/categorias/obtenerAPI');
        const categorias = await respuesta.json();
        
        mostrarCategorias(categorias);
    } catch (error) {
        console.error('Error al cargar categorías:', error);
        Swal.fire('Error', 'No se pudieron cargar las categorías', 'error');
    }
}

// MOSTRAR CATEGORÍAS
function mostrarCategorias(categorias) {
    const tbody = document.querySelector('#TablaCategorias tbody');
    
    if(!tbody) return;
    
    tbody.innerHTML = '';
    
    if(categorias.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">No hay categorías registradas</td></tr>';
        return;
    }
    
    categorias.forEach(categoria => {
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td>${categoria.cat_id}</td>
            <td>${categoria.cat_nombre}</td>
            <td class="text-center">
                <button class="btn btn-warning btn-sm" onclick="editarCategoria(${categoria.cat_id}, '${categoria.cat_nombre}')">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-danger btn-sm" onclick="eliminarCategoria(${categoria.cat_id})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(fila);
    });
}

// GUARDAR CATEGORÍA
async function guardarCategoria() {
    try {
        // Validar formulario
        if (!validarFormulario()) {
            return;
        }
        
        // Deshabilitar botón
        btnGuardar.disabled = true;
        
        const datos = new FormData(formulario);
        
        // Mostrar indicador de carga
        Swal.fire({
            title: 'Guardando...',
            text: 'Por favor espera',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        
        const respuesta = await fetch('/app01_jmp/categorias/guardarAPI', {
            method: 'POST',
            body: datos
        });
        
        if (!respuesta.ok) {
            throw new Error(`Error HTTP: ${respuesta.status}`);
        }
        
        const resultado = await respuesta.json();
        
        if (resultado.resultado) {
            // Cerrar indicador de carga
            Swal.close();
            
            // Mostrar mensaje de éxito
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: resultado.mensaje
            });
            
            // Limpiar formulario y recargar categorías
            limpiarFormulario();
            cargarCategorias();
        } else {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: resultado.mensaje || 'Error desconocido'
            });
        }
    } catch (error) {
        console.error('Error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Ocurrió un error al guardar la categoría: ' + error.message
        });
    } finally {
        btnGuardar.disabled = false;
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

// EDITAR CATEGORÍA
window.editarCategoria = function(id, nombre) {
    // Activar modo edición
    modoEdicion = true;
    
    // Llenar formulario
    document.getElementById('cat_id').value = id;
    document.getElementById('cat_nombre').value = nombre;
    
    // Cambiar texto del botón
    btnGuardar.textContent = 'Actualizar';
    btnGuardar.classList.remove('btn-primary');
    btnGuardar.classList.add('btn-warning');
    
    // Scroll al formulario
    formulario.scrollIntoView({ behavior: 'smooth' });
};

// ELIMINAR CATEGORÍA
window.eliminarCategoria = async function(id) {
    try {
        // Pedir confirmación
        const confirmacion = await Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });
        
        if (!confirmacion.isConfirmed) {
            return;
        }
        
        // Mostrar indicador de carga
        Swal.fire({
            title: 'Eliminando...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Crear FormData para enviar el ID
        const datos = new FormData();
        datos.append('cat_id', id);
        
        // Enviar solicitud
        const respuesta = await fetch('/app01_jmp/categorias/eliminarAPI', {
            method: 'POST',
            body: datos
        });
        
        // Verificar respuesta
        if (!respuesta.ok) {
            throw new Error(`Error HTTP: ${respuesta.status}`);
        }
        
        const contentType = respuesta.headers.get('content-type');
        
        if (contentType && contentType.indexOf('application/json') !== -1) {
            // Es JSON, intentar parsear
            const resultado = await respuesta.json();
            
            Swal.close();
            
            if (resultado.resultado) {
                Swal.fire('Eliminado', resultado.mensaje, 'success');
                cargarCategorias();
            } else {
                Swal.fire('Error', resultado.mensaje || 'No se pudo eliminar la categoría', 'error');
            }
        } else {
            // No es JSON, mostrar texto
            const textoRespuesta = await respuesta.text();
            console.error('Respuesta no JSON:', textoRespuesta);
            
            Swal.fire({
                icon: 'error',
                title: 'Error en la respuesta del servidor',
                text: 'No se recibió una respuesta JSON válida'
            });
        }
    } catch (error) {
        console.error('Error al eliminar categoría:', error);
        
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo eliminar la categoría: ' + error.message
        });
    }
};

// LIMPIAR FORMULARIO
function limpiarFormulario() {
    formulario.reset();
    document.getElementById('cat_id').value = '';
    modoEdicion = false;
    
    // Restaurar botón
    btnGuardar.textContent = 'Guardar';
    btnGuardar.classList.remove('btn-warning');
    btnGuardar.classList.add('btn-primary');
}