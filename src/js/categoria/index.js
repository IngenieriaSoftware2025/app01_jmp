import { Dropdown } from "bootstrap";
import Swal from "sweetalert2";

const formulario = document.getElementById('FormCategorias');
const tabla = document.getElementById('TableCategorias');
const btnGuardar = document.getElementById('BtnGuardar');
const btnLimpiar = document.getElementById('BtnLimpiar');

// Inicializar
document.addEventListener('DOMContentLoaded', function() {
    cargarCategorias();
});

// Evento del formulario
formulario.addEventListener('submit', guardarCategoria);
btnLimpiar.addEventListener('click', limpiarFormulario);

async function guardarCategoria(e) {
    e.preventDefault();
    
    const datos = new FormData(formulario);
    
    try {
        const url = '/app01_jmp/categorias/guardarAPI';
        const config = {
            method: 'POST',
            body: datos
        };
        
        const respuesta = await fetch(url, config);
        const resultado = await respuesta.json();
        
        if(resultado.resultado) {
            Swal.fire('Éxito', resultado.mensaje, 'success');
            limpiarFormulario();
            cargarCategorias();
        } else {
            Swal.fire('Error', resultado.mensaje, 'error');
        }
        
    } catch (error) {
        console.error(error);
        Swal.fire('Error', 'Ocurrió un error al guardar', 'error');
    }
}

async function cargarCategorias() {
    try {
        const url = '/app01_jmp/categorias/obtenerAPI';
        const respuesta = await fetch(url);
        const categorias = await respuesta.json();
        
        mostrarCategorias(categorias);
        
    } catch (error) {
        console.error(error);
    }
}

function mostrarCategorias(categorias) {
    const tbody = document.querySelector('#TableCategorias tbody');
    
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
                <button class="btn btn-warning btn-sm" onclick="editarCategoria(${categoria.cat_id}, '${categoria.cat_nombre}')">✏</button>
                <button class="btn btn-danger btn-sm" onclick="eliminarCategoria(${categoria.cat_id})">✗</button>
            </td>
        `;
        tbody.appendChild(fila);
    });
}

function editarCategoria(id, nombre) {
    document.getElementById('cat_id').value = id;
    document.getElementById('cat_nombre').value = nombre;
    
    btnGuardar.textContent = 'Actualizar';
    btnGuardar.classList.remove('btn-success');
    btnGuardar.classList.add('btn-warning');
}

async function eliminarCategoria(id) {
    const confirmacion = await Swal.fire({
        title: '¿Estás seguro?',
        text: 'Esta acción no se puede deshacer',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    });
    
    if(!confirmacion.isConfirmed) return;
    
    try {
        const datos = new FormData();
        datos.append('id', id);
        
        const url = '/app01_jmp/categorias/eliminarAPI';
        const config = {
            method: 'POST',
            body: datos
        };
        
        const respuesta = await fetch(url, config);
        const resultado = await respuesta.json();
        
        if(resultado.resultado) {
            Swal.fire('Eliminado', resultado.mensaje, 'success');
            cargarCategorias();
        } else {
            Swal.fire('Error', resultado.mensaje, 'error');
        }
        
    } catch (error) {
        console.error(error);
        Swal.fire('Error', 'Ocurrió un error al eliminar', 'error');
    }
}

function limpiarFormulario() {
    formulario.reset();
    document.getElementById('cat_id').value = '';
    
    btnGuardar.textContent = 'Guardar';
    btnGuardar.classList.remove('btn-warning');
    btnGuardar.classList.add('btn-success');
}

// Hacer funciones globales
window.editarCategoria = editarCategoria;
window.eliminarCategoria = eliminarCategoria;