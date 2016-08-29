<?php
defined('BASEPATH') OR exit('No direct script access allowed');
require '/var/www/html/SGCPITIC/vendor/autoload.php'; // carga las librerias del composer.json
class DocumentosExternosController extends CI_Controller{
	function __construct(){
		parent::__construct();
		$this->load->model('DocumentosExternosModel');
		$this->load->model('DocumentosModel');
		$this->load->library('form_validation');
		$this->load->library('email');
	}
	
	public function index(){}
	
	public function nvoDocumentoExterno(){
		if($this->session->userdata('logged_in') && ($this->session->userdata('permiso') == "W" || $this->session->userdata('permiso') == "A")){
			//$this->output->cache(2);
			$datos['puestos'] = $this->DocumentosModel->getPuestos();
			$this->load->view('templates/includes');
			$this->load->view('templates/navigation-bar');
			$this->load->view('documentos/nvo_doc_externo', $datos);
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
	
	public function guardarDocumentoExterno(){
		$this->output->cache(0);
		if($this->session->userdata('logged_in')&&$this->session->userdata('permiso') == "A"){
			// codigo para subir el archivo al servidor
			$lastDocumentExternoID = $this->DocumentosExternosModel->getLastDocumentExternoID()+1;
			$file_configs['file_name'] = $lastDocumentExternoID."_".$this->input->post('nombre_documento');
			$file_configs['upload_path'] = './'.$this->config->item('externos').'/';
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
				//echo 'soffice --headless --convert-to pdf --outdir '.$_SERVER['DOCUMENT_ROOT'].'/'.$this->config->item('app_name').'/'.$this->config->item('externos').'/ '.$_SERVER['DOCUMENT_ROOT'].'/'.$this->config->item('app_name').'/'.$this->config->item('externos').'/'.$data['uploaded_data_info']['file_name'];
				exec('soffice --headless --convert-to pdf --outdir '.$_SERVER['DOCUMENT_ROOT'].'/'.$this->config->item('app_name').'/'.$this->config->item('externos').'/ '.$_SERVER['DOCUMENT_ROOT'].'/'.$this->config->item('app_name').'/'.$this->config->item('externos').'/'.$data['uploaded_data_info']['file_name'], $output, $return);

				
				//guardar información en base de datos
				//Se crea el array con la información				
				
				$newDocExtData = array(
					'id_doc_externo' => $lastDocumentExternoID,
					'nombre_documento' => $this->input->post('nombre_documento'),
					'fecha_captura' => date('Y-m-d G:i:s'),
					'archivo' => $data['uploaded_data_info']['file_name'],
					'vista_archivo' => str_replace(" ", "_", $lastDocumentExternoID."_".$this->input->post('nombre_documento')).'.pdf',
					'activo' => '1'
				);

				//se guarda y se verifica si se guardo la información en la bd
				if($this->DocumentosExternosModel->addDocumentExterno($newDocExtData)){
					//los usuarios que serán notificados de los cambios en este documento.
					$notificar_a = $this->input->post('notificar_a');
					foreach($notificar_a as $na){
						$this->DocumentosExternosModel->asignarDocumentoExternoAPuesto($lastDocumentExternoID, $na);
					}
					$this->enviarNotificacionCambio($newDocExtData, "alta de");
					header('Location: '. base_url().'documentExt/'.$lastDocumentExternoID);
				}
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
	
	public function eliminarDocumentoExt($id_doc_ext){
		if($this->session->userdata('logged_in')){
			$datos['documentos'] = $this->DocumentosExternosModel->getDocumentExt('id_doc_externo', $id_doc_ext); 
			$documentoExt = $datos['documentos']->result()[0];
			$result = $this->DocumentosExternosModel->deleteDocumentExt($id_doc_ext);
			if($result['state']){
				//movemos el archivo a versiones obsoletas
				if(file_exists($_SERVER['DOCUMENT_ROOT']."/".$this->config->item('app_name')."/".$this->config->item('externos').'/'.$documentoExt->archivo)){
					if(unlink($_SERVER['DOCUMENT_ROOT']."/".$this->config->item('app_name')."/".$this->config->item('externos').'/'.$documentoExt->archivo) && unlink($_SERVER['DOCUMENT_ROOT']."/".$this->config->item('app_name')."/".$this->config->item('externos').'/'.$documentoExt->vista_archivo)){

					};
			    }else{}
			    //Creamos los mensajes de éxito al marcar documento como eliminado (0)
				$texto['texto1'] = "Documento eliminado.";
				$texto['texto2'] = $result["message"];
				//agregamos el movimiento al Log de documentos:
				$datos = array(
					'nombre_documento' => $documentoExt->nombre_documento,
					'id_doc_ext' => $id_doc_ext,
					);

				$this->enviarNotificacionCambio($datos, "eliminaci&oacute;n de"); // NO cambiar parametro de "eliminacion de" ya que
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
	
	public function obtenerDocumentoExterno($id_documento){
		if($this->session->userdata('logged_in')){
			$documento['documento'] = $this->DocumentosExternosModel->getDocumentExt('d.id_doc_externo', $id_documento);
			if($documento['documento']){
				$this->load->view('templates/includes');
				$this->load->view('templates/navigation-bar');
				$this->load->view('documentos/documento_ext', $documento);
				$this->load->view('templates/footer');
			}else{
				$datos['texto1']='&iexcl;Error!';
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
	
	public function enviarNotificacionCambio($datos, $tipo){
		$subject = "Notificacion de ".utf8_decode($tipo)." documento: ".$datos['nombre_documento']." (DOCUMENTO EXTERNO)";
		$body = "
		<html>
		</head>
			<title>Notificacion de ".utf8_decode($tipo)."</title>
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
					<h2>Notificacion de ".utf8_decode($tipo)." documento</h2>
				</span>
				<div id='contenido' >
					<div id='verAnt' style='float:left;'>Version Anterior: NA </div>
					<div id='verNue' style='float:right;'>Nueva Version: NA</div>
					<table style='margin: 0 auto; width:100%;'>
						<tr>
							<td>Documento:</td>
							<td>".$datos['nombre_documento']." (DOCUMENTO EXTERNO)</td>
						</tr>";
						if($tipo != "eliminaci&oacute;n de"){
						$body .= "<tr>
							<td colspan=2>Puede ver la actualizaci&oacute;n en el siguiente <a href='".$this->config->item('dominio')."/".$this->config->item('app_name')."/documentExt/".$datos['id_doc_externo']."'>link</a></td>
						</tr>";
						}
					$body .= "</table>
				</div><br /><br /><br />
				<div style='text-align:left;'>";
					if($tipo != "eliminaci&oacute;n de"){
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
		$this->email->subject(utf8_decode($subject));
		$this->email->message(utf8_decode($body));

		$usuarios = $this->DocumentosExternosModel->searchUsersGrantsDocument($datos['id_doc_ext']);
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
}
?>