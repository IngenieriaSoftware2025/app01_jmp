
import Swal from "sweetalert2";

const formulario = document.getElementById('FormProductos');
const btnGuardar = document.getElementById('BtnGuardar');
const btnModificar = document.getElementById('BtnModificar');
const btnLimpiar = document.getElementById('BtnLimpiar');

// Inicializar
document.addEventListener('DOMContentLoaded', function() {
    cargarProductos();
});

// Eventos
formulario.addEventListener('submit', guardarProducto);
btnLimpiar.addEventListener('click', limpiarFormulario);
btnModificar.addEventListener('click', modificarProducto);

async function guardarProducto(e) {
    e.preventDefault();
    
    const datos = new FormData(formulario);
    
    try {
        const url = '/app01_jmp/productos/guardarAPI';
        const config = {
            method: 'POST',
            body: datos
        };
        
        const respuesta = await fetch(url, config);
        const resultado = await respuesta.json();
        
        if(resultado.resultado) {
            Swal.fire('Éxito', resultado.mensaje, 'success');
            limpiarFormulario();
            cargarProductos();
        } else {
            Swal.fire('Error', resultado.mensaje, 'error');
        }
        
    } catch (error) {
        console.error(error);
        Swal.fire('Error', 'Ocurrió un error al guardar', 'error');
    }
}

async function modificarProducto() {
    const datos = new FormData(formulario);
    
    try {
        const url = '/app01_jmp/productos/guardarAPI';
        const config = {
            method: 'POST',
            body: datos
        };
        
        const respuesta = await fetch(url, config);
        const resultado = await respuesta.json();
        
        if(resultado.resultado) {
            Swal.fire('Éxito', resultado.mensaje, 'success');
            limpiarFormulario();
            cargarProductos();
        } else {
            Swal.fire('Error', resultado.mensaje, 'error');
        }
        
    } catch (error) {
        console.error(error);
        Swal.fire('Error', 'Ocurrió un error al modificar', 'error');
    }
}

async function cargarProductos() {
    try {
        const url = '/app01_jmp/productos/obtenerAPI';
        const respuesta = await fetch(url);
        const productos = await respuesta.json();
        
        mostrarProductos(productos);
        
    } catch (error) {
        console.error(error);
    }
}

function agruparPorCategoria(productos) {
    const agrupados = {};

    productos.forEach(p => {
        if (!agrupados[p.cat_nombre]) {
            agrupados[p.cat_nombre] = [];
        }
        agrupados[p.cat_nombre].push(p);
    });

    Object.keys(agrupados).forEach(categoria => {
        agrupados[categoria].sort((a, b) => {
            const prioridades = { "Alta": 1, "Media": 2, "Baja": 3 };
            return prioridades[a.pri_nombre] - prioridades[b.pri_nombre];
        });
    });

    return agrupados;
}

function mostrarProductos(productos) {
    const porComprar = document.getElementById('productos-por-comprar');
    const comprados = document.getElementById('productos-comprados');

    const noComprados = productos.filter(p => p.comprado == 0);
    const siComprados = productos.filter(p => p.comprado == 1);

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
                                        <button class="btn btn-success" onclick="marcarComprado(${producto.prod_id}, 1)">✓</button>
                                        <button class="btn btn-warning" onclick="editarProducto(${producto.prod_id}, '${producto.prod_nombre}', ${producto.prod_cantidad}, ${producto.cat_id}, ${producto.pri_id})">✏</button>
                                        <button class="btn btn-danger" onclick="eliminarProducto(${producto.prod_id})">✗</button>
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
                            <button class="btn btn-secondary" onclick="marcarComprado(${producto.prod_id}, 0)">↩</button>
                            <button class="btn btn-danger" onclick="eliminarProducto(${producto.prod_id})">✗</button>
                        </div>
                    </div>
                </div>
            `;
            comprados.appendChild(div);
        });
    }
}

function limpiarFormulario() {
    formulario.reset();
    document.getElementById('prod_id').value = '';
}

function getPrioridadColor(prioridad) {
    const colores = {
        "Alta": "danger",
        "Media": "warning",
        "Baja": "success"
    };
    return colores[prioridad] || "secondary";
}
