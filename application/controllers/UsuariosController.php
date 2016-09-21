<?php
defined('BASEPATH') OR exit('No se permite el acceso directo a los scripts');
class UsuariosController extends CI_Controller{
	function __construct(){
		parent::__construct();
		$this->load->model('DocumentosModel');
		$this->load->model('UsuariosModel');
		$this->load->library('pagination');
		$this->load->library('form_validation');
		$this->load->library('email');
	}

	public function index($pageRequested){}

	public function agregarUsuarioForm(){
		if($this->session->userdata('logged_in') && $this->session->userdata('permiso') == "A"){
			$datos['puestos'] = $this->DocumentosModel->getPuestos();
			$datos['permisos'] = $this->UsuariosModel->getCatalogoPermisosUsuarios();
			$this->load->view('templates/includes');
			$this->load->view('templates/navigation-bar.php');
			$this->load->view('templates/left_menu');
			$this->load->view('usuarios/alta_usuario', $datos);
			$this->load->view('templates/footer.php');
		}else{
	      //Si no hay sesión o no tiene permiso se le hace saber;
			$datos['texto1'] = "Página Restringida";
			$datos['texto2'] = "Es posible que usted no haya iniciado sesión o no tenga permiso a esta área";
	     	$this->load->view('templates/includes');
			$this->load->view('templates/mensaje_generico', $datos);
			$this->load->view('templates/footer.php');
	    }
	}

	public function guardarUsuario(){
		$lastUserId = $this->UsuariosModel->getLastUserID();
		$lastUserId++;
		$datos = array(
			'id_usuario' => $lastUserId,
			'usuario' => $this->input->post('usuario'),
			'nombre' => $this->input->post('nombre')." ".$this->input->post('apellidos'),
			'password' => md5($this->input->post('passwd')),
			'id_puesto' => $this->input->post('puesto'),
			'permiso' => $this->input->post('permiso'),
			'no_empleado' => $this->input->post('num_empleado'),
			'envio_correo' => $this->input->post('correo'),
			'correo' => $this->input->post('direccion_correo')
		);

		//Creamos la validación para verificar que no exista un documento con el mismo ID de Calidad
		$this->form_validation->set_rules('usuario', 'usuario', 'callback_checkUsuario');
		$this->form_validation->set_rules('num_empleado', 'num_empleado', 'callback_checkNumEmpleado');
		//Verificamos las validaciones
		if($this->form_validation->run() == true){
			$result = $this->UsuariosModel->guardarUsuario($datos);
			if($result){
				$datos['texto1'] = "¡Usuario agregado correctamente!";
				$datos['texto2'] = "";
				$this->load->view('templates/includes');
				$this->load->view('templates/navigation-bar.php');
				$this->load->view('templates/mensaje_generico', $datos);
				$this->load->view('templates/footer.php');
			}
		}else{
			$datos['texto1'] = "Error";
			$datos['texto2'] = validation_errors();
			$this->load->view('templates/includes.php');
			$this->load->view('templates/navigation-bar.php');
			$this->load->view('templates/mensaje_generico', $datos);
			$this->load->view('templates/footer.php');
		}
	}

	public function checkUsuario($usuario){
		$num_results = $this->UsuariosModel->getUsuario('usuario', $usuario);
		$num_usuarios = isset( $num_results->num_rows ) ? $num_results->num_rows : 0;
		if($num_usuarios > 0){
			$this->form_validation->set_message('checkUsuario', 'El usuario %s ya existe');
			return false;
		}else{
			return true;
		}
	}

	public function checkNumEmpleado($numEmpleado){
		$num_results = $this->UsuariosModel->getUsuario('no_empleado', $numEmpleado);
		$num_usuarios = isset( $num_results->num_rows ) ? $num_results->num_rows : 0;
		if($num_usuarios > 0){
			$this->form_validation->set_message('checkNumEmpleado', 'El número de empleado '.$numEmpleado.' ya esta registrado');
			return false;
		}else{
			return true;
		}
	}

