<div id="id_container1" class="container mt-1">
	<?php $infoUser = $data['infoUser'];//informacion del usuario?>
	<div class="row mt-4">
		
		<div class="col-12">
			<div class="card row mb-3" id="" aria-describedby="id_card">
				<div class="card-header text-center">
				    <h3 class="display-4">Ver usuario</h3>
				</div>
				<div class="d-flex justify-content-center my-4">
					<img id="img_user" class="" alt="" src="<?= base_url;?>public/media/users_img/<?= $infoUser->Id_Usuario."/".$infoUser->Path_Imagen_User;?>">
				</div>
			  
				<div class="card-body col-12">
					<div class="row mx-2">
						<div class="col-12 col-md-6" id="id_info_cuenta">
							<div class="row">
								<div class="col-12 text-center" id="id_title_info">
									<h5 class="card-title">Información de la cuenta</h5>
								</div>
								<div class="col-12 mt-4 text-justify table-responsive">
									<!--espacios orden:  5 15 8 7 11 14-->
									<table class="table table-sm">
										<tbody>
											<tr>
												<td>
													<i class="material-icons">person</i>
												</td>
												<td>
													<span>Nombre completo:  </span>
												</td>
												<td>
													<strong><?= mb_strtoupper($infoUser->Nombre." ".$infoUser->Ap_Paterno." ".$infoUser->Ap_Materno)?>	</strong>
												</td>
											</tr>
											<tr>
												<td>
													<i class="material-icons">email</i>
												</td>
												<td>
													<span>Email:</span>
												</td>
												<td>
													<strong><?= mb_strtoupper($infoUser->Email)?></strong>
												</td>
											</tr>
											<tr>
												<td>
													<i class="material-icons">domain</i>
												</td>
												<td>
													<span>Área laboral:</span>
												</td>
												<td>
													<strong><?= mb_strtoupper($infoUser->Area)?></strong>
												</td>
											</tr>
											<tr>
												<td>
													<i class="material-icons">assignment_ind</i>
												</td>
												<td>
													<span>Nombre Usuario:</span>
												</td>
												<td>
													<strong><?= $infoUser->User_Name?></strong>
												</td>
											</tr>
											<tr>
												<td>
													<i class="material-icons">lock</i>
												</td>
												<td>
													<span>Contraseña:</span>
												</td>
												<td>
													<strong><?= $infoUser->Pass_Decrypt?></strong>
												</td>
											</tr>
											<tr>
												<td>
													<i class="material-icons">verified_user</i>
												</td>
												<td>
													<span>Estatus:</span>
												</td>
												<td>
													<strong><?= mb_strtoupper(($infoUser->Estatus)?"Activo":"Inactivo")?></strong>
												</td>
											</tr>
										</tbody>
									</table>
									
								</div>
							</div>
						</div>
						<div class="col-12 col-md-6" id="id_permisos_cuenta">
							<div class="row">
								<div class="col-12 text-center">
									<h5 class="card-title">Permisos del usuario</h5>
								</div>
								<div class="col-auto mx-auto mt-4">
									<table class="table table-responsive">
								        <thead>
								            <tr class="align-middle text-center">
								            	<th> </th>
								                <th>CREAR</th>
								                <th>VER</th>
								                <th>MODIFICAR</th>
								                <!--<th>Borrar</th>-->
								            </tr>
								        </thead>
								        <tbody class="text-center">
								            <tr>
								            	<td>JURÍDICO</td>
								            	<td><i class="material-icons <?= ($infoUser->Juridico[3])?"check_icon":"close_icon";?>"><?= ($infoUser->Juridico[3])?"check":"close";?></i></td>
								            	<td><i class="material-icons <?= ($infoUser->Juridico[2])?"check_icon":"close_icon";?>"><?= ($infoUser->Juridico[2])?"check":"close";?></i></td>
								            	<td><i class="material-icons <?= ($infoUser->Juridico[1])?"check_icon":"close_icon";?>"><?= ($infoUser->Juridico[1])?"check":"close";?></i></td>
								            	<!--<td><i class="material-icons <?= ($infoUser->Juridico[0])?"check_icon":"close_icon";?>"><?= ($infoUser->Juridico[0])?"check":"close";?></i></td>-->
								        	</tr>
								        	<tr>
								            	<td>DICTAMEN M</td>
								            	<td><i class="material-icons <?= ($infoUser->Dictamen_M[3])?"check_icon":"close_icon";?>"><?= ($infoUser->Dictamen_M[3])?"check":"close";?></i></td>
								            	<td><i class="material-icons <?= ($infoUser->Dictamen_M[2])?"check_icon":"close_icon";?>"><?= ($infoUser->Dictamen_M[2])?"check":"close";?></i></td>
								            	<td><i class="material-icons <?= ($infoUser->Dictamen_M[1])?"check_icon":"close_icon";?>"><?= ($infoUser->Dictamen_M[1])?"check":"close";?></i></td>
								            	<!--<td><i class="material-icons <?= ($infoUser->Dictamen_M[0])?"check_icon":"close_icon";?>"><?= ($infoUser->Dictamen_M[0])?"check":"close";?></i></td>-->
								        	</tr>
								        	<tr>
								            	<td>REMISIONES</td>
								            	<td><i class="material-icons <?= ($infoUser->Remisiones[3])?"check_icon":"close_icon";?>"><?= ($infoUser->Remisiones[3])?"check":"close";?></i></td>
								            	<td><i class="material-icons <?= ($infoUser->Remisiones[2])?"check_icon":"close_icon";?>"><?= ($infoUser->Remisiones[2])?"check":"close";?></i></td>
								            	<td><i class="material-icons <?= ($infoUser->Remisiones[1])?"check_icon":"close_icon";?>"><?= ($infoUser->Remisiones[1])?"check":"close";?></i></td>
								            	<!--<td><i class="material-icons <?= ($infoUser->Remisiones[0])?"check_icon":"close_icon";?>"><?= ($infoUser->Remisiones[0])?"check":"close";?></i></td>-->
								        	</tr>
								        	<tr>
								            	<td>INSPECCIONES</td>
								            	<td><i class="material-icons <?= ($infoUser->Inspecciones[3])?"check_icon":"close_icon";?>"><?= ($infoUser->Inspecciones[3])?"check":"close";?></i></td>
								            	<td><i class="material-icons <?= ($infoUser->Inspecciones[2])?"check_icon":"close_icon";?>"><?= ($infoUser->Inspecciones[2])?"check":"close";?></i></td>
								            	<td><i class="material-icons <?= ($infoUser->Inspecciones[1])?"check_icon":"close_icon";?>"><?= ($infoUser->Inspecciones[1])?"check":"close";?></i></td>
								            	<!--<td><i class="material-icons <?= ($infoUser->Inspecciones[0])?"check_icon":"close_icon";?>"><?= ($infoUser->Inspecciones[0])?"check":"close";?></i></td>-->
								        	</tr>
								        	<tr>
								            	<td>INTELIGENCIA OP</td>
								            	<td><i class="material-icons <?= ($infoUser->Inteligencia_Op[3])?"check_icon":"close_icon";?>"><?= ($infoUser->Inteligencia_Op[3])?"check":"close";?></i></td>
								            	<td><i class="material-icons <?= ($infoUser->Inteligencia_Op[2])?"check_icon":"close_icon";?>"><?= ($infoUser->Inteligencia_Op[2])?"check":"close";?></i></td>
								            	<td><i class="material-icons <?= ($infoUser->Inteligencia_Op[1])?"check_icon":"close_icon";?>"><?= ($infoUser->Inteligencia_Op[1])?"check":"close";?></i></td>
								            	<!--<td><i class="material-icons <?= ($infoUser->Inteligencia_Op[0])?"check_icon":"close_icon";?>"><?= ($infoUser->Inteligencia_Op[0])?"check":"close";?></i></td>-->
								        	</tr>
								        	<tr>
								            	<td>IPH FINAL</td>
								            	<td><i class="material-icons <?= ($infoUser->IPH_Final[3])?"check_icon":"close_icon";?>"><?= ($infoUser->IPH_Final[3])?"check":"close";?></i></td>
								            	<td><i class="material-icons <?= ($infoUser->IPH_Final[2])?"check_icon":"close_icon";?>"><?= ($infoUser->IPH_Final[2])?"check":"close";?></i></td>
								            	<td><i class="material-icons <?= ($infoUser->IPH_Final[1])?"check_icon":"close_icon";?>"><?= ($infoUser->IPH_Final[1])?"check":"close";?></i></td>
								            	<!--<td><i class="material-icons <?= ($infoUser->IPH_Final[0])?"check_icon":"close_icon";?>"><?= ($infoUser->IPH_Final[0])?"check":"close";?></i></td>-->
								        	</tr>
								        	<tr>
								        		<!--Antes Corralón-->
								            	<td>SEGUIMIENTOS</td>
								            	<td><i class="material-icons <?= ($infoUser->Seguimientos[3])?"check_icon":"close_icon";?>"><?= ($infoUser->Seguimientos[3])?"check":"close";?></i></td>
								            	<td><i class="material-icons <?= ($infoUser->Seguimientos[2])?"check_icon":"close_icon";?>"><?= ($infoUser->Seguimientos[2])?"check":"close";?></i></td>
								            	<td><i class="material-icons <?= ($infoUser->Seguimientos[1])?"check_icon":"close_icon";?>"><?= ($infoUser->Seguimientos[1])?"check":"close";?></i></td>
								            	<!--<td><i class="material-icons <?= ($infoUser->Seguimientos[0])?"check_icon":"close_icon";?>"><?= ($infoUser->Seguimientos[0])?"check":"close";?></i></td>-->
								        	</tr>
								        	<tr>
								            	<td>EVENTO D</td>
								            	<td><i class="material-icons <?= ($infoUser->Evento_D[3])?"check_icon":"close_icon";?>"><?= ($infoUser->Evento_D[3])?"check":"close";?></i></td>
								            	<td><i class="material-icons <?= ($infoUser->Evento_D[2])?"check_icon":"close_icon";?>"><?= ($infoUser->Evento_D[2])?"check":"close";?></i></td>
								            	<td><i class="material-icons <?= ($infoUser->Evento_D[1])?"check_icon":"close_icon";?>"><?= ($infoUser->Evento_D[1])?"check":"close";?></i></td>
								            	<!--<td><i class="material-icons <?= ($infoUser->Evento_D[0])?"check_icon":"close_icon";?>"><?= ($infoUser->Evento_D[0])?"check":"close";?></i></td>-->
								        	</tr>
								        </tbody>
								    </table>
								</div>
								<div class="col-12 text-center mt-4">
									<h6 id="id_modo_admin">Permisos de validación (Remisiones): <?= mb_strtoupper(($infoUser->Nivel_User == 1)?"Activado":"Desactivado")?></h6>
								</div>
								<div class="col-12 text-center mt-3">
									<h6 id="id_modo_admin">Modo Administrador: <?= mb_strtoupper(($infoUser->Modo_Admin)?"Activado":"Desactivado")?></h6>
								</div>
							</div>
							
						</div>
						<div class="col-12 mt-5">
							<div class="d-flex justify-content-center">
								<a id="backButton" href="<?= base_url;?>UsersAdmin/index/" class="btn">
									<i class="material-icons v-a-middle" >arrow_back_ios</i>
                					<span class="v-a-middle">Regresar</span>
        
								</a>
							</div>
						</div>
					</div>
				    
				</div>
				<div class="card-footer text-center">
				    <div class="row">
				    	<div class="col-12 text-center">
				    		<h6><?= "Fecha registro: ".$infoUser->Fecha_Format;?></h6>
				    	</div>
				    </div>
				</div>
			</div>
			<div class="row">
				<div class="col-12 text-justify">
					H. Ayuntamiento de Puebla 2018-2021 | Blvd. San Felipe No. 2821 Col. Rancho Colorado | Tel. (222) 303-85-00 Ext 77102
			Secretaría de Seguridad Ciudadana del Municipio de Puebla. <br><br><br>
				</div>
			</div>
		</div>
	</div>

</div>