<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//Llamado de la clase para enviar emails
require '/var/www/html/SGCPITIC/vendor/autoload.php'; // carga las librerias del composer.json

class DocumentosController extends CI_Controller {
	function __construct(){
		parent::__construct();
		$this->load->model('DocumentosModel');
		$this->load->model('DocumentosExternosModel');
		$this->load->library('pagination');
		$this->load->library('form_validation');
		$this->load->library('email');
	}

	public function index($pageRequested){
		//$tabla = UltimosCambiosEnDocumentos();
	}

	public function nvoDocumento(){
		if($this->session->userdata('logged_in') && ($this->session->userdata('permiso') == "W" || $this->session->userdata('permiso') == "A")){
			//$this->output->cache(2);
			$datos['puestos'] = $this->DocumentosModel->getPuestos();
			$datos['documentos'] = $this->DocumentosModel->getDocumentos();
			$datos['metodoCompilacion'] = $this->DocumentosModel->getMetodosCompilacion();
			$datos['tiposDeDocumento'] = $this->DocumentosModel->getTipoDocumento();
			$this->load->view('templates/includes');
			$this->load->view('templates/navigation-bar');
			$this->load->view('documentos/nuevo_documento.php', $datos);
			$this->load->view('templates/footer.php');
		}else{
			$data['texto1'] = "Página Restringida";
			$data['texto2'] = "Es posible que no haya iniciado sesión o que usted no tenga permiso para esta sección";
			$this->load->view('templates/includes');
			$this->load->view('templates/navigation-bar');		
			$this->load->view('templates/mensaje_generico', $data);
			$this->load->view('templates/footer');
	    }
	}

	public function guardarDocumento(){
		$this->output->cache(0);
		if($this->session->userdata('logged_in')&& ($this->session->userdata('permiso') == "W" || $this->session->userdata('permiso') == "A")){
			//Creamos la validación para verificar que no exista un documento con el mismo ID de Calidad
			$this->form_validation->set_rules('id_calidad', 'id_calidad', 'callback_checkIdCalidad');
			//Verificamos las validaciones
			if($this->form_validation->run() == true){
				// codigo para subir el archivo al servidor
				$tipo = ($this->input->post('tipo')!== null) ? $this->input->post('tipo') : 9;
				if($tipo == 9){
					$id_calidad = $this->input->post('id_calidad')."-R";
				}else{
					$id_calidad = $this->input->post('id_calidad');
				}
				$file_configs['file_name'] = $id_calidad;
				$file_configs['upload_path'] = './'.$this->config->item('docs_dir').'/';
				$file_configs['allowed_types'] = 'xlsx|xls|docx|doc|ppt|pptx|txt|pdf';
				$file_configs['max_size']    = '9999';
				$file_configs['max_width']  = '9999';
				$file_configs['max_height']  = '9999';
				$this->load->library('upload', $file_configs);

				if(!$this->upload->do_upload('archivo')){
					$error = array('Error' => "ha ocurrido el siguiente error:<br />".$this->upload->display_errors());
					/*print("El siguiente error ha ocurrido: <br /><pre>");
					print_r($error);
					print("</pre>");*/
					$data['texto1'] = "Error";
					$data['texto2'] = $error["Error"];
					$this->load->view('templates/includes');	
					$this->load->view('templates/navigation-bar');			
					$this->load->view('templates/mensaje_generico', $data);
					$this->load->view('templates/footer');
				}else{
					$data = array('uploaded_data_info' => $this->upload->data());
					//generamos el pdf con libreoffice que tiene la utilidad soffice
					//es requerimiento tener instalado libreoffice en el servidor y la utilidad headless
					//yum install libreoffice
					//yum install openoffice.org-headless
					exec('soffice --headless --convert-to pdf --outdir '.$_SERVER['DOCUMENT_ROOT'].'/'.$this->config->item('app_name').'/'.$this->config->item('docs_dir').'/ '.$_SERVER['DOCUMENT_ROOT'].'/'.$this->config->item('app_name').'/'.$this->config->item('docs_dir').'/'.$data['uploaded_data_info']['file_name'], $output, $return);

					
					//guardar información en base de datos
					//Se crea el array con la información
					if($this->input->post('esRegistro') == true){$esRegistro = 1;}
					if($this->input->post('almacen_registro') != ""){
						$se_almacena_en = $this->input->post('almacen_registro');
					}else{
						$se_almacena_en=null;
					}
					$tipo = ($this->input->post('tipo')!== null) ? $this->input->post('tipo') : 9;
					$id_calidad = ($tipo == 9)? $this->input->post('id_calidad')."-R" : $this->input->post('id_calidad');
					$tretencion_uni = ($this->input->post('tiempo_retencion_uni')!== null) ? $this->input->post('tiempo_retencion_uni') : null;
					$tretencion_desc =($this->input->post('tiempo_retencion_desc')!== null) ? $this->input->post('tiempo_retencion_desc') : null;
					if($this->input->post('metodo_compilacion')!== null && $this->input->post('metodo_compilacion')!=""){
						$metodo_comp = $this->input->post('metodo_compilacion');
					}else{
						$metodo_comp = 18;
					}
					$docGenera = ($this->input->post('doc_que_lo_genera')!== null) ? $this->input->post('doc_que_lo_genera') : null;
					
					$lastDocumentID = $this->DocumentosModel->getLastDocumentID()+1;
					$newDocData = array(
						'id_documento' => $lastDocumentID,
						'nombre_documento' => $this->input->post('nombre_documento'),
						'id_calidad' => $id_calidad,
						'revision' => $this->input->post('revision'),
						'subrevision' => '0',
						'fecha_revision' => date('Y-m-d'),
						'doc_que_lo_genera' => $docGenera,
						'fecha_creacion' => date('Y-m-d G:i:s'),
						'tiempo_retencion_uni' => $tretencion_uni,
						'tiempo_retencion_desc' => $tretencion_desc,
						'id_metodo_comp' => $metodo_comp,
						'responsable' => $this->input->post('responsable'),
						'id_tipo' => $tipo,
						'archivo' => $data['uploaded_data_info']['file_name'],
						'vista_archivo' => $id_calidad.'.pdf',
						'activo' => '1',
						'se_almacena_en' => $se_almacena_en
					);
					//se guarda y se verifica si se guardo la información en la bd
					if($this->DocumentosModel->addDocument($newDocData)){
						$datos = array(
							'id_cambio' => null,
							'id_calidad' => $this->input->post('id_calidad'),
							'nombre_documento' => $this->input->post('nombre_documento'),
							'id_documento' => $lastDocumentID,
							'fecha_cambio' => date('Y-m-d G:i:s'),
							'causa_cambio' => 'Documento nuevo',
							'desc_cambio' => 'Se creó el documento',
							'usuario' => $this->session->userdata('usuario'),
							'revision_ant' => 'NA',
							'revision_actual' => $this->input->post('revision').".0",
							'archivo_obsoleto' => "NA"
							);
						$this->DocumentosModel->asignarDocumentosAPuesto($lastDocumentID, $this->input->post('responsable'));
						$resultLogCambios = $this->DocumentosModel->agregarAlLogdeCambios($datos);
						//los usuarios que serán notificados de los cambios en este documento.
						$notificar_a = $this->input->post('notificar_a');
						foreach($notificar_a as $na){
							$this->DocumentosModel->asignarDocumentosAPuesto($lastDocumentID, $na);
						}
						$this->enviarNotificacionCambio($datos, "alta de");
						
						header('Location: '. base_url().'document/'.$lastDocumentID);
					}
				}
		    }else{
					$data['texto1'] = "Error";
					$data['texto2'] = validation_errors();
					$this->load->view('templates/includes');
					$this->load->view('templates/navigation-bar');			
					$this->load->view('templates/mensaje_generico', $data);
					$this->load->view('templates/footer');
			}
		}else{
	      //Si no hay sesión se redirecciona la página o no hay permisos;
	     	$data['texto1'] = "Error";
			$data['texto2'] = "Es posible que no haya iniciado sesión o que usted no tenga permiso para esta sección";
			$this->load->view('templates/includes');
			$this->load->view('templates/navigation-bar');		
			$this->load->view('templates/mensaje_generico', $data);
			$this->load->view('templates/footer');
	    }
	}

