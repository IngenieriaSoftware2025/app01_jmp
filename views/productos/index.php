<div class="container py-4">
    <div class="row justify-content-center p-3">
        <div class="col-lg-10">
            <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
                <div class="card-body p-3">
                    <div class="row mb-3">
                        <h5 class="text-center mb-2">¡Lista de Compras de María!</h5>
                        <h4 class="text-center mb-2 text-primary">GESTIÓN DE PRODUCTOS</h4>
                    </div>

                    <div class="row justify-content-center p-4 shadow-lg rounded">
                        <form id="FormProductos">
                            <input type="hidden" id="prod_id" name="prod_id">

                            <div class="row mb-3 justify-content-center">
                                <div class="col-lg-6">
                                    <label for="prod_nombre" class="form-label">NOMBRE DEL PRODUCTO</label>
                                    <input type="text" class="form-control" id="prod_nombre" name="prod_nombre" placeholder="Ej: Papel higiénico" required>
                                </div>
                                <div class="col-lg-6">
                                    <label for="prod_cantidad" class="form-label">CANTIDAD</label>
                                    <input type="number" class="form-control" id="prod_cantidad" name="prod_cantidad" min="1" value="1" placeholder="Ej: 3" required>
                                </div>
                            </div>

                            <div class="row mb-3 justify-content-center">
                                <div class="col-lg-4">
                                    <label for="precio" class="form-label">PRECIO</label>
                                    <div class="input-group">
                                        <span class="input-group-text">Q.</span>
                                        <input type="number" class="form-control" id="precio" name="precio" min="0" step="0.01" value="0.00" placeholder="15.50" required>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <label for="stock" class="form-label">STOCK</label>
                                    <input type="number" class="form-control" id="stock" name="stock" min="0" value="0" placeholder="Ej: 10" required>
                                </div>
                                <div class="col-lg-4">
                                    <label for="pri_id" class="form-label">PRIORIDAD</label>
                                    <select name="pri_id" class="form-select" id="pri_id" required>
                                        <option value="" class="text-center"> -- SELECCIONE PRIORIDAD -- </option>
                                        <option value="1">Alta</option>
                                        <option value="2">Media</option>
                                        <option value="3">Baja</option>
                                    </select>
                                </div>
                            </div>

                            <div class="row mb-3 justify-content-center">
                                <div class="col-lg-12">
                                    <label for="cat_id" class="form-label">CATEGORÍA</label>
                                    <select name="cat_id" class="form-select" id="cat_id" required>
                                        <option value="" class="text-center"> -- SELECCIONE CATEGORÍA -- </option>
                                        <!-- Se cargará dinámicamente con JavaScript -->
                                    </select>
                                </div>
                            </div>

                            <!-- Campo oculto para 'comprado' por defecto -->
                            <input type="hidden" name="comprado" value="0">

                            <div class="row justify-content-center mt-4">
                                <div class="col-auto">
                                    <button class="btn btn-primary" type="submit" id="BtnGuardar">
                                        <i class="bi bi-plus-circle me-1"></i>Guardar
                                    </button>
                                </div>

                                <div class="col-auto">
                                    <button class="btn btn-warning" type="button" id="BtnModificar" style="display: none;">
                                        <i class="bi bi-pencil me-1"></i>Modificar
                                    </button>
                                </div>

                                <div class="col-auto">
                                    <button class="btn btn-secondary" type="button" id="BtnLimpiar">
                                        <i class="bi bi-x-circle me-1"></i>Limpiar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos por comprar -->
    <div class="row justify-content-center p-3">
        <div class="col-lg-10">
            <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
                <div class="card-body p-3">
                    <h3 class="text-center">PRODUCTOS POR COMPRAR</h3>
                    <div id="productos-por-comprar">
                        <!-- Se cargarán dinámicamente con JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Productos comprados -->
    <div class="row justify-content-center p-3">
        <div class="col-lg-10">
            <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
                <div class="card-body p-3">
                    <h3 class="text-center">PRODUCTOS COMPRADOS</h3>
                    <div id="productos-comprados">
                        <!-- Se cargarán dinámicamente con JavaScript -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= asset('build/js/productos/index.js'); ?>"></script>