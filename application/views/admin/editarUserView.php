<div id="id_container1" class="container mt-4">
	<div class="row">
		<div class="col-12 text-center">
			<h3 class="display-4">Editar usuario</h3>
		</div>
	</div>
	<?php $infoUser = $data['infoUser'];//informacion del usuario?>
	<?php echo (isset($data['resultStatus']))?$data['resultStatus']:""; //status del post (con exito o sin exito)?>
	<div class="row">
		<div class="col-auto mx-auto mt-4">
            <div id="preview_1" class="preview">
            	<img id="img_user" class="img-fluid" alt="Responsive image" src="<?= base_url;?>public/media/users_img/<?= $infoUser->Id_Usuario."/".$infoUser->Path_Imagen_User;?>">
            </div>
			
		</div>
		<div class="col-12 mt-3">
			<div class="row">
				<div class="col-12 col-md-4 mx-auto">
					<div id="id_image" class="input-group">
						<div class="custom-file">
							<label id="label_foto_file" class="custom-file-label" for="id_foto_file" data-browse="Buscar">Subir imagen</label>
					    	<input type="file" form="id_form" class="custom-file-input" id="id_foto_file" name="foto_file">
					  	</div>
					</div>
					<small id="error_img1" class="form-text text-danger">Tamaño máximo 8MB, formatos: jpg/png</small>
				</div>
			</div>
				
		</div>
	</div>

	<div class="row mt-4 mx-auto">
		<form id="id_form" class="col-12" method="post" action="<?= base_url;?>UsersAdmin/editarUser" enctype="multipart/form-data" accept-charset="utf-8">
			<div class="row">
				<div class="col-12 text-center mt-3 mb-3">
					<h5>Información general</h5>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-12 col-md-4">
					<label for="Nombre">Nombre</label>
				    <input type="text" class="form-control" name="Nombre" id="Nombre" placeholder="Nombre" required value="<?php echo (isset($infoUser))?$infoUser->Nombre:"";?>">
				    <small class="form-text text-muted"></small>
				</div>
				<div class="form-group col-12 col-md-4">
					<label for="Ap_Paterno">Apellido paterno</label>
				    <input type="text" class="form-control" name="Ap_Paterno" id="Ap_Paterno" placeholder="Apellido Paterno" required value="<?php echo (isset($infoUser))?$infoUser->Ap_Paterno:"";?>">
				    <!--small class="form-text text-muted">Puedes cambiar el nombre del usuario</small-->
				</div>
				<div class="form-group col-12 col-md-4">
					<label for="Ap_Materno">Apellido materno</label>
				    <input type="text" class="form-control" name="Ap_Materno" id="Ap_Materno" placeholder="Apellido Materno" required value="<?php echo (isset($infoUser))?$infoUser->Ap_Materno:"";?>">
				    <!--small class="form-text text-muted">Puedes cambiar el nombre del usuario</small-->
				</div>
			</div>
			<div class="row">
				<div class="form-group col-12 col-md-4">
					<label for="Email">Email</label>
				    <input type="email" class="form-control" name="Email" id="Email" placeholder="example@gmail.com" required value="<?php echo (isset($infoUser))?$infoUser->Email:"";?>">
				    <small class="form-text text-muted"><?= (isset($data['errorForm']['Email']))?$data['errorForm']['Email']:"";?></small>
				</div>
				<div class="form-group col-12 col-md-4">
				    <label for="Area">Área</label>
				    <select class="form-control" id="Area" name="Area">
				      <option value="Tecnologías" <?php echo ($infoUser->Area == "Tecnologías")?"selected":"";?>>TECNOLOGÍAS</option>
				      <option value="Estadística" <?php echo ($infoUser->Area == "Estadística")?"selected":"";?>>ESTADÍSTICA</option>
				      <option value="Administración" <?php echo ($infoUser->Area == "Administración")?"selected":"";?>>ADMINISTRACIÓN</option>
				      <option value="Remisiones" <?php echo ($infoUser->Area == "Remisiones")?"selected":"";?>>REMISIONES</option>
				      <option value="Incidencias" <?php echo ($infoUser->Area == "Incidencias")?"selected":"";?>>INCIDENCIAS</option>
					  <option value="Inteligencia" <?php echo (isset($_POST['crearUser']) && $_POST['Area'] == "Inteligencia")?"selected":"";?>>INTELIGENCIA</option>
				      <option value="Eventos Delictivos" <?php echo ($infoUser->Area == "Eventos Delictivos")?"selected":"";?>>EVENTOS DELICTIVOS</option>
					  <option value="PERITOS" <?php echo (isset($_POST['crearUser']) && $_POST['Area'] == "PERITOS")?"selected":"";?>>PERITOS</option>
				      <option value="Otros" <?php echo ($infoUser->Area == "Otros")?"selected":"";?>>OTROS</option>
				    </select>
				</div>
				<div class="form-group col-12 col-md-4">
				    <label for="Estatus">Estatus</label>
				    <select class="form-control" id="Estatus" name="Estatus">
				      <option value="1" <?php echo ($infoUser->Estatus == "1")?"selected":"";?>>ACTIVO</option>
				      <option value="0" <?php echo ($infoUser->Estatus == "0")?"selected":"";?>>INACTIVO</option>
				    </select>
				</div>
			</div>
			<div class="row">
				<div class="col-12 text-center mt-3 mb-3">
					<h5>Información de la sesión</h5>
				</div>
			</div>
			<div class="row">
				<div class="form-group col-12 col-md-4 offset-md-2">
					<label for="User_Name">Nombre de Usuario</label>
				    <input type="text" class="form-control" name="User_Name" id="User_Name" placeholder="Ejemplo: JuanDan123" required value="<?php echo (isset($infoUser))?$infoUser->User_Name:"";?>">
				    <small class="form-text text-muted"><?= (isset($data['errorForm']['User_Name']))?$data['errorForm']['User_Name']:"";?></small>
				</div>
				<div class="form-group col-12 col-md-4">
					<label for="id_pass">Contraseña</label>
					<div id="id_pass" class="input-group">
		                <input id="id_input_pass" type="password" name="Password" class="form-control py-2 border-right-0 border" placeholder="Contraseña" required value="<?php echo (isset($infoUser))?$infoUser->Pass_Decrypt:"";?>" aria-describedby="button-addon2" >
		                <span class="input-group-append">
		                    <div id="id_pass_button" class="input-group-text bg-transparent"><i class="material-icons md-18 ssc view-password">visibility</i></div>
		                </span>
		            </div>
		            <!--small class="form-text text-muted">Contraseña de usueario</small-->
				</div>
				
			</div>
			<div class="row">
				<div class="col-12 text-center mt-3 mb-3">
					<h5>Permisos</h5>
				</div>
			</div>
			<div class="row d-flex justify-content-center mt-2 mb-3" >
				<div class="col-auto">
					<table class="table table-responsive">
					  <thead class="thead-myTable">
						    <tr>
							    <th >
							    	<div class="row d-flex justify-content-center">
							    		Jurídico
							    	</div>
							    	<div class="row d-flex justify-content-center">
							    		<input class="checkPermisos" type="checkbox" value="1" id="all_juridico">
							    	</div>
							    </th>
							    <th >
							    	<div class="row d-flex justify-content-center">
							    		Dictamen M
							    	</div>
							    	<div class="row d-flex justify-content-center">
							    		<input class="checkPermisos" type="checkbox" value="1" id="all_dictamen">
							    	</div>
							    </th>
							    <th >
							    	<div class="row d-flex justify-content-center">
							    		Remisiones
							    	</div>
							    	<div class="row d-flex justify-content-center">
							    		<input class="checkPermisos" type="checkbox" value="1" id="all_remisiones">
							    	</div>
							    </th>
							    <th >
							    	<div class="row d-flex justify-content-center">
							    		Inspecciones
							    	</div>
							    	<div class="row d-flex justify-content-center">
							    		<input class="checkPermisos" type="checkbox" value="1" id="all_inteligencia">
							    	</div>
							    </th>
							    <th >
							    	<div class="row d-flex justify-content-center">
							    		Inteligencia Op
							    	</div>
							    	<div class="row d-flex justify-content-center">
							    		<input class="checkPermisos" type="checkbox" value="1" id="all_inteligencia_op">
							    	</div>
							    </th>
							    <th >
							    	<div class="row d-flex justify-content-center">
							    		IPH Final
							    	</div>
							    	<div class="row d-flex justify-content-center">
							    		<input class="checkPermisos" type="checkbox" value="1" id="all_iph_final">
							    	</div>
							    </th>
							    <th >
							    	<div class="row d-flex justify-content-center">
							    		<!--Antes Corralón-->
							    		Seguimientos
							    	</div>
							    	<div class="row d-flex justify-content-center">
							    		<input class="checkPermisos" type="checkbox" value="1" id="all_corralon">
							    	</div>
							    </th>
							    <th >
							    	<div class="row d-flex justify-content-center">
							    		Eventos D
							    	</div>
							    	<div class="row d-flex justify-content-center">
							    		<input class="checkPermisos" type="checkbox" value="1" id="all_eventos_d">
							    	</div>
							    </th>
						    </tr>
					  </thead>
					  <tbody>
						  	<tr>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="Ju_Create" name="Ju_Create" <?= ($infoUser->Juridico[3]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="Ju_Create">Crear</label>
									</div>
						  		</td>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="Dic_Create" name="Dic_Create" <?= ($infoUser->Dictamen_M[3]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="Dic_Create">Crear</label>
									</div>
						  		</td>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="R_Create" name="R_Create" <?= ($infoUser->Remisiones[3]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="R_Create">Crear</label>
									</div>
						  		</td>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="Int_Create" name="Int_Create" <?= ($infoUser->Inspecciones[3]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="Int_Create">Crear</label>
									</div>
						  		</td>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="IntOp_Create" name="IntOp_Create" <?= ($infoUser->Inteligencia_Op[3]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="IntOp_Create">Crear</label>
									</div>
						  		</td>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="IPH_Create" name="IPH_Create" <?= ($infoUser->IPH_Final[3]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="IPH_Create">Crear</label>
									</div>
						  		</td>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="Corr_Create" name="Corr_Create" <?= ($infoUser->Seguimientos[3]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="Corr_Create">Crear</label>
									</div>
						  		</td>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="E_Create" name="E_Create" <?= ($infoUser->Evento_D[3]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="E_Create">Crear</label>
									</div>
						  		</td>
						  	</tr>
						  	<tr>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="Ju_Read" name="Ju_Read" <?= ($infoUser->Juridico[2]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="Ju_Read">Consultar</label>
									</div>
						  		</td>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="Dic_Read" name="Dic_Read" <?= ($infoUser->Dictamen_M[2]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="Dic_Read">Consultar</label>
									</div>
						  		</td>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="R_Read" name="R_Read" <?= ($infoUser->Remisiones[2]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="R_Read">Consultar</label>
									</div>
						  		</td>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="Int_Read" name="Int_Read" <?= ($infoUser->Inspecciones[2]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="Int_Read">Consultar</label>
									</div>
						  		</td>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="IntOp_Read" name="IntOp_Read" <?= ($infoUser->Inteligencia_Op[2]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="IntOp_Read">Consultar</label>
									</div>
						  		</td>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="IPH_Read" name="IPH_Read" <?= ($infoUser->IPH_Final[2]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="IPH_Read">Consultar</label>
									</div>
						  		</td>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="Corr_Read" name="Corr_Read" <?= ($infoUser->Seguimientos[2]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="Corr_Read">Consultar</label>
									</div>
						  		</td>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="E_Read" name="E_Read" <?= ($infoUser->Evento_D[2]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="E_Read">Consultar</label>
									</div>
						  		</td>
						  	</tr>
						  	<tr>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="Ju_Update" name="Ju_Update" <?= ($infoUser->Juridico[1]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="Ju_Update">Modificar</label>
									</div>
						  		</td>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="Dic_Update" name="Dic_Update" <?= ($infoUser->Dictamen_M[1]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="Dic_Update">Modificar</label>
									</div>
						  		</td>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="R_Update" name="R_Update" <?= ($infoUser->Remisiones[1]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="R_Update">Modificar</label>
									</div>
						  		</td>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="Int_Update" name="Int_Update" <?= ($infoUser->Inspecciones[1]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="Int_Update">Modificar</label>
									</div>
						  		</td>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="IntOp_Update" name="IntOp_Update" <?= ($infoUser->Inteligencia_Op[1]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="IntOp_Update">Modificar</label>
									</div>
						  		</td>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="IPH_Update" name="IPH_Update" <?= ($infoUser->IPH_Final[1]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="IPH_Update">Modificar</label>
									</div>
						  		</td>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="Corr_Update" name="Corr_Update" <?= ($infoUser->Seguimientos[1]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="Corr_Update">Modificar</label>
									</div>
						  		</td>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="E_Update" name="E_Update" <?= ($infoUser->Evento_D[1]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="E_Update">Modificar</label>
									</div>
						  		</td>
						  	</tr>
						  	<!--<tr>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="Ju_Delete" name="Ju_Delete" <?= ($infoUser->Juridico[0]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="Ju_Delete">Borrar</label>
									</div>
						  		</td>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="Dic_Delete" name="Dic_Delete" <?= ($infoUser->Dictamen_M[0]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="Dic_Delete">Borrar</label>
									</div>
						  		</td>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="R_Delete" name="R_Delete" <?= ($infoUser->Remisiones[0]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="R_Delete">Borrar</label>
									</div>
						  		</td>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="Int_Delete" name="Int_Delete" <?= ($infoUser->Inspecciones[0]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="Int_Delete">Borrar</label>
									</div>
						  		</td>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="IntOp_Delete" name="IntOp_Delete" <?= ($infoUser->Inteligencia_Op[0]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="IntOp_Delete">Borrar</label>
									</div>
						  		</td>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="IPH_Delete" name="IPH_Delete" <?= ($infoUser->IPH_Final[0]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="IPH_Delete">Borrar</label>
									</div>
						  		</td>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="Corr_Delete" name="Corr_Delete" <?= ($infoUser->Seguimientos[0]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="Corr_Delete">Borrar</label>
									</div>
						  		</td>
						  		<td>
						  			<div class="form-group form-check col-12">
									    <input type="checkbox" class="form-check-input checkPermisos" value="1" id="E_Delete" name="E_Delete" <?= ($infoUser->Evento_D[0]=='1')?"checked":"";?> >
									    <label class="form-check-label" for="E_Delete">Borrar</label>
									</div>
						  		</td>
						  	</tr>-->
						  	
					  </tbody>
					</table>
				</div>
			</div>
			<div class="row mt-2 mb-5" >
				<div class="col-12 form-group form-check text-center">
				    <input type="checkbox" class="form-check-input" value="1" id="Nivel_User" name="Nivel_User" <?= ($infoUser->Nivel_User == 1)?"checked":"";?>>
				    <label class="form-check-label" for="Nivel_User" >Permisos de validación (Remisiones)</label>
				</div>
				<div class="col-12 form-group form-check text-center">
				    <input type="checkbox" class="form-check-input" value="1" id="Modo_Admin" onclick="disablePermisos()" name="Modo_Admin" <?= ($infoUser->Modo_Admin)?"checked":"";?>>
				    <label class="form-check-label" for="Modo_Admin" >Modo Administrador</label>
				</div>
			</div>
			<input type="hidden" name="Id_Usuario" value="<?= $infoUser->Id_Usuario;?>" style="display: none; color: transparent;">
			<div class="row mt-4 mb-5">
				<div class="col-6 col-md-3 offset-md-3 d-flex justify-content-center">
					<a href="<?= base_url;?>UsersAdmin/index/" id="backButton" class="btn">
						<i class="material-icons v-a-middle">arrow_back_ios</i>
        				<span class="v-a-middle">Regresar</span>
					</a>
				</div>
				<div class="col-6 col-md-3 d-flex justify-content-center">
					<button type="submit" id="mySubmit" class="btn" name="editarUser">Guardar Cambios</button>
				</div>
			</div>
			
		</form>

	</div>
</div>
