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

			$datos["imagencliente"]=$this->Model_Imagen->imgcliente($_ID_Empresa,"A","Cliente",$resumen=FALSE);
			$datos["imagenproveedor"]=$this->Model_Imagen->imgcliente($_ID_Empresa,"A","Proveedor",$resumen=FALSE);
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

			$_data["result"]=$datos;
		}
		$data["response"]=$_data;
		$this->response($data);
	}

}