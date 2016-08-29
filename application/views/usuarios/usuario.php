<style>
#contenido{
	margin: 0 auto;
	width:100%; 
	font-size:14px; 
	overflow: auto;
	}
#center{
	margin-top: 1em;
	margin-left: 25%;
	width: 50%;
	float: left;
	text-align: left;
}
</style>
<div id="center">
	<?php $userinfo = $datos->result()[0]; ?>
	<?php echo form_open_multipart('actualizarUsuario'); ?>
	<?php
		$usuario = array(
			'name' => 'usuario',
			'class' => 'form-control',
			'required' => 'required',
			'placeholder' => 'Nombre de usuario',
			'pattern' => '^[a-z]{4,20}$',
			'maxlength' => '20',
			'readonly' => 'readonly',
			'value' => $userinfo->usuario
			);
		$nombre = array(
			'name' => 'nombre',
			'class' => 'form-control',
			'required' => 'required',
			'placeholder' => 'Nombre(s)',
			'pattern' => '^[A-Za-zÑñáéíóúÁÉÍÓÚ\s]{5,100}$',
			'maxlength' => '100',
			'value' => $userinfo->nombre
			);

		$num_empleado = array(
			'name' => 'num_empleado',
			'class' => 'form-control',
			'required' => 'required',
			'placeholder' => 'Número de empleado',
			'pattern' => '^[0-9]{1,5}$',
			'value' => $userinfo->no_empleado,
			'size' => '4'
			);
			
		$puesto = array(
			'name' => 'puesto',
			'class' => 'form-control',
			'placeholder' => 'Puesto',
			'required' => 'required',
			/*'pattern' => '^[A-Za-zÑñáéíóúÁÉÍÓÚ\s]{5,50}$',
			'maxlength' => '50',
			'minlength' => '5',*/
			'id' => 'puesto'
			);
		$permiso = array(
			'name' => 'permiso',
			'class' => 'form-control',
			'placeholder' => 'Tipo de permiso',
			'required' => 'required',
			'id' => 'permiso',
		    );
		$direccion_correo  = array(
			'name' => 'direccion_correo',
			'class' => 'form-control',
			'placeholder' => 'Direcci&oacute;n de Correo',
			'required' => 'required',
			'id' => 'direccion_correo',
			'pattern' => '^[_a-z0-9-.]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$',
			'value' => $userinfo->correo,
		    );
			
		$correo = array(
			'name' => 'correo',
			'class' => 'form-control',
			'placeholder' => 'Enviar notificaciones',
			'required' => 'required',
			'id' => 'correo',
		    );
		$correos = array(
			1 => 'Si',
			0 => 'No'
		)
	?>
	<span style="text-align:center;"><h3>Informaci&oacute;n del usuario <?php echo $userinfo->usuario; ?>:</h3></span>
	<div class="row" style="width:100%">
		<div class="col-xs-4" style="width:100%">
			<div class="form-group">
				<label for="usuario">Usuario:</label>
				<?php echo form_input($usuario); ?>
			</div>
		</div>
	</div>
	<div class="row" style="width:100%">
		<div class="form-group">
			<div class="col-xs-4" style="width:50%">
				<label for="nombre">Nombre:</label>
				<?php echo form_input($nombre); ?>
			</div>
			<div class="col-xs-4" style="width:25%">
				<label for="num_empleado">No. Empleado:</label>
				<?php echo form_input($num_empleado); ?>
			</div>
		</div>
	</div>
	<div class="row" style="width:100%">
		<div class="col-xs-4" style="width:50%">
			<div class="form-group">
				<label for="puesto">Puesto:</label>
				<?php echo form_dropdown($puesto, $puestos, $userinfo->id_puesto); ?>
			</div>
		</div>
		<div class="col-xs-4" style="width:50%">
			<div class="form-group">
				<label for="direccon_correo">Correo Electr&oacute;nico:</label>
				<?php echo form_input($direccion_correo); ?>
			</div>
		</div>
	</div>
	<div class="row" style="width:100%">
		<div class="col-xs-4" style="width:30%">
			<div class="form-group">
				<label for="permiso">Permiso:</label>
				<?php echo form_dropdown($permiso, $permisos, $userinfo->permiso); ?>
			</div>
		</div>
		<div class="col-xs-4" style="width:20%">
			<div class="form-group">
				<label for="correo">Notificaciones:</label>
				<?php echo form_dropdown($correo, $correos); ?>
			</div>
		</div>
	</div>
	<div>
	<?php echo form_submit("", "Guardar Cambios", "class='btn btn-success'"); ?>
	<?php echo form_close(); ?>
</div><!-- div center