	public function checkIdCalidad($id_calidad){
		$tipo = ($this->input->post('tipo')!== null) ? $this->input->post('tipo') : 9;
		if($tipo == 9){
			$id_calidad = $this->input->post('id_calidad')."-R";
		}
		$num_results = $this->DocumentosModel->getDocument('id_calidad', $id_calidad);
		$num_docs = isset( $num_results->num_rows ) ? $num_results->num_rows : 0;
		if($num_docs > 0){
			$this->form_validation->set_message('checkIdCalidad', 'El documento %s ya existe favor de elegir otro ID de calidad');
			return false;
		}else{
			return true;
		}
	}

	public function buscarDocumentoForm(){
		try{
			$this->delete_cache();
		}catch(Exception $e){
			echo "Excepcion de cache";
		}
		if($this->session->userdata('logged_in')){
			$this->db->cache_delete(); //se borra la caché antes de la busqueda
			$this->load->view('templates/includes');
			$this->load->view('templates/navigation-bar');
			$this->load->view('documentos/buscar_doc_form');
			$this->load->view('templates/footer');
		}else{
	      //Si no hay sesión se redirecciona la página;
	      redirect('login', 'refresh');
	    }
	}
	
	
	//Funcion que elimina el caché para cuando no lo necesitamos.
	public function delete_cache(){
		$CI =& get_instance();
		$path = $CI->config->item('cache_path');

		$cache_path = ($path == '') ? APPPATH.'cache/' : $path;

		$handle = opendir($cache_path);
		while (($file = readdir($handle))!== FALSE) 
		{
			//Leave the directory protection alone
			if ($file != '.htaccess' && $file != 'index.html')
			{
			   @unlink($cache_path.'/'.$file);
			}
		}
		closedir($handle);
	}

	public function buscarDocumento(){
		if($this->session->userdata('logged_in')){
			//$this->output->cache(1);
			$externos = $this->input->get('externos');
			$datos['target'] = $this->input->get('target');
			if($externos == 1){
				if(trim($datos['target']) == ""){
					$datos['mensaje'] = "Por favor específique el texto a buscar.<br />Si desea ver todos los documentos escriba un asterisco ( * )";
					$datos['table'] = "";
				}else{
					if(trim($datos['target']) == '*'){
						$datos['target'] = "";
						$datos['mensaje'] = "Lista de documentos externos registrados.";
					}else{
						$datos['mensaje'] = "Resultados de búsqueda para: <br />'<span style='color:red;'>" . $datos['target'] . "</span>' en documentos EXTERNOS<hr />";
					}

					if($documentos = $this->DocumentosExternosModel->searchDocumentExterno($datos['target'])){
						//Se genera la tabla
						$tpl = array (
							'table_open' => '<table border=1 id="ultimosCambios" cellpadding=2 cellspacing=1 width=100%>',
							'heading_row_start'   => '<tr style="background-color: #2ecc71; font-weight:bold; color:white; padding:1em;">',
							'heading_row_end'     => '</tr>',
							'heading_cell_start'  => '<th style="text-align:center;border: 2px solid black; padding:1em; font-size:13px;">',
							'heading_cell_end'    => '</th>',
							'row_start'     => '<tr style="background-color: #DBF6ED; align:center;">',
							'row_alt_start' => '<tr bgcolor="white">',
							'row_end'             => '</tr>',
							'cell_start'      => '<td style="padding:0.5em;">',
							'cell_end'        => '</td>',
							'cell_alt_start'      => '<td style="padding:0.5em;">',
							'cell_alt_end'        => '</td>',
						);
						$this->table->set_heading(array('#', 'Documento', 'Nom. Archivo'));
						$i=1;
						foreach ($documentos->result() as $doc){
							$this->table->set_template($tpl);
							$this->table->add_row($i, "<a href='documentExt/".$doc->id_doc_externo."'>".$doc->nombre_documento."</a>", "<a href='".$this->config->item('externos')."/".$doc->vista_archivo."'>Descargar <span class='glyphicon glyphicon-download-alt'></span></a>");
							$i++;
						}

						$datos['table'] = $this->table->generate();
					}else{
						$datos['mensaje'] = "No se han encontrado resultados para: <br />'<span style='color:red;'>" . $datos['target'] . "</span>'<hr />";
						$datos['table'] = "";
					}
				}
			}else{
				if(trim($datos['target']) == ""){
					$datos['mensaje'] = "Por favor específique el texto a buscar.<br />Si desea ver todos los documentos escriba un asterisco ( * )";
					$datos['table'] = "";
				}else{
					if(trim($datos['target']) == '*'){
						$datos['target'] = "";
						$datos['mensaje'] = "Lista de documentos registrados.";
					}else{
						$datos['mensaje'] = "Resultados de búsqueda para: <br />'<span style='color:red;'>" . $datos['target'] . "</span>'<hr />";
					}

					if($documentos = $this->DocumentosModel->searchDocument($datos['target'])){
						//Se genera la tabla
						$tpl = array (
							'table_open' => '<table border=1 id="ultimosCambios" cellpadding=2 cellspacing=1 width=100%>',
							'heading_row_start'   => '<tr style="background-color: #2ecc71; font-weight:bold; color:white; padding:1em;">',
							'heading_row_end'     => '</tr>',
							'heading_cell_start'  => '<th style="text-align:center;border: 2px solid black; padding:1em; font-size:13px;">',
							'heading_cell_end'    => '</th>',
							'row_start'     => '<tr style="background-color: #DBF6ED; align:center;">',
							'row_alt_start' => '<tr bgcolor="white">',
							'row_end'             => '</tr>',
							'cell_start'      => '<td style="padding:0.5em;">',
							'cell_end'        => '</td>',
							'cell_alt_start'      => '<td style="padding:0.5em;">',
							'cell_alt_end'        => '</td>',
						);
						$this->table->set_heading(array('#', 'Documento','ID Calidad', 'Revisión', 'Generado por', 'Tiempo de Retención', 'Responsable', 'Nom. Archivo'));
						$i=1;
						foreach ($documentos->result() as $doc){
							if($doc->doc_que_lo_genera != ""){$doc_que_lo_genera = $doc->doc_que_lo_genera;}else{$doc_que_lo_genera=0;}
							$datos2 = $this->DocumentosModel->generadoPor($doc_que_lo_genera);
							$this->table->set_template($tpl);
							if($doc_que_lo_genera!=0){
								foreach($datos2->result() as $gp){
									$this->table->add_row($i, "<a href='document/".$doc->id_documento."'>".$doc->nombre_documento."</a>", $doc->id_calidad, $doc->revision.".".$doc->subrevision, $gp->nombre_documento, $doc->tiempo_retencion_uni." ".$doc->tiempo_retencion_desc, $doc->nombre_puesto, "<a href='".$this->config->item('docs_dir')."/".$doc->vista_archivo."'>".$doc->id_calidad."</a>");
								}
							}else{
								$this->table->add_row($i, "<a href='document/".$doc->id_documento."'>".$doc->nombre_documento."</a>", $doc->id_calidad, $doc->revision.".".$doc->subrevision, "NA", $doc->tiempo_retencion_uni." ".$doc->tiempo_retencion_desc, $doc->nombre_puesto, "<a href='".$this->config->item('docs_dir')."/".$doc->vista_archivo."'>".$doc->id_calidad."</a>");
							}
							$i++;
						}

						$datos['table'] = $this->table->generate();
					}else{
						$datos['mensaje'] = "No se han encontrado resultados para: <br />'<span style='color:red;'>" . $datos['target'] . "</span>'<hr />";
						$datos['table'] = "";
					}
				}
			}
			
			$this->load->view('templates/includes');
			$this->load->view('templates/navigation-bar');
			$this->load->view('documentos/buscar_documentos', $datos);
			$this->load->view('templates/footer');
		}else{
			//Si no hay sesión se redirecciona la página;
		    redirect('login', 'refresh');
		}
	}

