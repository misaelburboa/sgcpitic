<style>
#content{
	margin: 0 auto;
	float:left;
	width:80%; 
	font-size:14px; 
	overflow: auto;
	}
#center{
	margin-left:5%;
	width: 50%;
	float: left;
	text-align: left;
}

</style>
<script>
function validarForm(pass1, pass2){
	if(pass1.validity.patternMismatch && pass1.validity.patternMismatch){
		pass1.validity.patternMismatch
		pass1.setCustomValidity('el password debe contener al menos 8 caracteres alfanumericos, o sea un mezcla de numeros y letras');
	}else{
		if (pass1.value != pass2.value) {
			pass1.setCustomValidity('Los passwords no concuerdan favor de verificarlos');
			pass2.setCustomValidity('Los passwords no concuerdan favor de verificarlos');
		} else {
			//pass1.setCustomValidity('');
			document.getElementById('cambiarPassword').submit();
		}
	}
}
</script>
<div id="content">
	<div id="center">
		<?php echo form_open("cambiarPassword", array("id"=>"cambiarPassword", "onSubmit" => "javascript: return false")); ?>
		<?php
		$passActual = array(
			"type" => "password",
			"id" => "passActual",
			"name" => "passActual",
			"class" => "form-control",
			"required" => true,
			"value" => ""
		);
		
		$pass1 = array(
			"type" => "password",
			"id" => "pass1",
			"name" => "pass1",
			"class" => "form-control",
			"pattern" => "^(?=(.*\d){1})(.*\S)(?=.*[a-zA-Z\S])[0-9a-zA-Z\S]{7,}$",
			"required" => "required",
			"value" => ""
		);
		
		$pass2 = array(
			"type" => "password",
			"id" => "pass2",
			"name" => "pass2",
			"class" => "form-control",
			"pattern" => "^(?=(.*\d){1})(.*\S)(?=.*[a-zA-Z\S])[0-9a-zA-Z\S]{7,}$",
			"required" => "required",
			"value" => ""
		);
		?>
		<?php echo form_label("Escriba su password actual", "passActual").form_password($passActual); ?><br /><br />
		<?php echo form_label("Escriba su password nuevo", "pass1").form_password($pass1); ?>
		<?php echo form_label("Confirme el password nuevo", "pass2").form_password($pass2); ?><br />
		<?php echo form_submit("", "Cambiar Password", array("class" => "btn btn-success", "onClick" => "javascript:validarForm(this.form.pass1, this.form.pass2)")); ?>
		<?php form_close()?>
	</div>
</div>