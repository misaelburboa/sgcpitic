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
		text-align: center;
	}
	#resultados{
		margin: 0 auto;
		width:85%;
		min-height: 450px;
	}
</style>
<div id="resultados">
	<?php
	echo "<h3>".$mensaje."</h3><div style='text-align:left'><a href='javascript:history.back(-1);'> << Volver</a></div><br />";
	echo $table;
	?>
</div>