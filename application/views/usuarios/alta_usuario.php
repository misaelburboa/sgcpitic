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
	<?php echo form_open_multipart('altaUsuario'); ?>
	<?php
		$usuario = array(
			'name' => 'usuario',
			'class' => 'form-control',
			'required' => 'required',
			'placeholder' => 'Nombre de usuario',
			'pattern' => '^[a-z]{4,20}$',
			'maxlength' => '20'
			);
		$nombre = array(
			'name' => 'nombre',
			'class' => 'form-control',
			'required' => 'required',
			'placeholder' => 'Nombre(s)',
			'pattern' => '^[A-Za-zÑñáéíóúÁÉÍÓÚ\s]{5,50}$',
			'maxlength' => '30'
			);
		$apellido = array(
			'name' => 'apellidos',
			'class' => 'form-control',
			'required' => 'required',
			'placeholder' => 'Apellidos',
			'pattern' => '^[A-Za-zÑñáéíóúÁÉÍÓÚ\s]{5,50}$',
			'maxlength' => '30'
			);
		$num_empleado = array(
			'name' => 'num_empleado',
			'class' => 'form-control',
			'required' => 'required',
			'placeholder' => 'Número de empleado',
			'pattern' => '^[0-9]{1,5}$',
			'value' => '',
			'size' => '4'
			);
		$passwd = array(
			'type' => 'password',
			'name' => 'passwd',
			'required' => 'required',
			'class' => 'form-control',
			'onfocus' => "javascript:this.value=''",
			'placeholder' => 'Contraseña',
			'pattern' => '^[A-Za-z0-9]{8,15}$',//numeros y letras mayúsculas y minúsculas sin caracteres especiales ni ñ's
			'maxlength' => '15',
			'minlength' => '8'
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
			'placeholder' => 'Dirección de Correo',
			'required' => 'required',
			'id' => 'direccion_correo',
			'pattern' => '^[_a-z0-9-.]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$',
			'value' => '@tpitic.com.mx'
		    );
			
		$correo = array(
			'name' => 'correo',
			'class' => 'form-control',
			'placeholder' => 'Enviar notificaciones',
			'required' => 'required',
			'id' => 'correo',
		    );
		$correos = array(
			1 => 'Sí',
			0 => 'No'
		)
	?>
	<span style="text-align:center;"><h3>Agregar usuario al sistema:<nr /></h3></span>
	<div class="form-group">
		<label for="usuario">Usuario:</label>
		<?php echo form_input($usuario); ?>
	</div>
	<div class="form-group">
		<label for="nombre">Nombre:</label>
		<?php echo form_input($nombre); ?>
	</div>
	<div class="form-group">
		<label for="apellido">Apellido:</label>
		<?php echo form_input($apellido); ?>
	</div>
	<div class="form-group">
		<label for="num_empleado">No. Empleado:</label>
		<?php echo form_input($num_empleado); ?>
	</div>
	<div class="form-group">
		<label for="passwd">Password:</label>
		<?php echo form_input($passwd); ?>
	</div>
	<div class="form-group">
		<label for="puesto">Puesto:</label>
		<?php echo form_dropdown($puesto, $puestos); ?>
	</div>
	<div class="form-group">
		<label for="direccon_correo">Correo Electrónico:</label>
		<?php echo form_input($direccion_correo); ?>
	</div>
	<div class="form-group">
		<label for="permiso">Permiso:</label>
		<?php echo form_dropdown($permiso, $permisos); ?>
	</div>
	<div class="form-group">
		<label for="correo">Envío de correos:</label>
		<?php echo form_dropdown($correo, $correos); ?>
	</div>
	<?php echo form_submit("", "Agregar Usuario", "class='btn btn-success'"); ?>
	<?php echo form_close(); ?>
</div><!-- div center