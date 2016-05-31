<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class LoginController extends CI_Controller{
	function __construct(){
		parent::__construct();
	}

	function index(){
		$this->load->helper(array('form'));
		$this->load->view('templates/header');
		$this->load->view('documentos/login');
		$this->load->view('templates/footer');
	}
}
?>