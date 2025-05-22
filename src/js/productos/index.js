import Swal from "sweetalert2";

// ELEMENTOS DEL DOM
const formulario = document.getElementById('FormProductos');
const btnGuardar = document.getElementById('BtnGuardar');
const btnModificar = document.getElementById('BtnModificar');
const btnLimpiar = document.getElementById('BtnLimpiar');

// VARIABLE PARA CONTROLAR MODO DE EDICIÓN
let modoEdicion = false;

/**
 * INICIALIZACIÓN CUANDO EL DOM ESTÁ LISTO
 */
document.addEventListener('DOMContentLoaded', function() {
    cargarProductos();
    cargarCategorias();
    cargarPrioridades();
    configurarBotones();
});

/**
 * EVENTOS DE LOS FORMULARIOS Y BOTONES
 */
formulario.addEventListener('submit', guardarProducto);
btnLimpiar.addEventListener('click', limpiarFormulario);
btnModificar.addEventListener('click', modificarProducto);

/**
 * CONFIGURAR BOTONES INICIALES
 */
function configurarBotones() {
    btnGuardar.style.display = 'inline-block';
    btnModificar.style.display = 'none';
}

/**
 * GUARDAR NUEVO PRODUCTO
 */
async function guardarProducto(e) {
    e.preventDefault();
    
    const datos = new FormData(formulario);
    
    // Asegurar que comprado sea 0 para productos nuevos
    if (!modoEdicion) {
        datos.set('comprado', '0');
    }

    try {
        const respuesta = await fetch('/app01_jmp/productos/guardarAPI', {
            method: 'POST',
            body: datos
        });
        
        const resultado = await respuesta.json();

        if(resultado.resultado) {
            Swal.fire('Éxito', resultado.mensaje, 'success');
            limpiarFormulario();
            cargarProductos();
        } else {
            Swal.fire('Error', resultado.mensaje, 'error');
        }

    } catch (error) {
        console.error('Error al guardar:', error);
        Swal.fire('Error', 'Ocurrió un error al guardar', 'error');
    }
}

/**
 * MODIFICAR PRODUCTO EXISTENTE
 */
async function modificarProducto() {
    const datos = new FormData(formulario);
    
    try {
        const respuesta = await fetch('/app01_jmp/productos/guardarAPI', {
            method: 'POST',
            body: datos
        });
        
        const resultado = await respuesta.json();
        
        if(resultado.resultado) {
            Swal.fire('Éxito', resultado.mensaje, 'success');
            limpiarFormulario();
            cargarProductos();
        } else {
            Swal.fire('Error', resultado.mensaje, 'error');
        }
        
    } catch (error) {
        console.error('Error al modificar:', error);
        Swal.fire('Error', 'Ocurrió un error al modificar', 'error');
    }
}

/**
 * CARGAR LISTA DE PRODUCTOS DESDE EL SERVIDOR
 */
async function cargarProductos() {
    try {
        const respuesta = await fetch('/app01_jmp/productos/obtenerAPI');
        const productos = await respuesta.json();
        
        mostrarProductos(productos);
        
    } catch (error) {
        console.error('Error al cargar productos:', error);
    }
}

/**
 * CARGAR CATEGORÍAS DISPONIBLES
 */
async function cargarCategorias() {
    try {
        const respuesta = await fetch('/app01_jmp/categorias/obtenerAPI');
        const categorias = await respuesta.json();
        
        const select = document.getElementById('cat_id');
        select.innerHTML = '<option value="">Seleccionar categoría</option>';
        
        categorias.forEach(categoria => {
            const option = document.createElement('option');
            option.value = categoria.cat_id;
            option.textContent = categoria.cat_nombre;
            select.appendChild(option);
        });
        
    } catch (error) {
        console.error('Error al cargar categorías:', error);
        
        // FALLBACK: Categorías por defecto si no hay endpoint
        const select = document.getElementById('cat_id');
        select.innerHTML = `
            <option value="">Seleccionar categoría</option>
            <option value="1">Alimentos</option>
            <option value="2">Higiene</option>
            <option value="3">Hogar</option>
        `;
    }
}

