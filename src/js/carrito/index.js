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
// MODIFICAR FACTURA
window.modificarFactura = async function(facturaId) {
    try {
        // Mostrar loading
        Swal.fire({
            title: 'Cargando factura...',
            text: 'Por favor espere',
            allowOutsideClick: false,
            showConfirmButton: false,
            willOpen: () => {
                Swal.showLoading();
            }
        });

        // Obtener detalles de la factura
        const respuesta = await fetch(`/app01_jmp/carrito/obtenerDetalleFacturaAPI?factura_id=${facturaId}`);
        
        if (!respuesta.ok) {
            throw new Error(`Error HTTP: ${respuesta.status}`);
        }

        const resultado = await respuesta.json();

        Swal.close();

        if (!resultado.resultado) {
            Swal.fire('Error', resultado.mensaje, 'error');
            return;
        }

        // Limpiar carrito actual
        carrito = [];
        
        // Seleccionar el cliente
        clienteSelect.value = resultado.factura.cliente_id;
        
        // Cargar productos en el carrito
        resultado.detalles.forEach(detalle => {
            carrito.push({
                prod_id: detalle.prod_id,
                prod_nombre: detalle.prod_nombre,
                precio: parseFloat(detalle.detalle_precio),
                cantidad: parseInt(detalle.detalle_cantidad),
                subtotal: parseFloat(detalle.detalle_subtotal)
            });
        });

        // Actualizar vista del carrito
        actualizarVistaCarrito();
        verificarEstadoBoton();

        // Cambiar el botón para indicar que es una modificación
        btnGuardarCompra.textContent = 'Actualizar Compra';
        btnGuardarCompra.dataset.facturaId = facturaId;

        Swal.fire({
            icon: 'success',
            title: 'Factura cargada',
            text: `Factura #${facturaId} lista para modificar`,
            timer: 2000,
            showConfirmButton: false
        });

    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error', 'No se pudo cargar la factura: ' + error.message, 'error');
    }
};

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
    if (carrito.length === 0 && !btnGuardarCompra.dataset.facturaId) {
        Swal.fire('Info', 'El carrito ya está vacío', 'info');
        return;
    }

    const esEdicion = btnGuardarCompra.dataset.facturaId;
    const titulo = esEdicion ? '¿Cancelar edición?' : '¿Limpiar carrito?';
    const texto = esEdicion ? 'Se cancelará la edición de la factura' : 'Se eliminarán todos los productos del carrito';

    Swal.fire({
        title: titulo,
        text: texto,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, limpiar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            limpiarCarritoCompleto();
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

    // Verificar si es una actualización o nueva compra
    const esActualizacion = btnGuardarCompra.dataset.facturaId;
    const tituloOperacion = esActualizacion ? 'Actualizando compra...' : 'Guardando compra...';

    try {
        // Mostrar loading
        Swal.fire({
            title: tituloOperacion,
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
        
        // Si es actualización, agregar el ID de la factura
        if (esActualizacion) {
            datos.append('factura_id', btnGuardarCompra.dataset.facturaId);
        }

        const url = esActualizacion ? '/app01_jmp/carrito/actualizarCompraAPI' : '/app01_jmp/carrito/guardarCompraAPI';

        const respuesta = await fetch(url, {
            method: 'POST',
            body: datos
        });

        if (!respuesta.ok) {
            throw new Error(`Error HTTP: ${respuesta.status}`);
        }

        const resultado = await respuesta.json();

        Swal.close();

        if (resultado.resultado) {
            const mensajeExito = esActualizacion ? 'Compra actualizada correctamente' : 'Compra guardada correctamente';
            
            Swal.fire({
                icon: 'success',
                title: mensajeExito,
                text: `Factura #${resultado.factura_id}`,
                confirmButtonText: 'OK'
            }).then(() => {
                // Limpiar formulario
                limpiarCarritoCompleto();
                
                // Recargar facturas recientes
                cargarFacturasRecientes();
            });
        } else {
            Swal.fire('Error', resultado.mensaje, 'error');
        }

    } catch (error) {
        console.error('Error:', error);
        Swal.fire('Error', 'Ocurrió un error al procesar la compra: ' + error.message, 'error');
    }
}

// LIMPIAR CARRITO COMPLETO (incluyendo modo edición)
function limpiarCarritoCompleto() {
    carrito = [];
    clienteSelect.value = '';
    actualizarVistaCarrito();
    verificarEstadoBoton();
    
    // Limpiar inputs de cantidad
    document.querySelectorAll('.cantidad-input').forEach(input => {
        input.value = 0;
    });

    // Restaurar botón a modo normal
    btnGuardarCompra.textContent = 'Guardar Compra';
    delete btnGuardarCompra.dataset.facturaId;
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
                <div class="border-bottom pb-2 mb-2">
                    <div class="d-flex justify-content-between align-items-start">
                        <div class="flex-grow-1">
                            <div class="d-flex justify-content-between">
                                <small><strong>#${factura.factura_id}</strong></small>
                                <small>Q. ${parseFloat(factura.factura_total).toFixed(2)}</small>
                            </div>
                            <small class="text-muted">${factura.cliente_nombre}</small>
                        </div>
                    </div>
                    <div class="mt-1">
                        <button class="btn btn-sm btn-outline-primary" onclick="modificarFactura(${factura.factura_id})">
                            <i class="bi bi-pencil"></i> Modificar
                        </button>
                    </div>
                </div>
            `;
        });

        facturasRecientes.innerHTML = html;

    } catch (error) {
        console.error('Error al cargar facturas:', error);
        facturasRecientes.innerHTML = '<p class="text-center text-muted small">Error al cargar</p>';
    }
}