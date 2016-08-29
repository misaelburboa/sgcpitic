<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DocumentosExternosModel extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->library('table');
	}
	
	function addDocumentExterno($newDocumentData){
		if($this->db->insert('docs_externos',$newDocumentData)){
			return TRUE;
		}else{
			echo $this->db->error_message();
		}
	}
	
	public function getLastDocumentExternoID(){
		$this->db->select('MAX(id_doc_externo) as id_doc_externo');
		$this->db->from('docs_externos');
		$results = $this->db->get();
		$num_results = $results->num_rows();
		if($num_results > 0){
			return $results->result()[0]->id_doc_externo; 
		}
	}
	
	public function getDocumentExt($attr, $target){
		$this->db->select('d.*, date_format(d.fecha_captura, \'%d-%m%-%Y\') as fecha_captura');
		$this->db->from('docs_externos d');
		if($this->session->userdata('permiso') != "A"){ //si es administrador no aplican estos filtros
			$this->db->join('rel_docs_externos_puestos rdep', 'd.id_doc_externo = rdep.id_doc_ext', 'inner');
			$this->db->join('usuarios u','u.id_puesto = rdep.id_puesto', 'inner');
			$this->db->where('u.usuario = \''.$this->session->userdata('usuario').'\'');
		}
		$this->db->where($attr."= '".$target."'");
		$results = $this->db->get();
		$num_results = $results->num_rows();
		if($num_results > 0){
			return $results;
		}else{
			return $num_results;
		}
	}
	
	public function asignarDocumentoExternoAPuesto($documento, $puesto){
		$datos['id_doc_ext'] = $documento;
		$datos['id_puesto'] = $puesto;
		$result = $this->db->insert('rel_docs_externos_puestos', $datos);
		if($result){
			return true;
		}
	}
	
	function searchDocumentExterno($target){
		//si es administrador que muestre toooodos los documentos.
		if($this->session->userdata('permiso') == "A"){
			$this->db->select("d.*, DATE_FORMAT(d.fecha_captura, '%d-%m-%Y') as fecha_creacion");
			$this->db->from('docs_externos d');
			if(is_numeric($target)){//si es numerico buscará por el id del documento
				$where = "d.activo = '1' AND (
					d.nombre_documento LIKE '%".$target."%'  
					OR d.archivo LIKE '%".$target."%')";
				$this->db->where($where);
			}else{
				$where = "d.activo = '1' AND (
					d.nombre_documento LIKE '%".$target."%' 
					OR d.archivo LIKE '%".$target."%')";
				$this->db->where($where);
			}
			echo $this->db->last_query();
		}else{
			//Si no es usuario administrador solo le mostrará a los que tenga permiso
			$this->db->select("d.*, DATE_FORMAT(d.fecha_captura, '%d-%m-%Y') as fecha_creacion");
			$this->db->from('docs_externos d');
			$this->db->join('rel_docs_externos_puestos rdep', 'd.id_doc_externo=rdep.id_doc_ext', 'inner');
			$this->db->join('usuarios u','u.id_puesto = rdep.id_puesto', 'inner');
			$this->db->where('u.usuario = \''.$this->session->userdata('usuario').'\'');
			if(is_numeric($target)){//si es numerico buscará por el id del documento
				$where = "d.activo = '1' AND (
					d.nombre_documento LIKE '%".$target."%' 
					OR d.archivo LIKE '%".$target."%')";
				$this->db->where($where);
			}else{
				$where = "d.activo = '1' AND (
					d.nombre_documento LIKE '%".$target."%' 
					OR d.archivo LIKE '%".$target."%')";
				$this->db->where($where);
			}
		}

		$resultados = $this->db->get();
		//echo $this->db->last_query();
		$num_results = $resultados->num_rows();
		if($num_results > 0){
			return $resultados;
		}else{
			return false;
		}
	}
	
	function searchUsersGrantsDocument($id_documento_ext){
		//Se ejecutará este query:
		$this->db->select("u.usuario, u.nombre, u.envio_correo, u.correo");
		$this->db->from("usuarios u");
		$this->db->join("rel_docs_externos_puestos rdep", "u.id_puesto = rdep.id_puesto", "inner");
		$this->db->join("docs_externos d", "rdep.id_doc_ext = d.id_doc_externo");
		$this->db->where("d.id_doc_externo =".$id_documento_ext);
		$resultados = $this->db->get();
		
		$num_results = $resultados->num_rows();
		if($num_results > 0){
			//$tmpl = array('table_open' => '<table border=1 id="documentos" cellpadding=2 cellspacing=1>');
			//$headers_table = array('Documento','ID Calidad', 'Revisión', 'Generado por', 'Tiempo de Retención', 'Responsable', 'Nom. Archivo');
			//$this->table->set_template($tmpl);
			//$this->table->set_heading($headers_table);
			//return $table = $this->table->generate($query);
			return $resultados;
		}else{
			return false;
		}
	}
	
	function deleteDocumentExt($id_doc_ext){
		$desactivar_doc = array('activo' => 0);
		$this->db->set($desactivar_doc);
		$this->db->where('id_doc_externo', $id_doc_ext);
		$query = $this->db->update('docs_externos');

		if($query){
			$resultado['state'] = true;
			$resultado['message'] = "Se ha eliminado el documento externo del SGC.";
			return $resultado;
		}else{
			$resultado['state'] = false;
			$resultado['message'] = $this->db->_error_message();
			return $resultado;
		}
	}
}
?>