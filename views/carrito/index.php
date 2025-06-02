<div class="container py-4">
    <!-- Selección de Cliente -->
    <div class="row justify-content-center mb-4">
        <div class="col-lg-6">
            <div class="card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-person-check me-2"></i>Seleccionar Cliente</h5>
                </div>
                <div class="card-body">
                    <select class="form-select" id="cliente_id" required>
                        <option value="">-- Seleccione un cliente --</option>
                        <?php foreach ($clientes as $cliente): ?>
                            <option value="<?php echo $cliente->cliente_id; ?>">
                                <?php echo $cliente->cliente_nombre; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Lista de Productos -->
        <div class="col-lg-8">
            <div class="card shadow-lg" style="border-radius: 10px; border: 1px solid #28a745;">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-shop me-2"></i>Productos Disponibles</h5>
                </div>
                <div class="card-body">
                    <div id="productos-disponibles">
                        <?php if (empty($productos)): ?>
                            <p class="text-center text-muted">No hay productos disponibles</p>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($productos as $producto): ?>
                                    <?php if (isset($producto['stock']) ? $producto['stock'] > 0 : (isset($producto->stock) ? $producto->stock > 0 : false)): ?>
                                        <div class="col-md-6 mb-3">
                                            <div class="card border-success h-100">
                                                <div class="card-body">
                                                    <h6 class="card-title"><?php echo isset($producto['prod_nombre']) ? $producto['prod_nombre'] : $producto->prod_nombre; ?></h6>
                                                    <p class="card-text mb-2">
                                                        <strong>Precio:</strong> Q. <?php echo number_format(isset($producto['precio']) ? $producto['precio'] : $producto->precio, 2); ?><br>
                                                        <strong>Stock:</strong> <?php echo isset($producto['stock']) ? $producto['stock'] : $producto->stock; ?><br>
                                                        <strong>Categoría:</strong> <?php echo isset($producto['cat_nombre']) ? $producto['cat_nombre'] : $producto->cat_nombre; ?>
                                                    </p>
                                                    <div class="input-group input-group-sm">
                                                        <span class="input-group-text">Cantidad</span>
                                                        <input type="number" 
                                                               class="form-control cantidad-input" 
                                                               min="0" 
                                                               max="<?php echo isset($producto['stock']) ? $producto['stock'] : $producto->stock; ?>" 
                                                               value="0"
                                                               data-prod-id="<?php echo isset($producto['prod_id']) ? $producto['prod_id'] : $producto->prod_id; ?>"
                                                               data-prod-nombre="<?php echo htmlspecialchars(isset($producto['prod_nombre']) ? $producto['prod_nombre'] : $producto->prod_nombre); ?>"
                                                               data-precio="<?php echo isset($producto['precio']) ? $producto['precio'] : $producto->precio; ?>"
                                                               data-stock="<?php echo isset($producto['stock']) ? $producto['stock'] : $producto->stock; ?>">
                                                        <button class="btn btn-outline-success btn-agregar" 
                                                                data-prod-id="<?php echo isset($producto['prod_id']) ? $producto['prod_id'] : $producto->prod_id; ?>">
                                                            <i class="bi bi-cart-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Carrito de Compras -->
        <div class="col-lg-4">
            <div class="card shadow-lg" style="border-radius: 10px; border: 1px solid #ffc107;">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="bi bi-cart me-2"></i>Carrito de Compras</h5>
                </div>
                <div class="card-body">
                    <div id="carrito-items">
                        <p class="text-center text-muted">El carrito está vacío</p>
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <strong>Total: Q. <span id="total-carrito">0.00</span></strong>
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" id="btn-guardar-compra" disabled>
                            <i class="bi bi-check-circle me-1"></i>Guardar Compra
                        </button>
                        <button class="btn btn-secondary" id="btn-limpiar-carrito">
                            <i class="bi bi-trash me-1"></i>Limpiar Carrito
                        </button>
                    </div>
                </div>
            </div>

            <!-- Facturas Recientes -->
            <div class="card shadow-lg mt-4" style="border-radius: 10px; border: 1px solid #17a2b8;">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0"><i class="bi bi-receipt me-2"></i>Facturas Recientes</h6>
                </div>
                <div class="card-body">
                    <div id="facturas-recientes">
                        <p class="text-center text-muted small">Cargando...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo asset('build/js/carrito/index.js'); ?>"></script>