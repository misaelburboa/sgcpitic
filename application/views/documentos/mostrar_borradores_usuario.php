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
</style>
<h1>Borradores de este usuario:</h1>
<div id="container">
  <center><hr style="width:100%;"/></center>
  <div style="text-align:left;"><a href="javascript:history.back(-1)"> << Volver</a></div>
  <div id="borradores">
  <?php 
  foreach($borradores->result() as $bor){
    echo "<a href='".$ubicacion."/".$bor->archivo_borrador."'>".$bor->archivo_borrador."</a><br />";
  }
  ?>
  </div>
</div>