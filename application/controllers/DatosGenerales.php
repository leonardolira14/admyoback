<?
defined('BASEPATH') OR exit('No direct script access allowed');
require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;
/**
 * Obtener datos Generales
 */
class DatosGenerales extends REST_Controller
{
	
	function __construct()
	{
		header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
    	header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    	header("Access-Control-Allow-Origin: *");
    	parent::__construct();
    	$this->load->model("Model_General");
    	$this->load->model("Model_Usuario");
    	$this->load->model("Model_Follow");
    	$this->load->model("Model_Notificaciones");
    	$this->load->model("Model_Empresa");
    	$this->load->model("Model_Imagen");
    	$this->load->model("Model_Giros");
		$this->load->model("Model_Marcas");
		$this->load->model("Model_Norma");
		$this->load->model("Model_RiesgoN");
	}

	public function cerrarsession_post(){
		$datos=$this->post();
		
		$_Token=$datos["token"];
		
		//verifico que el token sea valido si no lo saco de la session 
		$_datos_Tocken=$this->Model_Usuario->checktoken($_Token);
		$this->Model_Usuario->cerrar($_Token);
		$_data["code"]=0;
		$_data["ok"]="SUCCES";
		$data["response"]=$_data;
		$this->response($data);




	}
	//funcion para obtener los todos los sectores
	public function getSector_get(){
		try{
			$data["result"]=$this->Model_General->getAllsector();
			$data["code"]=0;
			$data["ok"]="SUCCES";
			
		}catch(Exception $e){
			$data["code"]=1900;
			$data["ok"]=$e->getMessage();
		}
		$_data["response"]=$data;
		$this->response($_data);
	}
	public function getestados_get(){
		$data["result"]=$this->Model_General->getEstados('42');
		$data["code"]=0;
		$data["ok"]="SUCCES";
		$_data["response"]=$data;
		$this->response($_data);
	}
	public function getSubsector_get(){
		$datos=$this->get();
		try {
			$data["result"]=$this->Model_General->getSubsector($datos["sector"]);
			$data["code"]=0;
			$data["ok"]="SUCCES";
		} catch (Exception $e) {
			$data["code"]=1900;
			$data["ok"]=$e->getMessage();
		}
		$_data["response"]=$data;
		$this->response($_data);
	}
	public function getRama_get(){
		$datos=$this->get();
		try {
			$data["result"]=$this->Model_General->getRama($datos["subsector"]);
			$data["code"]=0;
			$data["ok"]="SUCCES";
		} catch (Exception $e) {
			$data["code"]=1900;
			$data["ok"]=$e->getMessage();
		}
		$_data["response"]=$data;
		$this->response($_data);
	}
	public function perfil_post(){
		$datos=$this->post();
		$_ID_Empresa=$datos["empresa"];
		$_Token=$datos["token"];
		$bandera=false;
		//verifico que el token sea valido si no lo saco de la session 
		$_datos_Tocken=$this->Model_Usuario->checktoken($_Token);
		$_datos_empresa=$this->Model_Empresa->getempresa($_ID_Empresa);
		if($_datos_Tocken===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["reult"]="Error de sesión";
			$bandera=true;
		}
		if($_datos_empresa===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["reult"]="Error de empresa";
			$bandera=true;
		}
		if($bandera===false){
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			//obtengo las empresa que siguen
			$Follow=$this->Model_Follow->getAll($_ID_Empresa);
			//obtengo las notificaciones
			$Notificaciones=$this->Model_Notificaciones->getten($_ID_Empresa,1);
			//obtengo la imagen como cliente

			$datos["imagencliente"]=$this->Model_Imagen->imgcliente($_ID_Empresa,"A","cliente",$resumen=FALSE);
			$datos["imagenproveedor"]=$this->Model_Imagen->imgcliente($_ID_Empresa,"A","proveedor",$resumen=FALSE);
			$datos["empresa"]=$_datos_empresa;
			$datos["notificaciones"]=$Notificaciones;
			$datos["follow"]=$Follow;
			$_data["result"]=$datos;
		}
		$data["response"]=$_data;
		$this->response($data);
	}
	//funcion para obtener perfil de empresa
	public function perfilempresa_post(){
		$datos=$this->post();
		//vdebug($datos);
		$_ID_Empresa=$datos["empresa"];
		$_Token=$datos["token"];
		$_datos_Tocken=$this->Model_Usuario->checktoken($_Token);
		if($_datos_Tocken===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["reult"]="Error de sesión";
			
		}else{
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			    //obtener los datos giros
			$datos["giros"]=$this->Model_Giros->getGirosEmpresa($_ID_Empresa);
			$datos["allgiros"]=$this->Model_General->getAllsector();
			$datos["tipoempresas"]=$this->Model_General->gettipoempresas();
			$datos["noempleados"]=$this->Model_General->getempleados();
			$datos["factanual"]=$this->Model_General->getfactanual();
			$datos["Estados"]=$this->Model_General->getEstados('42');
				//obtener  Marcas
			$datos["marcas"]=$this->Model_Marcas->getMarcasEmpresa($_ID_Empresa);
			$datos["Normas"]=$this->Model_Norma->getall($_ID_Empresa);

			$_data["result"]=$datos;
		}
		$data["response"]=$_data;
		$this->response($data);
	}
	// function para datos para calificar retorno empresas y sectotes
	public function gedataqualify_get(){
		$data["allgiros"]=$this->Model_General->getAllsector();
		$data['empresas']=$this->Model_Empresa->getempresa_calificar();
		$this->response($data,200);
	}

