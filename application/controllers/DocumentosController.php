<?php
defined('BASEPATH') OR exit('No direct script access allowed');
//Llamado de la clase para enviar emails
require '/var/www/html/SGCPITIC/vendor/autoload.php'; // carga las librerias del composer.json
//require 'assets/phpmailer/PHPMailerAutoload.php';

class DocumentosController extends CI_Controller {
	const DOCS_DIR = "uploads";
	const BORRADORES = "borradores";
	const VERS_OBSOLETAS = "Versiones obsoletas";
	const APPNAME = "SGCPITIC";

	function __construct(){
		parent::__construct();
		$this->load->model('DocumentosModel');
		$this->load->library('form_validation');
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
			$this->load->view('templates/header.php');
			$this->load->view('templates/left_menu');
			$this->load->view('documentos/nuevo_documento.php', $datos);
			$this->load->view('templates/footer.php');
		}else{
			$data['texto1'] = "Página Restringida";
			$data['texto2'] = "Es posible que no haya iniciado sesión o que usted no tenga permiso para esta sección";
			$this->load->view('templates/header');			
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
				$file_configs['upload_path'] = './'.self::DOCS_DIR.'/';
				$file_configs['allowed_types'] = 'xlsx|xls|docx|doc|ppt|pptx|txt|pdf';
				$file_configs['max_size']    = '9999';
				$file_configs['max_width']  = '9999';
				$file_configs['max_height']  = '9999';
				$this->load->library('upload', $file_configs);

				if(!$this->upload->do_upload('archivo')){
					$error = array('Error' => $this->upload->display_errors());
					print("El siguiente error ha ocurrido: <br /><pre>");
					print_r($error);
					print("</pre>");
				}else{
					$data = array('uploaded_data_info' => $this->upload->data());
					//generamos el pdf con libreoffice que tiene la utilidad soffice
					//es requerimiento tener instalado libreoffice en el servidor y la utilidad headless
					//yum install libreoffice
					//yum install openoffice.org-headless
					exec('soffice --headless --convert-to pdf --outdir '.$_SERVER['DOCUMENT_ROOT'].'/'.self::APPNAME.'/'.self::DOCS_DIR.'/ '.$_SERVER['DOCUMENT_ROOT'].'/'.self::APPNAME.'/'.self::DOCS_DIR.'/'.$data['uploaded_data_info']['file_name'], $output, $return);

					
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
					/*print("<pre>");
					print_r($newDocData);
					print("</pre>");*/
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
						$this->enviarNotificacionCambio($datos);
						
						header('Location: http://calidad.tpitic.com.mx/SGCPITIC/document/'.$lastDocumentID);
					}
				}
		    }else{
					$data['texto1'] = "Error";
					$data['texto2'] = validation_errors();
					$this->load->view('templates/header');			
					$this->load->view('templates/mensaje_generico', $data);
					$this->load->view('templates/footer');
			}
		}else{
	      //Si no hay sesión se redirecciona la página o no hay permisos;
	     	$data['texto1'] = "Error";
			$data['texto2'] = "Es posible que no haya iniciado sesión o que usted no tenga permiso para esta sección";
			$this->load->view('templates/header');			
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
			$this->load->view('templates/header');
			$this->load->view('templates/left_menu.php');
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
			$this->output->cache(1);
			$datos['target'] = $this->input->post('target');
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
							'table_open' => '<table border=1 id="documentos" cellpadding=2 cellspacing=1 width=100%>',
							'row_start'     => '<tr bgcolor="#B5FFB4">',
							'row_alt_start' => '<tr bgcolor="white">',
							'row_end'             => '</tr>'
							);
					$this->table->set_heading(array('#', 'Documento','ID Calidad', 'Revisión', 'Generado por', 'Tiempo de Retención', 'Responsable', 'Nom. Archivo'));
					$i=1;
					foreach ($documentos->result() as $doc){
						if($doc->doc_que_lo_genera != ""){$doc_que_lo_genera = $doc->doc_que_lo_genera;}else{$doc_que_lo_genera=0;}
						$datos2 = $this->DocumentosModel->generadoPor($doc_que_lo_genera);
						$this->table->set_template($tpl);
						if($doc_que_lo_genera!=0){
							foreach($datos2->result() as $gp){
								$this->table->add_row($i, "<a href='document/".$doc->id_documento."'>".$doc->nombre_documento."</a>", $doc->id_calidad, $doc->revision.".".$doc->subrevision, $gp->nombre_documento, $doc->tiempo_retencion_uni." ".$doc->tiempo_retencion_desc, $doc->nombre_puesto, "<a href='".self::DOCS_DIR."/".$doc->vista_archivo."'>".$doc->id_calidad."</a>");
							}
						}else{
							$this->table->add_row($i, "<a href='document/".$doc->id_documento."'>".$doc->nombre_documento."</a>", $doc->id_calidad, $doc->revision.".".$doc->subrevision, "NA", $doc->tiempo_retencion_uni." ".$doc->tiempo_retencion_desc, $doc->nombre_puesto, "<a href='".self::DOCS_DIR."/".$doc->vista_archivo."'>".$doc->id_calidad."</a>");
						}
						$i++;
					}

					$datos['table'] = $this->table->generate();
				}else{
					$datos['mensaje'] = "No se han encontrado resultados para: <br />'<span style='color:red;'>" . $datos['target'] . "</span>'<hr />";
					$datos['table'] = "";
				}
			}
			
			$this->load->view('templates/header');
			$this->load->view('documentos/buscar_documentos', $datos);
			$this->load->view('templates/footer');
		}else{
			//Si no hay sesión se redirecciona la página;
		    redirect('login', 'refresh');
		}
	}

	public function obtenerDocumento($id_documento){
		if($this->session->userdata('logged_in')){
			$documento['documento'] = $this->DocumentosModel->getDocument('id_documento', $id_documento);
			$documento['checkin'] = ($this->DocumentosModel->isInCheckin($id_documento)) ? $this->DocumentosModel->isInCheckin($id_documento) : 0;
			if($documento['documento']){
				$this->load->view('templates/header');
				$this->load->view('templates/left_menu');
				$this->load->view('documentos/documento', $documento);
				$this->load->view('templates/footer');
			}else{
				$error['heading']='¡No encontrado!';
				$error['message']='¡No se ha encontrado el documento solicitado!';
				$this->load->view('templates/header');
				$this->load->view('templates/not_found');
				$this->load->view('templates/footer');
			}
		}else{
			//Si no hay sesión se redirecciona la página;
		    redirect('login', 'refresh');
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
			$this->load->view('templates/header');
			$this->load->view('templates/left_menu');
			$this->load->view('documentos/modificar_documento_form', $data);
			$this->load->view('templates/footer');
		}else{
			//Si no hay sesión o no tiene permiso se le hace saber;
			$datos['texto1'] = "Página Restringida";
			$datos['texto2'] = "Es posible que usted no haya iniciado sesión o no tenga permiso a esta área";
	     	$this->load->view('templates/header.php');
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
					$texto['texto2'] .= $this->enviarNotificacionCambio($datos);
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
				if(file_exists('./'.self::DOCS_DIR.'/'.$this->input->post('archivo_en_servidor'))){
					if(file_exists('./'.self::DOCS_DIR.'/Versiones obsoletas/'.date('Y'))){
			    		rename('./'.self::DOCS_DIR.'/'.$this->input->post('archivo_en_servidor'), './'.self::DOCS_DIR.'/Versiones obsoletas/'.date('Y')."/Rev.".($revisionAnt)."-".$this->input->post('archivo_en_servidor'));
						@unlink('./'.self::DOCS_DIR.'/'.$this->input->post('id_calidad').'.pdf');
			    	}else{
			    		mkdir('./'.self::DOCS_DIR.'/Versiones obsoletas/'.date('Y'));
						rename('./'.self::DOCS_DIR.'/'.$this->input->post('archivo_en_servidor'), './'.self::DOCS_DIR.'/Versiones obsoletas/'.date('Y')."/Rev.".($revisionAnt)."-".$this->input->post('archivo_en_servidor'));
						@unlink('./'.self::DOCS_DIR.'/'.$this->input->post('id_calidad').'.pdf');
			    	}
			    }
				$file_configs['file_name'] = $this->input->post('id_calidad');
				$file_configs['upload_path'] = './'.self::DOCS_DIR.'/';
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
			    	$this->load->view('templates/header');
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
					exec('soffice --headless --convert-to pdf --outdir '.$_SERVER['DOCUMENT_ROOT'].'/'.self::APPNAME.'/'.self::DOCS_DIR.'/ '.$_SERVER['DOCUMENT_ROOT'].'/'.self::APPNAME.'/'.self::DOCS_DIR.'/'.$uploadedData['uploaded_data_info']['file_name'], $output, $return);
					
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
						$texto['texto2'] .= $this->enviarNotificacionCambio($datos);

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
			$this->load->view('templates/header');
			$this->load->view('templates/mensaje_generico', $texto);
			$this->load->view('templates/footer');
		}else{
			//Si no hay sesión se redirecciona la página;
		    redirect('login', 'refresh');
		}
	}

	public function notfound(){
		$this->load->view('templates/header');
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
				if(file_exists('./'.self::DOCS_DIR.'/'.$documento->archivo)){
					if(file_exists('./'.self::DOCS_DIR.'/Versiones obsoletas/'.date('Y'))){
			    		rename('./'.self::DOCS_DIR.'/'.$documento->archivo, './'.self::DOCS_DIR.'/Versiones obsoletas/'.date('Y')."/Rev.".($documento->revision.".".$documento->subrevision)."-".$documento->archivo);
			    	}else{
			    		mkdir('./'.self::DOCS_DIR.'/Versiones obsoletas/'.date('Y'));
						rename('./'.self::DOCS_DIR.'/'.$documento->archivo, './'.self::DOCS_DIR.'/Versiones obsoletas/'.date('Y')."/Rev.".($documento->revision.".".$documento->subrevision)."-".$documento->archivo);
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
					'id_calidad' => $this->input->post('id_calidad'),
					'fecha_cambio' => date('Y-m-d G:i:s'),
					'causa_cambio' => 'Documento Eliminado',
					'desc_cambio' => 'Se Eliminó el documento',
					'usuario' => $this->session->userdata('usuario'),
					'revision_ant' => 'NA',
					'revision_actual' => $documento->revision.".".$documento->subrevision,
					'archivo_obsoleto' => 'NA'
					);
				$resultLogCambios = $this->DocumentosModel->agregarAlLogdeCambios($datos);
				$this->enviarNotificacionCambio($datos);
			}else{
				$texto['texto1'] = "Ocurrió un error:";
				$texto['texto2'] = $result["message"];
			}
			$this->load->view('templates/header');
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
			$this->load->view('templates/header');
			$this->load->view('templates/left_menu');
			$this->load->view('documentos/checkout_doc', $data);
			$this->load->view('templates/footer');
		}else{
	      //Si no hay sesión se redirecciona la página;
	      redirect('login', 'refresh');
	    }
	}

	public function checkoutDocument($id_documento){
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
		if(file_exists('./'.self::DOCS_DIR.'/'.$this->input->post('archivo_en_servidor'))){
			if(file_exists('./'.self::DOCS_DIR.'/Versiones obsoletas/'.date('Y'))){
	    		rename('./'.self::DOCS_DIR.'/'.$this->input->post('archivo_en_servidor'), './'.self::DOCS_DIR.'/Versiones obsoletas/'.date('Y')."/Rev.".($revisionAnt)."-".$this->input->post('archivo_en_servidor'));
				@unlink('./'.self::DOCS_DIR.'/'.$this->input->post('id_calidad').'.pdf');
	    	}else{
	    		mkdir('./'.self::DOCS_DIR.'/Versiones obsoletas/'.date('Y'));
				rename('./'.self::DOCS_DIR.'/'.$this->input->post('archivo_en_servidor'), './'.self::DOCS_DIR.'/Versiones obsoletas/'.date('Y')."/Rev.".($revisionAnt)."-".$this->input->post('archivo_en_servidor'));
				@unlink('./'.self::DOCS_DIR.'/'.$this->input->post('id_calidad').'.pdf');
	    	}
		
			//subir el nuevo archivo
			$file_configs['file_name'] = $this->input->post('id_calidad');
			$file_configs['upload_path'] = './'.self::DOCS_DIR.'/';
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
				$this->load->view('templates/header');
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
				exec('soffice --headless --convert-to pdf --outdir '.$_SERVER['DOCUMENT_ROOT'].'/'.self::APPNAME.'/'.self::DOCS_DIR.'/ '.$_SERVER['DOCUMENT_ROOT'].'/'.self::APPNAME.'/'.self::DOCS_DIR.'/'.$uploadedData['uploaded_data_info']['file_name'], $output, $return);
				
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
						$texto['texto2'] .= $this->enviarNotificacionCambio($datos);

						//como último eliminamos el borrador que tenga sobre este documento.
						$upload_path_borradores = './'.self::DOCS_DIR.'/borradores/'.$this->session->userdata('usuario').'/'.str_replace(' ', '_', $this->input->post('id_calidad')).'_borrador*.*';
						$files = glob($upload_path_borradores);

						foreach ($files as $file) {
							if(is_file($file)){
								unlink($file);
							}
						}
						$folder = './'.self::DOCS_DIR.'/borradores/'.$this->session->userdata('usuario');
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
			$this->load->view('templates/header');
			$this->load->view('templates/mensaje_generico', $texto);
			$this->load->view('templates/footer');
		}
	}

	public function subirBorrador($id_documento){
		if($this->session->userdata('logged_in')){
			$data['documento'] = $this->DocumentosModel->searchDocument($id_documento);
			$this->load->view('templates/header');
			$this->load->view('templates/left_menu');
			$this->load->view('documentos/subir_borrador', $data);
			$this->load->view('templates/footer');
		}else{
	      //Si no hay sesión se redirecciona la página;
	      redirect('login', 'refresh');
	    }
	}

	public function subir_borrador($id_documento){
		if($this->session->userdata('logged_in')){ //verificamos si hay una sesión
			if(!file_exists('./'.self::DOCS_DIR.'/borradores/'.$this->session->userdata('usuario'))){
				if(!file_exists('./'.self::DOCS_DIR.'/borradores')){
					mkdir('./'.self::DOCS_DIR.'/borradores');
					mkdir('./'.self::DOCS_DIR.'/borradores/'.$this->session->userdata('usuario'));
					//antes que nada eliminaremos el borrador que tenga sobre este documento.
					$upload_path_borradores = './'.self::DOCS_DIR.'/borradores/'.$this->session->userdata('usuario').'/'.str_replace(' ', '_', $this->input->post('id_calidad')).'_borrador*.*';
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
					$datos = $this->DocumentosModel->searchDocument($id_documento);
					$data = $datos->result()[0];
					$file_configs['file_name'] = $data->id_calidad."_borrador";
					$file_configs['upload_path'] = './'.self::DOCS_DIR.'/borradores/'.$this->session->userdata('usuario').'/';
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
							
							$this->load->view('templates/header');
							$this->load->view('templates/mensaje_generico', $texto);
							$this->load->view('templates/footer');
						}else{
							$texto['texto1'] = "Error:";
							$texto['texto2'] = $result['message'];
							
							$this->load->view('templates/header');
							$this->load->view('templates/mensaje_generico', $texto);
							$this->load->view('templates/footer');
						}
				    }
				}else{
					mkdir('./'.self::DOCS_DIR.'/borradores/'.$this->session->userdata('usuario'));
					//antes que nada eliminaremos el borrador que tenga sobre este documento.
					$upload_path_borradores = './'.self::DOCS_DIR.'/borradores/'.$this->session->userdata('usuario').'/'.str_replace(' ', '_', $this->input->post('id_calidad')).'_borrador*.*';
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
					$datos = $this->DocumentosModel->searchDocument($id_documento);
					$data = $datos->result()[0];
					$file_configs['file_name'] = $data->id_calidad."_borrador";
					$file_configs['upload_path'] = './'.self::DOCS_DIR.'/borradores/'.$this->session->userdata('usuario').'/';
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
							
							$this->load->view('templates/header');
							$this->load->view('templates/mensaje_generico', $texto);
							$this->load->view('templates/footer');
						}else{
							$texto['texto1'] = "Error:";
							$texto['texto2'] = $result['message'];
							
							$this->load->view('templates/header');
							$this->load->view('templates/mensaje_generico', $texto);
							$this->load->view('templates/footer');
						}
				    }
				}
			}else{
				//antes que nada eliminaremos el borrador que tenga sobre este documento.
				$upload_path_borradores = './'.self::DOCS_DIR.'/borradores/'.$this->session->userdata('usuario').'/'.str_replace(' ', '_', $this->input->post('id_calidad')).'_borrador*.*';
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
				$datos = $this->DocumentosModel->searchDocument($id_documento);
				$data = $datos->result()[0];
				$file_configs['file_name'] = $data->id_calidad."_borrador";
				$file_configs['upload_path'] = './'.self::DOCS_DIR.'/borradores/'.$this->session->userdata('usuario').'/';
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
						
						$this->load->view('templates/header');
						$this->load->view('templates/mensaje_generico', $texto);
						$this->load->view('templates/footer');
					}else{
						$texto['texto1'] = "Error:";
						$texto['texto2'] = $result['message'];
						
						$this->load->view('templates/header');
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
		$borradores['ubicacion'] = base_url().self::DOCS_DIR."/".self::BORRADORES."/".$this->session->userdata('usuario')."/";
		if($borradores['borradores']){
			$this->load->view('templates/header');
			$this->load->view('documentos/mostrar_borradores_usuario', $borradores);
			$this->load->view('templates/footer');
		}else{
			$texto['texto1'] = "Lo sentimos:";
			$texto['texto2'] = "Este usuario esta revisando el documento, pero no ha subido ningún borrador";
			
			$this->load->view('templates/header');
			$this->load->view('templates/mensaje_generico', $texto);
			$this->load->view('templates/footer');
		}
	}

	public function historialDeCambios($id_documento){
		if($this->session->userdata('logged_in')){
			$datosHistorial = $this->DocumentosModel->getCambiosDocumento($id_documento);
			if($datosHistorial){
				//Se genera la tabla
				$tmpl = array('table_open' => '<table border=1 id="documentos" cellpadding=2 cellspacing=1 width=100%>');
				$this->table->set_template($tmpl);
				$this->table->set_heading(array('Documento','ID Calidad', 'Revision', 'Causa del cambio', 'Descripción del cambio', 'Fecha del cambio', 'Usuario', 'Archivo'));
				foreach ($datosHistorial->result() as $doc) {
					if($doc->archivo_obsoleto == "NA"){
						$archivo_obsoleto = "Archivo sin cambios";
					}else{
						$archivo_obsoleto = "<a href='../".self::DOCS_DIR."/".self::VERS_OBSOLETAS."/".date('Y')."/".$doc->archivo_obsoleto."'>".$doc->archivo_obsoleto."</a>";
					}
					$this->table->add_row($doc->nombre_documento, $doc->id_calidad, $doc->revision_ant, $doc->causa_cambio, $doc->desc_cambio, $doc->fecha_cambio, $doc->usuario, $archivo_obsoleto);
				}

				$datos['table'] = $this->table->generate();
			}

			$this->load->view('templates/header');
			$this->load->view('documentos/historial_cambios', $datos);
			$this->load->view('templates/footer');
		}
	}

	public function enviarNotificacionCambio($datos, $usuarios = array("cmburboa", "jrosales")){
		$mail = new PHPMailer();
		//$body             = file_get_contents('contents.html');
		//$body             = eregi_replace("[\]",'',$body);
		$mail->CharSet = "UTF-8";
		$body = "
		<html>
		</head>
			<title>Notificación de cambio</title>
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
					<h2>Notificación de cambio en documento</h2>
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
						</tr>
						<tr>
							<td colspan=2>Puede ver la actualizacion en el siguiente <a href='http://calidad.tpitic.com.mx/".self::APPNAME."/document/".$datos['id_documento']."'>link</a></td>
						</tr>
					</table>
				</div><br /><br /><br />
				<div style='text-align:left;'>
					Puede revisar este documento dentro del SGC web en calidad.tpitic.com.mx.<br />
					Cualquier duda o aclaraci&oacute;n favor de enviarlo a:<br /><br />
					Juli&aacute;n M. Rosales Valenzuela<br />
					<strong>Coordinador de Calidad</strong><br />
					<a href='mailto:jrosales@tpitic.com.mx'>jrosales@tpitic.com.mx</a><br /><br />

					Caleb Misael Burboa Mendoza<br />
					<strong>Auxiliar de Calidad</strong><br />
					<a href='mailto:cmburboa@tpitic.com.mx'>cmburboa@tpitic.com.mx</a><br /><br />

					Depto. de Calidad <a href='mailto:calidad@tpitic.com.mx'>calidad@tpitic.com.mx</a><br /><br />
					<span style='font-size:10px;'>DT/CAL ISO18 Rev. 2, 12-05</span>
				</div>
			</div>
		</body>
		</html>"; 
		$mail->IsSMTP(); 
		$mail->Host       = "smtp.tpitic.com.mx"; // SMTP server
		$mail->SMTPDebug  = 0;                     // enables SMTP debug information (for testing)
		                                           // 1 = errors and messages
		                                           // 2 = messages only
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		$mail->Host       = "smtp.tpitic.com.mx"; // sets the SMTP server
		$mail->Port       = 25;                    // set the SMTP port for the GMAIL server
		$mail->Username   = "cmburboa@tpitic.com.mx"; // SMTP account username
		$mail->Password   = "14705783";        // SMTP account password

		$mail->SetFrom('calidad@tpitic.com.mx', 'Depto. Calidad');
		//$mail->AddReplyTo("name@yourdomain.com","First Last");

		$mail->Subject    = "Notificación de Cambio para ".$datos['nombre_documento']." (".$datos['id_calidad'].")";
		$mail->AltBody    = "Para visualizar este correo, utilice un visor de correos compatible con HTML"; // optional, comment out and test
		$mail->MsgHTML($body);


		$usuarios = $this->DocumentosModel->searchUsersGrantsDocument($datos['id_documento']);
		if($usuarios){
			foreach ($usuarios->result() as $usu) {
				$mail->AddAddress($usu->usuario."@tpitic.com.mx", $usu->nombre);
			}
		}else{
			$textoNotificacionEnvío = "<br /><h4>No se envió ninguna notificación, al parecer ningún puesto tiene acceso a este documento.</h4>";
		}
		//$mail->AddAddress('cmburboa@tpitic.com.mx', 'Depto. Calidad');
		$mail->AddCC('cmburboa@tpitic.com.mx', 'Depto. Calidad');

		/*
		//$mail->AddAttachment("images/phpmailer.gif");      // attachment
		//$mail->AddAttachment("images/phpmailer_mini.gif"); // attachment*/

		if(!$mail->Send()) {
		  $textoNotificacionEnvío = "<br />Error al enviar la notificación:<br />" . $mail->ErrorInfo;
		} else {
			$textoNotificacionEnvío = "<br /><h4>Notificaciones por email enviadas</h4>";
		}
		return $textoNotificacionEnvío;
	}
} //Fin de la clase
