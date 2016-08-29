<style>
#contenido{
		margin: 0 auto 0 auto;
		width:100%; 
		font-size:14px; 
		overflow: auto;
		min-height: 470px;
	}
#buttons{
	margin: 0 auto 0 auto;
	width:19%;
}
</style>
<span style="font-size:70px;">Error 404</span><br />
<span style="font-size:30px;">La página solicitada no existe o fue cambiada de ubicación.</span><br />
<span style="font-size:15px;">
<hr />
<div id="buttons">
	<div style="float:left;margin-right:20px;">
		<button name='volver' id='volver' type='button' class='form-control' onclick='javascript:history.back(-1);' style='width:100px;'>
			<span class='glyphicon glyphicon-chevron-left' aria-hidden='true'></span>&nbsp;&nbsp;Volver
		</button>
	</div>
	<div style="float:left;">
		<button name="home" id="home" type="button" class="form-control" onclick="javascript:location.href='<?php echo base_url()."home"; ?>'" style="width:100px;">
			<span class='glyphicon glyphicon-home' aria-hidden='true'></span>&nbsp;&nbsp;Inicio
		</button>
	</div>
</div>
</span><br /><br />