<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start(); //we need to call PHP's session object to access it through CI
class HomeController extends CI_Controller {

  function __construct(){
    parent::__construct();
  }

  function index(){
    if($this->session->userdata('logged_in')){
		$session_data = $this->session->userdata('logged_in');
		$data['username'] = $session_data['username'];
		$this->load->view('templates/header');
		$this->load->view('templates/left_menu.php');
		$this->load->view('documentos/home', $data);
		$this->load->view('templates/footer');
    }else{
      //Si no hay sesión se redirecciona la página;
      redirect('login', 'refresh');
    }
  }

  function logout(){
    //destruye la variable logged_in, encargada de verificar si hay una sesión activa, osea que termina la sesión
    $this->session->unset_userdata('logged_in');
    session_destroy();
    redirect('home', 'refresh');
  }
}
?>