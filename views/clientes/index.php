<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
                <div class="card-body">
                    <h3 class="text-center mb-4">Gestión de Clientes</h3>
                    
                    <div class="row">
                        <!-- Formulario -->
                        <div class="col-md-4">
                            <div class="card shadow-sm mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Nuevo Cliente</h5>
                                </div>
                                <div class="card-body">
                                    <form id="FormClientes">
                                        <input type="hidden" id="cliente_id" name="cliente_id">
                                        
                                        <div class="mb-3">
                                            <label for="cliente_nombre" class="form-label">Nombre del Cliente</label>
                                            <input type="text" class="form-control" id="cliente_nombre" name="cliente_nombre" 
                                                placeholder="Ej: Juan Pérez" required>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between">
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
                        
                        <!-- Tabla de Clientes -->
                        <div class="col-md-8">
                            <div class="card shadow-sm">
                                <div class="card-header bg-success text-white">
                                    <h5 class="mb-0"><i class="bi bi-people me-2"></i>Listado de Clientes</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0" id="TablaClientes">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>#</th>
                                                    <th>Nombre</th>
                                                    <th class="text-center">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php if (empty($clientes)): ?>
                                                    <tr>
                                                        <td colspan="3" class="text-center py-3 text-muted">No hay clientes registrados</td>
                                                    </tr>
                                                <?php else: ?>
                                                    <?php foreach ($clientes as $cliente): ?>
                                                        <tr>
                                                            <td><?php echo $cliente->cliente_id; ?></td>
                                                            <td><?php echo $cliente->cliente_nombre; ?></td>
                                                            <td class="text-center">
                                                                <button class="btn btn-warning btn-sm" 
                                                                        onclick="editarCliente(<?php echo $cliente->cliente_id; ?>, '<?php echo $cliente->cliente_nombre; ?>')">
                                                                    <i class="bi bi-pencil"></i>
                                                                </button>
                                                                <button class="btn btn-danger btn-sm" 
                                                                        onclick="eliminarCliente(<?php echo $cliente->cliente_id; ?>)">
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
            </div>
        </div>
    </div>
</div>

<script src="<?php echo asset('build/js/clientes/index.js'); ?>"></script>