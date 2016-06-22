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
			<div id='warning-icon' style='float:left;margin-right:1em;'>
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
		    <div class="navbar-header" style="width:100%; text-align:center;">
		      <span style="font-size:20px;"><?php echo $doc->nombre_documento."<br />(".$doc->id_calidad.")"; ?></span><br />
		      <span><?php echo "Rev. ".$doc->revision.".".$doc->subrevision."/".substr($doc->fecha_revision,3); ?></span>
		    </div>

		    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		      <ul class="nav navbar-nav navbar-right">
		        <li class="dropdown">
		          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Opciones<span class="caret"></span></a>
		          <ul class="dropdown-menu">
					<?php if($permiso == "A"){ ?>
						<!--<li><a href=<?php echo "'../uploads/".$doc->vista_archivo."'"; ?>>Descargar PDF</a></li>-->
					<?php } ?>
		          	<?php if($permiso == "W" || $permiso == "A"){ ?>
						<li><a href=<?php echo "'../uploads/".$doc->archivo."'"; ?>>Descargar</a></li>
		            	<li><a href=<?php echo "'../actualizarDoc/".$doc->id_documento."'"; ?>>Modificar</a></li>
		            	<li><a href=<?php echo "'../historialdecambios/".$doc->id_documento."'"; ?>>Ver historial de cambios</a></li>
		            	<?php if($permiso == "A"){ ?>
		            		<li><a href=<?php echo "\"javascript:if(confirm('¿Realmente desea eliminar el documento?, después de esto ya no habrá vuelta atrás.')){location.href='../eliminarDoc/".$doc->id_documento."';}\""; ?> >Eliminar</a></li>
		            	<?php } ?>
		            	<li role="separator" class="divider"></li>
		            	<?php if (isset($revisando)) { //verifica si el usuario actual es uno de los que tiene el documento en checkin
			            	echo "<li><a href='../subirborrador/".$doc->id_documento."'>Subir Borrador</a></li>";
							echo "<li><a href='../checkoutdoc/".$doc->id_documento."'>Liberar revisi&oacute;n</a></li>";
						}else{
							echo "<li><a href='../checkin/".$doc->id_documento."'>Pasar a revisi&oacute;n</a></li>";
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
		//soffice --headless --convert-to pdf *.txt //comando con el que se transforman a pdf los archivos de office, para poder convertirlos es requerido lo siguiente:
		//yum install libreoffice
		//yum install openoffice.org-headless
		//se mostrara la vista (el pdf) correspondiente al documento
		echo "<embed src='http://calidad.tpitic.com.mx/SGCPITIC/uploads/".$doc->vista_archivo."' type='application/pdf' width='800' height='600'></embed>";
	}// fin de if($doc->activo == 1)
	else{
		echo "<h2>No se ha encontrado el documento solicitado, asegurese que no haya sido eliminado del sistema</h2>";
	}
	?>
	</div>
</div><!--div center