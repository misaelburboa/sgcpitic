<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class LoginController extends CI_Controller{
	function __construct(){
		parent::__construct();
	}

	function index(){
		$this->load->helper(array('form'));
		$this->load->view('templates/includes');
		$this->load->view('documentos/login');
		$this->load->view('templates/footer');
	}
	
	function loginUrl(){
		$pagina['pageReq'] = $this->session->flashdata('pagReq');
		$this->load->helper(array('form'));
		$this->load->view('templates/includes');
		$this->load->view('documentos/loginUrl', $pagina);
		$this->load->view('templates/footer');
	}
}
?>