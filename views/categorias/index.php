<div class="container py-4">
    <h2 class="text-center mb-4">Gestión de Categorías</h2>

    <!-- Formulario -->
    <div class="row mb-4">
        <div class="col-md-6 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Nueva Categoría</h5>
                </div>
                <div class="card-body">
                    <form id="FormCategorias">
                        <input type="hidden" id="cat_id" name="cat_id">
                        
                        <div class="mb-3">
                            <label for="cat_nombre" class="form-label">Nombre de la Categoría</label>
                            <input type="text" class="form-control" id="cat_nombre" name="cat_nombre" 
                                   placeholder="Ej: Alimentos, Higiene, Hogar" required>
                        </div>
                        
                        <div class="d-flex justify-content-end gap-2">
                            <button type="submit" class="btn btn-primary" id="BtnGuardar">
                                <i class="bi bi-save me-1"></i>Guardar
                            </button>
                            <button type="button" class="btn btn-secondary" id="BtnLimpiar">
                                <i class="bi bi-x-circle me-1"></i>Limpiar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Categorías -->
    <div class="row">
        <div class="col-md-8 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Listado de Categorías</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0" id="TablaCategorias">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($categorias)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-3 text-muted">No hay categorías registradas</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($categorias as $categoria): ?>
                                        <tr>
                                            <td><?php echo $categoria->cat_id; ?></td>
                                            <td><?php echo $categoria->cat_nombre; ?></td>
                                            <td class="text-center">
                                                <button class="btn btn-warning btn-sm" 
                                                        onclick="editarCategoria(<?php echo $categoria->cat_id; ?>, '<?php echo $categoria->cat_nombre; ?>')">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-danger btn-sm" 
                                                        onclick="eliminarCategoria(<?php echo $categoria->cat_id; ?>)">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo asset('src/js/categorias/index.js'); ?>"></script>