	public function getPuestos(){
		if($this->session->userdata('logged_in') && $this->session->userdata('permiso') == "A"){
			$jobs['puestos'] = $this->UsuariosModel->getJobs();
			$num_jobs = isset($jobs['puestos']->num_rows) ? $jobs['puestos']->num_rows : 0;
			if($num_jobs > 0){
				$this->load->view('templates/includes.php');
				$this->load->view('templates/navigation-bar.php');
				$this->load->view('usuarios/permiso_a_documentos', $jobs);
				$this->load->view('templates/footer.php');
			}
		}else{
	      //Si no hay sesión o no tiene permiso se le hace saber;
			$datos['texto1'] = "Página Restringida";
			$datos['texto2'] = "Es posible que usted no haya iniciado sesión o no tenga permiso a esta área";
	     	$this->load->view('templates/includes.php');
			$this->load->view('templates/mensaje_generico', $datos);
			$this->load->view('templates/footer.php');
	    }
	}
	
	public function getPuestosExternos(){
		if($this->session->userdata('logged_in') && $this->session->userdata('permiso') == "A"){
			$jobs['puestos'] = $this->UsuariosModel->getJobs();
			$num_jobs = isset($jobs['puestos']->num_rows) ? $jobs['puestos']->num_rows : 0;
			if($num_jobs > 0){
				$this->load->view('templates/includes.php');
				$this->load->view('templates/navigation-bar.php');
				$this->load->view('usuarios/permiso_a_documentos_externos', $jobs);
				$this->load->view('templates/footer.php');
			}
		}else{
	      //Si no hay sesión o no tiene permiso se le hace saber;
			$datos['texto1'] = "Página Restringida";
			$datos['texto2'] = "Es posible que usted no haya iniciado sesión o no tenga permiso a esta área";
	     	$this->load->view('templates/includes.php');
			$this->load->view('templates/mensaje_generico', $datos);
			$this->load->view('templates/footer.php');
	    }
	}

	public function getPermisosActuales($id_puesto){
		$actuales = $this->UsuariosModel->getCurrentJobsDocuments($id_puesto);
		$i = 0;
		foreach($actuales->result() as $act){
			$arrDocsActuales[$i]['id_documento'] = $act->id_documento;
			$arrDocsActuales[$i]['nombre_documento'] = $act->nombre_documento;
			$arrDocsActuales[$i]['id_calidad'] = $act->id_calidad;
			$i++;
		}

		header('Content-Type: application/json');
		print_r(json_encode($arrDocsActuales));
	}
	
	public function getPermisosActualesExt($id_puesto){
		$actuales = $this->UsuariosModel->getCurrentJobsDocumentsExt($id_puesto);
		$i = 0;
		foreach($actuales->result() as $act){
			$arrDocsActuales[$i]['id_doc_externo'] = $act->id_doc_externo;
			$arrDocsActuales[$i]['nombre_documento'] = $act->nombre_documento;
			$i++;
		}

		header('Content-Type: application/json');
		print_r(json_encode($arrDocsActuales));
	}

	public function getPermisosDisponibles($id_puesto){
		
		$disponibles = $this->UsuariosModel->getAvailableDocuments($id_puesto);
		$i = 0;
		$arrDocsActuales = array();
		foreach($disponibles->result() as $act){
			$arrDocsActuales[$i]['id_documento'] = $act->id_documento;
			$arrDocsActuales[$i]['nombre_documento'] = $act->nombre_documento;
			$arrDocsActuales[$i]['id_calidad'] = $act->id_calidad;
			$i++;
		}

		header('Content-Type: application/json');
		print_r(json_encode($arrDocsActuales));
	}
	
	public function getPermisosDisponiblesExt($id_puesto){
		
		$disponibles = $this->UsuariosModel->getAvailableDocumentsExt($id_puesto);
		$i = 0;
		$arrDocsActuales = array();
		foreach($disponibles->result() as $act){
			$arrDocsActuales[$i]['id_documento'] = $act->id_doc_externo;
			$arrDocsActuales[$i]['nombre_documento'] = $act->nombre_documento;
			$i++;
		}

		header('Content-Type: application/json');
		print_r(json_encode($arrDocsActuales));
	}

	public function quitarPermisosADocumento($id_puesto, $id_documento){
		$exito = $this->UsuariosModel->removeDocumentAccess($id_puesto, $id_documento);
		if($exito){
			header('Content-Type: application/json');
			print_r(json_encode(array("success" => "true")));
		}else{
			header('Content-Type: application/json');
			print_r(json_encode(array("success" => "false")));
		}
	}
	
