<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class VerificarLoginController extends CI_Controller{
	function __construct(){
		parent::__construct();
		$this->load->model('LoginModel', '', TRUE);
		$this->config->set_item('language', 'spanish');
	}

	function index(){
		$this->load->library('form_validation');
		$this->form_validation->set_rules('usuario', 'Usuario', 'trim|required');
   		$this->form_validation->set_rules('password', 'Password', 'trim|required|callback_checkDatabase');
   		if($this->form_validation->run() == false){
	     	//echo "Validacion de campos fallida.  Usuario redirigido a pagina de login";
	     	$this->load->view('templates/header');
	    	$this->load->view('documentos/login');
	    	$this->load->view('templates/footer');
	   	}else{
	     //Go to private area
	     redirect('home', 'refresh');
	   }
	}

	function checkDatabase($password){
		$username = $this->input->post('usuario');
		$result = $this->LoginModel->login($username, $password);
		if($result){
			$sess_array = array();
			foreach($result as $row){
				$sess_array = array(
					'id_usuario' => $row->id_usuario,
					'nombre' => $row->nombre,
					'usuario' => $row->usuario,
					'permiso' => $row->permiso,
					'logged_in' => true
					);
				$this->session->set_userdata($sess_array);
				return true;
			}

			
		}else{
			$this->form_validation->set_message('checkDatabase', 'Usuario o Password no válidos');
			return false;
		}
	}
}