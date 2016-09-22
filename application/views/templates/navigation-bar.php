<header>
<nav class="navbar navbar-default">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
		<a class="navbar-brand" href="<?php echo base_url()."home"; ?>"><img src="<?php echo base_url()."img/pitic-logo.png"; ?>" id="logoPitic" style="height:30px;width:95px;margin-top:-0.4em;" /></a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		<ul class="nav navbar-nav">
			<li class=""><a href="<?php echo base_url()."home"; ?>">&nbsp;<span class='glyphicon glyphicon-home' aria-hidden='true'></span>&nbsp;<span class="sr-only">(current)</span></a></li>
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">&nbsp;<span class='glyphicon glyphicon-file' aria-hidden='true'></span>&nbsp;<span class="caret"></span></a>
				<ul class="dropdown-menu">
					<li><a href=<?php echo base_url()."buscar" ?> ><span class='glyphicon glyphicon-search' aria-hidden='true'></span>&nbsp;&nbsp;&nbsp;Buscar Documento</a></li>
					<?php if($_SESSION["permiso"] == "A"){ ?>
					<li><a href=<?php echo base_url()."agregarDoc" ?> ><span class='glyphicon glyphicon-plus' aria-hidden='true'></span>&nbsp;&nbsp;&nbsp;Agregar Documento</a></li>
					<li><a href=<?php echo base_url()."buscar" ?> ><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>&nbsp;&nbsp;&nbsp;Modificar Documento</a></li>
					<li><a href="#"><span class='glyphicon glyphicon-minus' aria-hidden='true'></span>&nbsp;&nbsp;&nbsp;Eliminar Documento</a></li>
					<li role="separator" class="divider"></li>
					<li><a href=<?php echo base_url()."listaDocsRevision"; ?> ><span class='glyphicon glyphicon-check' aria-hidden='true'></span>&nbsp;&nbsp;&nbsp;Docs. en Revisi&oacute;n</a></li>
					<li><a href=<?php echo base_url()."historialCambiosForm" ?> ><span class='glyphicon glyphicon-time' aria-hidden='true'></span>&nbsp;&nbsp;&nbsp;Historial de cambios</a></li>
					<li role="separator" class="divider"></li>
					<li><a href=<?php echo base_url()."agregarDocExterno" ?> ><span class='glyphicon glyphicon-plus' aria-hidden='true'></span>&nbsp;&nbsp;&nbsp;Agregar Documento Externo</a></li>
				</ul>
			</li>
			<li class="dropdown">
				<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class='glyphicon glyphicon-user' aria-hidden='true'></span><span class="caret"></span></a>
				<ul class="dropdown-menu">
					<li><a href=<?php echo base_url()."adduser" ?> ><span class='glyphicon glyphicon-plus' aria-hidden='true'></span>&nbsp;&nbsp;&nbsp;Agregar Usuario</a></li>
					<li><a href=<?php echo base_url()."manageusers" ?> ><span class='glyphicon glyphicon-user' aria-hidden='true'></span>&nbsp;&nbsp;&nbsp;Administrar Usuarios</a></li>
					<li><a href=<?php echo base_url()."jobpermits" ?> ><span class='glyphicon glyphicon-th-list' aria-hidden='true'></span>&nbsp;&nbsp;&nbsp;Permiso a documentos</a></li>
					<li><a href=<?php echo base_url()."jobpermitsExternos" ?> ><span class='glyphicon glyphicon-th-large' aria-hidden='true'></span>&nbsp;&nbsp;&nbsp;Permiso a documentos Externos</a></li>
					<?php } ?>
				</ul>
			</li>
		</ul>
		<form class="navbar-form navbar-left" action="<?php echo base_url()."searchdocument" ?>" method="get">
			<div class="form-group">
			  <input id="target" name="target" type="text" class="form-control" style="width:400px;" placeholder="Búsqueda de DOCUMENTOS">
			</div>
			<button type="submit" class="btn btn-default">
				&nbsp;<span class='glyphicon glyphicon-search' aria-hidden='true'></span>&nbsp;
			</button>
		</form>
		<ul class="nav navbar-nav navbar-right">
			<li class="dropdown">
			<a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span class='glyphicon glyphicon-user' aria-hidden='true'></span>&nbsp;<?php echo $_SESSION["usuario"]; ?><span class="caret"></span></a>
			<ul class="dropdown-menu">
				<li><a href=<?php echo base_url()."logout" ?> ><span class='glyphicon glyphicon-log-out' aria-hidden='true'></span>&nbsp; Cerrar Sesión</a></li>
				<li><a href=<?php echo base_url()."ajustesCuenta/ajustes_usuario" ?> ><span class='glyphicon glyphicon-cog' aria-hidden='true'></span>&nbsp; Ajustes de la cuenta</a></li>
			</ul>
			</li>
		</ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->
</nav>
</header>