	public function quitarPermisosADocumentoExt($id_puesto, $id_documento){
		$exito = $this->UsuariosModel->removeDocumentAccessExt($id_puesto, $id_documento);
		if($exito){
			header('Content-Type: application/json');
			print_r(json_encode(array("success" => "true")));
		}else{
			header('Content-Type: application/json');
			print_r(json_encode(array("success" => "false")));
		}
	}

	public function agregarPermisosADocumento($id_puesto, $id_documento){
		$exito = $this->UsuariosModel->grantDocumentAccess($id_puesto, $id_documento);
		if($exito){
			header('Content-Type: application/json');
			print_r(json_encode(array("success" => "true")));
		}else{
			header('Content-Type: application/json');
			print_r(json_encode(array("success" => "false")));
		}
	}
	
	public function agregarPermisosADocumentoExt($id_puesto, $id_documento){
		$exito = $this->UsuariosModel->grantDocumentAccessExt($id_puesto, $id_documento);
		if($exito){
			header('Content-Type: application/json');
			print_r(json_encode(array("success" => "true")));
		}else{
			header('Content-Type: application/json');
			print_r(json_encode(array("success" => "false")));
		}
	}
	
	public function manageUsers(){
		$this->output->cache(2);
		if($this->session->userdata('logged_in') && $this->session->userdata('permiso') == "A"){
			$datos['puestos'] = $this->DocumentosModel->getPuestos();
			$datos['permisos'] = $this->UsuariosModel->getCatalogoPermisosUsuarios();
			$this->load->view('templates/includes.php');
	     	$this->load->view('templates/navigation-bar.php');
			$this->load->view('usuarios/administrar_usuarios.php', $datos);
			$this->load->view('templates/footer.php');
		}else{
	      //Si no hay sesión o no tiene permiso se le hace saber;
			$datos['texto1'] = "Página Restringida";
			$datos['texto2'] = "Es posible que usted no haya iniciado sesión o no tenga permiso a esta área";
	     	$this->load->view('templates/includes.php');
			$this->load->view('templates/mensaje_generico', $datos);
			$this->load->view('templates/footer.php');
	    }
	}
	
