<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UsuariosModel extends CI_Model{
	CONST catalogo_permisos = "cat_permisos";
	CONST tabla_usuarios = "usuarios";
	CONST tabla_puestos = "puestos";
	CONST tabla_documentos = "documentos";
	function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->library('table');
	}

	function getCatalogoPermisosUsuarios(){
		$this->db->select("*");
		$this->db->from(self::catalogo_permisos);
		$results = $this->db->get();
		$num_results = $results->num_rows();
		$arrPermisos[''] = "Seleccione";
		if($num_results > 0){
			foreach($results->result() as $permiso){
				$arrPermisos[$permiso->clave_permiso] = $permiso->titulo_permiso;
			}

			return $arrPermisos;
		}else{
			return false;
		}
	}

	function guardarUsuario($datos){
		$result = $this->db->insert(self::tabla_usuarios, $datos);
		if($result){
			return true;
		}else{
			return false;
		}
	}

	public function getLastUserID(){
		$this->db->select('MAX(id_usuario) as id_usuario');
		$this->db->from(self::tabla_usuarios);
		$results = $this->db->get();
		$num_results = $results->num_rows();
		if($num_results > 0){
			return $results->result()[0]->id_usuario; 
		}
	}

	public function getUsuario($attr, $target){
		$this->db->select('*');
		$this->db->from(self::tabla_usuarios);
		$this->db->where($attr."= '".$target."'");
		$results = $this->db->get();
		$num_results = $results->num_rows();
		if($num_results > 0){
			return $results;
		}else{
			return $num_results;
		}
	}

	public function getJobs(){ //Consultamos todos los puestos
		$this->db->select('*');
		$this->db->from(self::tabla_puestos);
		$results = $this->db->get();
		$num_results = $results->num_rows();
		if($num_results > 0){
			return $results;
		}else{
			return $num_results;
		}
	}

	public function getCurrentJobsDocuments($id_puesto){
		$this->db->select('documentos.id_documento, documentos.nombre_documento, documentos.id_calidad');
		$this->db->from(self::tabla_documentos);
		$this->db->join('relacion_documento_puesto', 'documentos.id_documento=relacion_documento_puesto.id_documento', 'inner');
		$this->db->join(self::tabla_puestos, self::tabla_puestos.'.id_puesto=relacion_documento_puesto.id_puesto');
		$this->db->where(self::tabla_puestos.".id_puesto=".$id_puesto." and activo=1");
		$this->db->order_by(self::tabla_documentos.".nombre_documento", "asc");
		$results = $this->db->get();
		$num_results = $results->num_rows();
		//echo $this->db->last_query();
		if($num_results > 0){
			return $results;
		}else{
			return $num_results;
		}
	}
	
	public function getCurrentJobsDocumentsExt($id_puesto){
		$this->db->select('d.id_doc_externo, d.nombre_documento');
		$this->db->from('docs_externos d');
		$this->db->join('rel_docs_externos_puestos rdep', 'd.id_doc_externo = rdep.id_doc_ext', 'inner');
		$this->db->join(self::tabla_puestos, self::tabla_puestos.'.id_puesto=rdep.id_puesto');
		$this->db->where(self::tabla_puestos.".id_puesto=".$id_puesto." and activo=1");
		$this->db->order_by("d.nombre_documento", "asc");
		$results = $this->db->get();
		$num_results = $results->num_rows();
		//echo $this->db->last_query();
		if($num_results > 0){
			return $results;
		}else{
			return $num_results;
		}
	}
	
	public function getAvailableDocuments($id_puesto){
		$qry = "
		SELECT DISTINCT(a.id_documento), a.nombre_documento,a.id_calidad
FROM documentos a
WHERE a.id_documento NOT IN (SELECT d.id_documento 
			FROM documentos d
INNER JOIN relacion_documento_puesto e ON d.id_documento = e.id_documento
INNER JOIN puestos f ON f.id_puesto = e.id_puesto
WHERE e.id_puesto = ".$id_puesto.") AND a.activo = 1 order by a.nombre_documento asc";
		$results = $this->db->query($qry);
		$num_results = $results->num_rows();
		//echo $this->db->last_query();
		if($num_results > 0){
			return $results;
		}else{
			return $num_results;
		}
	}
	
	public function getAvailableDocumentsExt($id_puesto){
		$qry = "
		SELECT DISTINCT(a.id_doc_externo), a.nombre_documento
FROM docs_externos a
WHERE a.id_doc_externo NOT IN (SELECT d.id_doc_externo 
			FROM docs_externos d
INNER JOIN rel_docs_externos_puestos e ON d.id_doc_externo = e.id_doc_ext
INNER JOIN puestos f ON f.id_puesto = e.id_doc_ext
WHERE e.id_puesto = ".$id_puesto.") AND a.activo = 1 order by a.nombre_documento asc";
		$results = $this->db->query($qry);
		$num_results = $results->num_rows();
		//echo $this->db->last_query();
		if($num_results > 0){
			return $results;
		}else{
			return $num_results;
		}
	}

	public function removeDocumentAccess($id_puesto, $id_documento){
		$this->db->where("id_puesto=".$id_puesto);
		$this->db->where("id_documento=".$id_documento);
		if($this->db->delete('relacion_documento_puesto')){
			return true;
		}else{
			return false;
		}
	}
	
	public function removeDocumentAccessExt($id_puesto, $id_documento){
		$this->db->where("id_doc_ext=".$id_documento);
		$this->db->where("id_puesto=".$id_puesto);
		if($this->db->delete('rel_docs_externos_puestos')){
			return true;
		}else{
			return false;
		}
	}

	public function grantDocumentAccess($id_puesto, $id_documento){
		$datos = array("id_documento" => $id_documento, "id_puesto" => $id_puesto);
		$result = $this->db->insert("relacion_documento_puesto", $datos);
		if($result){
			return true;
		}else{
			return false;
		}
	}
	
	public function grantDocumentAccessExt($id_puesto, $id_documento){
		$datos = array("id_doc_ext" => $id_documento, "id_puesto" => $id_puesto);
		$result = $this->db->insert("rel_docs_externos_puestos", $datos);
		if($result){
			return true;
		}else{
			return false;
		}
	}
	
	public function getUsers($POST){
		$query = "SELECT u.*, p.*, per.*, CASE u.envio_correo WHEN 1 THEN 'SI' ELSE 'NO' END as notificaciones FROM usuarios u INNER JOIN puestos p ON u.id_puesto=p.id_puesto INNER JOIN cat_permisos per ON u.permiso=per.clave_permiso ";
		$this->db->query($query);
		$where = "";
		if($POST['target'] != ""){
			$where = "WHERE(nombre like '%".$POST['target']."%' OR
			u.usuario like '%".$POST['target']."%' OR 
			u.correo like '%".$POST['target']."%' OR 
			u.no_empleado like '%".$POST['target']."%')";
			if($POST['puesto'] != ""){
				$where.= " AND u.id_puesto = '".$POST['puesto']."'";
			}
			if($POST['permiso']!= ""){
				$where.= " AND u.permiso = '".$POST['permiso']."'";
			}
			if(isset($POST['notificaciones'])){
				$where.= " AND u.envio_correo = '".$POST['notificaciones']."'";
			}
		}elseif($POST['puesto'] != ""){
			$where.= " WHERE u.id_puesto = '".$POST['puesto']."'";
			if($POST['permiso'] != ""){
				$where.= " AND u.permiso = '".$POST['permiso']."'";
			}
			if(isset($POST['notificaciones'])){
				$where.= " AND u.envio_correo = '".$POST['notificaciones']."'";
			}
		}elseif($POST['permiso'] != ""){
			$where.= " AND u.permiso = '".$POST['permiso']."'";
			if(isset($POST['notificaciones'])){
				$where.= " AND u.envio_correo = '".$POST['notificaciones']."'";
			}
		}elseif(isset($POST['notificaciones'])){
				$where.= " AND u.envio_correo = '".$POST['notificaciones']."'";
			}
		if($where != ""){
			$query.= $where;
		}
		$query .= " ORDER BY u.nombre";

		$results = $this->db->query($query);
		$num_results = $results->num_rows();
		//echo $this->db->last_query();
		if($num_results > 0){
			return $results;
		}else{
			return false;
		}
	}
	
	function actualizarUsuario($datos){
		$this->db->where('usuario', $datos['usuario']);
		$this->db->set($datos);
		$query = $this->db->update('usuarios');

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
	
	function actualizarUsuarioId($datos){
		$this->db->where('id_usuario', $datos['id_usuario']);
		$this->db->set($datos);
		$query = $this->db->update('usuarios');

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
	
	public function eliminar_usuario($id_usuario){
		$this->db->where("id_usuario=".$id_usuario);
		if($this->db->delete('usuarios')){
			return true;
		}else{
			return false;
		}
	}
}