import Swal from "sweetalert2";

// ELEMENTOS DEL DOM
const formulario = document.getElementById('FormProductos');
const btnGuardar = document.getElementById('BtnGuardar');
const btnModificar = document.getElementById('BtnModificar');
const btnLimpiar = document.getElementById('BtnLimpiar');

// INICIALIZACIÓN
document.addEventListener('DOMContentLoaded', function() {
    cargarProductos();
    cargarCategorias();
    configurarEventos();
});

// CONFIGURAR EVENTOS
function configurarEventos() {
    formulario.addEventListener('submit', function(e) {
        e.preventDefault();
        guardarProducto();
    });
    
    btnModificar.addEventListener('click', function(e) {
        e.preventDefault();
        guardarProducto();
    });
    
    btnLimpiar.addEventListener('click', limpiarFormulario);
}

// GUARDAR PRODUCTO
// En src/js/productos/index.js
async function guardarProducto() {
    try {
        // Recoger datos del formulario
        const datos = new FormData(formulario);
        
        // Enviar la solicitud
        const respuesta = await fetch('/app01_jmp/productos/guardarAPI', {
            method: 'POST',
            body: datos
        });
        
        // Comprobar si la respuesta es válida
        if (!respuesta.ok) {
            throw new Error(`Error HTTP: ${respuesta.status}`);
        }
        
        // Intentar parsear la respuesta como JSON
        let resultado;
        try {
            resultado = await respuesta.json();
        } catch (jsonError) {
            // Si hay un error al parsear JSON, mostrar el texto de la respuesta
            const textoRespuesta = await respuesta.text();
            console.error('Error parseando JSON:', textoRespuesta);
            throw new Error('La respuesta del servidor no es un JSON válido');
        }
        
        // Procesar la respuesta JSON
        if (resultado.resultado) {
            Swal.fire({
                icon: 'success',
                title: 'Éxito',
                text: resultado.mensaje
            });
            
            limpiarFormulario();
            cargarProductos();
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
            text: 'Ocurrió un error al procesar la solicitud: ' + error.message
        });
    }
}

// VALIDAR FORMULARIO
function validarFormulario() {
    const nombre = document.getElementById('prod_nombre').value.trim();
    const cantidad = document.getElementById('prod_cantidad').value;
    const categoria = document.getElementById('cat_id').value;
    const prioridad = document.getElementById('pri_id').value;
    
    if (nombre === '') {
        Swal.fire('Error', 'El nombre del producto es obligatorio', 'error');
        return false;
    }
    
    if (cantidad < 1) {
        Swal.fire('Error', 'La cantidad debe ser mayor a 0', 'error');
        return false;
    }
    
    if (categoria === '') {
        Swal.fire('Error', 'Debe seleccionar una categoría', 'error');
        return false;
    }
    
    if (prioridad === '') {
        Swal.fire('Error', 'Debe seleccionar una prioridad', 'error');
        return false;
    }
    
    return true;
}

// CARGAR CATEGORÍAS
async function cargarCategorias() {
    try {
        const respuesta = await fetch('/app01_jmp/categorias/obtenerAPI');
        const categorias = await respuesta.json();
        
        const selectCategorias = document.getElementById('cat_id');
        selectCategorias.innerHTML = '<option value="">-- Seleccione una categoría --</option>';
        
        categorias.forEach(categoria => {
            const option = document.createElement('option');
            option.value = categoria.cat_id;
            option.textContent = categoria.cat_nombre;
            selectCategorias.appendChild(option);
        });
    } catch (error) {
        console.error('Error al cargar categorías:', error);
    }
}

// CARGAR PRODUCTOS
async function cargarProductos() {
    try {
        const respuesta = await fetch('/app01_jmp/productos/obtenerAPI');
        const productos = await respuesta.json();
        
        mostrarProductos(productos);
    } catch (error) {
        console.error('Error al cargar productos:', error);
    }
}

