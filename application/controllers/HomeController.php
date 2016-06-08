<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
//session_start(); //we need to call PHP's session object to access it through CI
class HomeController extends CI_Controller {

  function __construct(){
    parent::__construct();
	$this->load->model('DocumentosModel');
  }

  function index(){
    if($this->session->userdata('logged_in')){
		$session_data = $this->session->userdata('logged_in');
		$data['username'] = $session_data['username'];
		$data['tabla'] = $this->UltimosCambiosEnDocumentos();
		$this->load->view('templates/header');
		$this->load->view('templates/left_menu.php');
		$this->load->view('documentos/home', $data);
		$this->load->view('templates/footer');
    }else{
      //Si no hay sesión se redirecciona la página;
      redirect('login', 'refresh');
    }
  }
  
	public function UltimosCambiosEnDocumentos(){
		if($documentos = $this->DocumentosModel->obtenerUltimosCambiosDocumentos()){
			//Se genera la tabla
			$tpl = array (
					'table_open' => '<table border=1 id="ultimosCambios" cellpadding=2 cellspacing=1 width=100%>',
					'row_start'     => '<tr bgcolor="#B5FFB4">',
					'row_alt_start' => '<tr bgcolor="white">',
					'row_end'             => '</tr>'
					);
			$this->table->set_heading(array('ID Calidad', 'Documento','Fecha Cambio', 'Causa del Cambio', 'Desc. del Cambio', 'Usuario', 'Rev. Anterior', 'Rev. Actual'));
			$i=1;
			foreach ($documentos->result() as $doc){
				$this->table->set_template($tpl);
				$this->table->add_row($doc->id_calidad, "<a href='".base_url('document')."/".$doc->id_documento."'>".$doc->nombre_documento."</a>", substr($doc->fecha_cambio,0,10), $doc->causa_cambio, $doc->desc_cambio, $doc->usuario, $doc->revision_ant, $doc->revision_actual);
				$i++;
			}
			
			$cambios = "AVISO, se listan los cambios en la documentación de los últimos 30 días:<br /><br />";
			$cambios .= $this->table->generate();
			return $cambios;
		}else{
			return "No ha habido cambios en la documentación en los últimos 30 días";
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