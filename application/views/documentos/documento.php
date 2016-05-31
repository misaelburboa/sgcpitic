<style type="text/css">
	#contenido{
		margin: 0 auto;
		width:100%;
		height: auto;
		min-height: 500px;
		overflow: hidden;
		text-align: center;
	}
	#comentariosDocumento li{list-style: none;/*display: inline;*/}
	#comentariosDocumento a {color: blue;/*display: inline;*/}
	#comentariosDocumento{text-align:left;border:solid #F2E600 1px;}
	#documento{width:100%;}
	#center{
		margin-left: 25%;
		width: 60%;
		float: left;
	}
</style>
<div id="center" style="margin-top:1em;">
	<?php
	$usuario = $session_data = $this->session->userdata('usuario');
	$permiso = $session_data = $this->session->userdata('permiso');
	$doc = $documento->result()[0]; //solo será un archivo por eso tomamos la posición [0] del arreglo $documento->result();
	?>
	<?php
	if ($checkin === 0) {
		$checkinUsrs = "";
	}else{
		$checkinUsrs = "
		<div id='comentariosDocumento' class='alert alert-warning' >
			<div id='warning-icon' style='float:left;height:100%;margin-right:1em;'>
				<img src='../img/warning-icon-hi.png' height='50px' width='50px' />
			</div>
			<span style='font-weight:bold;'>El documento esta <span style='color:red'>en revisión</span> por los siguientes usuarios:</span><br /><ul><p>";
			foreach($checkin->result() as $chkin){
				$checkinUsrs .= "<li><a href='../getborrador/".$chkin->usuario."/".$doc->id_documento."' title='Ver este borrador'>".$chkin->usuario."</a></li>";
				if($usuario === $chkin->usuario){
					$revisando = true;
				}
			}
			$checkinUsrs .= "</p></ul>
			Posiblemente cambie próximamente, tome precauciones.
		</div>";
		//echo "<p>".$checkinUsrs."</p>";
	}
	
	//Aquí verificaremos si el archivo se encuentra marcado como activo o no, de no estarlo no se mostrará nada.
	if($doc->activo == 1){
	?>
	<div id="topmenufile">
		<nav class="navbar navbar-default">
		  <div class="container-fluid">
		    <!-- Brand and toggle get grouped for better mobile display -->
		    <div class="navbar-header">
		      <span style="font-size:20px;"><?php echo $doc->nombre_documento; ?></span><br />
		      <span><?php echo "Rev.".$doc->revision.".".$doc->subrevision."/".$doc->fecha_creacion; ?></span>
		    </div>

		    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		      <ul class="nav navbar-nav navbar-right">
		        <li class="dropdown">
		          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Opciones<span class="caret"></span></a>
		          <ul class="dropdown-menu">
						<li><a href=<?php echo "'../uploads/".$doc->archivo."'"; ?>>Descargar</a></li>
		          	<?php if($permiso == "W" || $permiso == "A"){ ?>
		            	<li><a href=<?php echo "'../actualizarDoc/".$doc->id_documento."'"; ?>>Modificar</a></li>
		            	<li><a href=<?php echo "'../historialdecambios/".$doc->id_documento."'"; ?>>Ver historial de cambios</a></li>
		            	<?php if($permiso == "A"){ ?>
		            		<li><a href=<?php echo "\"javascript:if(confirm('¿Realmente desea eliminar el documento?, después de esto ya no habrá vuelta atrás.')){location.href='../eliminarDoc/".$doc->id_documento."';}\""; ?> >Eliminar</a></li>
		            	<?php } ?>
		            	<li role="separator" class="divider"></li>
		            	<?php if (isset($revisando)) { //verifica si el usuario actual es uno de los que tiene el documento en checkin
			            	echo "<li><a href='../subirborrador/".$doc->id_documento."'>Subir Borrador</a></li>";
							echo "<li><a href='../checkoutdoc/".$doc->id_documento."'>Checkout</a></li>";
						}else{
							echo "<li><a href='../checkin/".$doc->id_documento."'>Checkin</a></li>";
						}?>
		            <?php } ?>
		            
		          </ul>
		        </li>
		      </ul>
		    </div><!-- /.navbar-collapse -->
		  </div><!-- /.container-fluid -->
		</nav>
	</div>
	<?php
		echo $checkinUsrs;
	?>
	<div id="documento">
	<?php
		//echo "<pre>";
		//echo '<iframe id="preview" src="http://docs.google.com/gview?url='.base_url().'uploads/'.$doc->archivo .'&embedded=true" width="100%" height="70%;" align="left" frameborder="0"></iframe>';
		//=echo '<iframe src="http://docs.google.com/gview?url=http://misaelburboa.com/CMBMcv.pdf&embedded=true" style="width:100%; height:650px;" frameborder="0"></iframe>';
		//echo "</pre>";

	}// fin de if($doc->activo == 1)
	else{
		echo "<h2>No se ha encontrado el documento solicitado, asegurese que no haya sido eliminado del sistema</h2>";
	}
	?>
	</div>
</div><!--div center