// MOSTRAR PRODUCTOS
function mostrarProductos(productos) {
    // Separar productos por estado
    const porComprar = productos.filter(p => p.comprado == 0);
    const comprados = productos.filter(p => p.comprado == 1);
    
    // Agrupar productos por comprar por categoría
    const porCategoria = agruparPorCategoria(porComprar);
    
    // Mostrar productos por comprar
    const contenedorPorComprar = document.getElementById('productos-por-comprar');
    contenedorPorComprar.innerHTML = '';
    
    if (porComprar.length === 0) {
        contenedorPorComprar.innerHTML = '<p class="text-center text-muted">No hay productos pendientes por comprar</p>';
    } else {
        Object.keys(porCategoria).forEach(categoria => {
            const divCategoria = document.createElement('div');
            divCategoria.classList.add('mb-4');
            divCategoria.innerHTML = `
                <h5 class="text-primary mb-3">${categoria}</h5>
                <div class="row">
                    ${porCategoria[categoria].map(producto => `
                        <div class="col-md-4 mb-3">
                            <div class="card border-${obtenerColorPrioridad(producto.pri_nombre)}">
                                <div class="card-body">
                                    <h6 class="card-title">${producto.prod_nombre}</h6>
                                    <p class="card-text mb-2">
                                        Cantidad: ${producto.prod_cantidad}
                                    </p>
                                    <span class="badge bg-${obtenerColorPrioridad(producto.pri_nombre)} mb-3">
                                        ${producto.pri_nombre}
                                    </span>
                                    <div class="d-flex justify-content-between">
                                        <button class="btn btn-sm btn-success" onclick="marcarComprado(${producto.prod_id}, 1)">
                                            <i class="bi bi-check-circle"></i> Comprado
                                        </button>
                                        <button class="btn btn-sm btn-warning" onclick="editarProducto(${producto.prod_id})">
                                            <i class="bi bi-pencil"></i> Editar
                                        </button>
                                        <button class="btn btn-sm btn-danger" onclick="eliminarProducto(${producto.prod_id})">
                                            <i class="bi bi-trash"></i> Eliminar
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
            contenedorPorComprar.appendChild(divCategoria);
        });
    }
    
    // Mostrar productos comprados
    const contenedorComprados = document.getElementById('productos-comprados');
    contenedorComprados.innerHTML = '';
    
    if (comprados.length === 0) {
        contenedorComprados.innerHTML = '<p class="text-center text-muted">No hay productos comprados</p>';
    } else {
        const divComprados = document.createElement('div');
        divComprados.classList.add('row');
        
        comprados.forEach(producto => {
            divComprados.innerHTML += `
                <div class="col-md-4 mb-3">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title text-decoration-line-through">${producto.prod_nombre}</h6>
                            <p class="card-text mb-2">
                                Cantidad: ${producto.prod_cantidad} - ${producto.cat_nombre}
                            </p>
                            <div class="d-flex justify-content-between">
                                <button class="btn btn-sm btn-secondary" onclick="marcarComprado(${producto.prod_id}, 0)">
                                    <i class="bi bi-arrow-counterclockwise"></i> Desmarcar
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="eliminarProducto(${producto.prod_id})">
                                    <i class="bi bi-trash"></i> Eliminar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        contenedorComprados.appendChild(divComprados);
    }
}

// AGRUPAR PRODUCTOS POR CATEGORÍA
function agruparPorCategoria(productos) {
    const agrupados = {};
    
    productos.forEach(producto => {
        if (!agrupados[producto.cat_nombre]) {
            agrupados[producto.cat_nombre] = [];
        }
        agrupados[producto.cat_nombre].push(producto);
    });
    
    // Ordenar por prioridad
    Object.keys(agrupados).forEach(categoria => {
        agrupados[categoria].sort((a, b) => {
            const prioridades = { 'Alta': 1, 'Media': 2, 'Baja': 3 };
            return prioridades[a.pri_nombre] - prioridades[b.pri_nombre];
        });
    });
    
    return agrupados;
}

// OBTENER COLOR SEGÚN PRIORIDAD
function obtenerColorPrioridad(prioridad) {
    switch (prioridad) {
        case 'Alta': return 'danger';
        case 'Media': return 'warning';
        case 'Baja': return 'success';
        default: return 'secondary';
    }
}

// EDITAR PRODUCTO
window.editarProducto = async function(id) {
    try {
        const respuesta = await fetch('/app01_jmp/productos/obtenerAPI');
        const productos = await respuesta.json();
        
        const producto = productos.find(p => p.prod_id == id);
        
        if (producto) {
            document.getElementById('prod_id').value = producto.prod_id;
            document.getElementById('prod_nombre').value = producto.prod_nombre;
            document.getElementById('prod_cantidad').value = producto.prod_cantidad;
            document.getElementById('cat_id').value = producto.cat_id;
            document.getElementById('pri_id').value = producto.pri_id;
            
            btnGuardar.style.display = 'none';
            btnModificar.style.display = 'inline-block';
            
            // Scroll al formulario
            formulario.scrollIntoView({ behavior: 'smooth' });
        }
    } catch (error) {
        console.error('Error al editar producto:', error);
    }
}

// MARCAR COMO COMPRADO
window.marcarComprado = async function(id, valor) {
    try {
        const datos = new FormData();
        datos.append('prod_id', id);
        datos.append('valor', valor);
        
        const respuesta = await fetch('/app01_jmp/productos/marcarCompradoAPI', {
            method: 'POST',
            body: datos
        });
        
        const resultado = await respuesta.json();
        
        if (resultado.resultado) {
            cargarProductos();
        } else {
            Swal.fire('Error', resultado.mensaje, 'error');
        }
    } catch (error) {
        console.error('Error al marcar producto:', error);
    }
}

// ELIMINAR PRODUCTO
window.eliminarProducto = async function(id) {
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
            datos.append('prod_id', id);
            
            const respuesta = await fetch('/app01_jmp/productos/eliminarAPI', {
                method: 'POST',
                body: datos
            });
            
            const resultado = await respuesta.json();
            
            if (resultado.resultado) {
                Swal.fire('Eliminado', resultado.mensaje, 'success');
                cargarProductos();
            } else {
                Swal.fire('Error', resultado.mensaje, 'error');
            }
        } catch (error) {
            console.error('Error al eliminar producto:', error);
        }
    }
}

// LIMPIAR FORMULARIO
function limpiarFormulario() {
    formulario.reset();
    document.getElementById('prod_id').value = '';
    btnGuardar.style.display = 'inline-block';
    btnModificar.style.display = 'none';
}