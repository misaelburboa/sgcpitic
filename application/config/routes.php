<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	http://codeigniter.com/user_guide/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'HomeController/index';
$route['404_override'] = 'HomeController/override_404';

//SGCPitic
$route['agregarDoc'] = 'DocumentosController/nvodocumento';
$route['savedocument'] = 'DocumentosController/guardarDocumento';
$route['buscar'] = 'DocumentosController/buscarDocumentoForm';
$route['searchdocument'] = 'DocumentosController/buscarDocumento';
$route["document/(:num)"] ="DocumentosController/obtenerDocumento/$1";
$route["actualizarDoc/(:num)"] ="DocumentosController/modificarDocumentoForm/$1";
$route["actualizarDocumento"] ="DocumentosController/actualizarDocumento";
$route["eliminarDoc/(:num)"] ="DocumentosController/eliminarDocumento/$1";
$route["checkin/(:num)"] ="DocumentosController/tocheckin/$1";
$route["checkoutdoc/(:num)"] ="DocumentosController/tocheckout/$1";
$route["checkoutDocument/(:num)"] = "DocumentosController/checkoutdocument/$1";
$route["liberarRevSinCambios/(:num)"] = "DocumentosController/liberarRevSinCambios/$1";
$route["subirborrador/(:num)"] = "DocumentosController/subirborrador/$1";
$route["subir_borrador/(:num)"] = "DocumentosController/subir_borrador/$1";
$route["eliminar_borrador/(:num)/(:num)"] = "DocumentosController/eliminar_borrador/$1/$2";
$route["getborrador/(:any)/(:num)"] = "DocumentosController/getborrador/$1/$2";
$route["historialdecambios/(:num)"] = "DocumentosController/historialdecambios/$1";//Reporte de archivo
$route["historialCambiosForm"] = "DocumentosController/historialCambiosForm/"; //Reporte General
$route["historialCambiosConcentrado"] = "DocumentosController/historialCambiosConcentrado"; //Reporte General
$route["listaDocsRevision"] = "DocumentosController/listaDocsRevision/$1";
$route["agregarDocExterno"] = "DocumentosExternosController/nvoDocumentoExterno";
$route["savedocumentexterno"] = "DocumentosExternosController/guardarDocumentoExterno";
$route["documentExt/(:num)"] ="DocumentosExternosController/obtenerDocumentoExterno/$1";
$route["eliminarDocExt/(:num)"] ="DocumentosExternosController/eliminarDocumentoExt/$1";
$route["home"] ="HomeController/index";
$route["login"] = "LoginController/index";
$route["loginUrl"] = "LoginController/loginUrl";
$route["logout"] = "HomeController/logout";
$route["verifylogin"] = "VerificarLoginController/index";
$route["verifyloginUrl"] = "VerificarLoginController/verifyLoginUrl";
$route["adduser"] = "UsuariosController/agregarUsuarioForm";
$route["manageusers"] = "UsuariosController/manageUsers";
$route["getusers/(:num)"] = "UsuariosController/getUsers/$1";
$route["altaUsuario"] = "UsuariosController/guardarUsuario";
$route["jobpermits"] = "UsuariosController/getPuestos/";
$route["jobpermitsExternos"] = "UsuariosController/getPuestosExternos";
$route["getJobsCurrentDocs/(:num)"] = "UsuariosController/getPermisosActuales/$1";
$route["getJobsCurrentDocsExt/(:num)"] = "UsuariosController/getPermisosActualesExt/$1";
$route["getAvailableDocs/(:num)"] = "UsuariosController/getPermisosDisponibles/$1";
$route["getAvailableDocsExt/(:num)"] = "UsuariosController/getPermisosDisponiblesExt/$1";
$route["removeDocumentAccess/(:num)/(:num)"] = "UsuariosController/quitarPermisosADocumento/$1/$2";
$route["removeDocumentAccessExt/(:num)/(:num)"] = "UsuariosController/quitarPermisosADocumentoExt/$1/$2";
$route["grantDocumentAccess/(:num)/(:num)"] = "UsuariosController/agregarPermisosADocumento/$1/$2";
$route["grantDocumentAccessExt/(:num)/(:num)"] = "UsuariosController/agregarPermisosADocumentoExt/$1/$2";
$route["usuario/(:num)"] = "UsuariosController/usuario/$1";
$route["actualizarUsuario"] = "UsuariosController/actualizarUsuario";
$route["eliminarUsuario/(:num)"] = "UsuariosController/eliminarUsuario/$1";
$route["resetPassword/(:num)"] = "UsuariosController/resetPassword/$1";
$route["ajustesCuenta/(:any)"] = "UsuariosController/ajustesCuenta/$1";
$route["cambiarPassword"] = "UsuariosController/cambiarPassword";
//$route['translate_uri_dashes'] = FALSE;
