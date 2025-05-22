<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <h2 class="text-center mb-4">Lista de Compras de María</h2>
        </div>
    </div>

    <!-- Formulario -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5>Agregar Producto</h5>
                </div>
                <div class="card-body">
                    <form id="FormProductos" method="POST">
                        <input type="hidden" id="prod_id" name="prod_id">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="prod_nombre" class="form-label">Producto</label>
                                <input type="text" class="form-control" id="prod_nombre" name="prod_nombre" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="prod_cantidad" class="form-label">Cantidad</label>
                                <input type="number" class="form-control" id="prod_cantidad" name="prod_cantidad" min="1" value="1" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="cat_id" class="form-label">Categoría</label>
                                <select class="form-select" id="cat_id" name="cat_id" required>
                                    <option value="">Seleccionar...</option>
                                    <?php foreach($categorias as $cat): ?>
                                        <option value="<?= $cat->cat_id ?>"><?= $cat->cat_nombre ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pri_id" class="form-label">Prioridad</label>
                                <select class="form-select" id="pri_id" name="pri_id" required>
                                    <option value="">Seleccionar...</option>
                                    <?php foreach($prioridades as $pri): ?>
                                        <option value="<?= $pri->pri_id ?>"><?= $pri->pri_nombre ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="text-center">
                            <button type="submit" class="btn btn-success" id="BtnGuardar">Agregar producto</button>
                            <button type="reset" class="btn btn-secondary" id="BtnLimpiar">Limpiar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Productos -->
    <div class="row">
        <div class="col-12">
            <?php 
            $categoriaActual = '';
            $productosComprados = [];
            $productosPendientes = [];
            
            // Separar productos
            foreach($productos as $producto) {
                if($producto->comprado == 1) {
                    $productosComprados[] = $producto;
                } else {
                    $productosPendientes[] = $producto;
                }
            }
            ?>
            
            <!-- Productos Pendientes -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5>Por Comprar</h5>
                </div>
                <div class="card-body">
                    <?php if(empty($productosPendientes)): ?>
                        <p class="text-muted">No hay productos pendientes</p>
                    <?php else: ?>
                        <?php 
                        $categoriaActual = '';
                        foreach($productosPendientes as $producto): 
                            if($categoriaActual != $producto->cat_nombre): 
                                if($categoriaActual != '') echo '</div>';
                                $categoriaActual = $producto->cat_nombre;
                        ?>
                            <h6 class="mt-3 mb-2 text-secondary"><?= $producto->cat_nombre ?></h6>
                            <div class="row">
                        <?php endif; ?>
                        
                        <div class="col-md-6 col-lg-4 mb-2">
                            <div class="card card-body p-2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <strong><?= $producto->prod_nombre ?></strong>
                                        <small class="text-muted d-block">Cantidad: <?= $producto->prod_cantidad ?></small>
                                        <span class="badge bg-<?= $producto->pri_nombre == 'Alta' ? 'danger' : ($producto->pri_nombre == 'Media' ? 'warning' : 'success') ?>">
                                            <?= $producto->pri_nombre ?>
                                        </span>
                                    </div>
                                    <div class="btn-group-vertical btn-group-sm">
                                        <button class="btn btn-success btn-sm btn-comprado" data-id="<?= $producto->prod_id ?>">✓</button>
                                        <button class="btn btn-warning btn-sm btn-editar" data-id="<?= $producto->prod_id ?>">✏</button>
                                        <button class="btn btn-danger btn-sm btn-eliminar" data-id="<?= $producto->prod_id ?>">✗</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <?php endforeach; ?>
                        <?php if($categoriaActual != '') echo '</div>'; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Productos Comprados -->
            <?php if(!empty($productosComprados)): ?>
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5>Comprados</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php foreach($productosComprados as $producto): ?>
                        <div class="col-md-6 col-lg-4 mb-2">
                            <div class="card card-body p-2 bg-light">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <span class="text-decoration-line-through text-muted"><?= $producto->prod_nombre ?></span>
                                        <small class="text-muted d-block">Cantidad: <?= $producto->prod_cantidad ?></small>
                                    </div>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-secondary btn-sm btn-comprado" data-id="<?= $producto->prod_id ?>">↩</button>
                                        <button class="btn btn-outline-danger btn-sm btn-eliminar" data-id="<?= $producto->prod_id ?>">✗</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="<?= asset('src/js/productos/index.js') ?>"></script>