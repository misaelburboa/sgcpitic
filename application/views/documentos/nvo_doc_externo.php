
<style type="text/css">

#center{
	margin: 0 auto;
	width: 50%;
	padding-left:2em;
	padding-right:2em;
	text-align:left;
}

label{
	font-size: 14px;
}
</style>
<div id="center">
	<?php echo form_open_multipart('savedocumentexterno'); ?>
	<?php
		$nombre_documento = array(
			'name' => 'nombre_documento',
			'class' => 'form-control',
			'placeholder' => 'Nombre del documento',
			'title' => 'nombre necesario',
			'required' => 'required',
			'pattern' => '^[A-Za-zñÑ -(0-9)]+$',
			'maxlength' => '150'
			);

		$opt_notificar_a = array(
			'name' => 'notificar_a[]',
			'class' => 'form-control',
			'required' => 'required',
			'size' => 15,
			);

		$archivo = array(
			'name' => 'archivo',
			'class' => 'btn btn-default',
			'size' => '50',
			'required' => 'required'
			);
	?>
	<span style="text-align:center;"><h2>Agregar documento externo al SGCPitic</h2></span>
	<div class="form-group">
		<label for="nombre_documento">Documento:</label>
		<?php echo form_input($nombre_documento); ?>
	</div>
	<div class="form-group">
		<label for="responsable">Notificar cambios en este documento a:</label>
		<?php unset($puestos['']); ?>
		<?php echo form_multiselect($opt_notificar_a, $puestos,'',"id='notificar_a'"); ?>
	</div>
	<div class="form-group">
		<label for="Archivo">Subir Archivo:</label>
		<?php echo form_upload($archivo); ?>
	</div>
	<div style="text-align:right;">
	<?php echo form_submit("", "Subir Documento", "class='btn btn-success'"); ?>
	</div>
	<?php echo form_close(); ?>
</div>