<style type="text/css">
	th, td{
		padding-left: 0.25em;
		padding-right: 0.25em;
		text-align: center;
		font-size: 12px;
	}
	#contenido{
		margin: 0 auto;
		width:100%;
		height: auto;
		min-height: 450px;
		overflow: hidden;
		/*border:solid gray 1px;*/
		text-align: center;
	}
	#container2{
		margin: 0 auto;
		width:100%;
		padding: 2em;
		text-align:center;
	}

	th{
		font-size: 15px;
		font-weight: bold;
	}
</style>
<?php echo form_open_multipart('historialCambiosConcentrado'); ?>
<?php 
$fecha_inicio = array(
	'name' => 'inicio',
	'class' => 'form-control',
	'placeholder' => 'Fecha de Inicio',
	"data-provide" => "datepicker",
	'value' => ''
	);
$fecha_final = array(
	'name' => 'final',
	'class' => 'form-control',
	'placeholder' => 'Fecha Final',
	"data-provide" => "datepicker",
	'value' => ''
);
?>
<div id="contenido" style="float:left">
<div id="container2">
<h2>Historial de Cambios</h2>
<div id="busqueda" style="text-align:center;padding-bottom:1em;">Seleccione por favor el rango de fechas en que desea consultar los cambios a documentos:</div>
	<div class="form-group" style="margin:0 auto; width:90%;text-align:center;">
		<div class="container" style="width:100%;margin:0 auto;">
			<div class="row" style="width:60%;margin:0 auto;">
				<div class='col-sm-6' style="width:46%;">
					<div class="form-group">
						<div class='input-group date'>
							<?php echo form_input($fecha_inicio); ?>
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</div>
				<div class='col-sm-6' style="width:46%;">
					<div class="form-group">
						<div class='input-group date' id='fecha_final'>
							<?php echo form_input($fecha_final); ?>
							<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
							</span>
						</div>
					</div>
				</div>
				<div class='col-sm-6' style="width:8%;margin:0 auto;">
					<?php echo form_submit("", "Buscar", "class='btn btn-success'"); ?>
				</div>
				<?php echo form_close(); ?>
				<script type="text/javascript">
					$(function () {
						$.fn.datepicker.dates['en'] = {
							days: ["Domingo", "Lunes", "Martes", "Miercoles", "Jueves", "Viernes", "Sabado"],
							daysShort: ["Dom", "Lun", "Mar", "Mie", "Jue", "Vie", "Sab"],
							daysMin: ["Do", "Lu", "Ma", "Mi", "Ju", "Vi", "Sa"],
							months: ["Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"],
							monthsShort: ["Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic"],
							today: "Hoy",
							clear: "Limpiar",
							format: "yyyy-mm-dd",
							titleFormat: "MM yyyy", /* Leverages same syntax as 'format' */
							weekStart: 0
						};
						
						$('.datepicker').datepicker({});
					});
				</script>
			</div>
		</div>
	</div>
	<div id="resultados">
	<?php 
		if(isset($table)){
			echo $table;
		}else{
			?>
			<script>document.getElementById('resultados').innerHTML = "";</script>
			<?php
		}
	?>
	</div>
</div>
</div>