/**
 * CARGAR PRIORIDADES DISPONIBLES
 */
async function cargarPrioridades() {
    try {
        const select = document.getElementById('pri_id');
        select.innerHTML = `
            <option value="">Seleccionar prioridad</option>
            <option value="1">Alta</option>
            <option value="2">Media</option>
            <option value="3">Baja</option>
        `;
        
    } catch (error) {
        console.error('Error al cargar prioridades:', error);
    }
}

/**
 * AGRUPAR PRODUCTOS POR CATEGORÍA Y ORDENAR POR PRIORIDAD
 */
function agruparPorCategoria(productos) {
    const agrupados = {};

    // Agrupar por categoría
    productos.forEach(p => {
        if (!agrupados[p.cat_nombre]) {
            agrupados[p.cat_nombre] = [];
        }
        agrupados[p.cat_nombre].push(p);
    });

    // Ordenar dentro de cada categoría por prioridad
    Object.keys(agrupados).forEach(categoria => {
        agrupados[categoria].sort((a, b) => {
            const prioridades = { "Alta": 1, "Media": 2, "Baja": 3 };
            return prioridades[a.pri_nombre] - prioridades[b.pri_nombre];
        });
    });

    return agrupados;
}

/**
 * MOSTRAR PRODUCTOS EN LA INTERFAZ
 */
function mostrarProductos(productos) {
    const porComprar = document.getElementById('productos-por-comprar');
    const comprados = document.getElementById('productos-comprados');

    // Dividir productos por estado
    const noComprados = productos.filter(p => p.comprado == 0);
    const siComprados = productos.filter(p => p.comprado == 1);

    // MOSTRAR PRODUCTOS POR COMPRAR
    const agrupados = agruparPorCategoria(noComprados);
    porComprar.innerHTML = '';

    if (Object.keys(agrupados).length === 0) {
        porComprar.innerHTML = '<p class="text-muted text-center">No hay productos pendientes</p>';
    } else {
        Object.keys(agrupados).forEach(categoria => {
            const div = document.createElement('div');
            div.innerHTML = `
                <h6 class="text-primary mb-3">📦 ${categoria}</h6>
                <div class="row mb-4">
                    ${agrupados[categoria].map(producto => `
                        <div class="col-md-6 col-lg-4 mb-3">
                            <div class="card border-left-${getPrioridadColor(producto.pri_nombre)}">
                                <div class="card-body p-3">
                                    <h6 class="card-title">${producto.prod_nombre}</h6>
                                    <p class="card-text">
                                        <small class="text-muted">Cantidad: ${producto.prod_cantidad}</small><br>
                                        <span class="badge bg-${getPrioridadColor(producto.pri_nombre)}">${producto.pri_nombre}</span>
                                    </p>
                                    <div class="btn-group btn-group-sm w-100">
                                        <button class="btn btn-success" onclick="marcarComprado(${producto.prod_id}, 1)" title="Marcar como comprado">✓</button>
                                        <button class="btn btn-warning" onclick="editarProducto(${producto.prod_id}, '${producto.prod_nombre.replace(/'/g, "\\'")}', ${producto.prod_cantidad}, ${producto.cat_id}, ${producto.pri_id})" title="Editar">✏</button>
                                        <button class="btn btn-danger" onclick="eliminarProducto(${producto.prod_id})" title="Eliminar">✗</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `).join('')}
                </div>
            `;
            porComprar.appendChild(div);
        });
    }

    // MOSTRAR PRODUCTOS COMPRADOS
    comprados.innerHTML = '';
    if (siComprados.length === 0) {
        comprados.innerHTML = '<p class="text-muted text-center">No hay productos comprados</p>';
    } else {
        siComprados.forEach(producto => {
            const div = document.createElement('div');
            div.classList.add('col-md-6', 'col-lg-4', 'mb-3');
            div.innerHTML = `
                <div class="card border-left-secondary bg-light">
                    <div class="card-body p-3">
                        <h6 class="card-title"><del>${producto.prod_nombre}</del></h6>
                        <p class="card-text">
                            <small class="text-muted">Cantidad: ${producto.prod_cantidad}</small><br>
                            <span class="badge bg-secondary">${producto.pri_nombre}</span>
                        </p>
                        <div class="btn-group btn-group-sm w-100">
                            <button class="btn btn-secondary" onclick="marcarComprado(${producto.prod_id}, 0)" title="Desmarcar">↩</button>
                            <button class="btn btn-danger" onclick="eliminarProducto(${producto.prod_id})" title="Eliminar">✗</button>
                        </div>
                    </div>
                </div>
            `;
            comprados.appendChild(div);
        });
    }
}