	public function getUsers($desde){
		if($this->session->userdata('logged_in') && $this->session->userdata('permiso') == "A"){
			$str ="";
			foreach($_GET as $k => $v){
				$str.= $k."=".$v."&";
			}
			$config = array();
			$config['per_page'] = 15;
			$desde = (null ==! $this->uri->segment(2)) ? $this->uri->segment(2) : 0;	
			$datos['listaUsuarios'] = $this->UsuariosModel->getUsers($_GET, $config['per_page'], $desde);
			$datos['getQueryTotal'] = $this->UsuariosModel->getQueryTotal($_GET, $config['per_page'], $desde);
			if($datos['listaUsuarios'] != false){ 
				$num_reg = $datos['getQueryTotal']->num_rows();
			}else{
				$num_reg=0;
			}
			$config['base_url'] = base_url().'getusers/';
			$config['total_rows'] = $num_reg;
			$config['uri_segment'] = 2;
			$config['first_url'] = 0 . "?" . $str;
			$config['page_query_string'] = FALSE;
			$config['cur_tag_open'] = '<li class="active"><a href="#">';
			$config['cur_tag_close'] = '</a></li>';
			$config['num_tag_open'] = '<li>';
			$config['num_tag_close'] = '</li>';
			$config['next_link'] = '&raquo;';
			$config['next_tag_open'] = '<li>';
			$config['next_tag_close'] = '</li>';
			$config['prev_link'] = '&laquo;';
			$config['prev_tag_open'] = '<li>';
			$config['prev_tag_close'] = '</li>';
			$config['reuse_query_string'] = true;
			$this->pagination->initialize($config);
			
			$datos['paginacion'] =  $this->pagination->create_links();
			if($num_reg > 0){
				$tpl = array (
					'table_open' => '<table border=1 id="ultimosCambios" cellpadding=2 cellspacing=1 width=100%>',
					'heading_row_start'   => '<tr style="background-color: #2ecc71; font-weight:bold; color:white; padding:1em;">',
					'heading_row_end'     => '</tr>',
					'heading_cell_start'  => '<th style="text-align:center;border: 2px solid black; padding:0.5em; font-size:15px;">',
					'heading_cell_end'    => '</th>',
					'row_start'     => '<tr bgcolor="#DBF6ED">',
					'row_alt_start' => '<tr bgcolor="white">',
					'row_end'             => '</tr>',
					'cell_start'      => '<td style="padding:0.3em;text-align:center;font-size:14px;">',
					'cell_end'        => '</td>',
					'cell_alt_start'      => '<td style="padding:0.3em;text-align:center;font-size:14px;">',
					'cell_alt_end'        => '</td>',
				);
				$this->table->set_heading(array(
				"No.<br /><span style='color:white;padding-left:1em;padding-right:1em;' class='glyphicon glyphicon-star'></span>",
				"Usuario<br /><span style='color:white;padding-left:1em;padding-right:1em;' class='glyphicon glyphicon-user'></span>",
				"Nombre<br /><span style='color:white;padding-left:1em;padding-right:1em;'><strong></strong></span>",
				"Correo<br /><span style='color:white;padding-left:1em;padding-right:1em;' class='glyphicon glyphicon-envelope'></span>",
				"Puesto<br /><span style='color:white;padding-left:1em;padding-right:1em;'><strong></strong></span>",
				"Permiso<br /><span style='color:white;padding-left:1em;padding-right:1em;' class='glyphicon glyphicon-lock'></span>",
				"Notif.<br /><span style='color:white;' class='glyphicon glyphicon-bell'></span>",
				"Editar<br /><span style='color:white;' class='glyphicon glyphicon-cog'></span>",
				"Contraseña<br /><span style='color:white;' class='glyphicon glyphicon-refresh'></span>",
				"Eliminar<br /><span style='color:white;' class='glyphicon glyphicon-remove-sign'></span>"));
				$this->table->set_template($tpl);
				
				$i = 0;
				foreach($datos['listaUsuarios']->result() as $u){
					if($u->notificaciones == "SI"){$notificaciones = "<span style='color:green;' class='glyphicon glyphicon-ok'></span>";}else{$notificaciones = "<span style='color:red;' class='glyphicon glyphicon-remove'></span>";}
					$usuario = "<a href='".base_url()."usuario/".$u->id_usuario."'>".$u->usuario."</a>";
					$editar = "<a href='".base_url()."usuario/".$u->id_usuario."'><span style='color:black;' class='glyphicon glyphicon-pencil'></span></a>";
					$resetPass = "<a href='javascript:if(confirm(\"¿Realmente desea reestablecer esta contraseña?.\")){location.href=\"../resetPassword/".$u->id_usuario."\";}'><span style='color:blue;' class='glyphicon glyphicon-refresh'></span></a>";
					$eliminar = "<a href='javascript:if(confirm(\"¿Realmente desea eliminar este usuario?, después de esto ya no habrá vuelta atrás.\")){location.href=\"./eliminarUsuario/".$u->id_usuario."\";}'><span style='color:red;' class='glyphicon glyphicon-remove-sign'></span></a>";
					$this->table->add_row($u->no_empleado, $usuario, $u->nombre, $u->correo, $u->nombre_puesto, $u->titulo_permiso, $notificaciones, $editar, $resetPass, $eliminar);
					$i++;
				}
				$datos['tablaUsuarios'] = $this->table->generate();
				$datos['leyenda'] = "<div style='width:100%;text-align:left;'>Se encontr&oacute; una coincidencia con un total de <span style='font-weight:900;color:red;'>".$num_reg."</span> registro(s).<br />Mostrando resultados del ".($desde+1)." al ".($desde+$i)."</div>";
				$this->load->view('templates/includes.php');
				$this->load->view('templates/navigation-bar.php');
				$this->load->view('usuarios/tabla_busqueda_usuarios.php', $datos);
				$this->load->view('templates/footer.php');
			}else{
				$datos['texto1'] = ":/";
				$datos['texto2'] = "No se han encontrado coincidencias con esos filtros de búsqueda";
				$this->load->view('templates/includes.php');
				$this->load->view('templates/navigation-bar.php');
				$this->load->view('templates/mensaje_generico', $datos);
				$this->load->view('templates/footer.php');
			}
			
		}else{
	      //Si no hay sesión o no tiene permiso se le hace saber;
			$datos['texto1'] = "Página Restringida";
			$datos['texto2'] = "Es posible que usted no haya iniciado sesión o no tenga permiso a esta área";
	     	$this->load->view('templates/includes.php');
			$this->load->view('templates/mensaje_generico', $datos);
			$this->load->view('templates/footer.php');
	    }
	}
	
