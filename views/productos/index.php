<div class="container py-4">
    <h2 class="text-center mb-4">Lista de Compras de María</h2>

    <!-- Formulario -->
    <div class="card mb-4">
        <div class="card-header bg-success text-white">Agregar Producto</div>
        <div class="card-body">


            <form id="FormProductos">
                <input type="hidden" id="prod_id" name="prod_id">

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="prod_nombre" class="form-label">Nombre del Producto</label>
                        <input type="text" class="form-control" id="prod_nombre" name="prod_nombre" required>
                    </div>
                    <div class="col-md-6">
                        <label for="prod_cantidad" class="form-label">Cantidad</label>
                        <input type="number" class="form-control" id="prod_cantidad" name="prod_cantidad" min="1" required>
                    </div>
                </div>

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="cat_id" class="form-label">Categoría</label>
                        <select class="form-select" id="cat_id" name="cat_id" required>
                            <!-- opciones dinámicas -->
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="pri_id" class="form-label">Prioridad</label>
                        <select class="form-select" id="pri_id" name="pri_id" required>
                            <!-- opciones dinámicas -->
                        </select>
                    </div>
                </div>

                <!-- Campo oculto para 'comprado' por defecto -->
                <input type="hidden" name="comprado" value="0">

                <div class="text-end">
                    <button type="submit" class="btn btn-success" id="BtnGuardar">Guardar</button>
                    <button type="button" class="btn btn-warning" id="BtnModificar">Modificar</button>
                    <button type="button" class="btn btn-secondary" id="BtnLimpiar">Limpiar</button>
                </div>
            </form>

        </div>
    </div>

    <!-- Productos por comprar -->
    <div class="mb-5">
        <h4 class="text-primary">📝 Por Comprar</h4>
        <div class="row" id="productos-por-comprar"></div>
    </div>

    <!-- Productos comprados -->
    <div class="mb-5">
        <h4 class="text-success">✅ Comprados</h4>
        <div class="row" id="productos-comprados"></div>
    </div>
</div>