<style type="text/css">
#center{
	margin-left: 20%;
	width: 80%;
	float: left;
	padding-left:2em;
	padding-right:2em;
}
</style>
<div id="center">
	<h2>&iexcl;Bienvenido <?php echo $this->session->userdata('nombre'); ?>!</h2>
	<?php
	echo $tabla; ?>
</div>