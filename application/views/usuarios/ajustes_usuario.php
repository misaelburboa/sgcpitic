<style>
#contenido{
	margin: 0 auto;
	width:100%; 
	font-size:14px; 
	overflow: auto;
	}
#center{
	margin-left:5%;
	width: 50%;
	float: left;
	text-align: left;
}
</style>
<div id="center">
	<?php $userinfo = $usuario->result()[0]; ?>
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
			'value' => $userinfo->usuario,
			'readonly' => true
			);
		$nombre = array(
			'name' => 'nombre',
			'class' => 'form-control',
			'required' => 'required',
			'placeholder' => 'Nombre(s)',
			'pattern' => '^[A-Za-zÑñáéíóúÁÉÍÓÚ\s]{5,100}$',
			'maxlength' => '100',
			'value' => $userinfo->nombre,
			'readonly' => true
			);

		$num_empleado = array(
			'name' => 'num_empleado',
			'class' => 'form-control',
			'required' => 'required',
			'placeholder' => 'Número de empleado',
			'pattern' => '^[0-9]{1,5}$',
			'value' => $userinfo->no_empleado,
			'size' => '4',
			'readonly' => true
			);
			

		$direccion_correo  = array(
			'name' => 'direccion_correo',
			'class' => 'form-control',
			'placeholder' => 'Direcci&oacute;n de Correo',
			'required' => 'required',
			'id' => 'direccion_correo',
			'pattern' => '^[_a-z0-9-.]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$',
			'value' => $userinfo->correo,
			'readonly' => true
		    );
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
				<label for="direccon_correo">Correo Electr&oacute;nico:</label>
				<?php echo form_input($direccion_correo); ?>
			</div>
		</div>
	</div>
	<div>
	<?php echo form_close(); ?>
</div><!-- div center