	public function usuario($id_usuario){
		$datos['datos'] = $this->UsuariosModel->getUsuario('id_usuario', $id_usuario);
		$datos['puestos'] = $this->DocumentosModel->getPuestos();
		$datos['permisos'] = $this->UsuariosModel->getCatalogoPermisosUsuarios();
		$this->load->view('templates/includes.php');
		$this->load->view('templates/navigation-bar.php');
		$this->load->view('usuarios/usuario.php', $datos);
		$this->load->view('templates/footer.php');
	}
	
	public function actualizarUsuario(){
		$datos_usuario = array(
		'usuario' =>  $this->input->post('usuario'),
		'nombre' => $this->input->post('nombre'),
		'no_empleado' => $this->input->post('num_empleado'),
		'id_puesto' => $this->input->post('puesto'),
		'correo' => $this->input->post('direccion_correo'),
		'permiso' => $this->input->post('permiso'),
		'envio_correo' => $this->input->post('correo')
		);
		$result = $this->UsuariosModel->actualizarUsuario($datos_usuario);
		if($result['state']){
			$texto['texto1'] = "¡Éxito!";
			$texto['texto2'] = "Se ha actualizado la información del usuario correctamente";
			$this->load->view('templates/includes');
			$this->load->view('templates/navigation-bar');
			$this->load->view('templates/mensaje_generico', $texto);
			$this->load->view('templates/footer');
		}
	}
	
	public function eliminarUsuario($id_usuario){
		$result = $this->UsuariosModel->eliminar_usuario($id_usuario);
		if($result){
			$texto['texto1'] = "¡Éxito!";
			$texto['texto2'] = "Se ha eliminado el usuario correctamente";
			$this->load->view('templates/includes');
			$this->load->view('templates/navigation-bar');
			$this->load->view('templates/mensaje_generico', $texto);
			$this->load->view('templates/footer');
		}else{
			$texto['texto1'] = "¡Error!";
			$texto['texto2'] = "Ocurrió un problema al eliminar el usuario";
			$this->load->view('templates/includes');
			$this->load->view('templates/navigation-bar');
			$this->load->view('templates/mensaje_generico', $texto);
			$this->load->view('templates/footer');
		}
	}
	
		
	function generarPass(){
		//Se define una cadena de caractares. Te recomiendo que uses esta.
		$cadena = "abcdefghijklmnopqrstuvwxyz1234567890";
		//Obtenemos la longitud de la cadena de caracteres
		$longitudCadena=strlen($cadena);
		 
		//Se define la variable que va a contener la contraseña
		$pass = "";
		//Se define la longitud de la contraseña, en mi caso 10, pero puedes poner la longitud que quieras
		$longitudPass=8;
		 
		//Creamos la contraseña
		for($i=1 ; $i<=$longitudPass ; $i++){
			//Definimos numero aleatorio entre 0 y la longitud de la cadena de caracteres-1
			$pos=rand(0,$longitudCadena-1);
		 
			//Vamos formando la contraseña en cada iteraccion del bucle, añadiendo a la cadena $pass la letra correspondiente a la posicion $pos en la cadena de caracteres definida.
			$pass .= substr($cadena,$pos,1);
		}
		return $pass;
	}
	
	public function resetPassword($id_usuario){
		$datos['id_usuario'] = $id_usuario;
		$pasSinEncriptar = $this->generarPass();
		$datos['password'] = md5($pasSinEncriptar);
		$result = $this->UsuariosModel->actualizarUsuarioId($datos);
		$usuario = $this->UsuariosModel->getUsuario('id_usuario', $id_usuario);
		$usuario = $usuario->result()[0];
		if($result){
			$this->enviarMail("Se ha actualizado su password", "su nuevo password es: ".$pasSinEncriptar, $usuario->correo);
			$texto['texto1'] = "¡Éxito!";
			$texto['texto2'] = "Se ha cambiado el password de este usuario correctamente. También se ha enviado correo al usuario con su nuevo password.";
			$this->load->view('templates/includes');
			$this->load->view('templates/navigation-bar');
			$this->load->view('templates/mensaje_generico', $texto);
			$this->load->view('templates/footer');
		}
	}
	
