<style>
#contenido{
	margin: 0 auto;
	width:100%; 
	font-size:14px; 
	overflow: auto;
	}
#left{
	margin-top: 1em;
	margin-left: 10%;
	width: 80%;
	float: left;
	text-align: left;
}
</style>
<script language="javascript">
$(function(){
	$('#actuales').attr('disabled', false);
	$('#disponibles').attr('disabled', false);
});

function activarActuales(){
	// Limpiamos el select
	$('#actuales').find('option').remove();
	var puesto_id = $("#puesto").val();
	if(puesto_id != ""){
		$.ajax({
			type:'POST',
			url:'<?php echo base_url()."getJobsCurrentDocs/"; ?>'+puesto_id,
			success: function(respuesta){
	            $(respuesta).each(function(i, v){ // indice, valor
	                $('#actuales').append('<option value="'+v.id_documento+'" title="'+v.nombre_documento+'('+v.id_calidad+')">'+v.nombre_documento+' ( '+v.id_calidad+' )</option>');
	            })

	            $('#actuales').prop('disabled', false);
			}

		});
	}else{
		$('#actuales').prop('disabled', true);
	}
}

function activarDisponibles(){
	var puesto_id = $("#puesto").val();
	// Limpiamos el select
	$('#disponibles').find('option').remove();
	if(puesto_id != ""){
		$.ajax({
			type:'POST',
			url:'<?php echo base_url()."getAvailableDocs/"; ?>'+puesto_id,
			success: function(respuesta){
	            // Limpiamos el select
	            $('#disponibles').find('option').remove();

	            $(respuesta).each(function(i, v){ // indice, valor
	                $('#disponibles').append('<option value="'+v.id_documento+'" title="'+v.nombre_documento+'('+v.id_calidad+')">'+v.nombre_documento+' ( '+v.id_calidad+' )</option>');
	            })

	            $('#disponibles').prop('disabled', false);
			}
		});
	}else{
		$('#disponibles').prop('disabled', true);
	}
}
function getSelectedValues(select){
	sel=document.getElementById(select);
	var result = [];
	var options = sel && sel.options;
	var opt;
	var ilen=options.length;
	for(i=0; i<ilen; i++){
		opt = options[i];
		if(opt.selected){
			result.push(opt);
		}
	}

	return result;
}
function buscarEnSelect(value, objSelect){
	opciones = objSelect.options;
	valor = false;
	for(i=0; i<opciones.length; i++){
		if(value == opciones[i].value){
			valor = true;
		}else{
			valor = false;
		}
	}

	return valor;
}
function quitarOpcion() {
	optActuales = document.getElementById('actuales');
	optDisp = document.getElementById('disponibles');

    selectedVals = getSelectedValues('actuales');
    cont = 0;
    exito = false;
    while(cont < selectedVals.length){
    	if(buscarEnSelect(selectedVals[cont].value, optDisp)==false){
    		newOptionIndex = optDisp.options.length;
	    	optDisp.options[newOptionIndex] = new Option(selectedVals[cont].text, selectedVals[cont].value);
	    	optDisp.options[newOptionIndex].title = selectedVals[cont].text;
	    	j=0;
	    	while(j < optActuales.options.length){
	    		if(optActuales.options[j].value == selectedVals[cont].value){
	    			$.ajax({
						type:'POST',
						url:'<?php echo base_url()."removeDocumentAccess/"; ?>'+document.getElementById('puesto').value+'/'+optActuales.options[j].value,
						success: function(respuesta){
				            exito = true;
						},
						failure: function(respuesta){
							exito = false;
							alert("Ocurrió un error al quitar el acceso a este documento");
						}
					});
					optActuales.options[j] = null;
	    		}
	    		j++;
	    	}
	    }else{}
	    cont++;
	}
}

function agregarOpcion() {
   optActuales = document.getElementById('actuales');
	optDisp = document.getElementById('disponibles');

    selectedVals = getSelectedValues('disponibles');
    cont = 0;
    while(cont < selectedVals.length){
    	if(buscarEnSelect(selectedVals[cont].value, optActuales)==false){
    		newOptionIndex = optActuales.options.length;
	    	optActuales.options[newOptionIndex] = new Option(selectedVals[cont].text, selectedVals[cont].value);
	    	optActuales.options[newOptionIndex].title = selectedVals[cont].text;
	    	$.ajax({
				type:'POST',
				async: true,
				url:'<?php echo base_url()."grantDocumentAccess/"; ?>'+document.getElementById('puesto').value+'/'+selectedVals[cont].value,
				success: function(respuesta){
		            exito = true;
				},
				failure: function(respuesta){
					exito = false;
				}
			});
	    	for(j=0; j<optDisp.options.length; j++){
	    		//alert(j+" "+optDisp.options.length+" = "+selectedVals[cont].value);
	    		if(optDisp.options[j].value == selectedVals[cont].value){
	    			optDisp.options[j] = null;
	    		}
	    	}
	    }else{}
	    cont++;
	}
}
</script>
<div id="left">
	<?php echo form_open_multipart('altaUsuario'); ?>
	<?php
		$arrJobs[''] = 'Seleccione';
		foreach($puestos->result() as $job){
			$arrJobs[$job->id_puesto] = $job->nombre_puesto;
		}

		$puesto = array(
			'name' => 'puesto',
			'class' => 'form-control',
			'placeholder' => 'Puesto',
			'id' => 'puesto',
			'onChange' => 'javascript:activarActuales();activarDisponibles()'
			);
		$actuales = array(
			'name' => 'actuales',
			'class' => 'form-control',
			'placeholder' => 'Opciones actuales',
			'style' => 'height:30em;',
			'id' => 'actuales',
			'multiple' => 'multiple'
			);

		$documentos_act = array();

		$disponibles = array(
			'name' => 'disponibles',
			'class' => 'form-control',
			'placeholder' => 'Opciones disponibles',
			'style' => 'height:30em;overflow: auto;',
			'id' => 'disponibles',
			'multiple' => 'multiple',
			);
		$documentos_disp = array();
	?>
	<button name="agregar" id="agregar" type="button" class="form-control" onclick="javascript:location.href='home'" style="width:100px;"><span class='glyphicon glyphicon-home' aria-hidden='true'></span>&nbsp;&nbsp;Inicio</button><br />
	
	<div class="form-group">
		<label for="puesto">Puesto:</label>
		<?php echo form_dropdown($puesto, $arrJobs); ?>
	</div>
	<div class="form-group">
		<div class="row">
			<div class="col-xs-5">
				<label for="actuales">Puesto con permiso a:</label>
				<div style="overflow-x:scroll;overflow: -moz-scrollbars-horizontal;">
				<?php echo form_dropdown($actuales, $documentos_act, array('' => 'Seleccione')); ?>
				</div>
			</div>
			<div class="col-sm-1" style="height:70%;padding:1em;margin:0;">
					<button name="agregar" id="agregar" type="button" class="form-control" onclick="javascript:agregarOpcion()" style="border: solid #BDBDBD 1px;width:70px;"><span class='glyphicon glyphicon-chevron-left' aria-hidden='true'></span></button>
				<br />
					<button name="agregar" id="agregar" type="button" class="form-control" onclick="javascript:quitarOpcion()" style="border: solid #BDBDBD 1px;width:70px;"><span class='glyphicon glyphicon-chevron-right' aria-hidden='true'></span></button>

			</div>
			<div class="col-xs-5">
				<label for="disponibles">Documentos disponibles:</label>
				<?php echo form_dropdown($disponibles, $documentos_disp, array('' => 'Seleccione')); ?>
			</div>
		</div>
	</div>
</div>