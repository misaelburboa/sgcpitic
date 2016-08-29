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
		width: 70%;
		margin: 0 auto;
	}
</style>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<div id="center" style="margin-top:1em;">
	<?php
	$usuario = $session_data = $this->session->userdata('usuario');
	$permiso = $session_data = $this->session->userdata('permiso');
	$doc = $documento->result()[0]; //solo será un archivo por eso tomamos la posición [0] del arreglo $documento->result();
	?>
	<?php
	
	//Aquí verificaremos si el archivo se encuentra marcado como activo o no, de no estarlo no se mostrará nada.
	if($doc->activo == 1){
	?>
	<div id="topmenufile">
		<nav class="navbar navbar-default">
		  <div class="container-fluid">
		    <!-- Brand and toggle get grouped for better mobile display -->
		    <div class="navbar-header" style="width:100%; text-align:center;">
		      <span style="font-size:18px;padding-top:1em;"><?php echo $doc->nombre_documento." (DOCUMENTO EXTERNO)"; ?></span><br />
		    </div>

		    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		      <ul class="nav navbar-nav navbar-right">
		        <li class="dropdown">
		          <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Opciones&nbsp;&nbsp;&nbsp;<span class='glyphicon glyphicon-option-vertical' aria-hidden='true'><!--</span><span class="caret"></span>--></a>
				  <ul class="dropdown-menu">
		          	<?php if($permiso == "W" || $permiso == "A"){ ?>
						<li><a href=<?php echo "'../uploads/externos/".$doc->archivo."'"; ?>><span class='glyphicon glyphicon-download-alt' aria-hidden='true'></span>&nbsp;&nbsp;&nbsp;Descargar</a></li>
						<?php if($permiso == "A"){ ?>
							<!--<li><a href=<?php echo "'../actualizarDoc/".$doc->id_documento."'"; ?>><span class='glyphicon glyphicon-pencil' aria-hidden='true'></span>&nbsp;&nbsp;&nbsp;Modificar</a></li>-->
						<?php } ?>
		            	<!--<li><a href=<?php echo "'../historialdecambios/".$doc->id_documento."'"; ?>><span class='glyphicon glyphicon-time' aria-hidden='true'></span>&nbsp;&nbsp;&nbsp;Ver historial de cambios</a></li>-->
		            	<?php if($permiso == "A"){ ?>
		            		<li><a href="javascript:if(confirm('¿Realmente desea eliminar el documento?, después de esto ya no habrá vuelta atrás.')){location.href='../eliminarDocExt/<?php echo $doc->id_doc_externo; ?>';}";><span class='glyphicon glyphicon-remove-sign' aria-hidden='true'></span>&nbsp;&nbsp;&nbsp;Eliminar</a></li>
		            	<?php } ?>
		            	<!--<li role="separator" class="divider"></li>
		            	<?php if (isset($revisando)) { //verifica si el usuario actual es uno de los que tiene el documento en checkin
			            	echo "<li><a href='../subirborrador/".$doc->id_documento."'><span class='glyphicon glyphicon glyphicon-open' aria-hidden='true'></span>&nbsp;&nbsp;Subir Borrador</a></li>";
							echo "<li><a href='../checkoutdoc/".$doc->id_documento."'><span class='glyphicon glyphicon-floppy-open' aria-hidden='true'></span>&nbsp;&nbsp;&nbsp;Liberar revisi&oacute;n</a></li>";?>
							<li><a href=<?php echo "\"javascript:if(confirm('¿Realmente desea liberar el documento sin cambios?, se perderán todos sus borradores (en caso de haberlos)')){location.href='../liberarRevSinCambios/".$doc->id_documento."';}\""; ?> ><span class='glyphicon glyphicon-new-window' aria-hidden='true'></span>&nbsp;&nbsp;&nbsp;Liberar revision sin cambios</a></li>
						<?php	//echo "<li><a href='javascript:if(confirm(\"¿Realmente desea Liberar la revisión sin cambios?, se perderan TODOS LOS BORRADORES QUE HAYA SUBIDO)\"){location.href=\"../liberarRevSinCambios/".$doc->id_documento."\";}'>Liberar revisi&oacute;n sin cambios</a></li>";
						}else{
							echo "<li><a href='../checkin/".$doc->id_documento."'><span class='glyphicon glyphicon-floppy-save' aria-hidden='true'></span>&nbsp;&nbsp;&nbsp;Pasar a revisi&oacute;n</a></li>";
						}?>-->
		            <?php } ?>
		            
		          </ul>
		        </li>
		      </ul>
		    </div><!-- /.navbar-collapse -->
		  </div><!-- /.container-fluid -->
		</nav>
	</div>
	<div id="documento">
	<?php
		//soffice --headless --convert-to pdf *.txt //comando con el que se transforman a pdf los archivos de office, para poder convertirlos es requerido lo siguiente:
		//yum install libreoffice
		//yum install openoffice.org-headless
		//se mostrara la vista (el pdf) correspondiente al documento
		echo "<embed src='http://calidad.tpitic.com.mx/SGCPITIC/uploads/externos/".$doc->vista_archivo."' type='application/pdf' width='800' height='600'></embed>";
	}// fin de if($doc->activo == 1)
	else{
		echo "<h2>No se ha encontrado el documento solicitado, asegurese que no haya sido eliminado del sistema</h2>";
	}
	?>
	</div>
</div><!--div center