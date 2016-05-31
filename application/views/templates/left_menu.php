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
	#left_menu{
		float: left;
		width: 20%;
		color:#F2F5A9;
		position: fixed;
	}
	#left_menu a{color: #006547;}
	#left_menu a:hover{
		color:#2E2E2E;
		background-color: #BDBDBD;
		display: block;
		text-decoration: none;
		height: 100%;
	}
	#left_menu li{
		background-color: #E6E6E6;
		font-size: 15px;
		text-decoration: none;
		list-style: none;
		display: block;
		height: 30px;
	}
</style>
<div id="left_menu">
	<ul style="border: solid #BDBDBD 1px;">
		<li><a href=<?php echo base_url()."home"; ?> >Home</a></li>
		<li><a href=<?php echo base_url()."logout" ?> >Logout</a></li>
		<li><a href=<?php echo base_url()."buscar" ?> >Buscar Documento</a></li>
		<?php if($this->session->userdata("permiso") == "A" || $this->session->userdata("permiso") == "W"){ ?>
			<li><a href=<?php echo base_url()."agregarDoc" ?> >Agregar Documento</a></li>
			<li><a href=<?php echo base_url()."buscar" ?> >Modificar Documento</a></li>
			<li><a href="#">Eliminar Documento</a></li>
			<li><a href=<?php echo base_url()."adduser" ?> >Alta de Usuarios</a></li>
			<li><a href=<?php echo base_url()."jobpermits" ?> >Permiso a documentos</a></li>
		<?php } ?>
	</ul>
</div>