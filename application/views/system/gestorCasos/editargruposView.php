<div class="container mt-20">
    <form id='grupo_delictivo'>
    <?php
       $no_grupo    = (isset($_GET['no_grupo'])) ? $_GET['no_grupo'] : '0';
        ?>
    <input type="hidden" name="no_grupo" id="no_grupo" value=<?= $no_grupo ?>>
        <div class="form-row mt-5">
            <div class="col-12" id="msg_principales"></div>
            <div class="form-group col-lg-6">
                <label for="principal_actividad" class="label-form">Principal actividad delictiva:</label>
                <input type="text" class="form-control form-control-sm " id="principal_actividad" name="principal_actividad" >
                <span class="span_error" id="principal_actividad_error"></span>
            </div>

            <div class="form-group col-lg-6">
                <label for="nombre_grupo" class="label-form">Nombre del grupo:</label>
                <input type="text" class="form-control form-control-sm " id="nombre_grupo" name="nombre_grupo" >
                <span class="span_error" id="nombre_grupo_error"></span>
            </div>

            <div class="form-group col-lg-6">
                <label for="delitos_asociados" class="label-form">Delitos asociados:</label>
                <textarea rows="10" class="form-control form-control-sm " id="delitos_asociados" name="delitos_asociados" ></textarea>
                <span class="span_error" id="delitos_asociados_error"></span>
            </div>
            <div class="form-group col-lg-6">
                <label for="antecedentes" class="label-form">Antecedentes:</label>
                <textarea rows="10" class="form-control form-control-sm" id="antecedentes" name="antecedentes" ></textarea>
            </div>
            <div class="form-group col-lg-6">
                <label for="peligrosidad" class="label-form">Peligrosidad:</label>
                <select class="custom-select custom-select-sm" id="peligrosidad" name="peligrosidad">
                    <option value="Baja" selected>Baja</option>
                    <option value="Media">Media</option>
                    <option value="Alta">Alta</option>
                </select>
                <span class="span_error" id="peligrosidad_error"></span>
            </div>
        </div>
       

        <div class="form-row row">
            <div class="form-group col-lg-12">
                <div class="alert alert-warning" role="alert" id="alertEditZona" style="display: none">
                    Está realizando edición a un elemento.
                </div>
            </div>
            <div class="form-group col-lg-6">
                <label for="zona_int" class="label-form">Zona:</label>
                <select class="custom-select custom-select-sm" id="zona_int" name="zona_int">
                    <option value="Zona 1">Zona 1</option>
                    <option value="Zona 2">Zona 2</option>
                    <option value="Zona 3">Zona 3</option>
                    <option value="Zona 4">Zona 4</option>
                    <option value="Zona 5">Zona 5</option>
                    <option value="Zona 6">Zona 6</option>
                    <option value="Zona 7">Zona 7</option>
                    <option value="Zona 8">Zona 8</option>
                    <option value="Zona 9">Zona 9</option>
                    <option value="Zona CH">Zona CH</option>
                </select>
                <span class="span_error" id="zona_int_error"></span>
            </div>
            <div class="form-group col-lg-6">
                <label for="colonias_int" class="label-form">Colonias:</label>
                <input type="text" class="form-control form-control-sm " id="colonias_int" name="colonias_int" >
                <span class="span_error" id="colonias_int_error"></span>
            </div>
            <button type="button" class="btn btn-primary button-movil-plus" id="button_query" onclick="onFormZonaSubmit()">+</button>
            <div class="form-group col-lg-12">
                <p class="label-form ml-2"> Zonas de operacion: </p>
                <div class="table-responsive">
                    <table class="table table-bordered" id="zonas_table">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">Zonas</th>
                                <th scope="col">Colonias</th>
                                <th scope="col"></th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="form-row mt-5">
            <div class="form-group col-lg-12">
                <div class="alert alert-warning" role="alert" id="alertEditIntegrante" style="display: none">
                    Está realizando edición a un elemento.
                </div>
            </div>
            <div class="form-group col-lg-3">
                <label for="nombre_int" class="label-form">Nombre:</label>
                <input type="text" class="form-control form-control-sm " id="nombre_int" name="nombre_int" >
                <span class="span_error" id="nombre_int_error"></span>
            </div>
            <div class="form-group col-lg-3">
                <label for="apem_int" class="label-form">Apellido paterno:</label>
                <input type="text" class="form-control form-control-sm " id="apem_int" name="apem_int" >
                <span class="span_error" id="apem_int_error"></span>
            </div>
            <div class="form-group col-lg-3">
                <label for="apep_int" class="label-form">Apellido materno:</label>
                <input type="text" class="form-control form-control-sm " id="apep_int" name="apep_int" >
                <span class="span_error" id="apep_int_error"></span>
            </div>
            <div class="form-group col-lg-3">
                <label for="sexo_int" class="label-form">Sexo:</label>
                <select class="custom-select custom-select-sm" id="sexo_int" name="sexo_int">
                    <option value="HOMBRE">Hombre</option>
                    <option value="MUJER">Mujer</option>
                </select>
            </div>
            <div class="form-group col-lg-3">
                <label for="estado_int" class="label-form">Estado:</label>
                <select class="custom-select custom-select-sm" id="estado_int" name="estado_int">
                    <option value="ACTIVO">Activo</option>
                    <option value="INACTIVO">Inactivo</option>
                </select>
                
            </div>
            <div class="form-group col-lg-3">
                <label for="alias_int" class="label-form">Alias:</label>
                <input type="text" class="form-control form-control-sm " id="alias_int" name="alias_int" >
                <span class="span_error" id="alias_int_error"></span>
            </div>
            <div class="form-group col-lg-3">
                <label for="curp_int" class="label-form">CURP:</label>
                <input type="text" class="form-control form-control-sm text-uppercase" id="curp_int" name="curp_int" >
                <span class="span_error" id="curp_int_error"></span>
            </div>
            <div class="form-group col-lg-3">
                <label for="udc_int" class="label-form">Ultimo domicilio conocido:</label>
                <input type="text" class="form-control form-control-sm " id="udc_int" name="udc_int" >
                <span class="span_error" id="udc_int_error"></span>
            </div>
            <div class="form-group col-lg-3">
                <label for="utc_int" class="label-form">Ultimo telefono colocido:</label>
                <input type="int" class="form-control form-control-sm " id="utc_int" name="utc_int" >
                <span class="span_error" id="utc_int_error"></span>
            </div>
            <div class="form-group col-lg-3">
                <label for="face_int" class="label-form">Facebook :</label>
                <input type="text" class="form-control form-control-sm " id="face_int" name="face_int" >
                <span class="span_error" id="face_int_error"></span>
            </div>
        </div>
        <div class="form-row mt-5">
            <div class="form-group col-lg-6">
                <label for="asociado_int" class="label-form">Descripción::</label>
                <textarea rows="10" class="form-control form-control-sm " id="asociado_int" name="asociado_int" ></textarea>
                <span class="span_error" id="asociado_int_error"></span>
            </div>
            <div class="form-group col-lg-6">
                <label for="antece_int" class="label-form">Antecedentes por :</label>
                <textarea rows="10" class="form-control form-control-sm " id="antece_int" name="antece_int" ></textarea>
                <span class="span_error" id="antece_int_error"></span>
            </div>
            <button type="button" class="btn btn-primary button-movil-plus" id="button_query2" onclick="onFormIntegranteSubmit()">+</button>
        </div>
        <div class="form-row row"> 
            <div class="form-group col-lg-12">
                <label class="label-form">Integrantes:</label>
                <div class="table-responsive">
                    <table class="table table-bordered" id="integrantes_banda">
                        <thead class="thead-dark">
                            <tr>
                                <th scope="col">Nombre</th>
                                <th scope="col">Apellido paterno</th>
                                <th scope="col">Apellido materno</th>
                                <th scope="col">Genero</th>
                                <th scope="col">Estado</th>
                                <th scope="col">Alias</th>
                                <th scope="col">CURP</th>
                                <th scope="col">Udc</th>
                                <th scope="col">Utc</th>
                                <th scope="col">Facebook</th>
                                <th scope="col">Descripción </th>
                                <th scope="col">Antecedentes por</th>
                                <th scope="col">Fotografía</th>
                                <th scope="col"></th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        
        <div class="row mt-5 mb-5">
            <div class="form-group col-lg-12">
                <button type="button" class="btn btn-primary button-movil-plus" onclick="crear_guardar(event)" id="button_grupos_editar">GUARDAR</button>
            </div>
        </div>
    </form>

</div>