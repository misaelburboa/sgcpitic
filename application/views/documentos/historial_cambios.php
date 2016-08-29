<style type="text/css">
	th, td{
		padding-left: 0.25em;
		padding-right: 0.25em;
		text-align: center;
		font-size: 12px;
	}
	#contenido{
		margin: 0 auto;
		height: auto;
		min-height: 450px;
		overflow: hidden;
		/*border:solid gray 1px;*/
		text-align: center;
	}
	#resultados{
		margin: 0 auto;
		width: 90%;
	}

	th{
		font-size: 15px;
		font-weight: bold;
	}
</style>
<div id="resultados">
	<div id="backbutton" style="text-align:left; width:10%;">
		<button name='volver' id='volver' type='button' class='form-control' onclick='javascript:history.back(-1);' style='width:100px;'>
			<span class='glyphicon glyphicon-chevron-left' aria-hidden='true'></span>&nbsp;&nbsp;Volver
		</button><br />
	</div>
	<?php
	echo $table;
	?>
</div>