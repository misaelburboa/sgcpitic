<style>
#contenido{
		margin: 0 auto 0 auto;
		width:100%; 
		font-size:14px; 
		overflow: auto;
		min-height: 470px;
	}
#generico{
		margin: 0 auto 0 auto;
		width:50%; 
		font-size:14px; 
		padding-top:1em;
		overflow: auto;
		min-height: 470px;
	}
</style>
<div id="generico">
	<div style="float:left;width:20%;">
	<button name='volver' id='volver' type='button' class='form-control' onclick='javascript:history.back(-1);' style='width:100px;'>
		<span class='glyphicon glyphicon-chevron-left' aria-hidden='true'></span>&nbsp;&nbsp;Volver
	</button></div>
	<div style="float:left;width:20%;">
	<button name="home" id="home" type="button" class="form-control" onclick="javascript:location.href='<?php echo base_url()."home"; ?>'" style="width:100px;">
		<span class='glyphicon glyphicon-home' aria-hidden='true'></span>&nbsp;&nbsp;Inicio
	</button></div>
	<br /><br /><hr />
	<div class="page-header">
	  <h1><span style="font-size:70px"><?php echo $texto1; ?></span><br /><small><?php echo $texto2; ?></span></small></h1>
	</div>	
</div>