	public function obtenerDocumento($id_documento){
		if($this->session->userdata('logged_in')){
			$documento['documento'] = $this->DocumentosModel->getDocument('documentos.id_documento', $id_documento);
			$documento['checkin'] = ($this->DocumentosModel->isInCheckin($id_documento)) ? $this->DocumentosModel->isInCheckin($id_documento) : 0;
			if($documento['documento']){
				$this->load->view('templates/includes');
				$this->load->view('templates/navigation-bar');
				$this->load->view('documentos/documento', $documento);
				$this->load->view('templates/footer');
			}else{
				$datos['texto1']='¡Error!';
				$datos['texto2']='El documento que intenta visualizar no existe, o posiblemente usted no tenga los permisos para verlo.';
				$this->load->view('templates/includes');
				$this->load->view('templates/navigation-bar');
				$this->load->view('templates/mensaje_generico', $datos);
				$this->load->view('templates/footer');
			}
		}else{
			//Si no hay sesión se redirecciona la página;
			$url = base_url(uri_string());
			$this->session->set_flashdata('pagReq', str_replace('/','-',$url));
			redirect('loginUrl', 'refresh');
		}
	}

	public function modificarDocumentoForm($target){
		if($this->session->userdata('logged_in') && ($this->session->userdata('permiso') == "A" || $this->session->userdata('permiso') == "W")){
			$this->output->cache(2);
			$data['documento'] = $this->DocumentosModel->getDocument('id_documento', $target);
			$data['puestos'] = $this->DocumentosModel->getPuestos();
			$data['documentos'] = $this->DocumentosModel->getDocumentos();
			$data['metodoCompilacion'] = $this->DocumentosModel->getMetodosCompilacion();
			$data['tiposDeDocumento'] = $this->DocumentosModel->getTipoDocumento();
			$this->load->view('templates/includes');
			$this->load->view('templates/navigation-bar');
			$this->load->view('documentos/modificar_documento_form', $data);
			$this->load->view('templates/footer');
		}else{
			//Si no hay sesión o no tiene permiso se le hace saber;
			$datos['texto1'] = "Página Restringida";
			$datos['texto2'] = "Es posible que usted no haya iniciado sesión o no tenga permiso a esta área";
	     	$this->load->view('templates/navigation-bar.php');
			$this->load->view('templates/mensaje_generico', $datos);
			$this->load->view('templates/footer.php');
		}
	}

	public function actualizarDocumento(){
		if($this->session->userdata('logged_in')){
			//si se selecciona aumentar revision aumenta la revision actual en 1
			if($this->input->post('aumentarRev') == 1){
				$revisionAnt = $this->input->post('revision').".".$subrevision = $this->input->post('subrevision');
				$revision = $this->input->post('revision')+1;
				$revisionAct = $revision.".0";
				$subrevision = 0;
			}else{
				$revision = $this->input->post('revision');
				$subrevision = $this->input->post('subrevision')+1;
				$revisionAnt = $revision.".".$this->input->post('subrevision');
				$revisionAct = $revision.".".$subrevision;
			}

			if($_FILES['archivo']['name'] == ""){ //si no se eligio un archivo a subir para reemplazar al actual
				$documento = array(
		    		'id_documento' => $this->input->post('id_documento'),
					'nombre_documento' => $this->input->post('nombre_documento'),
					'id_metodo_comp' => $this->input->post('metodo_compilacion'),
					'responsable' => $this->input->post('responsable'),
					'tiempo_retencion_uni' => $this->input->post('tiempo_retencion_uni'),
					'tiempo_retencion_desc' => $this->input->post('tiempo_retencion_desc'),
					'responsable' => $this->input->post('responsable'),
					'revision' => $revision,
					'subrevision' => $subrevision,
					'fecha_revision' => date('Y-m-d')
					);

				$datos = array(
					'id_cambio' => null,
					'nombre_documento' => $this->input->post('nombre_documento'),
					'id_calidad' => $this->input->post('id_calidad'),
					'id_documento' => $this->input->post('id_documento'),
					'fecha_cambio' => date('Y-m-d G:i:s'),
					'causa_cambio' => $this->input->post('causa_cambio'),
					'desc_cambio' => $this->input->post('desc_cambio'),
					'usuario' => $this->session->userdata('usuario'),
					'revision_ant' => $revisionAnt,
					'revision_actual' => $revisionAct,
					'archivo_obsoleto' => "NA",
					);

				$result = $this->DocumentosModel->actualizarDocumento($documento);
				if($result['state']){
					$texto['texto1'] = "Completado";
					$texto['texto2'] = $result['message'];
					$resultLogCambios = $this->DocumentosModel->agregarAlLogdeCambios($datos);
					$texto['texto2'] .= $this->enviarNotificacionCambio($datos, "cambio en");
					if($resultLogCambios['state']){
					}else{
						$texto['texto2'] .= $resultLogCambios['message'];
					}
				}else{
					$resultLogCambios = $this->DocumentosModel->agregarAlLogdeCambios($datos);
					$texto['texto1'] = "Error";
					$texto['texto2'] = $result['message'];
				}
			}else{
				//mover el archivo a versiones obsoletas y borrar el pdf
				if(file_exists('./'.$this->config->item('docs_dir').'/'.$this->input->post('archivo_en_servidor'))){
					if(file_exists('./'.$this->config->item('docs_dir').'/Versiones obsoletas/'.date('Y'))){
			    		rename('./'.$this->config->item('docs_dir').'/'.$this->input->post('archivo_en_servidor'), './'.$this->config->item('docs_dir').'/Versiones obsoletas/'.date('Y')."/Rev.".($revisionAnt)."-".$this->input->post('archivo_en_servidor'));
						@unlink('./'.$this->config->item('docs_dir').'/'.$this->input->post('id_calidad').'.pdf');
			    	}else{
			    		mkdir('./'.$this->config->item('docs_dir').'/Versiones obsoletas/'.date('Y'));
						rename('./'.$this->config->item('docs_dir').'/'.$this->input->post('archivo_en_servidor'), './'.$this->config->item('docs_dir').'/Versiones obsoletas/'.date('Y')."/Rev.".($revisionAnt)."-".$this->input->post('archivo_en_servidor'));
						@unlink('./'.$this->config->item('docs_dir').'/'.$this->input->post('id_calidad').'.pdf');
			    	}
			    }
				$file_configs['file_name'] = $this->input->post('id_calidad');
				$file_configs['upload_path'] = './'.$this->config->item('docs_dir').'/';
			    $file_configs['allowed_types'] = 'xlsx|xls|pdf|docx|doc|ppt|pptx|txt';
			    $file_configs['max_size']    = '4096';
			    $file_configs['max_width']  = '4096';
			    $file_configs['max_height']  = '4096';
			    $this->load->library('upload', $file_configs);
			    if( ! $this->upload->do_upload('archivo')){
			    	$error = array('Error' => $this->upload->display_errors());
			    	//mostramos el mensaje de error 
			    	$texto['texto1'] ="Error";
			    	$texto['texto2'] = "Ha ocurrido el siguiente error: <br />".$error['Error'];
			    	$this->load->view('templates/includes');
					$this->load->view('templates/navigation-bar');
					$this->load->view('templates/mensaje_generico', $texto);
					$this->load->view('templates/footer');
			    }else{
			    	$uploadedData = array('uploaded_data_info' => $this->upload->data());
					//preparamos la información para actualizar la tabla de documentos en la base de datos
			    	$documento = array(
			    		'id_documento' => $this->input->post('id_documento'),
						'nombre_documento' => $this->input->post('nombre_documento'),
						'id_metodo_comp' => $this->input->post('metodo_compilacion'),
						'responsable' => $this->input->post('responsable'),
						'tiempo_retencion_uni' => $this->input->post('tiempo_retencion_uni'),
						'tiempo_retencion_desc' => $this->input->post('tiempo_retencion_desc'),
						'responsable' => $this->input->post('responsable'),
						'archivo' => $uploadedData['uploaded_data_info']['file_name'],
						'revision' => $revision,
						'subrevision' => $subrevision,
						'fecha_revision' => date('Y-m-d')
					);
					
					//generamos el pdf con libreoffice que tiene la utilidad soffice
					//es requerimiento tener instalado libreoffice en el servidor y la utilidad headless
					//yum install libreoffice
					//yum install openoffice.org-headless
					exec('soffice --headless --convert-to pdf --outdir '.$_SERVER['DOCUMENT_ROOT'].'/'.$this->config->item('app_name').'/'.$this->config->item('docs_dir').'/ '.$_SERVER['DOCUMENT_ROOT'].'/'.$this->config->item('app_name').'/'.$this->config->item('docs_dir').'/'.$uploadedData['uploaded_data_info']['file_name'], $output, $return);
					
					//preparamos la informacion para actualizar el log la base de datos
					$datos = array(
						'id_cambio' => null,
						'nombre_documento' => $this->input->post('nombre_documento'),
						'id_documento' => $this->input->post('id_documento'),
						'id_calidad' => $this->input->post('id_calidad'),
						'fecha_cambio' => date('Y-m-d G:i:s'),
						'causa_cambio' => $this->input->post('causa_cambio'),
						'desc_cambio' => $this->input->post('desc_cambio'),
						'usuario' => $this->session->userdata('usuario'),
						'revision_ant' => $revisionAnt,
						'revision_actual' => $revisionAct,
						'archivo_obsoleto' => "Rev.".($revisionAnt)."-".$this->input->post('archivo_en_servidor')
						);
					
					$result = $this->DocumentosModel->actualizarDocumento($documento);
					if($result['state']){
						$texto['texto1'] = "Completado";
						$texto['texto2'] = $result['message'];
						$resultLogCambios = $this->DocumentosModel->agregarAlLogdeCambios($datos);
						$texto['texto2'] .= $this->enviarNotificacionCambio($datos, "cambio en");

						if($resultLogCambios['state']){
						}else{
							$texto['texto2'] .= $resultLogCambios['message'];
						}
					}else{
						$texto['texto1'] = "Error";
						$texto['texto2'] = $result['message'];
					}

			    }//end else se subio el archivo correctamente
			}//endelse Si existe archivo para subir
			$this->load->view('templates/includes');
			$this->load->view('templates/navigation-bar');
			$this->load->view('templates/mensaje_generico', $texto);
			$this->load->view('templates/footer');
		}else{
			//Si no hay sesión se redirecciona la página;
		    redirect('login', 'refresh');
		}
	}

