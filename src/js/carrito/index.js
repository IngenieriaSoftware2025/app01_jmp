import Swal from "sweetalert2";

// VARIABLES GLOBALES
let carrito = [];
let totalCarrito = 0;

// ELEMENTOS DEL DOM
const clienteSelect = document.getElementById('cliente_id');
const carritoItems = document.getElementById('carrito-items');
const totalCarritoSpan = document.getElementById('total-carrito');
const btnGuardarCompra = document.getElementById('btn-guardar-compra');
const btnLimpiarCarrito = document.getElementById('btn-limpiar-carrito');
const facturasRecientes = document.getElementById('facturas-recientes');

// INICIALIZACIÓN
document.addEventListener('DOMContentLoaded', function() {
    configurarEventos();
    cargarFacturasRecientes();
});

// CONFIGURAR EVENTOS
function configurarEventos() {
    // Eventos para agregar productos al carrito
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('btn-agregar') || e.target.closest('.btn-agregar')) {
            const btn = e.target.closest('.btn-agregar');
            const prodId = btn.dataset.prodId;
            agregarAlCarrito(prodId);
        }
    });

    // Evento para cambios en selección de cliente
    clienteSelect.addEventListener('change', verificarEstadoBoton);

    // Eventos de botones del carrito
    btnGuardarCompra.addEventListener('click', guardarCompra);
    btnLimpiarCarrito.addEventListener('click', limpiarCarrito);

    // Eventos para cambios en cantidad
    document.addEventListener('input', function(e) {
        if (e.target.classList.contains('cantidad-input')) {
            const input = e.target;
            const max = parseInt(input.getAttribute('max'));
            const value = parseInt(input.value);
            
            if (value > max) {
                input.value = max;
                Swal.fire('Atención', `Stock máximo disponible: ${max}`, 'warning');
            }
        }
    });
}

// AGREGAR PRODUCTO AL CARRITO
function agregarAlCarrito(prodId) {
    const input = document.querySelector(`input[data-prod-id="${prodId}"]`);
    const cantidad = parseInt(input.value);
    
    if (cantidad <= 0) {
        Swal.fire('Error', 'Debe especificar una cantidad mayor a 0', 'error');
        return;
    }

    const prodNombre = input.dataset.prodNombre;
    const precio = parseFloat(input.dataset.precio);
    const stock = parseInt(input.dataset.stock);

    if (cantidad > stock) {
        Swal.fire('Error', `Stock insuficiente. Disponible: ${stock}`, 'error');
        return;
    }

    // Verificar si el producto ya está en el carrito
    const productoExistente = carrito.find(item => item.prod_id === prodId);
    
    if (productoExistente) {
        const nuevaCantidad = productoExistente.cantidad + cantidad;
        if (nuevaCantidad > stock) {
            Swal.fire('Error', `No puede agregar más. Total en carrito sería: ${nuevaCantidad}, stock disponible: ${stock}`, 'error');
            return;
        }
        productoExistente.cantidad = nuevaCantidad;
        productoExistente.subtotal = productoExistente.cantidad * precio;
    } else {
        carrito.push({
            prod_id: prodId,
            prod_nombre: prodNombre,
            precio: precio,
            cantidad: cantidad,
            subtotal: cantidad * precio
        });
    }

    // Limpiar input
    input.value = 0;
    
    // Actualizar vista del carrito
    actualizarVistaCarrito();
    verificarEstadoBoton();

    Swal.fire({
        icon: 'success',
        title: 'Producto agregado',
        text: `${prodNombre} agregado al carrito`,
        timer: 1500,
        showConfirmButton: false
    });
}

// ACTUALIZAR VISTA DEL CARRITO
function actualizarVistaCarrito() {
    if (carrito.length === 0) {
        carritoItems.innerHTML = '<p class="text-center text-muted">El carrito está vacío</p>';
        totalCarrito = 0;
    } else {
        let html = '';
        totalCarrito = 0;

        carrito.forEach((item, index) => {
            totalCarrito += item.subtotal;
            html += `
                <div class="carrito-item border-bottom pb-2 mb-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">${item.prod_nombre}</h6>
                            <small class="text-muted">Q. ${item.precio.toFixed(2)} c/u</small>
                        </div>
                        <button class="btn btn-sm btn-outline-danger" onclick="eliminarDelCarrito(${index})">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <div class="input-group input-group-sm" style="max-width: 100px;">
                            <button class="btn btn-outline-secondary" onclick="cambiarCantidad(${index}, -1)">-</button>
                            <input type="text" class="form-control text-center" value="${item.cantidad}" readonly>
                            <button class="btn btn-outline-secondary" onclick="cambiarCantidad(${index}, 1)">+</button>
                        </div>
                        <strong>Q. ${item.subtotal.toFixed(2)}</strong>
                    </div>
                </div>
            `;
        });

        carritoItems.innerHTML = html;
    }

    totalCarritoSpan.textContent = totalCarrito.toFixed(2);
}

