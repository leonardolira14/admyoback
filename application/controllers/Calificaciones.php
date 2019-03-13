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
	public function detalles_post(){
		$datos=$this->post();

		$resumen=$this->Model_Calificaciones->detallescalif($datos["IDValora"]);

		$_data["code"]=0;
		$_data["ok"]="SUCCESS";
		$_data["result"]=array("lista"=>$resumen);
		$data["response"]=$_data;
		$this->response($data);
	}
}