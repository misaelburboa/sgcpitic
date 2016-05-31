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
	<?php $doc = $documento->result()[0]; 
	//Aquí verificaremos si el archivo se encuentra marcado como activo o no, de no estarlo no se mostrará nada.
	if($doc->activo == 1){
	?>
	<?php echo form_open_multipart('actualizarDocumento'); ?>
	<?php
		$doc_genera = isset($doc->doc_que_lo_genera) ? $doc->doc_que_lo_genera : "Ninguno";
		if($doc_genera == ""){$doc_genera = "Ninguno";}
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
		$revision = array(
			'type' => 'number',
			'name' => 'revision',
			'class' => 'form-control',
			'readonly' => 'readonly',
			'value' => $doc->revision
			);
		$subrevision = array(
			'type' => 'text',
			'name' => 'subrevision',
			'class' => 'form-control',
			'readonly' => 'readonly',
			'value' => $doc->subrevision
			);
		$aumentarRev = array(
			'type' => 'checkbox',
		    'name' => 'aumentarRev',
		    'id' => 'aumentarRev',
		    'value' => 1,
		    'class' => 'styled'
		    );
		$doc_que_lo_genera = array(
			'name' => 'doc_que_lo_genera',
			'class' => 'form-control',
			'placeholder' => 'Documento que lo genera',
			'readonly' => 'readonly',
			'value' => $doc_genera
			);
		$metodo_compilacion = array(
			'name' => 'metodo_compilacion',
			'class' => 'form-control',
			'placeholder' => 'Método de compilación',
			'required' => 'required',
			'id' => 'metodo_compilacion'
			);
		$opt_responsable = array(
			'name' => 'responsable',
			'class' => 'form-control',
			'required' => 'required',
			'id' => 'responsable'
			);
		$tipo = array(
			'name' => 'tipo',
			'class' => 'form-control',
			'required' => 'required',
			'id' => 'tipo'
			);

		$archivo = array(
			'type' => 'file',
			'name' => 'archivo',
			'class' => 'btn btn-default',
			'size' => '2000',
			'required' => 'required'
			);
		$tiempo_retencion_uni = array(
			'name' => 'tiempo_retencion_uni',
			'type' => 'number',
			'class' => 'form-control',
			'value' => $doc->tiempo_retencion_uni
			);
		$tiempo_retencion_desc = array(
			'dia(s)' => 'Día(s)',
			'mes(es)' => 'Mes(es)',
			'año(s)' => 'Año(s)',
			);
		$opt_desc_t_ret = array(
			'name' => 'tiempo_retencion_desc',
			'class' => 'form-control'
			);
		$causa_cambio = array(
			'name' => 'causa_cambio',
			'required' => 'required',
			'class' => 'form-control'
			);
		$desc_cambio = array(
			'name' => 'desc_cambio',
			'required' => 'required',
			'class' => 'form-control'
			);
	?>
	<span style="text-align:center;"><h3>Modificar:<nr /> <?php echo $doc->nombre_documento; ?></h3></span>
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
		<div class="row">
			<div class="col-xs-2">
				<label for="revision">Revisión:</label>
				<?php echo form_input($revision); ?>
			</div>
			<div class="col-xs-2">
				<label for="revision">Sub revisión:</label>
				<?php echo form_input($subrevision); ?>
			</div>
			<div class="col-xs-3">
				<div class="checkbox checkbox-success" >
					<br />
					<?php echo form_checkbox($aumentarRev); ?>
					<label for="aumentarRev">Aumentar revisión</label>
				</div>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label for="doc_que_lo_genera">Documento que lo genera:</label>
		<?php echo form_input($doc_que_lo_genera); ?>
	</div>
	<div class="form-group">
		<label for="metodo_compilacion">Método de compilación:</label>
		<?php echo form_dropdown($metodo_compilacion, $metodoCompilacion, $doc->id_metodo_comp); ?>
	</div>
	<div class="form-group">
		<label for="responsable">Responsable:</label>
		<?php echo form_dropdown($opt_responsable, $puestos, $doc->responsable); ?>
	</div>
	<div class="form-group">
		<label for="tipo">Tipo:</label>
		<?php echo form_dropdown($tipo, $tiposDeDocumento, $doc->id_tipo); ?>
	</div>
	<div class="form-group">
		<label for="">Tiempo de retención:</label>
		<div class="row">
			<div class="col-xs-3">
				<?php echo form_input($tiempo_retencion_uni); ?>
			</div>
			<div class="col-xs-3">
				<?php echo form_dropdown($opt_desc_t_ret, $tiempo_retencion_desc, $doc->tiempo_retencion_desc, 'id=tiempo_retencion_desc'); ?>
			</div>
		</div>
	</div>
	<div class="form-group">
		<label for="Archivo">Subir Archivo:</label><br />
		<?php echo form_upload($archivo); ?>
	</div>
	<div class="row">
		<div class="col-xs-6">
			<label for="causa_cambio">Causa del cambio:</label>
			<?php echo form_textarea($causa_cambio); ?>
		</div>
		<div class="col-xs-6">
			<label for="desc_cambio">Descripción del cambio:</label>
			<?php echo form_textarea($desc_cambio); ?>
		</div>
	</div>
	<div class="row" style="text-align:right;">
		<div class="col-xs-12">
			<br />
			<?php echo form_submit("", "Actualizar Documento", "class='btn btn-success'"); ?>
		</div>
	</div>
	<?php echo form_close(); 
	
	}// fin de if($doc->activo == 1)
	else{
		echo "<h2>No se ha encontrado el documento solicitado, asegurese que no haya sido eliminado del sistema</h2>";
	}
	?>
</div><!-- div center