	public function notfound(){
		$this->load->view('templates/includes');
		$this->load->view('templates/navigation-bar');
		$this->load->view('templates/not_found');
		$this->load->view('templates/footer');
	}

	public function eliminarDocumento($id_documento){
		if($this->session->userdata('logged_in')){
			$datos['documentos'] = $this->DocumentosModel->getDocument('id_documento', $id_documento); 
			$documento = $datos['documentos']->result()[0];
			$result = $this->DocumentosModel->deleteDocument($id_documento);
			if($result['state']){
				//movemos el archivo a versiones obsoletas
				if(file_exists('./'.$this->config->item('docs_dir').'/'.$documento->archivo)){
					if(file_exists('./'.$this->config->item('docs_dir').'/Versiones obsoletas/'.date('Y'))){
			    		rename('./'.$this->config->item('docs_dir').'/'.$documento->archivo, './'.$this->config->item('docs_dir').'/Versiones obsoletas/'.date('Y')."/Rev.".($documento->revision.".".$documento->subrevision)."-".$documento->archivo);
						unlink('./'.$this->config->item('docs_dir').'/'.$documento->vista_archivo);
			    	}else{
			    		mkdir('./'.$this->config->item('docs_dir').'/Versiones obsoletas/'.date('Y'));
						rename('./'.$this->config->item('docs_dir').'/'.$documento->archivo, './'.$this->config->item('docs_dir').'/Versiones obsoletas/'.date('Y')."/Rev.".($documento->revision.".".$documento->subrevision)."-".$documento->archivo);
						unlink('./'.$this->config->item('docs_dir').'/'.$documento->vista_archivo);
			    	}
			    }
			    //Creamos los mensajes de éxito al marcar documento como eliminado (0)
				$texto['texto1'] = "Documento eliminado.";
				$texto['texto2'] = $result["message"];
				//agregamos el movimiento al Log de documentos:
				$datos = array(
					'id_cambio' => null,
					'nombre_documento' => $documento->nombre_documento,
					'id_documento' => $id_documento,
					'id_calidad' => $documento->id_calidad,
					'fecha_cambio' => date('Y-m-d G:i:s'),
					'causa_cambio' => 'Documento Eliminado',
					'desc_cambio' => 'Se Eliminó el documento',
					'usuario' => $this->session->userdata('usuario'),
					'revision_ant' => 'NA',
					'revision_actual' => $documento->revision.".".$documento->subrevision,
					'archivo_obsoleto' => 'NA'
					);
				$resultLogCambios = $this->DocumentosModel->agregarAlLogdeCambios($datos);
				$this->enviarNotificacionCambio($datos, "eliminación de"); // NO cambiar parametro de "eliminacion de" ya que
				//aparece en la func. envio de notif de correo
			}else{
				$texto['texto1'] = "Ocurrió un error:";
				$texto['texto2'] = $result["message"];
			}
			$this->load->view('templates/includes');
			$this->load->view('templates/navigation-bar');
			$this->load->view('templates/mensaje_generico', $texto);
			$this->load->view('templates/footer');
		}else{
	      //Si no hay sesión se redirecciona la página;
	      redirect('login', 'refresh');
	    }
	}

	public function toCheckin($id_documento){
		if($this->session->userdata('logged_in')){
			$result = $this->DocumentosModel->toCheckin($id_documento);
			redirect('document/'.$id_documento);
		}else{
	      //Si no hay sesión se redirecciona la página;
	      redirect('login', 'refresh');
	    }
	}

	public function toCheckout($id_documento){
		if($this->session->userdata('logged_in')){
			$data['documento'] = $this->DocumentosModel->searchDocumentById($id_documento);
			$this->load->view('templates/includes');
			$this->load->view('templates/navigation-bar');
			$this->load->view('documentos/checkout_doc', $data);
			$this->load->view('templates/footer');
		}else{
	      //Si no hay sesión se redirecciona la página;
	      redirect('login', 'refresh');
	    }
	}

