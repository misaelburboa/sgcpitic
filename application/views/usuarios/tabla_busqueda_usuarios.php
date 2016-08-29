<style>
#contenido{
	margin: 0 auto;
	width:100%; 
	font-size:14px; 
	overflow: auto;
	}
#center{
	margin: 0 auto;
	width: 80%;
	text-align: center;
}
tr:hover{
	background-color:#A9F5A9;
}
</style>
<div id="center"><?php
if(isset($tablaUsuarios)){
	
	echo "<h2>Usuarios que coinciden con su b&uacute;squeda:</h2><br />".$tablaUsuarios;
}
?></div>