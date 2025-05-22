<div class="container">
    <div class="row">
        <div class="col-12">
            <h2 class="text-center my-4">Categorías</h2>
        </div>
    </div>

    <!-- Formulario -->
    <div class="row mb-4">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    <h5>Agregar Categoría</h5>
                </div>
                <div class="card-body">
                    <form id="FormCategorias" method="POST">
                        <input type="hidden" id="cat_id" name="cat_id">

                        <div class="mb-3">
                            <label for="cat_nombre" class="form-label">Nombre de Categoría</label>
                            <input type="text" class="form-control" id="cat_nombre" name="cat_nombre" required>
                        </div>

                        <div class="text-center">
                            <button type="submit" class="btn btn-success" id="BtnGuardar">Guardar</button>
                            <button type="reset" class="btn btn-secondary" id="BtnLimpiar">Limpiar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Categorías -->
    <div class="row">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5>Listado de Categorías</h5>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-striped mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($categorias as $cat): ?>
                            <tr>
                                <td><?php echo $cat->cat_id; ?></td>
                                <td><?php echo $cat->cat_nombre; ?></td>
                                <td class="text-center">
                                    <button class="btn btn-warning btn-sm btn-editar" data-id="<?php echo $cat->cat_id; ?>" data-nombre="<?php echo $cat->cat_nombre; ?>">✏</button>
                                    <button class="btn btn-danger btn-sm btn-eliminar" data-id="<?php echo $cat->cat_id; ?>">✗</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if(empty($categorias)): ?>
                            <tr>
                                <td colspan="3" class="text-center text-muted">No hay categorías registradas</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo asset('src/js/categorias/index.js'); ?>"></script>
