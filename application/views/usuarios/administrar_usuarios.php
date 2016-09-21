<style>
#contenido{
	margin: 0 auto;
	width:100%; 
	font-size:14px; 
	overflow: auto;
	}
#center{
	margin: 0 auto;
	width: 80%;
	text-align: center;
}
</style>
<div id="center">
<form action="getusers/0" method="get">
<?php //echo form_open_multipart('getusers'); ?>
<?php
	$target = array(
		'name' => 'target',
		'class' => 'form-control',
		'placeholder' => 'Escriba el usuario, num. de empleado, nombre o correo a buscar',
		);
	$puesto = array(
		'name' => 'puesto',
		'class' => 'form-control',
		'placeholder' => 'Puesto',
		'id' => 'puesto'
		);
	$permiso = array(
		'name' => 'permiso',
		'class' => 'form-control',
		'placeholder' => 'Tipo de permiso',
		'id' => 'permiso',
		);
?>
<span style="text-align:center;"><h3>Administrar Usuarios</h3></span>
<div class="form-group" style="margin:0 auto; width:70%;">
	<div class="row" style="margin 0 auto; width:100%;">
		<div class="col-xs-4" style="width:100%;">
			<?php echo form_input($target); ?>
		</div>
		<div class="col-xs-4" style="width:50%;">
			<label for="permiso">Puesto</label>
			<?php echo form_dropdown($puesto, $puestos); ?>
		</div>
		<div class="col-xs-4" style="width:50%;">
			<label for="permiso">Tipo de Permiso:</label>
			<?php echo form_dropdown($permiso, $permisos); ?>
		</div>
	</div>
	<div class="row" style="margin 0 auto; width:100%;">	
		<div class="col-xs-4" style="width:100%;margin-top:1.5em; text-align:left;">
			Env&iacute;o de notificaciones:&nbsp;&nbsp;<br />
			<label for="si" style="margin:0.5em;">S&iacute;</label>
			<input type="radio" name="notificaciones" id="si" value=1 />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<label for="no">No</label>
			<input type="radio" name="notificaciones" id="no" value=0 />
		</div>
	</div>
	<div class="row" style="margin 0 auto; width:100%;">
		<div class="col-xs-4" style="width:30%;">
			<br />
			<button class="form-control" id="buscarUsuarios" >Buscar&nbsp;&nbsp;<span class='glyphicon glyphicon-search' aria-hidden='true'></span></button>
		</div>
	</div>
</div><br />
</div>
<?php echo form_close(); ?>
</div><!-- div center