	public function checkoutDocument($id_documento){
		$enRevisionResult = $this->DocumentosModel->estaEnRevision($id_documento);
		if($enRevisionResult){
			//contamos las personas que estan trabajando en el archivo y regresamos el numero de ellas en la variable num_resultados
			$num_resultados = count($enRevisionResult->result());	
		}else{ 
			//Si regresa falso (debido a que no hay resultados), se le asigna a la variable num_resultados el valor de 0
			$num_resultados = 0;
		}

		if($num_resultados < 2 ){ //si hay mas de un usuario revisando este documento no dejará liberar la revision
			if($this->input->post('aumentarRev') == 1){
				$revisionAnt = $this->input->post('revision').".".$subrevision = $this->input->post('subrevision');
				$revision = $this->input->post('revision')+1;
				$revisionAct = $revision.".0";
				$subrevision = 0;
			}else{
				$revision = $this->input->post('revision');
				$subrevision = $this->input->post('subrevision')+1;
				$revisionAnt = $revision.".".$this->input->post('subrevision');
				$revisionAct = $revision.".".$subrevision;
			}
			
			//mover los archivos a versiones obsoletas y eliminar el pdf correspondiente
			if(file_exists('./'.$this->config->item('docs_dir').'/'.$this->input->post('archivo_en_servidor'))){
				if(file_exists('./'.$this->config->item('docs_dir').'/Versiones obsoletas/'.date('Y'))){
					rename('./'.$this->config->item('docs_dir').'/'.$this->input->post('archivo_en_servidor'), './'.$this->config->item('docs_dir').'/Versiones obsoletas/'.date('Y')."/Rev.".($revisionAnt)."-".$this->input->post('archivo_en_servidor'));
					@unlink('./'.$this->config->item('docs_dir').'/'.$this->input->post('id_calidad').'.pdf');
				}else{
					mkdir('./'.$this->config->item('docs_dir').'/Versiones obsoletas/'.date('Y'));
					rename('./'.$this->config->item('docs_dir').'/'.$this->input->post('archivo_en_servidor'), './'.$this->config->item('docs_dir').'/Versiones obsoletas/'.date('Y')."/Rev.".($revisionAnt)."-".$this->input->post('archivo_en_servidor'));
					@unlink('./'.$this->config->item('docs_dir').'/'.$this->input->post('id_calidad').'.pdf');
				}
			
				//subir el nuevo archivo
				$file_configs['file_name'] = $this->input->post('id_calidad');
				$file_configs['upload_path'] = './'.$this->config->item('docs_dir').'/';
				$file_configs['allowed_types'] = 'xlsx|xls|pdf|docx|doc|ppt|pptx|txt';
				$file_configs['max_size']    = '4096';
				$file_configs['max_width']  = '4096';
				$file_configs['max_height']  = '4096';
				$this->load->library('upload', $file_configs);
				if( ! $this->upload->do_upload('archivo')){
					$error = array('Error' => $this->upload->display_errors());
					//mostramos el mensaje de error 
					$texto['texto1'] ="Error";
					$texto['texto2'] = "Ha ocurrido el siguiente error: <br />".$error['Error'];
					$this->load->view('templates/includes');
					$this->load->view('templates/navigation-bar');
					$this->load->view('templates/mensaje_generico', $texto);
					$this->load->view('templates/footer');
				}else{
					$uploadedData = array('uploaded_data_info' => $this->upload->data());
					$documento = array(
						'id_documento' => $this->input->post('id_documento'),
						'revision' => $this->input->post('revision'),
						'subrevision' => $subrevision,
						'archivo' => $uploadedData['uploaded_data_info']['file_name'],
						'revision' => $revision,
						'fecha_revision' => date('Y-m-d'),
						'vista_archivo' => $this->input->post('id_calidad').'.pdf'
					);
					//generamos el pdf con libreoffice que tiene la utilidad soffice
					//es requerimiento tener instalado libreoffice en el servidor y la utilidad headless
					//yum install libreoffice
					//yum install openoffice.org-headless
					exec('soffice --headless --convert-to pdf --outdir '.$_SERVER['DOCUMENT_ROOT'].'/'.$this->config->item('app_name').'/'.$this->config->item('docs_dir').'/ '.$_SERVER['DOCUMENT_ROOT'].'/'.$this->config->item('app_name').'/'.$this->config->item('docs_dir').'/'.$uploadedData['uploaded_data_info']['file_name'], $output, $return);
					
					$datos = array(
						'id_cambio' => null,
						'nombre_documento' => $this->input->post('nombre_documento'),
						'id_calidad' => $this->input->post('id_calidad'),
						'id_documento' => $this->input->post('id_documento'),
						'fecha_cambio' => date('Y-m-d G:i:s'),
						'causa_cambio' => $this->input->post('causa_cambio'),
						'desc_cambio' => $this->input->post('desc_cambio'),
						'usuario' => $this->session->userdata('usuario'),
						'revision_ant' => $revisionAnt,
						'revision_actual' => $revisionAct,
						'archivo_obsoleto' => "Rev.".($revisionAnt)."-".$this->input->post('archivo_en_servidor')
						);
					$result = $this->DocumentosModel->actualizarDocumento($documento);
					if($result['state']){
						$texto['texto1'] = "Completado";
						$texto['texto2'] = $result['message'];
						if($this->DocumentosModel->toCheckout($datos)){
							$resultLogCambios = $this->DocumentosModel->agregarAlLogdeCambios($datos);
							$texto['texto2'] .= $this->enviarNotificacionCambio($datos, "cambio en");

							//como último eliminamos el borrador que tenga sobre este documento.
							$upload_path_borradores = './'.$this->config->item('docs_dir').'/borradores/'.$this->session->userdata('usuario').'/'.str_replace(' ', '_', $this->input->post('id_calidad')).'_borrador*.*';
							$files = glob($upload_path_borradores);

							foreach ($files as $file) {
								if(is_file($file)){
									unlink($file);
								}
							}
							$folder = './'.$this->config->item('docs_dir').'/borradores/'.$this->session->userdata('usuario');
							if(file_exists($folder)){
								$archivos = scandir($folder);
								if(count($archivos) < 3){
									rmdir($folder);
								}
							}
							redirect('document/'.$id_documento); //redirigimos
						}

						if($resultLogCambios['state']){
						}else{
							$texto['texto2'] .= $resultLogCambios['message'];
						}
					}else{
						$texto['texto1'] = "Error";
						$texto['texto2'] = $result['message'];
					}
				}
			}else{
				$texto['texto1'] ="Error";
				$texto['texto2'] = "No se ha encontrado el archivo del documento en el servidor";
				$this->load->view('templates/includes');
				$this->load->view('templates/navigation-bar');
				$this->load->view('templates/mensaje_generico', $texto);
				$this->load->view('templates/footer');
			}
		}else{
			$texto['texto1'] ="¡Error!";
			$texto['texto2'] = "Otro(s) usuario(s) se encuentra(n) revisando este documento, no podrá actualizarlo hasta que el o ellos lo libere(n)";
			$this->load->view('templates/includes');
			$this->load->view('templates/navigation-bar');
			$this->load->view('templates/mensaje_generico', $texto);
			$this->load->view('templates/footer');
		}
	}
	
