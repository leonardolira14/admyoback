<?php
defined('BASEPATH') OR exit('No direct script access allowed');


$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['getallsector']="DatosGenerales/getSector";
$route['getallsubsector']="DatosGenerales/getSubsector";
$route['getallrama']="DatosGenerales/getRama";
$route['saveregister']="Registro/addempresa";
$route['login']="Usuario/login";
$route['getperfil']="DatosGenerales/perfil";
$route['getperfilempresa']="DatosGenerales/perfilempresa";
$route['reggiro']="Giro/addnew";
$route['deletegiro']="Giro/delete";
$route['updategiro']="Giro/edit";
$route['principal']="Giro/principal";
$route['addmarca']="Marca/add";
$route['deletemarca']="Marca/delete";
$route['updatemarca']="Marca/update";
$route['updateempresa']="Empresa/updatedatgen";
$route['updatecontacto']="Empresa/updatecontacto";
$route['gettels']="Empresa/gettels";
$route['addtel']="empresa/addtel";
$route['deletetel']="empresa/deletetel";
$route['updatetel']="empresa/updatetel";
$route['getestados']="datosgenerales/getestados";

$route['usuarioupdate']="usuario/update";
$route['updateclave']="usuario/updateclave";
$route['getalluser']='usuario/getAlluser';
$route['updatestatususer']='usuario/delete';
$route['update']='usuario/update';
$route['saveususer']="usuario/saveususer";
$route['master']="usuario/master";

$route['getallprducts']="Productos/getall";
$route['saveprducts']="Productos/save";
$route['updateprducts']="Productos/update";
$route['deleteprducts']="Productos/delete";

$route["getallnorma"]="norma/getall";

