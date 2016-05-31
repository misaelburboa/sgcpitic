<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DocumentosModel extends CI_Model{

	function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->library('table');
	}

	function addDocument($newDocumentData){
		if($this->db->insert('documentos',$newDocumentData)){
			return TRUE;
		}else{
			echo $this->db->error_message();
		}
	}

	function searchDocument($target){
		$this->db->select("documentos.*, metodos_compilacion.metodo_compilacion, DATE_FORMAT(documentos.fecha_creacion, '%d-%m-%Y') as fecha_creacion, puestos.nombre_puesto");
		$this->db->from('documentos');
		$this->db->join('puestos', 'documentos.responsable = puestos.id_puesto', 'inner');
		$this->db->join('relacion_documento_puesto', 'documentos.id_documento = relacion_documento_puesto.id_documento', 'inner');
		$this->db->join('usuarios','usuarios.id_puesto = relacion_documento_puesto.id_puesto', 'inner');
		$this->db->join('metodos_compilacion', 'metodos_compilacion.id_metodo_comp=documentos.id_metodo_comp', 'inner');
		$this->db->where('usuarios.usuario = \''.$this->session->userdata('usuario').'\'');
		if(is_numeric($target)){//si es numerico buscará por el id del documento
			$where = "documentos.activo = '1' AND (
				documentos.nombre_documento LIKE '%".$target."%' 
				OR documentos.id_calidad LIKE '%".$target."%' 
				OR documentos.doc_que_lo_genera LIKE '%".$target."%' 
				OR documentos.tiempo_retencion_uni = '".$target."' 
				OR documentos.tiempo_retencion_desc LIKE '%".$target."%' 
				OR documentos.responsable LIKE '%".$target."%' 
				OR documentos.archivo LIKE '%".$target."%')";
			$this->db->where($where);
		}else{
			$where = "documentos.activo = '1' AND (
				documentos.nombre_documento LIKE '%".$target."%' 
				OR documentos.id_calidad LIKE '%".$target."%' 
				OR documentos.doc_que_lo_genera LIKE '%".$target."%'  
				OR documentos.tiempo_retencion_desc LIKE '%".$target."%' 
				OR documentos.responsable LIKE '%".$target."%' 
				OR documentos.archivo LIKE '%".$target."%')";
			$this->db->where($where);
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
	
	function searchDocumentById($target){
		$this->db->select("documentos.*, metodos_compilacion.metodo_compilacion, DATE_FORMAT(documentos.fecha_creacion, '%d-%m-%Y') as fecha_creacion, puestos.nombre_puesto");
		$this->db->from('documentos');
		$this->db->join('puestos', 'documentos.responsable = puestos.id_puesto', 'inner');
		$this->db->join('relacion_documento_puesto', 'documentos.id_documento = relacion_documento_puesto.id_documento', 'inner');
		$this->db->join('usuarios','usuarios.id_puesto = relacion_documento_puesto.id_puesto', 'inner');
		$this->db->join('metodos_compilacion', 'metodos_compilacion.id_metodo_comp=documentos.id_metodo_comp', 'inner');
		$this->db->where('usuarios.usuario = \''.$this->session->userdata('usuario').'\'');
		$where = "documentos.activo = '1' AND documentos.id_documento = '".$target."'";
		$this->db->where($where);


		$resultados = $this->db->get();
		//echo $this->db->last_query();
		$num_results = $resultados->num_rows();
		if($num_results > 0){
			return $resultados;
		}else{
			return false;
		}
	}

	function generadoPor($id_documento){
		$this->db->select("nombre_documento");
		$this->db->from("documentos");
		$this->db->where("id_documento=".$id_documento);
		$resultados = $this->db->get();
		$num_results = $resultados->num_rows();
		//echo "<br /><br />".$this->db->last_query();
		if($num_results > 0){
			return $resultados;
		}else{
			return false;
		}
	}

	function isInCheckin($target){
		$this->db->select('*');
		$this->db->where('id_documento', $target);
		$resultados = $this->db->get('documentos_checkin');
		//echo $this->db->last_query();
		$num_results = $resultados->num_rows();
		if($num_results > 0){
			return $resultados;
		}else{
			return false;
		}
	}

	function isReviewingDocument($user, $document){
		$this->db->select('*');
		$this->db->where('usuario', $user);
		$this->db->where('id_documento', $document);
		$result = $this->db->get('documentos_checkin');
		$isInCheckin = $result->num_rows();
		
		if($isInCheckin > 0){
			return true;
		}else{
			return false;
		}
	}

	function actualizarDocumento($datos){
		$this->db->where('id_documento', $datos['id_documento']);
		$this->db->set($datos);
		$query = $this->db->update('documentos');

		if($query){
			$resultado['state'] = true;
			$resultado['message'] = "El documento se ha actualizado correctamente.";
			return $resultado;
		}else{
			$resultado['state'] = false;
			$resultado['message'] = $this->db->_error_message();
			return $resultado;
		}
	}

	function agregarAlLogdeCambios($datos){
		$result = $this->db->insert('log_cambios', $datos);
		if($result){
			$resultado['state'] = true;
			$resultado['message'] = "";
			return $resultado;
		}else{
			$resultado['state'] = false;
			$resultado['message'] = $this->db->_error_message();
			return $resultado;
		}
	}

	function searchUsersGrantsDocument($id_documento){
		//Se ejecutará este query:
		$this->db->select("usuarios.usuario, usuarios.nombre");
		$this->db->from("usuarios");
		$this->db->join("relacion_documento_puesto", "usuarios.id_puesto = relacion_documento_puesto.id_puesto", "inner");
		$this->db->join("documentos", "relacion_documento_puesto.id_documento = documentos.id_documento");
		$this->db->where("documentos.id_documento =".$id_documento);
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

	function deleteDocument($id_documento){
		$desactivar_doc = array('activo' => 0);
		$this->db->set($desactivar_doc);
		$this->db->where('id_documento', $id_documento);
		$query = $this->db->update('documentos');

		if($query){
			$resultado['state'] = true;
			$resultado['message'] = "Se ha eliminado el documento del SGC.";
			return $resultado;
		}else{
			$resultado['state'] = false;
			$resultado['message'] = $this->db->_error_message();
			return $resultado;
		}
	}

	function toCheckin($id_documento){
		$checkin = array(
			'id_documento' => $id_documento,
			'usuario' => $this->session->userdata('usuario'),
			'fecha' => date('Y-m-d  h:m:s'));
		if($this->db->insert('documentos_checkin',$checkin)){
			return TRUE;
		}else{
			echo $this->db->error_message();
		}
	}

	function toCheckout($datos){
		//Se borra de la tabla de ckeckin
		$this->db->where('id_documento', $datos['id_documento']);
		$this->db->where('usuario', $this->session->userdata('usuario'));
		if($this->db->delete('documentos_checkin')){
			//nos traemos los datos del usuario para averiguar su id
			$userdata_result = self::getDatosUsuario($this->session->userdata('usuario'));
			$userdata = $userdata_result->result()[0];
			//ahora procedemos a eliminar el borrador de la base de datos
			$this->db->where('id_documento', $datos['id_documento']);
			$this->db->where('id_usuario', $userdata->id_usuario);
			if($this->db->delete('borradores')){
				//echo $this->db->last_query();
				return TRUE;
			}
		}else{
			echo $this->db->error_message();
			return FALSE;
		}
	}

	function getDatosUsuario($user){
		$this->db->select('*');
		$this->db->from('usuarios');
		$where = "
			id_usuario = '".$user."' 
			OR usuario = '".$user."' 
			OR nombre = '".$user."'";
		$this->db->where($where);
		$resultados = $this->db->get();
		//echo $this->db->last_query();
		$num_results = $resultados->num_rows();
		if($num_results > 0){
			return $resultados;
		}else{
			return false;
		}
	}

	function agregarBorrador($newBorrador){
		if($this->db->insert('borradores',$newBorrador)){
			$resultado['state'] = true;
			$resultado['message'] = "Archivo guardado correctamente.";
		}else{
			$resultado['state'] = false;
			$resultado['message'] = $this->db->error_message();
		}

		return $resultado;
	}

	public function getBorrador($id_usuario, $id_documento){
		$this->db->select('*');
		$this->db->from('borradores');
		$this->db->where('id_usuario = '.$id_usuario);
		$this->db->where('id_documento = '.$id_documento);
		$resultados = $this->db->get();
		//echo $this->db->last_query();
		$num_results = $resultados->num_rows();
		if($num_results > 0){
			return $resultados;
		}else{
			return false;
		}
	}

	public function getCambiosDocumento($id_documento){
		$this->db->select('log_cambios.*, documentos.*');
		$this->db->from('log_cambios');
		$this->db->join('documentos', 'log_cambios.id_documento = documentos.id_documento', 'inner');
		$this->db->where('log_cambios.id_documento = '.$id_documento);
		$this->db->order_by('log_cambios.fecha_cambio', 'asc');
		$resultados = $this->db->get();
		$num_results = $resultados->num_rows();

		if($num_results > 0){
			return $resultados;
		}else{
			return false;
		}
	}

	public function getPuestos(){
		$this->db->select("*");
		$this->db->from("puestos");
		$results = $this->db->get();
		$num_results = $results->num_rows();
		$arrPuestos[''] = "Seleccione";
		if($num_results > 0){
			foreach($results->result() as $puesto){
				$arrPuestos[$puesto->id_puesto] = $puesto->nombre_puesto;
			}

			return $arrPuestos;
		}else{
			return false;
		}
	}

	public function getDocumentos(){
		$this->db->select("*");
		$this->db->from("documentos");
		$this->db->where("activo=1");
		$results = $this->db->get();
		$num_results = $results->num_rows();
		$arrDocumentos[''] = "Seleccione";
		if($num_results > 0){
			foreach($results->result() as $documento){
				$arrDocumentos[$documento->id_documento] = $documento->nombre_documento;
			}

			return $arrDocumentos;
		}else{
			return false;
		}
	}

	public function getMetodosCompilacion(){
		$this->db->select("*");
		$this->db->from("metodos_compilacion");
		$results = $this->db->get();
		$num_results = $results->num_rows();
		$arrMetComp[''] = "Seleccione";
		if($num_results > 0){
			foreach($results->result() as $mc){
				$arrMetComp[$mc->id_metodo_comp] = $mc->metodo_compilacion;
			}

			return $arrMetComp;
		}else{
			return false;
		}
	}

	public function getTipoDocumento(){
		$this->db->select("*");
		$this->db->from("tipo_documento");
		$results = $this->db->get();
		$num_results = $results->num_rows();
		$arrTipoDoc[''] = "Seleccione";
		if($num_results > 0){
			foreach($results->result() as $td){
				$arrTipoDoc[$td->id_tipo] = $td->tipo_documento;
			}

			return $arrTipoDoc;
		}else{
			return false;
		}
	}

	public function getLastDocumentID(){
		$this->db->select('MAX(id_documento) as id_documento');
		$this->db->from('documentos');
		$results = $this->db->get();
		$num_results = $results->num_rows();
		if($num_results > 0){
			return $results->result()[0]->id_documento; 
		}
	}

	public function getDocument($attr, $target){
		$this->db->select('*');
		$this->db->from('documentos');
		$this->db->where($attr."= '".$target."'");
		$results = $this->db->get();
		$num_results = $results->num_rows();
		if($num_results > 0){
			return $results;
		}else{
			return $num_results;
		}
	}

	public function asignarDocumentosAPuesto($documento, $puesto){
		$datos['id_documento'] = $documento;
		$datos['id_puesto'] = $puesto;
		$result = $this->db->insert('relacion_documento_puesto', $datos);
		if($result){
			return true;
		}
	}
}
?>
