<style type="text/css">
	th, td{
		padding-left: 0.25em;
		padding-right: 0.25em;
		text-align: center;
		font-size: 12px;
	}
	#contenido{
		margin: 0 auto;
		padding: 2em;
		padding-top:1em;
		width:90%;
		height: auto;
		min-height: 450px;
		overflow: hidden;
		/*border:solid gray 1px;*/
		text-align: center;
	}
</style>
	<?php
	echo "<h3>".$mensaje."</h3><div style='text-align:left'><a href='javascript:history.back(-1);'> << Volver</a></div><br />";
	echo $table;
	?>