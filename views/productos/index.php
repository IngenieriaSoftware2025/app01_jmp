<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <div class="row mb-3">
                    <h5 class="text-center mb-2">¡Bienvenido a la Aplicación para organizar las compras del hogar!</h5>
                    <h4 class="text-center mb-2 text-primary">MANIPULACION DE PRODUCTOS</h4>
                </div>

                <div class="row justify-content-center p-5 shadow-lg">

                    <form id="FormProductos">
                        <input type="hidden" id="prod_id" name="prod_id">

                        <div class="row mb-3 justify-content-center">
                            <!-- NOMBRE -->
                            <div class="col-lg-6">
                                <label for="prod_nombre" class="form-label">INGRESE NOMBRE DEL PRODUCTO</label>
                                <input type="text" class="form-control" id="prod_nombre" name="prod_nombre" placeholder="ingrese aca el nombre del producto">
                            </div>
                            <!-- CANTIDAD -->
                            <div class="col-lg-6">
                                <label for="prod_cantidad" class="form-label">INGRESE LA CANTIDAD DEL PRODUCTO</label>
                                <input type="number" class="form-control" id="prod_cantidad" name="prod_cantidad" placeholder="Ingrese aca la cantidad del producto">
                            </div>
                            <!-- CATEGORIA -->
                            <div class="col-lg-6">
                                <label for="cat_id" class="form-label">INGRESE LA CANTEGORIA DEL PRODUCTO</label>
                                <input type="number" class="form-control" id="cat_id" name="cat_id" placeholder="Ingrese aca la categoria del producto">
                            </div>
                            <!-- PRIORIDAD -->
                            <div class="col-lg-6">
                                <label for="pri_id" class="form-label">INGRESE LA PRIORIDAD DEL PRODUCTO</label>
                                <input type="number" class="form-control" id="pri_id" name="pri_id" placeholder="Ingrese aca la prioridad del producto">
                            </div>
                            <!-- COMPRADO -->
                            <div class="col-lg-6">
                                <label for="comprado" class="form-label">COMPRADO</label>
                                <input type="number" class="form-control" id="comprado" name="comprado" placeholder="Ingrese aca sus apellidos">
                            </div>
                        </div>


                        <div class="row justify-content-center mt-5">
                            <div class="col-auto">
                                <button class="btn btn-success" type="submit" id="BtnGuardar">
                                    Guardar
                                </button>
                            </div>

                            <div class="col-auto ">
                                <button class="btn btn-warning d-none" type="button" id="BtnModificar">
                                    Modificar
                                </button>
                            </div>

                            <div class="col-auto">
                                <button class="btn btn-secondary" type="reset" id="BtnLimpiar">
                                    Limpiar
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row justify-content-center p-3">
    <div class="col-lg-10">
        <div class="card custom-card shadow-lg" style="border-radius: 10px; border: 1px solid #007bff;">
            <div class="card-body p-3">
                <h3 class="text-center">USUARIOS REGISTRADOS PRODUCTOS ANOTADOS EN EL CUADERNO DIGITAL</h3>

                <div class="table-responsive p-2">
                    <table class="table table-striped table-hover table-bordered w-100 table-sm" id="TableProductos">
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
</div>
<script src="<?= asset('build/js/productos/index.js') ?>"></script>