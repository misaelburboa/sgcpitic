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
	<?php $doc = $documento->result()[0]; ?>
	<?php echo form_open_multipart('subir_borrador/'.$doc->id_documento); ?>
	<?php
		$nombre_documento = array(
			'name' => 'nombre_documento',
			'class' => 'form-control',
			'placeholder' => 'Nombre del documento',
			'readonly' => 'readonly',
			'value' => $doc->nombre_documento
			);
		$archivo_en_servidor = array(
			'name' => 'archivo_en_servidor',
			'class' => 'form-control',
			'placeholder' => 'Archivo en Servidor',
			'readonly' => 'readonly',
			'value' => $doc->archivo
			);
		$id_calidad = array(
			'name' => 'id_calidad',
			'class' => 'form-control',
			'placeholder' => 'Escriba el id de calidad',
			'readonly' => 'readonly',
			'value' => $doc->id_calidad
			);
		
		$archivo = array(
			'type' => 'file',
			'name' => 'archivo',
			'class' => 'btn btn-default',
			'size' => '1000',
			'required' => 'required'
			);
	?>
	<span style="text-align:center;"><h3>Subir un borrador para el documento:<br /> <?php echo $doc->nombre_documento; ?></h3></span>
	<div class="form-group">
		<?php echo form_hidden('id_documento', $doc->id_documento); ?>
		<label for="nombre_documento">Documento:</label>
		<?php echo form_input($nombre_documento); ?>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-xs-4">
				<label for="id_calidad">ID Calidad:</label>
				<?php echo form_input($id_calidad); ?>
			</div>
			<div class="col-xs-4">
				<label for="archivo_en_servidor">Archivo en Servidor:</label>
				<?php echo form_input($archivo_en_servidor); ?>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label for="Archivo">Subir Archivo:</label><br />
		<?php echo form_upload($archivo); ?>
	</div>
	<div class="row" style="text-align:right;">
		<div class="col-xs-12">
			<br />
			<?php echo form_submit("", "Subir Borrador", "class='btn btn-success'"); ?>
		</div>
	</div>
	<?php echo form_close(); ?>
</div><!-- div center