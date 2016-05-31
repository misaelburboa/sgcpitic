<html>
</head>
	<title>Notificaci&oacute;n de cambio</title>
	<style type="text/css">
		#contenedor{
			margin: 0 auto;
			width: 750px;
			border:solid black 1px;
			text-align: center;
			padding: 2em;
			padding-left: 3em;
			padding-right: 3em;
			font-family: Arial;
		}

		table{border: solid black 1px;border-collapse: collapse;}
		td{border: solid black 1px; padding: 3px; padding-left: 5px;}

	</style>
</head>
<body>
	<div id="contenedor" >
		<div style="text-align:right;"><?php echo date('d/m/Y'); ?></div>
		<span id="title">
			<h2>Notificaci&oacute;n de cambio en documento</h2>
		</span>
		<div id='verAnt' style='float:left;'>Version Anterior:</div>
		<div id='verNue' style='float:right;'>Nueva Versión:</div>
		<section id="contenido" >
			<table style="margin: 0 auto; width:100%;">
				<tr>
					<td>Documento:</td>
					<td>$nombre_documento</td>
				</tr>
				<tr>
					<td>Descripci&oacute;n del cambio:</td>
					<td>$desc_cambio</td>
				</tr>
				<tr>
					<td>Antes del cambio:</td>
					<td>$antes_cambio</td>
				</tr>
				<tr>
					<td>Después del cambio:</td>
					<td>$despues_cambio</td>
				</tr>
			</table>
		</section><br /><br /><br />
		<footer style="text-align:left;">
			Cualquier duda o aclaraci&ocaute;n favor de enviarlo a:<br /><br />
			Juli&aacute;n M. Rosales Valenzuela<br />
			<strong>Coordinador de Calidad</strong><br />
			<a href="mailto:jrosales@tpitic.com.mx">jrosales@tpitic.com.mx</a><br /><br />

			Caleb Misael Burboa Mendoza<br />
			<strong>Auxiliar de Calidad</strong><br />
			<a href="mailto:cmburboa@tpitic.com.mx">cmburboa@tpitic.com.mx</a><br /><br />
			<span style="font-size:10px;">DT/CAL ISO18 Rev. 2, 12-05</span>
		</footer>
	</div>
</body>
</html>