	public function liberarRevSinCambios($id_documento){
		$datos['id_documento'] = $id_documento;
		$documento = $this->DocumentosModel->searchDocumentById($id_documento);
		$doc = $documento->result()[0];
		$liberado = $this->DocumentosModel->toCheckout($datos);
		if($liberado){
			//Eliminamos TODOS los borradores de este usuario en este documento.
			$upload_path_borradores = './'.$this->config->item('docs_dir').'/borradores/'.$this->session->userdata('usuario').'/'.$doc->id_calidad.'_borrador*.*';
			$files = glob($upload_path_borradores);
			foreach ($files as $file) {
				if(is_file($file)){
					unlink($file);
				}
			}
			$texto['texto1'] ="¡Liberado!";
			$texto['texto2'] = "Se han eliminado todos sus borradores y ha liberado usted este documento";
		}else{
			$texto['texto1'] ="¡Error!";
			$texto['texto2'] = "Ocurrió un error al liberar el documento en la base de datos";
		}
		
		$this->load->view('templates/includes');
		$this->load->view('templates/navigation-bar');
		$this->load->view('templates/mensaje_generico', $texto);
		$this->load->view('templates/footer');
	}

	public function subirBorrador($id_documento){
		if($this->session->userdata('logged_in')){
			$data['documento'] = $this->DocumentosModel->searchDocumentById($id_documento);
			$this->load->view('templates/includes');
			$this->load->view('templates/navigation-bar');
			$this->load->view('documentos/subir_borrador', $data);
			$this->load->view('templates/footer');
		}else{
	      //Si no hay sesión se redirecciona la página;
	      redirect('login', 'refresh');
	    }
	}

	public function subir_borrador($id_documento){
		if($this->session->userdata('logged_in')){ //verificamos si hay una sesión
			if(!file_exists('./'.$this->config->item('docs_dir').'/borradores/'.$this->session->userdata('usuario'))){
				if(!file_exists('./'.$this->config->item('docs_dir').'/borradores')){
					mkdir('./'.$this->config->item('docs_dir').'/borradores');
					mkdir('./'.$this->config->item('docs_dir').'/borradores/'.$this->session->userdata('usuario'));
					//antes que nada eliminaremos el borrador que tenga sobre este documento.
					$upload_path_borradores = './'.$this->config->item('docs_dir').'/borradores/'.$this->session->userdata('usuario').'/'.str_replace(' ', '_', $this->input->post('id_calidad')).'_borrador*.*';
					$files = glob($upload_path_borradores);
					/*print("<pre>");
					print_r($files);
					print("</pre>");*/
					foreach ($files as $file) {
						if(is_file($file)){
							unlink($file);
						}
					}

					//declaramos las configuraciones del archivo y lo subimos:
					$datos = $this->DocumentosModel->searchDocumentById($id_documento);
					$data = $datos->result()[0];
					$file_configs['file_name'] = $data->id_calidad."_borrador";
					$file_configs['upload_path'] = './'.$this->config->item('docs_dir').'/borradores/'.$this->session->userdata('usuario').'/';
				    $file_configs['allowed_types'] = 'xlsx|xls|pdf|docx|doc|ppt|pptx|txt';
				    $file_configs['max_size']    = '4096';
				    $file_configs['max_width']  = '4096';
				    $file_configs['max_height']  = '4096';
				    $this->load->library('upload', $file_configs);

				    if( ! $this->upload->do_upload('archivo')){ //si no se subió
				    	$error = array('Error' => $this->upload->display_errors());
				    	echo $error['Error'];
				    }else{
				    	$uploaded = array('uploaded_data_info' => $this->upload->data());
					    //nos traemos toda la informacion del usuario de la sesion actual
					    $userdata_result = $this->DocumentosModel->getDatosUsuario($this->session->userdata('usuario'));
					    $userdata = $userdata_result->result()[0];
					    //guardar información del archivo
					    //Se crea el array con la información
						$newBorrador = array(
							'id_borrador' => '',
							'id_usuario' => $userdata->id_usuario,
							'id_documento' => $data->id_documento,
							'archivo_borrador' => $uploaded['uploaded_data_info']['file_name'],
							'fecha' => date('Y-m-d')
							);

						$result = $this->DocumentosModel->agregarBorrador($newBorrador);
						if($result['state']){
							$texto['texto1'] = "¡guardado!";
							$texto['texto2'] = "Se ha subido el borrador del documento ".$data->id_calidad." con éxito";
							
							$this->load->view('templates/includes');
							$this->load->view('templates/navigation-bar');
							$this->load->view('templates/mensaje_generico', $texto);
							$this->load->view('templates/footer');
						}else{
							$texto['texto1'] = "Error:";
							$texto['texto2'] = $result['message'];
							
							$this->load->view('templates/includes');
							$this->load->view('templates/navigation-bar');
							$this->load->view('templates/mensaje_generico', $texto);
							$this->load->view('templates/footer');
						}
				    }
				}else{
					mkdir('./'.$this->config->item('docs_dir').'/borradores/'.$this->session->userdata('usuario'));
					//antes que nada eliminaremos el borrador que tenga sobre este documento.
					$upload_path_borradores = './'.$this->config->item('docs_dir').'/borradores/'.$this->session->userdata('usuario').'/'.str_replace(' ', '_', $this->input->post('id_calidad')).'_borrador*.*';
					$files = glob($upload_path_borradores);
					/*print("<pre>");
					print_r($files);
					print("</pre>");*/
					foreach ($files as $file) {
						if(is_file($file)){
							unlink($file);
						}
					}

					//declaramos las configuraciones del archivo y lo subimos:
					$datos = $this->DocumentosModel->searchDocumentById($id_documento);
					$data = $datos->result()[0];
					$file_configs['file_name'] = $data->id_calidad."_borrador";
					$file_configs['upload_path'] = './'.$this->config->item('docs_dir').'/borradores/'.$this->session->userdata('usuario').'/';
				    $file_configs['allowed_types'] = 'xlsx|xls|pdf|docx|doc|ppt|pptx|txt';
				    $file_configs['max_size']    = '4096';
				    $file_configs['max_width']  = '4096';
				    $file_configs['max_height']  = '4096';
				    $this->load->library('upload', $file_configs);

				    if( ! $this->upload->do_upload('archivo')){ //si no se subió
				    	$error = array('Error' => $this->upload->display_errors());
				    	echo $error['Error'];
				    }else{
				    	$uploaded = array('uploaded_data_info' => $this->upload->data());
					    //nos traemos toda la informacion del usuario de la sesion actual
					    $userdata_result = $this->DocumentosModel->getDatosUsuario($this->session->userdata('usuario'));
					    $userdata = $userdata_result->result()[0];
					    //guardar información del archivo
					    //Se crea el array con la información
						$newBorrador = array(
							'id_borrador' => '',
							'id_usuario' => $userdata->id_usuario,
							'id_documento' => $data->id_documento,
							'archivo_borrador' => $uploaded['uploaded_data_info']['file_name'],
							'fecha' => date('Y-m-d')
							);

						$result = $this->DocumentosModel->agregarBorrador($newBorrador);
						if($result['state']){
							$texto['texto1'] = "¡guardado!";
							$texto['texto2'] = "Se ha subido el borrador del documento ".$data->id_calidad." con éxito";
							
							$this->load->view('templates/includes');
							$this->load->view('templates/navigation-bar');
							$this->load->view('templates/mensaje_generico', $texto);
							$this->load->view('templates/footer');
						}else{
							$texto['texto1'] = "Error:";
							$texto['texto2'] = $result['message'];
							
							$this->load->view('templates/includes');
							$this->load->view('templates/navigation-bar');
							$this->load->view('templates/mensaje_generico', $texto);
							$this->load->view('templates/footer');
						}
				    }
				}
			}else{
				//antes que nada eliminaremos el borrador que tenga sobre este documento.
				$upload_path_borradores = './'.$this->config->item('docs_dir').'/borradores/'.$this->session->userdata('usuario').'/'.str_replace(' ', '_', $this->input->post('id_calidad')).'_borrador*.*';
				$files = glob($upload_path_borradores);
				/*print("<pre>");
				print_r($files);
				print("</pre>");*/
				foreach ($files as $file) {
					if(is_file($file)){
						unlink($file);
					}
				}

				//declaramos las configuraciones del archivo y lo subimos:
				$datos = $this->DocumentosModel->searchDocumentById($id_documento);
				$data = $datos->result()[0];
				$file_configs['file_name'] = $data->id_calidad."_borrador";
				$file_configs['upload_path'] = './'.$this->config->item('docs_dir').'/borradores/'.$this->session->userdata('usuario').'/';
			    $file_configs['allowed_types'] = 'xlsx|xls|pdf|docx|doc|ppt|pptx|txt';
			    $file_configs['max_size']    = '4096';
			    $file_configs['max_width']  = '4096';
			    $file_configs['max_height']  = '4096';
			    $this->load->library('upload', $file_configs);

			    if( ! $this->upload->do_upload('archivo')){ //si no se subió
			    	$error = array('Error' => $this->upload->display_errors());
			    	echo $error['Error'];
			    }else{
			    	$uploaded = array('uploaded_data_info' => $this->upload->data());
				    //nos traemos toda la informacion del usuario de la sesion actual
				    $userdata_result = $this->DocumentosModel->getDatosUsuario($this->session->userdata('usuario'));
				    $userdata = $userdata_result->result()[0];
				    //guardar información del archivo
				    //Se crea el array con la información
					$newBorrador = array(
						'id_borrador' => '',
						'id_usuario' => $userdata->id_usuario,
						'id_documento' => $data->id_documento,
						'archivo_borrador' => $uploaded['uploaded_data_info']['file_name'],
						'fecha' => date('Y-m-d')
						);

					$result = $this->DocumentosModel->agregarBorrador($newBorrador);
					if($result['state']){
						$texto['texto1'] = "¡guardado!";
						$texto['texto2'] = "Se ha subido el borrador del documento ".$data->id_calidad." con éxito";
						
						$this->load->view('templates/includes');
						$this->load->view('templates/navigation-bar');
						$this->load->view('templates/mensaje_generico', $texto);
						$this->load->view('templates/footer');
					}else{
						$texto['texto1'] = "Error:";
						$texto['texto2'] = $result['message'];
						
						$this->load->view('templates/includes');
						$this->load->view('templates/navigation-bar');
						$this->load->view('templates/mensaje_generico', $texto);
						$this->load->view('templates/footer');
					}
			    }
			}
		}else{
	      //Si no hay sesión se redirecciona la página;
	      redirect('login', 'refresh');
	    }
	}