// CAMBIAR CANTIDAD EN CARRITO
window.cambiarCantidad = function(index, cambio) {
    const item = carrito[index];
    const nuevaCantidad = item.cantidad + cambio;
    
    if (nuevaCantidad <= 0) {
        eliminarDelCarrito(index);
        return;
    }

    // Verificar stock disponible
    const input = document.querySelector(`input[data-prod-id="${item.prod_id}"]`);
    const stock = parseInt(input.dataset.stock);
    
    if (nuevaCantidad > stock) {
        Swal.fire('Error', `Stock máximo disponible: ${stock}`, 'error');
        return;
    }

    item.cantidad = nuevaCantidad;
    item.subtotal = item.cantidad * item.precio;
    
    actualizarVistaCarrito();
    verificarEstadoBoton();
};

// ELIMINAR DEL CARRITO
window.eliminarDelCarrito = function(index) {
    carrito.splice(index, 1);
    actualizarVistaCarrito();
    verificarEstadoBoton();
};

// LIMPIAR CARRITO
function limpiarCarrito() {
    if (carrito.length === 0) {
        Swal.fire('Info', 'El carrito ya está vacío', 'info');
        return;
    }

    Swal.fire({
        title: '¿Limpiar carrito?',
        text: 'Se eliminarán todos los productos del carrito',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, limpiar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            carrito = [];
            actualizarVistaCarrito();
            verificarEstadoBoton();
            
            // Limpiar todos los inputs de cantidad
            document.querySelectorAll('.cantidad-input').forEach(input => {
                input.value = 0;
            });
        }
    });
}

// VERIFICAR ESTADO DEL BOTÓN GUARDAR
function verificarEstadoBoton() {
    const clienteSeleccionado = clienteSelect.value;
    const hayProductos = carrito.length > 0;
    
    btnGuardarCompra.disabled = !(clienteSeleccionado && hayProductos);
}

// GUARDAR COMPRA
async function guardarCompra() {
    if (!clienteSelect.value) {
        Swal.fire('Error', 'Debe seleccionar un cliente', 'error');
        return;
    }

    if (carrito.length === 0) {
        Swal.fire('Error', 'Debe agregar al menos un producto', 'error');
        return;
    }

    try {
        // Mostrar loading
        Swal.fire({
            title: 'Guardando compra...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        const datos = new FormData();
        datos.append('cliente_id', clienteSelect.value);
        datos.append('productos', JSON.stringify(carrito));
        datos.append('total', totalCarrito.toFixed(2));

        const respuesta = await fetch('/app01_jmp/carrito/guardarCompraAPI', {
            method: 'POST',
            body: datos
        });

        if (!respuesta.ok) {
            throw new Error(`Error HTTP: ${respuesta.status}`);
        }

        const resultado = await respuesta.json();

        Swal.close();

        if (resultado.resultado) {
            Swal.fire({
                icon: 'success',
                title: 'Compra guardada',
                text: `Factura #${resultado.factura_id} creada correctamente`,
                confirmButtonText: 'OK'
            }).then(() => {
                // Limpiar formulario
                carrito = [];
                clienteSelect.value = '';
                actualizarVistaCarrito();
                verificarEstadoBoton();
                
                // Limpiar inputs de cantidad
                document.querySelectorAll('.cantidad-input').forEach(input => {
                    input.value = 0;
                });

                // Recargar facturas recientes
                cargarFacturasRecientes();
            });
        } else {
            Swal.fire('Error', resultado.mensaje, 'error');
        }

    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error', 'Ocurrió un error al guardar la compra: ' + error.message, 'error');
    }
}

// CARGAR FACTURAS RECIENTES
async function cargarFacturasRecientes() {
    try {
        const respuesta = await fetch('/app01_jmp/carrito/obtenerFacturasAPI');
        const facturas = await respuesta.json();

        if (facturas.length === 0) {
            facturasRecientes.innerHTML = '<p class="text-center text-muted small">No hay facturas</p>';
            return;
        }

        let html = '';
        facturas.slice(0, 5).forEach(factura => {
            html += `
                <div class="border-bottom pb-1 mb-1">
                    <div class="d-flex justify-content-between">
                        <small><strong>#${factura.factura_id}</strong></small>
                        <small>Q. ${parseFloat(factura.factura_total).toFixed(2)}</small>
                    </div>
                    <small class="text-muted">${factura.cliente_nombre}</small>
                </div>
            `;
        });

        facturasRecientes.innerHTML = html;

    } catch (error) {
        console.error('Error al cargar facturas:', error);
        facturasRecientes.innerHTML = '<p class="text-center text-muted small">Error al cargar</p>';
    }
}