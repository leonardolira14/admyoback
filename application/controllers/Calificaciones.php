<?
defined('BASEPATH') OR exit('No direct script access allowed');
require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;
/**
 * 
 */
class Calificaciones extends REST_Controller
{
	
	function __construct()
	{
		header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
    	header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    	header("Access-Control-Allow-Origin: *");
    	parent::__construct();
    	$this->load->model("Model_Calificaciones");
    	$this->load->model("Model_Clieprop");
		$this->load->model("Model_Proveedores");
		$this->load->model("Model_Empresa");
		$this->load->model("Model_Usuario");
		$this->load->model("Model_Email");
		$this->load->model("Model_Notificaciones");
		$this->load->model("Model_Imagen");
		$this->load->model("Model_Follow");
		
	}

	public function getallrealizadas_post(){
		$datos=$this->post();

		$_ID_Empresa=$datos["IDEmpresa"];
		if($datos["tipo"]=="clientes"){
			$resumen=$this->Model_Clieprop->listaclientes($_ID_Empresa);
			$tip="Cliente";
		}else{
			$resumen=$this->Model_Proveedores->listaproveedores($_ID_Empresa);
			$tip="Proveedor";
			
		}
		(!isset($datos["estatus"])?$status="":$status=$datos["estatus"]);
		(!isset($datos["Ifechainicio"])?$fechainicio="":$fechainicio=$datos["Ifechainicio"]);
		(!isset($datos["Ifechafin"])?$fechafin="":$fechafin=$datos["Ifechafin"]);
		(!isset($datos["empresabuscada"])?$empresabuscada="":$empresabuscada=$datos["empresabuscada"]);
		
		$calificaciones=$this->Model_Calificaciones->CalificacionesAcumuladasBruto($_ID_Empresa,"Realizada",$tip,$status,$fechainicio,$fechafin,$empresabuscada);
		
		$_data["code"]=0;
		$_data["ok"]="SUCCESS";
		$_data["result"]=array("lista"=>$resumen,"calificaciones"=>$calificaciones);
		$data["response"]=$_data;
		$this->response($data);
	}
	public function getallrecibidas_post(){
		$datos=$this->post();

		$_ID_Empresa=$datos["IDEmpresa"];
		if($datos["tipo"]=="clientes"){
			$resumen=$this->Model_Clieprop->listaclientes($_ID_Empresa);
			$tip="Cliente";
			
		}else{
			$resumen=$this->Model_Proveedores->listaproveedores($_ID_Empresa);
			$tip="Proveedor";
			
		}

		(!isset($datos["estatus"])?$status="":$status=$datos["estatus"]);
		(!isset($datos["Ifechainicio"])?$fechainicio="":$fechainicio=$datos["Ifechainicio"]);
		(!isset($datos["Ifechafin"])?$fechafin="":$fechafin=$datos["Ifechafin"]);
		(!isset($datos["empresabuscada"])?$empresabuscada="":$empresabuscada=$datos["empresabuscada"]);
		
		$calificaciones=$this->Model_Calificaciones->CalificacionesAcumuladasBruto($_ID_Empresa,"Recibida",$tip,$status,$fechainicio,$fechafin,$empresabuscada);
		
		$_data["code"]=0;
		$_data["ok"]="SUCCESS";
		$_data["result"]=array("lista"=>$resumen,"calificaciones"=>$calificaciones);
		$data["response"]=$_data;
		$this->response($data);
	}
	public function detalles_post(){
		$datos=$this->post();

		$resumen=$this->Model_Calificaciones->detallescalif($datos["IDValora"]);

		$_data["code"]=0;
		$_data["ok"]="SUCCESS";
		$_data["result"]=array("lista"=>$resumen);
		$data["response"]=$_data;
		$this->response($data);
	}
	public function calificar_post(){
		$datos=$this->post();
		$_flag_paso=TRUE;
		$_datos_empresa_emisora=$this->Model_Empresa->getempresa($datos["Emisor"]["IDEmpresa"]);
		$_datos_empresa_receptora=$this->Model_Empresa->datosRFCEm($datos["rfc"]);
		$_Email_Empresa_receptora=$datos["email"];
		$_Razon_Social_Empresa_receptora=$datos["Razon"];
		$_RFC_Empresa_receptora=$datos["rfc"];
		$_Giro_Empresa_emisora=$datos["Giro"];
		if(array_key_exists("Subgiro",$datos)){
			$_SubGiro_Empresa_receptora=$datos["Subgiro"];;
		}else{
			$_SubGiro_Empresa_receptora=0;
		}
		if(array_key_exists("Rama",$datos)){
			$_Rama_Empresa_receptora=$datos["Rama"];;
		}else{
			$_Rama_Empresa_receptora=0;
		}
		
		
		if($_datos_empresa_emisora["IDEmpresa"]===$_datos_empresa_receptora["IDEmpresa"]):
			$_data["code"]=1990;
			$_data["ok"]="Error";
			$_data["mensaje"]="Usted no puede calificarse a si mismo";
			$_flag_paso=FALSE;
		elseif($_datos_empresa_receptora===false):
			$_flag_paso=TRUE;
			$this->Model_Empresa->AddEmpresa($persona='PFAE',$_Razon_Social_Empresa_receptora,"",$_RFC_Empresa_receptora,$_Giro_Empresa_emisora,$_SubGiro_Empresa_receptora,$_Rama_Empresa_receptora);
			$_datos_empresa_receptora=$this->Model_Empresa->datosRFCEm($_RFC_Empresa_receptora);
			$datos["IDReceptor"]=$_datos_empresa_receptora["IDEmpresa"];
					//registro el correo del usuario
			$_token_usario_receptor=$this->Model_Usuario->Preusuario($_Email_Empresa_receptora,$_datos_empresa_receptora["IDEmpresa"]);
					//mando el mail al usario para avisarle que ha sido registrado en admyo
			$this->Model_Email->invitar_usuario($_datos_empresa_receptora["Razon_Social"],$_Email_Empresa_receptora,"PGEG243%",$_token_usario_receptor);
		endif;
		$_datos_usuario_receptor=$this->Model_Usuario->DatosUsuarioCorreo($_Email_Empresa_receptora);
				//si el usuario no esta agregado lo agregamos
		if($_datos_usuario_receptor===false)
		{
			$_token_usario_receptor=$this->Model_Usuario->Preusuario($_Email_Empresa_receptora,$_datos_empresa_receptora["IDEmpresa"]);
						//envio un correo para avisarle al usuario que sea registrado
			$this->Model_Email->invitar_usuario($_datos_empresa_receptora["Razon_Social"],$_Email_Empresa_receptora,"PGEG243%",$_token_usario_receptor);
		}else if($_datos_usuario_receptor["IDEmpresa"]!=$_datos_empresa_receptora["IDEmpresa"]){
		
			$_data["code"]=1990;
			$_data["ok"]="Error";
			$_data["mensaje"]="La dirección de correo electrónico que decea calificar pertenece a otra empresa";
			$_flag_paso=FALSE;
		}
		if(array_key_exists("Subgiro",$datos)){
			$Nivel=$datos["Subgiro"];
		}else{
			$Nivel=$datos["Giro"];	
		}
		
		if($_flag_paso)
		{
			
			$resumen=$this->Model_Calificaciones->cuestionario_calificar($datos["TipoReceptor"],$datos["Emisor"]["IDEmpresa"],$datos["IDReceptor"],$Nivel);
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$_data["result"]=$resumen;
		}
		

		$data["response"]=$_data;
		$this->response($data);
	}
	public function calificarfinal_post(){
		$datos=$this->post();
		
		$_sub_giro=$datos["Subgiro"];
		$IDEmpresa=$datos["IDReceptor"];
		$_tipo_imagen=$datos["TipoReceptor"];
		$_puntos_ob_calidad=0;
		$_puntos_pos_calidad=0;
		$_puntos_ob_cumplimiento=0;
		$_puntos_pos_cumplimiento=0;
		$_puntos_ob_oferta=0;
		$_puntos_pos_oferta=0;
		//primero tengo que guardar la calificaciones netas
		//obtener el giro de la empresa emisora
		$_IDGiro_emisora=$this->Model_Empresa->Get_Giro_Principal($datos["Emisor"]["IDEmpresa"]);
		$_datos_usuario_receptor=$this->Model_Usuario->DatosUsuarioCorreo($datos["email"]);
		$_datos_usuario_emisor=$this->Model_Usuario->DatosUsuario($datos["Emisor"]["IDUsuario"]);
		
		if(isset($datos["IDReceptor"])){
			$_ID_Empresa_receptora=$datos["IDReceptor"];
		}else{
			$_datos_empresa_receptora=$this->Model_Empresa->getempresaRFC($datos["rfc"]);
			$_ID_Empresa_receptora=$_datos_empresa_receptora["IDEmpresa"];
			$datos["IDReceptor"]=$_ID_Empresa_receptora;
		}
		
		$_datos_empresa_emisora=$this->Model_Empresa->getempresa($datos["Emisor"]["IDEmpresa"]);
		$IDValora=$this->Model_Calificaciones->addCalificacion(
			$datos["Emisor"]["IDUsuario"],
			$datos["Emisor"]["IDEmpresa"],
			$_IDGiro_emisora["IDGiro"],
			$_datos_usuario_receptor["IDUsuario"],
			$datos["IDReceptor"],
			$_sub_giro,
			strtoupper($datos["TipoReceptor"])
		);
		//ahora inserto los detalles de esa calificacion
		
		//primero voy con calidad
		$Cuestionario_calidad=$datos["cuestionarios"]["calidad"];
		$cuestionario_gen=[];
		$puntos_obtenidos=0;
		$puntos_posibles=0;
		foreach ($Cuestionario_calidad as $value) {
			$respuesta=$this->Model_Calificaciones->AddDetalleValoracion2('0',$value["Nump"],$value["Respuesta_usuario"]);
			array_push($cuestionario_gen,array("Pregunta"=>$value["Pregunta"],"Respuesta"=>$value["Respuesta_usuario"]));
			$puntos_obtenidos=$puntos_obtenidos+(int)$respuesta["PuntosObtenidos"];
			$puntos_posibles=$puntos_posibles+(int)$respuesta["PuntosPosibles"];
			$_puntos_ob_calidad=$_puntos_ob_calidad+(int)$respuesta["PuntosObtenidos"];
			$_puntos_pos_calidad=$_puntos_pos_calidad+(int)$respuesta["PuntosPosibles"];
		}
		$Cuestionario_cumplimiento=$datos["cuestionarios"]["cumplimiento"];
				
		//cumplimiento
		foreach ($Cuestionario_cumplimiento as $value) {
			$respuesta=$this->Model_Calificaciones->AddDetalleValoracion2('0',$value["Nump"],$value["Respuesta_usuario"]);
			array_push($cuestionario_gen,array("Pregunta"=>$value["Pregunta"],"Respuesta"=>$value["Respuesta_usuario"]));
			$puntos_obtenidos=$puntos_obtenidos+(int)$respuesta["PuntosObtenidos"];
			$puntos_posibles=$puntos_posibles+(int)$respuesta["PuntosPosibles"];
			$_puntos_ob_cumplimiento=$_puntos_ob_cumplimiento+(int)$respuesta["PuntosObtenidos"];
			$_puntos_pos_cumplimiento=$_puntos_pos_cumplimiento+(int)$respuesta["PuntosPosibles"];
		}
		$Cuestionario_oferta=$datos["cuestionarios"]["oferta"];
		
	
		//oferta
		foreach ($Cuestionario_oferta as $value) {
			$respuesta=$this->Model_Calificaciones->AddDetalleValoracion2('0',$value["Nump"],$value["Respuesta_usuario"]);
			array_push($cuestionario_gen,array("Pregunta"=>$value["Pregunta"],"Respuesta"=>$value["Respuesta_usuario"]));
			$puntos_obtenidos=$puntos_obtenidos+(int)$respuesta["PuntosObtenidos"];
			$puntos_posibles=$puntos_posibles+(int)$respuesta["PuntosPosibles"];
			$_puntos_ob_oferta=$_puntos_ob_oferta+(int)$respuesta["PuntosObtenidos"];
			$_puntos_pos_oferta=$_puntos_pos_oferta+(int)$respuesta["PuntosPosibles"];
		}
		$Cuestionario_recomendacion=$datos["cuestionarios"]["recomendacion"];
		
		//recomendacion
		foreach ($Cuestionario_recomendacion as $value) {
			$respuesta=$this->Model_Calificaciones->AddDetalleValoracion2('0',$value["Nump"],$value["Respuesta_usuario"]);
			array_push($cuestionario_gen,array("Pregunta"=>$value["Pregunta"],"Respuesta"=>$value["Respuesta_usuario"]));
			$puntos_obtenidos=$puntos_obtenidos+(int)$respuesta["PuntosObtenidos"];
			$puntos_posibles=$puntos_posibles+(int)$respuesta["PuntosPosibles"];
		}
		$_promedio=round(($puntos_obtenidos/$puntos_posibles)*10,2);
		$this->Model_Imagen->updateimagen(
									$IDEmpresa,
									$puntos_obtenidos,
									$puntos_posibles,
									$_puntos_ob_calidad,
									$_puntos_pos_calidad,
									$_puntos_ob_cumplimiento,
									$_puntos_pos_cumplimiento,
									$_puntos_ob_oferta,
									$_puntos_pos_oferta,
									$_tipo_imagen,
									$_sub_giro
								);
		//ahora mando los mails
		

		$this->Model_Empresa->addRelacion($_ID_Empresa_emisora,$_ID_Empresa_receptora,$datos["TipoReceptor"]);
		/*
		//
		//envio de correos
		//
		*/
		$this->Model_Email->recibir_valoracion($datos["email"],$_datos_empresa_emisora["Razon_Social"],$datos["Razon"],$datos["TipoReceptor"],$cuestionario_gen,$_promedio);
		
		$this->Model_Email->enviar_valoracion($datos["Emisor"]["Correo"],$datos["TipoReceptor"],$_datos_empresa_emisora["Razon_Social"],$_promedio,$datos["Razon"],$cuestionario_gen);
		/*
		//
		//envio de respuesta
		//
		*/
		// agregamos la notificacion de quien realizo la calificacion
		if($datos["TipoReceptor"]==='CLIENTE'){
			$descripemisor="calificacionp";
			$descripreceptor="calificacionrp";
			$_riesgo_notificaion="riesgop";
			$_imagen_notificaion="imagenp";
			$_imagen_notificaion_seguida="imagensp";
			$_riesgo_notificaion_seguida="riegosp";
		}else{
			$descripemisor="calificacionC";
			$descripreceptor="calificacionrc";
			$_riesgo_notificaion="riesgoc";
			$_imagen_notificaion="imagenc";
			$_imagen_notificaion_seguida="imagensc";
			$_riesgo_notificaion_seguida="riegosc";
		}
		$this->Model_Notificaciones->add($_ID_Empresa_receptora,$descripreceptor,$_ID_Empresa_emisora,$_datos_usuario_receptor["IDUsuario"],"crealizadas");
		// agregamos la notificacion a quien se le realizo la calificacion
		$this->Model_Notificaciones->add($_ID_Empresa_emisora,$descripemisor,$_ID_Empresa_receptora,$datos["Emisor"]["IDUsuario"],"crecibidas");
		
		//ahora mando las notificaciones de que la imagen cambio de quien recibio la calificación
		$_lista_clientes=$this->Model_Clieprop->listaclientes($_ID_Empresa_receptora);

		
		foreach ($_lista_clientes as $_empresa) {
			$this->Model_Notificaciones->add($_empresa["num"],$_riesgo_notificaion,$_ID_Empresa_receptora,"0",$_riesgo_notificaion);
			$this->Model_Notificaciones->add($_empresa["num"],$_imagen_notificaion,$_ID_Empresa_receptora,"0",$_imagen_notificaion);
		}
		
		$_lista_proveedores=$this->Model_Proveedores->listaproveedores($_ID_Empresa_receptora);
		foreach ($_lista_proveedores as $_empresa) {
			$this->Model_Notificaciones->add($_empresa["num"],$_riesgo_notificaion,$_ID_Empresa_receptora,"0",$_riesgo_notificaion);
			$this->Model_Notificaciones->add($_empresa["num"],$_imagen_notificaion,$_ID_Empresa_receptora,"0",$_imagen_notificaion);
		}
		
		//ahora mando las notificaciones a las empresas que siguen a esta empresa
		$_lista_follow =$this->Model_Follow->getAll_que_la_siguen($_ID_Empresa_receptora);
		foreach ($_lista_follow as $_empresa) {
			$this->Model_Notificaciones->add($_empresa["num"],$_riesgo_notificaion_seguida,$_ID_Empresa_receptora,"0",$_riesgo_notificaion_seguida);
			$this->Model_Notificaciones->add($_empresa["num"],$_imagen_notificaion_seguida,$_ID_Empresa_receptora,"0",$_imagen_notificaion_seguida);
		}
		
		
		
		$dat["ok"]=1;
		$dat["mensaje"]=$_promedio;
		$this->response($dat);
	}
	//funcion para poner en pendiente de resolucion o anulacion una valoracion
	public function pendiente_post(){
		$datos=$this->post();
		//pongo la valoracion en pendiente
		$this->Model_Calificaciones->cambio_status($datos["valoracion"],$datos["motivo"]);
		$dat["ok"]=1;
		$this->response($dat);
	}
}