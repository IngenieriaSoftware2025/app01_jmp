import Swal from "sweetalert2";

// VARIABLES GLOBALES
let modoEdicion = false;

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
    // Evento para guardar nuevo producto
    formulario.addEventListener('submit', function(e) {
        e.preventDefault();
        guardarProducto();
    });
    
    // Evento para modificar producto
    btnModificar.addEventListener('click', function() {
        modificarProducto();
    });
    
    // Evento para limpiar formulario
    btnLimpiar.addEventListener('click', limpiarFormulario);
}

// CARGAR CATEGORÍAS
async function cargarCategorias() {
    try {
        const respuesta = await fetch('/app01_jmp/categorias/obtenerAPI');
        const categorias = await respuesta.json();
        
        const selectCategorias = document.getElementById('cat_id');
        selectCategorias.innerHTML = '<option value="" class="text-center"> -- SELECCIONE CATEGORÍA -- </option>';
        
        categorias.forEach(categoria => {
            const option = document.createElement('option');
            option.value = categoria.cat_id;
            option.textContent = categoria.cat_nombre;
            selectCategorias.appendChild(option);
        });
    } catch (error) {
        console.error('Error al cargar categorías:', error);
        Swal.fire('Error', 'No se pudieron cargar las categorías', 'error');
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
        Swal.fire('Error', 'No se pudieron cargar los productos', 'error');
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
            
            let html = `
                <h5 class="text-primary mb-3">${categoria}</h5>
                <div class="row">
            `;
            
            porCategoria[categoria].forEach(producto => {
                html += `
                    <div class="col-md-4 mb-3">
                        <div class="card h-100 border-${obtenerColorPrioridad(producto.pri_nombre)}">
                            <div class="card-body">
                                <h6 class="card-title">${producto.prod_nombre}</h6>
                                <p class="card-text mb-2">
                                    Cantidad: ${producto.prod_cantidad}<br>
                                    <strong>Precio: Q. ${parseFloat(producto.precio || 0).toFixed(2)}</strong><br>
                                    <strong>Stock: ${producto.stock || 0}</strong>
                                </p>
                                <span class="badge bg-${obtenerColorPrioridad(producto.pri_nombre)} mb-3">
                                    ${producto.pri_nombre}
                                </span>
                                <div class="d-flex justify-content-between mt-3">
                                    <button class="btn btn-sm btn-success" onclick="marcarComprado(${producto.prod_id}, 1)">
                                        <i class="bi bi-check-circle"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="editarProducto(${producto.prod_id})">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="eliminarProducto(${producto.prod_id})">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            divCategoria.innerHTML = html;
            contenedorPorComprar.appendChild(divCategoria);
        });
    }
    
    // Mostrar productos comprados
    const contenedorComprados = document.getElementById('productos-comprados');
    contenedorComprados.innerHTML = '';
    
    if (comprados.length === 0) {
        contenedorComprados.innerHTML = '<p class="text-center text-muted">No hay productos comprados</p>';
    } else {
        let html = '<div class="row">';
        
        comprados.forEach(producto => {
            html += `
                <div class="col-md-4 mb-3">
                    <div class="card h-100 bg-light">
                        <div class="card-body">
                            <h6 class="card-title text-decoration-line-through">${producto.prod_nombre}</h6>
                            <p class="card-text mb-2">
                                Cantidad: ${producto.prod_cantidad} - ${producto.cat_nombre}<br>
                                Precio: Q. ${parseFloat(producto.precio || 0).toFixed(2)}<br>
                                Stock: ${producto.stock || 0}
                            </p>
                            <div class="d-flex justify-content-between mt-3">
                                <button class="btn btn-sm btn-secondary" onclick="marcarComprado(${producto.prod_id}, 0)">
                                    <i class="bi bi-arrow-counterclockwise"></i>
                                </button>
                                <button class="btn btn-sm btn-danger" onclick="eliminarProducto(${producto.prod_id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        });
        
        html += '</div>';
        contenedorComprados.innerHTML = html;
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

// GUARDAR PRODUCTO
async function guardarProducto() {
    try {
        // Validar formulario antes de enviar
        if (!validarFormulario()) {
            return;
        }
        
        // Deshabilitar botón para evitar múltiples envíos
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
        
        const respuesta = await fetch('/app01_jmp/productos/guardarAPI', {
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
            
            // Limpiar formulario y recargar productos
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
            text: 'Ocurrió un error al guardar el producto: ' + error.message
        });
    } finally {
        btnGuardar.disabled = false;
    }
}

// VALIDAR FORMULARIO
function validarFormulario() {
    const nombre = document.getElementById('prod_nombre').value.trim();
    const cantidad = document.getElementById('prod_cantidad').value;
    const categoria = document.getElementById('cat_id').value;
    const prioridad = document.getElementById('pri_id').value;
    const precio = parseFloat(document.getElementById('precio').value);
    const stock = parseInt(document.getElementById('stock').value);
    
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
    
    if (precio < 0) {
        Swal.fire('Error', 'El precio no puede ser negativo', 'error');
        return false;
    }
    
    if (stock < 0) {
        Swal.fire('Error', 'El stock no puede ser negativo', 'error');
        return false;
    }
    
    return true;
}

// EDITAR PRODUCTO
window.editarProducto = async function(id) {
    try {
        // Mostrar indicador de carga
        Swal.fire({
            title: 'Cargando...',
            text: 'Obteniendo información del producto',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });
        
        // Obtener todos los productos
        const respuesta = await fetch('/app01_jmp/productos/obtenerAPI');
        const productos = await respuesta.json();
        
        // Buscar el producto por ID
        const producto = productos.find(p => p.prod_id == id);
        
        if (!producto) {
            throw new Error('Producto no encontrado');
        }
        
        // Cerrar indicador de carga
        Swal.close();
        
        // Activar modo edición
        modoEdicion = true;
        
        // Llenar formulario con datos del producto
        document.getElementById('prod_id').value = producto.prod_id;
        document.getElementById('prod_nombre').value = producto.prod_nombre;
        document.getElementById('prod_cantidad').value = producto.prod_cantidad;
        document.getElementById('cat_id').value = producto.cat_id;
        document.getElementById('pri_id').value = producto.pri_id;
        document.getElementById('precio').value = parseFloat(producto.precio || 0).toFixed(2);
        document.getElementById('stock').value = producto.stock || 0;
        
        // Cambiar visibilidad de botones
        btnGuardar.style.display = 'none';
        btnModificar.style.display = 'inline-block';
        
        // Scroll al formulario
        formulario.scrollIntoView({ behavior: 'smooth' });
    } catch (error) {
        console.error('Error al editar producto:', error);
        Swal.fire('Error', 'No se pudo cargar la información del producto', 'error');
    }
};

// MODIFICAR PRODUCTO
async function modificarProducto() {
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
        
        const respuesta = await fetch('/app01_jmp/productos/guardarAPI', {
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
            
            // Limpiar formulario y recargar productos
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
            text: 'Ocurrió un error al modificar el producto: ' + error.message
        });
    } finally {
        btnModificar.disabled = false;
    }
}

// MARCAR COMO COMPRADO/NO COMPRADO
window.marcarComprado = async function(id, valor) {
    try {
        const datos = new FormData();
        datos.append('prod_id', id);
        datos.append('valor', valor);
        
        const respuesta = await fetch('/app01_jmp/productos/marcarCompradoAPI', {
            method: 'POST',
            body: datos
        });
        
        if (!respuesta.ok) {
            throw new Error(`Error HTTP: ${respuesta.status}`);
        }
        
        const resultado = await respuesta.json();
        
        if (resultado.resultado) {
            // Recargar productos para mostrar cambios
            cargarProductos();
        } else {
            Swal.fire('Error', resultado.mensaje, 'error');
        }
    } catch (error) {
        console.error('Error al marcar producto:', error);
        Swal.fire('Error', 'No se pudo actualizar el estado del producto', 'error');
    }
};

// ELIMINAR PRODUCTO
window.eliminarProducto = async function(id) {
    try {
        const confirmacion = await Swal.fire({
            title: '¿Estás seguro?',
            text: "Esta acción no se puede deshacer",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        });
        
        if (!confirmacion.isConfirmed) {
            return;
        }
        
        // Mostrar cargando
        Swal.fire({
            title: 'Eliminando...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const respuesta = await fetch(`/app01_jmp/productos/eliminar?prod_id=${id}`, {
            method: 'GET',
            headers: {
                'Accept': 'application/json'
            }
        });
        
        const textoRespuesta = await respuesta.text();
        
        let resultado;
        try {
            resultado = JSON.parse(textoRespuesta);
            
            Swal.close();
            
            if (resultado.resultado || resultado.codigo === 1) {
                Swal.fire('Eliminado', resultado.mensaje, 'success');
                cargarProductos();
            } else {
                Swal.fire('Error', resultado.mensaje || 'No se pudo eliminar el producto', 'error');
            }
        } catch (e) {
            console.error('Error al parsear JSON:', e);
            Swal.fire('Error', 'La respuesta del servidor no es JSON válido', 'error');
        }
    } catch (error) {
        console.error('Error completo:', error);
        Swal.fire('Error', 'No se pudo eliminar el producto: ' + error.message, 'error');
    }
};

// LIMPIAR FORMULARIO
function limpiarFormulario() {
    formulario.reset();
    document.getElementById('prod_id').value = '';
    document.getElementById('precio').value = '0.00';
    document.getElementById('stock').value = '0';
    modoEdicion = false;
    
    // Restaurar botones
    btnGuardar.style.display = 'inline-block';
    btnModificar.style.display = 'none';
}