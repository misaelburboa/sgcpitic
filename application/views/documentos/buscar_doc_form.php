<style type="text/css">
#contenido{
	margin: 0 auto 0 auto;
	width:100%; 
	font-size:13px; 
	height: 500px;
}
#center{
	margin-left: 20%;
	width: 80%;
	float: left;
}
</style>
<div id="center">
<?php echo form_open_multipart('searchdocument'); ?>
<?php
	$target = array(
			'name' => 'target',
			'class' => 'form-control',
			'placeholder' => 'Escriba el nombre del documento',
			'width' => '10em'
		);

	$btn_buscar = array(
        'name' => 'button',
        'id' => 'button',
        'value' => 'Buscar',
        'type' => 'submit',
        'class' => 'btn btn-default',
    );
?>

	<div id="contenido">
		<div class="input-group" style="width:50%;margin:0 auto;padding-top:3em;">
			<?php echo form_input($target); ?>
			<span class="input-group-btn">
				<?php// echo form_button($btn_buscar); ?>
				<button name="button" type="submit" id="button" value="Buscar" class="btn btn-default" >
					Buscar <span class='glyphicon glyphicon-search' aria-hidden='true'></span>
				</button>
			</span>
		</div>
		<?php echo form_close(); ?>
	</div>
</div>