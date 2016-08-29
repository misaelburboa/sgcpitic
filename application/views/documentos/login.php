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
  #divLogin{
    margin: 0 auto;
    padding: 2em;
    padding-top:1em;
    width:40%;
    height: auto;
  }
</style>
<h1>Bienvenido a SGCPitic</h1>
<div id="divLogin">
<?php if(validation_errors()){ ?>
  <div class="alert alert-warning">
    <?php echo validation_errors(); ?>
  </div>
<?php } ?>
  <?php echo form_open('verifylogin'); ?>
    <div class="row" style="margin-bottom:5px;">
      <div class="col-xs-2">
        <label for="usuario">Usuario:</label>
      </div>
      <div class="col-xs-10">
        <input type="text" id="usuario" name="usuario" class="form-control"/>
      </div>
    </div>
    <div class="row" style="margin-bottom:5px;">
      <div class="col-xs-2">
        <label for="password">Password:</label>
      </div>
      <div class="col-xs-10">
        <input type="password" id="password" name="password" class="form-control"/>
      </div>
    </div>
    <div class="row" >
        <input type="submit" name="entrar" value="Entrar" class="btn btn-success"/>
    <div>
  </form>
</div>