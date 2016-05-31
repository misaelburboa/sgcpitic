<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class LoginModel extends CI_Model{
	function __construct(){
		parent::__construct();
	}
	function login($username, $password){
		$this->db->select('id_usuario, usuario, password, nombre, permiso');
		$this->db->from('usuarios');
		$this->db->where('usuario', $username);
		$this->db->where('password', md5($password));
		$query = $this->db->get();

		if($query->num_rows() == 1){
			return $query->result();
		}else{
			//echo $this->db->last_query();
			return false;
		}
	}
}
?>