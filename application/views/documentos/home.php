<style type="text/css">
#center{
	margin: 0 auto;
	width: 90%;
	text-align:center;
}
td{
	font-size: 12px;
	text-align: center;
}
</style>
<div id="center">
	<h2>&iexcl;Bienvenido(a) <?php echo $this->session->userdata('nombre'); ?>!</h2>
	<?php
	echo $tabla; ?>
</div>