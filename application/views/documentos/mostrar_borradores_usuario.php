<style type="text/css">
  #contenido{
    margin: 0 auto;
    padding: 2em;
    padding-top:1em;
    width:90%;
    height: auto;
    min-height: 510px;
    overflow: hidden;
    /*border:solid gray 1px;*/
    text-align: center;
  }
  #container{
    margin: 0 auto;
    padding: 2em;
    padding-top:1em;
    width:70%;
    height: auto;
  }
  #borradores{
	  margin: 0 auto;
	  width:45%;
  }
  #borradores td{
	  text-align:center;
  }
</style>
<h1>Borradores de este usuario:</h1>
<div id="container">
  <center><hr style="width:100%;"/></center>
  <div style="text-align:left;"><a href="javascript:history.back(-1)"> << Volver</a></div>
  <div id="borradores">
  <table border=1 width="100%">
  <tr><td colspan=2>Lista de borradores</td></tr>
  <?php 
  foreach($borradores->result() as $bor){
    echo "<tr><td><a href='".$ubicacion."/".$bor->archivo_borrador."'>".$bor->archivo_borrador."</a>";
	if($bor->id_usuario == $this->session->userdata("id_usuario")){
		echo " </td><td><a href='".base_url()."eliminar_borrador/".$bor->id_borrador."/".$bor->id_documento."'><img src='".base_url()."img/trash_icon.png' width='40px' height='40px'/></a></td></tr>";
	}else{
		echo "</td></tr>";
	}
  }
  ?>
  </table>
  </div>
</div>