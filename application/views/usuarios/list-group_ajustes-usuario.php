<style>
#left{
	margin-top: -1.4em;
	padding: 0;
	width:20%;
	float:left;
}

#left a:link{color:#2E2E2E; text-decoration: none;}
#left a:visited{color:#2E2E2E; text-decoration: none;}
#left a:hover{color:#2E2E2E; text-decoration: none;}
#left a:active{color:#2E2E2E; text-decoration: none;}
li:hover{
	background-color:#E6E6E6;
	text-decoration: none;
}
</style>
<div id="left">
	<ul class="list-group">
	  <li class="list-group-item"><a href="<?php echo base_url()."ajustesCuenta/ajustes_usuario"; ?>">Informaci&oacute;n del usuario</a></li>
	  <li class="list-group-item"><a href="<?php echo base_url()."ajustesCuenta/cambiar_password"; ?>">Cambiar password</a></li>
	</ul>
</div>