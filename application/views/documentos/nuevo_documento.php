
<style type="text/css">
#contenido{
	/*border:solid #ABABAB 1px;*/
	margin: 0 auto 0 auto;
	width:100%; 
	font-size:10px; 
	margin-top:2em;
	overflow: auto;
}
#center{
	margin-left: 20%;
	width: 80%;
	padding-left: 15em;
	padding-right: 15em;
	float: left;
	text-align: left;
}
label{
	font-size: 14px;
}
</style>
<div id="center">
	<?php echo form_open_multipart('savedocument'); ?>
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
		$id_calidad = array(
			'name' => 'id_calidad',
			'class' => 'form-control',
			'placeholder' => 'Escriba el id de calidad',
			'title' => 'Ingresa un ID de calidad correcto',
			'required' => 'required',
			'onKeyUp' => 'javascript: this.value=this.value.toUpperCase();',
			'pattern' => '^DT-(OP|SOP|CAL)-(JF|ES|MTO|INS|M|MOD|ORG|PLAN|POL|PROC|TAB|DIR|ISO|RH|VTA|ATC|ST|SEG)[0-9]{1,4}$'
			);
		$revision = array(
			'type' => 'number',
			'name' => 'revision',
			'class' => 'form-control',
			'value' => 0,
			'required' => 'required',
			'min' => '0'
			);
		$subrevision = array(
			'type' => 'number',
			'name' => 'subrevision',
			'class' => 'form-control',
			'value' => 0,
			'required' => 'required'
			);
		$doc_que_lo_genera = array(
			'id' => 'doc_que_lo_genera',
			'name' => 'doc_que_lo_genera',
			'class' => 'form-control',
			'placeholder' => 'Documento que lo genera',
			);
		$opt_responsable = array(
			'name' => 'responsable',
			'class' => 'form-control',
			'required' => 'required'
			);
		$opt_doc_que_lo_genera = array(
			'name' => 'doc_que_lo_genera',
			'class' => 'form-control'
			);
		$opt_desc_t_ret = array(
			'id' => 'tiempo_retencion_desc',
			'name' => 'tiempo_retencion_desc',
			'class' => 'form-control'
			);
		$tiempo_retencion_uni = array(
			'id' => 'tiempo_retencion_uni',
			'name' => 'tiempo_retencion_uni',
			'type' => 'number',
			'class' => 'form-control',
			'value' => 0,
			'required' => 'required'
			);
		$tiempo_retencion_desc = array(
			'dia(s)' => 'Día(s)',
			'mes(es)' => 'Mes(es)',
			'año(s)' => 'Año(s)',
			);
		$metodo_compilacion = array(
			'id' => 'metodo_compilacion',
			'name' => 'metodo_compilacion',
			'class' => 'form-control',
			'required' => 'required'
			);
			
		$esRegistro = array(
			'type' => 'checkbox',
		    'name' => 'esRegistro',
		    'id' => 'esRegistro',
		    'value' => 0,
		    'class' => 'styled',
			'onclick' => 'javascript:
			txtAlmReg = document.getElementById(\'almacen_registro\');
			tipo = document.getElementById(\'tipo\');
			divTipo = document.getElementById(\'divTipo\');
			divAlmReg = document.getElementById(\'divAlmReg\');
			divTiempoRet = document.getElementById(\'divTiempoRet\');
			tiempoRetUni = document.getElementById(\'tiempo_retencion_uni\');
			tiempoRetDesc = document.getElementById(\'tiempo_retencion_desc\');
			metComp = document.getElementById(\'metComp\');
			metodoComp = document.getElementById(\'metodo_compilacion\');
			docGenera = document.getElementById(\'metodo_compilacion\');
			documentoGenera = document.getElementById(\'doc_que_lo_genera\');
			if(divAlmReg.style.display == \'none\' && this.checked){
				txtAlmReg.required=true;
				tipo.disabled=true;
				divTipo.style.display=\'none\';
				divAlmReg.style.display = \'inline\';
				divTiempoRet.style.display=\'block\';
				tiempoRetUni.disabled = false;
				tiempoRetUni.required = true;
				tiempoRetDesc.disabled = false;
				tiempoRetDesc.required = true;
				metComp.style.display=\'block\';
				metodoComp.disabled = false;
				metodoComp.required = true;
				docGenera.style.display=\'block\';
				documentoGenera.disabled = false;
			}else{
				txtAlmReg.required=false;
				tipo.disabled=false;
				divTipo.style.display=\'block\';
				divAlmReg.style.display = \'none\';
				divTiempoRet.display = \'none\';
				divTiempoRet.style.display=\'none\';
				tiempoRetUni.disabled = true;
				tiempoRetUni.required = false;
				tiempoRetDesc.disabled = true;
				tiempoRetDesc.required = false;
				metComp.style.display=\'none\';
				metodoComp.disabled = true;
				metodoComp.required = false;
				docGenera.style.display=\'none;\';
				documentoGenera.disabled = true;
			}'
		    );
			
		$almacen_registro = array(
			'name' => 'almacen_registro',
			'id' => 'almacen_registro',
			'maxlength' => 100,
			'rows' => 2,
			'cols' => 3,
			'class' => 'form-control',
			);

		$ubicacion = array(
			'name' => 'ubicacion',
			'class' => 'form-control',
			'placeholder' => 'Ubicación',
			'required' => 'required'
			);

		$archivo = array(
			'name' => 'archivo',
			'class' => 'btn btn-default',
			'size' => '50',
			'required' => 'required'
			);
		$tipo = array(
			'id' => 'tipo',
			'name' => 'tipo',
			'class' => 'form-control',
			'required' => 'required'
			);
	?>
	<span style="text-align:center;"><h2>Agregar documento al SGCPitic</h2></span>
	<div class="form-group">
		<label for="nombre_documento">Documento:</label>
		<?php echo form_input($nombre_documento); ?>
	</div>
	<div class="form-group">
		<div class="checkbox checkbox-success" style="padding-left:20px;" >
			<?php echo form_checkbox($esRegistro); ?>
			<label for="esRegistro">Registro de Calidad</label>
		</div>
		<div id='divAlmReg' style="display:none;">
			<label for="almacen_registro">Se almacena en:</label>
			<?php echo form_textarea($almacen_registro);?>
		</div>
	</div>
	<div class="form-group">
		<label for="id_calidad">ID Calidad:</label>
		<?php echo form_input($id_calidad); ?>
	</div>
	<div class="form-group">
		<label for="revision">Revisión:</label>
		<?php echo form_input($revision); ?>
	</div>
	<div class="form-group">
		<label for="subrevision">Subrevisión:</label>
		<?php echo form_input($subrevision); ?>
	</div>
	<div class="form-group">
		<label for="doc_que_lo_genera">Documento que lo genera:</label>
		<?php echo form_dropdown($opt_doc_que_lo_genera, $documentos, 'id=doc_que_lo_genera'); ?>
	</div>
	<div class="form-group" id="divTiempoRet" style="display:none;">
		<label for="">Tiempo de retención:</label>
		<div class="row">
			<div class="col-xs-3">
				<?php echo form_input($tiempo_retencion_uni); ?>
			</div>
			<div class="col-xs-3">
				<?php echo form_dropdown($opt_desc_t_ret, $tiempo_retencion_desc, 'id=tiempo_retencion_desc'); ?>
			</div>
		</div>
	</div>
	<div class="form-group">
		<div id='metComp' style="display:none;">
			<label for="metodo_compilacion">Método de compilación:</label>
			<?php echo form_dropdown($metodo_compilacion, $metodoCompilacion, 'id=metodo_compilacion'); ?>
		</div>
	</div>
	<div class="form-group">
		<label for="responsable">Responsable:</label>
		<?php echo form_dropdown($opt_responsable, $puestos, 'id=responsable'); ?>
	</div>
	<div class="form-group" id="divTipo">
		<label for="tipo">Tipo:</label>
		<?php echo form_dropdown($tipo, $tiposDeDocumento, 'id=tipo'); ?>
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