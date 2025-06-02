import Swal from "sweetalert2";

// ELEMENTOS DEL DOM
const formulario = document.getElementById('FormClientes');
const btnGuardar = document.getElementById('BtnGuardar');
const btnModificar = document.getElementById('BtnModificar');
const btnLimpiar = document.getElementById('BtnLimpiar');
let modoEdicion = false;

// INICIALIZACIÓN
document.addEventListener('DOMContentLoaded', function() {
    cargarClientes();
    configurarEventos();
});

// CONFIGURAR EVENTOS
function configurarEventos() {
    formulario.addEventListener('submit', function(e) {
        e.preventDefault();
        guardarCliente();
    });
    
    // Evento para modificar cliente
    btnModificar.addEventListener('click', function() {
        modificarCliente();
    });
    
    btnLimpiar.addEventListener('click', limpiarFormulario);
}

// CARGAR CLIENTES
async function cargarClientes() {
    try {
        const respuesta = await fetch('/app01_jmp/clientes/obtenerAPI');
        const clientes = await respuesta.json();
        
        mostrarClientes(clientes);
    } catch (error) {
        console.error('Error al cargar clientes:', error);
        Swal.fire('Error', 'No se pudieron cargar los clientes', 'error');
    }
}

// MOSTRAR CLIENTES
function mostrarClientes(clientes) {
    const tbody = document.querySelector('#TablaClientes tbody');
    
    if(!tbody) return;
    
    tbody.innerHTML = '';
    
    if(clientes.length === 0) {
        tbody.innerHTML = '<tr><td colspan="3" class="text-center text-muted">No hay clientes registrados</td></tr>';
        return;
    }
    
    clientes.forEach(cliente => {
        const fila = document.createElement('tr');
        fila.innerHTML = `
            <td>${cliente.cliente_id}</td>
            <td>${cliente.cliente_nombre}</td>
            <td class="text-center">
                <button class="btn btn-warning btn-sm" onclick="editarCliente(${cliente.cliente_id}, '${cliente.cliente_nombre}')">
                    <i class="bi bi-pencil"></i>
                </button>
                <button class="btn btn-danger btn-sm" onclick="eliminarCliente(${cliente.cliente_id})">
                    <i class="bi bi-trash"></i>
                </button>
            </td>
        `;
        tbody.appendChild(fila);
    });
}

// GUARDAR CLIENTE
async function guardarCliente() {
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
        
        const respuesta = await fetch('/app01_jmp/clientes/guardarAPI', {
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
            
            // Limpiar formulario y recargar clientes
            limpiarFormulario();
            cargarClientes();
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
            text: 'Ocurrió un error al guardar el cliente: ' + error.message
        });
    } finally {
        btnGuardar.disabled = false;
    }
}

// VALIDAR FORMULARIO
function validarFormulario() {
    const nombre = document.getElementById('cliente_nombre').value.trim();
    
    if (nombre === '') {
        Swal.fire('Error', 'El nombre del cliente es obligatorio', 'error');
        return false;
    }
    
    return true;
}

// EDITAR CLIENTE
window.editarCliente = function(id, nombre) {
    // Activar modo edición
    modoEdicion = true;
    
    // Llenar formulario
    document.getElementById('cliente_id').value = id;
    document.getElementById('cliente_nombre').value = nombre;
    
    // Cambiar visibilidad de botones
    btnGuardar.style.display = 'none';
    btnModificar.style.display = 'inline-block';
    
    // Scroll al formulario
    formulario.scrollIntoView({ behavior: 'smooth' });
};

// MODIFICAR CLIENTE
async function modificarCliente() {
    try {
        // Validar formulario antes de enviar
        if (!validarFormulario()) {
            return;
        }
        
        // Deshabilitar botón para evitar múltiples envíos
        btnModificar.disabled = true;
        
        const datos = new FormData(formulario);
        
        // Mostrar indicador de carga
        Swal.fire({
            title: 'Modificando...',
            text: 'Por favor espera',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        
        const respuesta = await fetch('/app01_jmp/clientes/guardarAPI', {
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
            
            // Limpiar formulario y recargar clientes
            limpiarFormulario();
            cargarClientes();
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
            text: 'Ocurrió un error al modificar el cliente: ' + error.message
        });
    } finally {
        btnModificar.disabled = false;
    }
}

// ELIMINAR CLIENTE
window.eliminarCliente = async function(id) {
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
        datos.append('cliente_id', id);
        
        // Enviar solicitud
        const respuesta = await fetch('/app01_jmp/clientes/eliminarAPI', {
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
                cargarClientes();
            } else {
                Swal.fire('Error', resultado.mensaje || 'No se pudo eliminar el cliente', 'error');
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
        console.error('Error al eliminar cliente:', error);
        
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se pudo eliminar el cliente: ' + error.message
        });
    }
};

// LIMPIAR FORMULARIO
function limpiarFormulario() {
    formulario.reset();
    document.getElementById('cliente_id').value = '';
    modoEdicion = false;
    
    // Restaurar botones
    btnGuardar.style.display = 'inline-block';
    btnModificar.style.display = 'none';
}