	public function getborrador($user, $id_documento){
		$userdata_result = $this->DocumentosModel->getDatosUsuario($user);
		$userdata = $userdata_result->result()[0];
		$borradores['borradores'] = $this->DocumentosModel->getBorrador($userdata->id_usuario, $id_documento);
		$borradores['ubicacion'] = base_url().$this->config->item('docs_dir')."/".$this->config->item('borradores')."/".$this->session->userdata('usuario')."/";
		if($borradores['borradores']){
			$this->load->view('templates/includes');
			$this->load->view('templates/navigation-bar');
			$this->load->view('documentos/mostrar_borradores_usuario', $borradores);
			$this->load->view('templates/footer');
		}else{
			$texto['texto1'] = "Lo sentimos:";
			$texto['texto2'] = "Este usuario esta revisando el documento, pero no ha subido ningún borrador";
			
			$this->load->view('templates/includes');
			$this->load->view('templates/navigation-bar');
			$this->load->view('templates/mensaje_generico', $texto);
			$this->load->view('templates/footer');
		}
	}
	
	public function eliminar_borrador($id_borr, $id_documento){
		//antes que nada eliminaremos el borrador que tenga sobre este documento.
		$datos = $this->DocumentosModel->searchDocumentById($id_documento);
		$data = $datos->result()[0];
		$upload_path_borradores = './'.$this->config->item('docs_dir').'/borradores/'.$this->session->userdata('usuario').'/'.$data->id_calidad.'_borrador*.*';
		$files = glob($upload_path_borradores);
		/*print("<pre>");
		print_r($files);
		print("</pre>");*/
		foreach ($files as $file) {
			if(is_file($file)){
				if(unlink($file)){
					$eliminado = $this->DocumentosModel->eliminar_borrador($id_borr);
				}
				if($eliminado){
					$texto['texto1'] = "Borrador Eliminado";
					$texto['texto2'] = "¡Ojo! Aunque el borrador se ha eliminado, el archivo sigue en revisión. Si ya no trabajará en el, libere la revisión";
					
					$this->load->view('templates/includes');
					$this->load->view('templates/navigation-bar');
					$this->load->view('templates/mensaje_generico', $texto);
					$this->load->view('templates/footer');
				}
			}
		}
	}

	public function historialDeCambios($id_documento){
		if($this->session->userdata('logged_in')){
			$datosHistorial = $this->DocumentosModel->getCambiosDocumento($id_documento);
			if($datosHistorial){
				//Se genera la tabla
				$tmpl = array(
					'table_open' => '<table border=1 id="ultimosCambios" cellpadding=2 cellspacing=1 width=100%>',
					'heading_row_start'   => '<tr style="background-color: #2ecc71; font-weight:bold; color:white;">',
					'heading_row_end'     => '</tr>',
					'heading_cell_start'  => '<th style="text-align:center;border: 2px solid black;">',
					'heading_cell_end'    => '</th>',
					'row_start'     => '<tr style="background-color: #DBF6ED; align:center; padding:0.5em">',
					'row_alt_start' => '<tr bgcolor="white">',
					'row_end'             => '</tr>'
				);
				$this->table->set_template($tmpl);
				$this->table->set_heading(array('Documento','ID Calidad', 'Revision', 'Causa del cambio', 'Descripción del cambio', 'Fecha del cambio', 'Usuario', 'Archivo'));
				foreach ($datosHistorial->result() as $doc) {
					if($doc->archivo_obsoleto == "NA"){
						$archivo_obsoleto = "Archivo sin cambios";
					}else{
						$archivo_obsoleto = "<a href='../".$this->config->item('docs_dir')."/".$this->config->item('versiones_obsoletas')."/".date('Y')."/".$doc->archivo_obsoleto."'>".$doc->archivo_obsoleto."</a>";
					}
					$this->table->add_row($doc->nombre_documento, $doc->id_calidad, $doc->revision_ant, $doc->causa_cambio, $doc->desc_cambio, $doc->fecha_cambio, $doc->usuario, $archivo_obsoleto);
				}

				$datos['table'] = $this->table->generate();
			}

			$this->load->view('templates/includes');
			$this->load->view('templates/navigation-bar');
			$this->load->view('documentos/historial_cambios', $datos);
			$this->load->view('templates/footer');
		}
	}
	
