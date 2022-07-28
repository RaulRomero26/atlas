<div class="container mt-20">
    <form id='grupo_delictivo'>
    <input type="hidden" name="no_grupo" id="no_grupo" value="0">
        <div class="form-row mt-5">
            <div class="col-12" id="msg_principales"></div>
            <div class="form-group col-lg-6">
                <label for="principal_actividad" class="label-form">Principal actividad delictiva:</label>
                <input type="text" class="form-control form-control-sm text-uppercase" id="principal_actividad" name="principal_actividad" >
                <span class="span_error" id="principal_actividad_error"></span>
            </div>

            <div class="form-group col-lg-6">
                <label for="nombre_grupo" class="label-form">Nombre del grupo:</label>
                <input type="text" class="form-control form-control-sm text-uppercase" id="nombre_grupo" name="nombre_grupo" >
                <span class="span_error" id="nombre_grupo_error"></span>
            </div>

            <div class="form-group col-lg-6">
                <label for="delitos_asociados" class="label-form">Delitos asociados:</label>
                <textarea rows="6" class="form-control form-control-sm text-uppercase" id="delitos_asociados" name="delitos_asociados" ></textarea>
                <span class="span_error" id="delitos_asociados_error"></span>
            </div>

            <div class="form-group col-lg-6">
                <label for="modus_operandi" class="label-form">Modus operandi:</label>
                <textarea rows="6" type="number" class="form-control form-control-sm" id="modus_operandi" name="modus_operandi" ></textarea>
                <span class="span_error" id="modus_operandi_error"></span>
            </div>
            <div class="form-group col-lg-6">
                <label for="peligrosidad" class="label-form">Peligrosidad:</label>
                <select class="custom-select custom-select-sm" id="peligrosidad" name="peligrosidad">
                    <option value="Baja">Baja</option>
                    <option value="Media">Media</option>
                    <option value="Alta">Alta</option>
                </select>
                <span class="span_error" id="peligrosidad_error"></span>
            </div>
            <div class="form-group col-lg-4">
                <div class="row">
                    <label class="label-form">Eventos:</label>
                </div>
                <div class="row">
                    <div class="form-group col-lg-4">
                        <label for="ev_asociados" class="label-form">Asociados:</label>
                        <input type="number" class="form-control form-control-sm text-uppercase" id="ev_asociados" name="ev_asociados" >
                    </div>
                    <div class="form-group col-lg-4">
                        <label for="ev_confirmados" class="label-form">Confirmados:</label>
                        <input type="number" class="form-control form-control-sm text-uppercase" id="ev_confirmados" name="ev_confirmados" >
                    </div>
                    <div class="form-group col-lg-4">
                        <label for="ev_cdi" class="label-form">Con CDI:</label>
                        <input type="number" class="form-control form-control-sm text-uppercase" id="ev_cdi" name="ev_cdi" >
                    </div>
                    <span class="span_error" id="eventos_error"></span>
                </div>
                
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
                <input type="text" class="form-control form-control-sm text-uppercase" id="zona_int" name="zona_int" >
                <span class="span_error" id="zona_int_error"></span>
            </div>
            <div class="form-group col-lg-6">
                <label for="colonias_int" class="label-form">Colonias:</label>
                <input type="text" class="form-control form-control-sm text-uppercase" id="colonias_int" name="colonias_int" >
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
        <div class="form-row row">
            <div class="form-group col-lg-6">
                <label for="antecedentes" class="label-form">Antecedentes:</label>
                <textarea type="number" class="form-control form-control-sm" id="antecedentes" name="antecedentes" ></textarea>
            </div>
            <div class="form-group col-lg-6">
                <label for="origen" class="label-form" id="label_procedencia">Origen:</label>
                <input type="text" class="form-control form-control-sm text-uppercase" id="origen" name="origen">
                <span class="span_error" id="origen_error"></span>
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
                <input type="text" class="form-control form-control-sm text-uppercase" id="nombre_int" name="nombre_int" >
                <span class="span_error" id="nombre_int_error"></span>
            </div>
            <div class="form-group col-lg-3">
                <label for="apem_int" class="label-form">Apellido paterno:</label>
                <input type="text" class="form-control form-control-sm text-uppercase" id="apem_int" name="apem_int" >
                <span class="span_error" id="apem_int_error"></span>
            </div>
            <div class="form-group col-lg-3">
                <label for="apep_int" class="label-form">Apellido materno:</label>
                <input type="text" class="form-control form-control-sm text-uppercase" id="apep_int" name="apep_int" >
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
                <input type="text" class="form-control form-control-sm text-uppercase" id="alias_int" name="alias_int" >
                <span class="span_error" id="alias_int_error"></span>
            </div>
            <div class="form-group col-lg-3">
                <label for="curp_int" class="label-form">CURP:</label>
                <input type="text" class="form-control form-control-sm text-uppercase" id="curp_int" name="curp_int" >
                <span class="span_error" id="curp_int_error"></span>
            </div>
            <div class="form-group col-lg-3">
                <label for="udc_int" class="label-form">Ultimo domicilio conocido:</label>
                <input type="text" class="form-control form-control-sm text-uppercase" id="udc_int" name="udc_int" >
                <span class="span_error" id="udc_int_error"></span>
            </div>
            <div class="form-group col-lg-3">
                <label for="utc_int" class="label-form">Ultimo telefono colocido:</label>
                <input type="int" class="form-control form-control-sm text-uppercase" id="utc_int" name="utc_int" >
                <span class="span_error" id="utc_int_error"></span>
            </div>
            <div class="form-group col-lg-3">
                <label for="face_int" class="label-form">Facebook :</label>
                <input type="text" class="form-control form-control-sm text-uppercase" id="face_int" name="face_int" >
                <span class="span_error" id="face_int_error"></span>
            </div>
            <div class="form-group col-lg-3">
                <label for="otros_dom_int" class="label-form">Otros domicilios:</label>
                <input type="text" class="form-control form-control-sm text-uppercase" id="otros_dom_int" name="otros_dom_int" >
                <span class="span_error" id="otros_dom_int_error"></span>
            </div>
            
            <div class="form-group col-lg-3">
                <label for="vehi_int" class="label-form">Vehiculos :</label>
                <input type="text" class="form-control form-control-sm text-uppercase" id="vehi_int" name="vehi_int" >
                <span class="span_error" id="vehi_int_error"></span>
            </div>
            <div class="form-group col-lg-3">
                <label for="asociado_int" class="label-form">Asociado a :</label>
                <input type="text" class="form-control form-control-sm text-uppercase" id="asociado_int" name="asociado_int" >
                <span class="span_error" id="asociado_int_error"></span>
            </div>
            <div class="form-group col-lg-3">
                <label for="antece_int" class="label-form">Antecedentes por :</label>
                <input type="text" class="form-control form-control-sm text-uppercase" id="antece_int" name="antece_int" >
                <span class="span_error" id="antece_int_error"></span>
            </div>
            <button type="button" class="btn btn-primary button-movil-plus" id="button_query" onclick="onFormIntegranteSubmit()">+</button>
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
                                <th scope="col">Otros domicilios </th>
                                <th scope="col">Vehiculos</th>
                                <th scope="col">Asociado de vehiculos a </th>
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
                <button type="button" class="btn btn-primary button-movil-plus" onclick="crear_guardar(event)" id="button_grupos">GUARDAR</button>
            </div>
        </div>
    </form>

</div>