	// funcion para llenar los campos al selccionar una empresa para calificar
	public function getdataqualifyC_post(){
		$datos=$this->post();
		$_ID_Empresa=$datos["IDEmpresa"];
		// necesito el el correo del usuario principal
		$data["Usuarios"]=$this->Model_Usuario->getAlluser($_ID_Empresa);
		// obtengo el giro principal
		$data["GiroPrincipal"]=$this->Model_Giros->getGiroPrincipalEmpresa($_ID_Empresa);
		$data["Subgiros"]=$this->Model_General->getSubsector($data["GiroPrincipal"][0]['IDGiro']);
		$data["Ramas"]=$this->Model_General->getRama($data["GiroPrincipal"][0]['IDGiro2']);
		$this->response($data,200);
		
	}
	// funcion para obtener el riesgo y la imagen del cliente
	public function getDataPerfil_post(){
		$datos=$this->post();
		
		$respuesta = $this->Model_Imagen->ImagenGenFecha($datos['IDEmpresa'],'cliente',$datos['Periodo']);
		
		$respuesta_ = $this->Model_Imagen->ImagenGenFecha($datos['IDEmpresa'],'proveedor',$datos['Periodo']);
		
		// Riesgo
		$riesgo_cliente_cliente =  $this->Model_RiesgoN->RiesgoGenPerfil($datos['IDEmpresa'],'',$datos['Periodo'],'cliente','cliente');
		$riesgo_cliente_proveedor =  $this->Model_RiesgoN->RiesgoGenPerfil($datos['IDEmpresa'],'',$datos['Periodo'],'cliente','proveedor');
		
		$riesgo_proveedor_cliente =  $this->Model_RiesgoN->RiesgoGenPerfil($datos['IDEmpresa'],'',$datos['Periodo'],'proveedor','cliente');
		$riesgo_proveedor_proveedor =  $this->Model_RiesgoN->RiesgoGenPerfil($datos['IDEmpresa'],'',$datos['Periodo'],'proveedor','proveedor');


		$_data["code"]=0;
		$_data["ok"]="SUCCESS";
		$_data["imagen"]['cliente']=$respuesta;
		$_data["imagen"]['proveedor'] = $respuesta_;
		
		$_data["riesgo"]['cliente']['cliente'] = $riesgo_cliente_cliente;
		$_data["riesgo"]['cliente']['proveedor'] = $riesgo_cliente_proveedor;
		$_data["riesgo"]['proveedor']['cliente'] = $riesgo_proveedor_cliente;
		$_data["riesgo"]['proveedor']['proveedor'] = $riesgo_proveedor_proveedor;

		
		$this->response($_data);
	}
}