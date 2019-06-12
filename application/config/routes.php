<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route["getallempresas"]="Empresa/getall";
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

$route['getallprducts']="Servicio/getall";
$route['saveprducts']="Servicio/save";
$route['updateprducts']="Servicio/update";
$route['deleteprducts']="Servicio/delete";

$route["getallnorma"]="norma/getall";
$route["savenorma"]="norma/save";
$route["updatenorma"]="norma/update";
$route["deletenorma"]="norma/delete";

$route["getallcamara"]="camaras/getall";
$route["savecamara"]="camaras/save";
$route["updatecamara"]="camaras/update";
$route["deletecamara"]="camaras/delete";

$route["visitas"]="Visitas/Visitasg";

$route["getallfollow"]="Follow/getallfollow";
$route["olvidarfollow"]="Follow/olvidarfollow";
$route["addfollow"]="Follow/addfllow";

$route["perfil"]="Busqueda/perfil";
$route["busquedas"]="Busqueda/busquedas";

$route["cerrarsession"]="DatosGenerales/cerrarsession";

$route["getimagen"]="imagen/getImagen";
$route["detallesimagen"]="imagen/detalle";

$route["getriesgo"]="Riesgo/getriesgo";
$route["getdetalle"]="Riesgo/detalle";

$route["getaresumen"]="ClieProv/getaresumen";
$route["getlista"]="ClieProv/getlista";

$route["getallrealizadas"]="Calificaciones/getallrealizadas";
$route["detallescalificacion"]="Calificaciones/detalles";

$route["getallrecibidas"]="Calificaciones/getallrecibidas";

$route["pendientevaloracion"]="Calificaciones/pendiente";

//rutas para las calificaciones
$route["getcuestionario"]="Calificaciones/calificar";
$route["calificar"]="Calificaciones/calificarfinal";

//funcion para realziar los cargos
$route["pago"]="Registro/pago";
$route["activarpago"]="Registro/activarpago";