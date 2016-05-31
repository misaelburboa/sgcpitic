<?php
defined('BASEPATH') OR exit('No se permite el acceso directo a los scripts');
class UsuariosController extends CI_Controller{
	function __construct(){
		parent::__construct();
		$this->load->model('DocumentosModel');
		$this->load->model('UsuariosModel');
		$this->load->library('form_validation');
	}

	public function index($pageRequested){}

	public function agregarUsuarioForm(){
		if($this->session->userdata('logged_in') && $this->session->userdata('permiso') == "A"){
			$datos['puestos'] = $this->DocumentosModel->getPuestos();
			$datos['permisos'] = $this->UsuariosModel->getCatalogoPermisosUsuarios();
			$this->load->view('templates/header.php');
			$this->load->view('templates/left_menu');
			$this->load->view('usuarios/alta_usuario', $datos);
			$this->load->view('templates/footer.php');
		}else{
	      //Si no hay sesión o no tiene permiso se le hace saber;
			$datos['texto1'] = "Página Restringida";
			$datos['texto2'] = "Es posible que usted no haya iniciado sesión o no tenga permiso a esta área";
	     	$this->load->view('templates/header.php');
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
			'no_empleado' => $this->input->post('num_empleado')
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
				$this->load->view('templates/header.php');
				$this->load->view('templates/mensaje_generico', $datos);
				$this->load->view('templates/footer.php');
			}
		}else{
			$datos['texto1'] = "Error";
			$datos['texto2'] = validation_errors();
			$this->load->view('templates/header.php');
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
				$this->load->view('templates/header.php');
				$this->load->view('usuarios/permiso_a_documentos', $jobs);
				$this->load->view('templates/footer.php');
			}
		}else{
	      //Si no hay sesión o no tiene permiso se le hace saber;
			$datos['texto1'] = "Página Restringida";
			$datos['texto2'] = "Es posible que usted no haya iniciado sesión o no tenga permiso a esta área";
	     	$this->load->view('templates/header.php');
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
}