	public function listaDocsRevision(){
		if($this->session->userdata('logged_in') && $this->session->userdata('permiso') == "A"){
			$docsRev = $this->DocumentosModel->listaDocsRevision();
			if($docsRev){
				//Se genera la tabla
				$tmpl = array(
					'table_open' => '<table border=1 id="ultimosCambios" cellpadding=2 cellspacing=1 width=100%>',
					'heading_row_start'   => '<tr style="background-color: #2ecc71; font-weight:bold; color:white;">',
					'heading_row_end'     => '</tr>',
					'heading_cell_start'  => '<th style="text-align:center;border: 2px solid black;">',
					'heading_cell_end'    => '</th>',
					'row_start'     => '<tr style="background-color: #DBF6ED; align:center; padding:0.5em; font-size:13px;">',
					'row_alt_start' => '<tr bgcolor="white">',
					'row_end'             => '</tr>'
					);
				$this->table->set_template($tmpl);
				$this->table->set_heading(array('Documento','ID Calidad', 'Revision', 'Usuarios<br /> Revisando', 'Borradores'));
				foreach ($docsRev->result() as $docr) {
					$this->table->add_row("<a href=".base_url()."document/".$docr->id_documento.">".$docr->nombre_documento."</a>", $docr->id_calidad, "<center>".$docr->revision."</center>", "<center>".$docr->num_usuarios."</center>", $docr->num_borradores);
				}
				$datos['table'] = $this->table->generate();
			}else{
				$datos['table'] = "<h2>No existen documentos en <br />revisión en este momento</h2>";
			}
			$this->load->view('templates/includes');
			$this->load->view('templates/navigation-bar');
			$this->load->view('documentos/listaDocsRevision', $datos);
			$this->load->view('templates/footer');
			
		}else{
			//Si no hay sesión o no tiene permiso se le hace saber;
			$datos['texto1'] = "Página Restringida";
			$datos['texto2'] = "Es posible que usted no haya iniciado sesión o no tenga permiso a esta área";
	     	$this->load->view('templates/navigation-bar.php');
			$this->load->view('templates/mensaje_generico', $datos);
			$this->load->view('templates/footer.php');
		}
	}
	
	public function historialCambiosForm(){
		if($this->session->userdata('logged_in') && ($this->session->userdata('permiso') == "A")){
			$this->load->view('templates/includes');
			$this->load->view('templates/navigation-bar');
			$this->load->view('documentos/historial_CambiosConcentrado');
			$this->load->view('templates/footer');
		}
	}
	
	public function historialCambiosConcentrado(){
		if($this->session->userdata('logged_in') && ($this->session->userdata('permiso') == "A")){
			//$this->output->cache(2);
			$inicio = $this->input->post("inicio");
			$final = $this->input->post("final");
			$histCambios = $this->DocumentosModel->historialCambiosConcentrado($inicio, $final);
			if($histCambios){
				//Se genera la tabla
				$tmpl = array('table_open' => '<table border=1 id="ultimosCambios" cellpadding=2 cellspacing=1 width=100%>',
					'heading_row_start'   => '<tr style="background-color: #2ecc71; font-weight:bold; color:white;">',
					'heading_row_end'     => '</tr>',
					'heading_cell_start'  => '<th style="text-align:center;border: 2px solid black;">',
					'heading_cell_end'    => '</th>',
					'row_start'     => '<tr style="background-color: #DBF6ED; align:center; padding:0.5em">',
					'row_alt_start' => '<tr bgcolor="white">',
					'row_end'             => '</tr>'
					);
				$this->table->set_template($tmpl);
				$this->table->set_heading(array('Documento','ID Calidad', 'Causa del cambio', 'Desc. del Cambio', 'Fecha', 'Rev. Anterior', 'Rev. Actual', 'lo cambió'));
				foreach ($histCambios->result() as $histc) {
					$this->table->add_row("<a href='". base_url()."document/".$histc->id_documento."'>".$histc->nombre_documento."</a>", $histc->id_calidad, "<center>".$histc->causa_cambio."</center>", "<center>".$histc->desc_cambio."</center>", $histc->fecha_cambio, $histc->revision_ant, $histc->revision_actual, $histc->usuario);
				}
				$datos['table'] = $this->table->generate();
			}else{
				$datos['table'] = "No se han encontrado cambios entre las fechas especificadas";
			}
			$this->load->view('templates/includes');
			$this->load->view('templates/navigation-bar');
			$this->load->view('documentos/historial_CambiosConcentrado', $datos);
			$this->load->view('templates/footer');
		}
	}
	
	public function enviarNotificacionCambio($datos, $tipo){
		$subject = "Notificación de ".$tipo." documento: ".$datos['nombre_documento']." (".$datos['id_calidad'].")";
		$body = "
		<html>
		</head>
			<title>Notificación de ".$tipo."</title>
			<style type='text/css'>
				#contenedor{
					margin: 0 auto;
					width: 750px;
					text-align: center;
					padding: 2em;
					padding-left: 3em;
					padding-right: 3em;
					font-family: Arial;
				}
				
				table{
					font-family: Arial;
				}

				table{border: solid black 1px;border-collapse: collapse;}
				td{border: solid black 1px; padding: 3px; padding-left: 5px;}

			</style>
			<meta charset=UTF-8'>
		</head>
		<body>
			<div id='contenedor' >
				<div style='text-align:right;'>".date('d/m/Y')."</div>
				<span id='title'>
					<h2>Notificación de ".$tipo." documento</h2>
				</span>
				<div id='contenido' >
					<div id='verAnt' style='float:left;'>Versión Anterior: ".$datos['revision_ant']."</div>
					<div id='verNue' style='float:right;'>Nueva Versión:".$datos['revision_actual']."</div>
					<table style='margin: 0 auto; width:100%;'>
						<tr>
							<td>Documento:</td>
							<td>".$datos['nombre_documento']." (".$datos['id_calidad'].")</td>
						</tr>
						<tr>
							<td>Descripci&oacute;n del cambio:</td>
							<td>".$datos['desc_cambio']."</td>
						</tr>
						<tr>
							<td>Causa del cambio:</td>
							<td>".$datos['causa_cambio']."</td>
						</tr>";
						if($tipo != "eliminación de"){
						$body .= "<tr>
							<td colspan=2>Puede ver la actualizacion en el siguiente <a href='".$this->config->item('dominio')."/".$this->config->item('app_name')."/document/".$datos['id_documento']."'>link</a></td>
						</tr>";
						}
					$body .= "</table>
				</div><br /><br /><br />
				<div style='text-align:left;'>";
					if($tipo != "eliminación de"){
						$body .= "Puede revisar este documento dentro del SGC web en calidad.tpitic.com.mx.<br />";
					}
					$body .= "Cualquier duda o aclaraci&oacute;n favor de enviarlo a:<br /><br />";
					$admins = $this->DocumentosModel->getAdministradores();
					$admins = $admins->result();
					foreach($admins as $ad){
						$body .= $ad->nombre."<br />
						<strong>".$ad->nombre_puesto."</strong><br />
						<a href='mailto:".$ad->correo."'>".$ad->correo."</a><br /><br />";
					}
					$body .= "<br />Depto. de Calidad <a href='mailto:calidad@tpitic.com.mx'>calidad@tpitic.com.mx</a><br /><br />
					<span style='font-size:10px;'>DT/CAL ISO18 Rev. 2, 12-05</span>
				</div>
			</div>
		</body>
		</html>";
		
		$this->email->from($this->config->item('show_email'), $this->config->item('show_email_text'));
		$this->email->subject($subject);
		$this->email->message($body);

		$usuarios = $this->DocumentosModel->searchUsersGrantsDocument($datos['id_documento']);
		$to = "";
		if($usuarios){
			foreach ($usuarios->result() as $usu) {
				if($usu->envio_correo == 1){
					$to .= $usu->correo.",";
				}
			}
			$to = substr($to, 0, -1);
		}else{
			$textoNotificacionEnvío = "<br /><h4>No se envió ninguna notificación, al parecer ningún puesto tiene acceso a este documento.</h4>";
		}
		
		$this->email->to($to);
		$this->email->cc($this->config->item('system_admin'));
		if(!$this->email->send(false)) {//al pasarle con parametro el falso no limpia la información del envío de otro modo no se podría mostrar un posible error
		  $textoNotificacionEnvío = "<br />Error al enviar la notificación:<br />".$this->email->print_debugger();
		} else {
			$textoNotificacionEnvío = "<br /><h4>Notificaciones por email enviadas</h4>";
		}
		return $textoNotificacionEnvío;
	}
} //Fin de la clase
