import Swal from "sweetalert2";

const formulario = document.getElementById('FormPrioridades');
const btnGuardar = document.getElementById('BtnGuardar');
const btnModificar = document.getElementById('BtnModificar');
const btnLimpiar = document.getElementById('BtnLimpiar');

// Inicializar
document.addEventListener('DOMContentLoaded', function() {
    cargarPrioridades();
});

formulario.addEventListener('submit', guardarPrioridad);
btnLimpiar.addEventListener('click', limpiarFormulario);
btnModificar.addEventListener('click', modificarPrioridad);

async function guardarPrioridad(e) {
    e.preventDefault();

    const datos = new FormData(formulario);

    try {
        const url = '/app01_jmp/prioridades/guardarAPI';
        const config = {
            method: 'POST',
            body: datos
        };

        const respuesta = await fetch(url, config);
        const resultado = await respuesta.json();

        if (resultado.resultado) {
            Swal.fire('Éxito', resultado.mensaje, 'success');
            limpiarFormulario();
            cargarPrioridades();
        } else {
            Swal.fire('Error', resultado.mensaje, 'error');
        }

    } catch (error) {
        console.error(error);
        Swal.fire('Error', 'Ocurrió un error al guardar', 'error');
    }
}

async function modificarPrioridad() {
    const datos = new FormData(formulario);

    try {
        const url = '/app01_jmp/prioridades/guardarAPI';
        const config = {
            method: 'POST',
            body: datos
        };

        const respuesta = await fetch(url, config);
        const resultado = await respuesta.json();

        if (resultado.resultado) {
            Swal.fire('Éxito', resultado.mensaje, 'success');
            limpiarFormulario();
            cargarPrioridades();
        } else {
            Swal.fire('Error', resultado.mensaje, 'error');
        }

    } catch (error) {
        console.error(error);
        Swal.fire('Error', 'Ocurrió un error al modificar', 'error');
    }
}

async function cargarPrioridades() {
    try {
        const url = '/app01_jmp/prioridades/obtenerAPI';
        const respuesta = await fetch(url);
        const prioridades = await respuesta.json();

        mostrarPrioridades(prioridades);
    } catch (error) {
        console.error(error);
    }
}

function mostrarPrioridades(prioridades) {
    const contenedor = document.getElementById('lista-prioridades');
    contenedor.innerHTML = '';

    if (prioridades.length === 0) {
        contenedor.innerHTML = '<p class="text-muted text-center">No hay prioridades registradas</p>';
        return;
    }

    prioridades.forEach(p => {
        const div = document.createElement('div');
        div.classList.add('card', 'mb-2');
        div.innerHTML = `
            <div class="card-body d-flex justify-content-between align-items-center">
                <span>${p.pri_nombre}</span>
                <div>
                    <button class="btn btn-warning btn-sm me-2" onclick="editarPrioridad(${p.pri_id}, '${p.pri_nombre}')">✏</button>
                    <button class="btn btn-danger btn-sm" onclick="eliminarPrioridad(${p.pri_id})">✗</button>
                </div>
            </div>
        `;
        contenedor.appendChild(div);
    });
}

function editarPrioridad(id, nombre) {
    document.getElementById('pri_id').value = id;
    document.getElementById('pri_nombre').value = nombre;
}

function eliminarPrioridad(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then(async (result) => {
        if (result.isConfirmed) {
            try {
                const datos = new FormData();
                datos.append('pri_id', id);

                const url = '/app01_jmp/prioridades/eliminarAPI';
                const config = {
                    method: 'POST',
                    body: datos
                };

                const respuesta = await fetch(url, config);
                const resultado = await respuesta.json();

                if (resultado.resultado) {
                    Swal.fire('Eliminado', resultado.mensaje, 'success');
                    cargarPrioridades();
                } else {
                    Swal.fire('Error', resultado.mensaje, 'error');
                }

            } catch (error) {
                console.error(error);
                Swal.fire('Error', 'No se pudo eliminar', 'error');
            }
        }
    });
}

function limpiarFormulario() {
    formulario.reset();
    document.getElementById('pri_id').value = '';
}
