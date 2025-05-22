<div class="container">
    <div class="row">
        <div class="col-12">
            <h2 class="text-center my-4">🛒 Lista de Compras de María</h2>
        </div>
    </div>

    <!-- Formulario -->
    <div class="row mb-4">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5>➕ Agregar Producto</h5>
                </div>
                <div class="card-body">
                    <form id="FormProductos">
                        <input type="hidden" id="prod_id" name="prod_id">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="prod_nombre" class="form-label">Nombre del Producto</label>
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
                                <select class="form-control" id="cat_id" name="cat_id" required>
                                    <option value="">Seleccione una categoría</option>
                                    <?php foreach($categorias as $categoria): ?>
                                        <option value="<?php echo $categoria->cat_id; ?>"><?php echo $categoria->cat_nombre; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="pri_id" class="form-label">Prioridad</label>
                                <select class="form-control" id="pri_id" name="pri_id" required>
                                    <option value="">Seleccione prioridad</option>
                                    <?php foreach($prioridades as $prioridad): ?>
                                        <option value="<?php echo $prioridad->pri_id; ?>"><?php echo $prioridad->pri_nombre; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-success" id="BtnGuardar">Guardar</button>
                            <button type="button" class="btn btn-warning d-none" id="BtnModificar">Modificar</button>
                            <button type="reset" class="btn btn-secondary" id="BtnLimpiar">Limpiar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Productos por Comprar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-success text-white">
                    <h5>📋 Productos por Comprar</h5>
                </div>
                <div class="card-body" id="productos-por-comprar">
                    <!-- Se llenará dinámicamente -->
                </div>
            </div>
        </div>
    </div>

    <!-- Lista de Productos Comprados -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-secondary text-white">
                    <h5>✅ Productos Comprados</h5>
                </div>
                <div class="card-body" id="productos-comprados">
                    <!-- Se llenará dinámicamente -->
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/productos/index.js') ?>"></script>