/**
 * FUNCIÓN PARA EDITAR PRODUCTO (GLOBAL)
 */
window.editarProducto = function(id, nombre, cantidad, catId, priId) {
    // Llenar formulario con datos del producto
    document.getElementById('prod_id').value = id;
    document.getElementById('prod_nombre').value = nombre;
    document.getElementById('prod_cantidad').value = cantidad;
    document.getElementById('cat_id').value = catId;
    document.getElementById('pri_id').value = priId;
    
    // Cambiar a modo edición
    modoEdicion = true;
    btnGuardar.style.display = 'none';
    btnModificar.style.display = 'inline-block';
    
    // Scroll al formulario
    formulario.scrollIntoView({ behavior: 'smooth' });
}

/**
 * FUNCIÓN PARA MARCAR COMO COMPRADO (GLOBAL)
 */
window.marcarComprado = async function(id, valor) {
    const datos = new FormData();
    datos.append('prod_id', id);
    datos.append('valor', valor);
    
    try {
        const respuesta = await fetch('/app01_jmp/productos/marcarCompradoAPI', {
            method: 'POST',
            body: datos
        });
        
        const resultado = await respuesta.json();
        
        if(resultado.resultado) {
            cargarProductos(); // Recargar la lista
        } else {
            Swal.fire('Error', resultado.mensaje, 'error');
        }
        
    } catch (error) {
        console.error('Error al marcar producto:', error);
        Swal.fire('Error', 'Ocurrió un error al actualizar el producto', 'error');
    }
}

/**
 * FUNCIÓN PARA ELIMINAR PRODUCTO (GLOBAL)
 */
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
        const datos = new FormData();
        datos.append('prod_id', id);
        
        try {
            const respuesta = await fetch('/app01_jmp/productos/eliminarAPI', {
                method: 'POST',
                body: datos
            });
            
            const resultado = await respuesta.json();
            
            if(resultado.resultado) {
                Swal.fire('Eliminado', resultado.mensaje, 'success');
                cargarProductos();
            } else {
                Swal.fire('Error', resultado.mensaje, 'error');
            }
            
        } catch (error) {
            console.error('Error al eliminar:', error);
            Swal.fire('Error', 'Ocurrió un error al eliminar', 'error');
        }
    }
}

/**
 * LIMPIAR FORMULARIO Y RESETEAR MODO
 */
function limpiarFormulario() {
    formulario.reset();
    document.getElementById('prod_id').value = '';
    
    // Volver al modo agregar
    modoEdicion = false;
    btnGuardar.style.display = 'inline-block';
    btnModificar.style.display = 'none';
}

/**
 * OBTENER COLOR SEGÚN PRIORIDAD
 */
function getPrioridadColor(prioridad) {
    const colores = {
        "Alta": "danger",    // Rojo
        "Media": "warning",  // Amarillo
        "Baja": "success"    // Verde
    };
    return colores[prioridad] || "secondary";
}