	public function ajustesCuenta($seccion){
		if($this->session->userdata('logged_in')){
			$usuario['usuario'] = $this->UsuariosModel->getUsuario('id_usuario', $this->session->userdata('id_usuario'));
			$usuario['puestos'] = $this->DocumentosModel->getPuestos();
			$this->load->view('templates/includes');
			$this->load->view('templates/navigation-bar');
			$this->load->view('usuarios/list-group_ajustes-usuario');
			$this->load->view('usuarios/'.$seccion, $usuario);
			$this->load->view('templates/footer');
		}else{
			$url = base_url(uri_string());
			$this->session->set_flashdata('pagReq', str_replace('/','-',$url));
			redirect('loginUrl', 'refresh');
		}
	}
	
	public function delete_cache(){
		$CI =& get_instance();
		$path = $CI->config->item('cache_path');

		$cache_path = ($path == '') ? APPPATH.'cache/' : $path;

		$handle = opendir($cache_path);
		while (($file = readdir($handle))!== FALSE) 
		{
			//Leave the directory protection alone
			if ($file != '.htaccess' && $file != 'index.html')
			{
			   @unlink($cache_path.'/'.$file);
			}
		}
		closedir($handle);
	}
	
	public function cambiarPassword(){
		$this->form_validation->set_rules('currentPassword', 'currentPassword', 'callback_currentPassword');
		if($this->form_validation->run() == true){
			$datos['password'] = md5($this->input->post('pass1'));
			$resultado = $this->UsuariosModel->actualizarPassword($datos);
			if($resultado['state'])
			$datos["texto1"] = "¡Exito!";
			$datos["texto2"] = $resultado['message'];
			$this->load->view('templates/includes');
			$this->load->view('templates/navigation-bar');
			$this->load->view('templates/mensaje_generico', $datos);
			$this->load->view('templates/footer');
		}else{
			$datos["texto1"] = "¡Error!";
			$datos["texto2"] = "El password actual no es el correcto.";
			$this->load->view('templates/includes');
			$this->load->view('templates/navigation-bar');
			$this->load->view('templates/mensaje_generico', $datos);
			$this->load->view('templates/footer');
		}
	}
	
	public function currentPassword(){
		$current = $this->UsuariosModel->getCurrentPassword();
		if(md5($this->input->post('passActual')) == $current->result()[0]->password){
			return true;
		}else{
			return false;
		}
	}
	
	public function enviarMail($subject, $body, $to){
		$bodyTpl = 
		"<html>
			<body>".$body.
				"<div>";
					$bodyTpl .= "Cualquier duda o aclaraci&oacute;n favor de enviarlo a:<br /><br />";
					$admins = $this->DocumentosModel->getAdministradores();
					$admins = $admins->result();
					foreach($admins as $ad){
						$bodyTpl .= $ad->nombre."<br />
						<strong>".$ad->nombre_puesto."</strong><br />
						<a href='mailto:".$ad->correo."'>".$ad->correo."</a><br /><br />";
					}
					$bodyTpl .= "<br />Depto. de Calidad <a href='mailto:calidad@tpitic.com.mx'>calidad@tpitic.com.mx</a><br /><br />
					<span style='font-size:10px;'>DT/CAL ISO18 Rev. 2, 12-05</span>
				</div>
			</div>
		</body>
		</html>";
		
		$this->email->from($this->config->item('show_email'), $this->config->item('show_email_text'));
		$this->email->subject($subject);
		$this->email->message($bodyTpl);

		$this->email->to($to);
		if(!$this->email->send(false)) {//al pasarle con parametro el falso no limpia la información del envío de otro modo no se podría mostrar un posible error
		  $textoNotificacionEnvío = "<br />Error al enviar la notificación:<br />".$this->email->print_debugger();
		} else {
			$textoNotificacionEnvío = "<br /><h4>Notificaciones por email enviadas</h4>";
		}
		return $textoNotificacionEnvío;
	}
}