<?
defined('BASEPATH') OR exit('No direct script access allowed');
require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;
/**
 * 
 */
class ClieProv extends REST_Controller
{
	
	function __construct()
	{
		header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
    	header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    	header("Access-Control-Allow-Origin: *");
    	parent::__construct();
    	$this->load->model("Model_Clieprop");
    	$this->load->model("Model_Proveedores");
	}

	public function getaresumen_post(){
		$datos=$this->post();
		$_ID_Empresa=$datos["IDEmpresa"];
		if($datos["tipo"]=="clientes"){
			$resumen=$this->Model_Clieprop->Resumen($_ID_Empresa);
		}else{
			$resumen=$this->Model_Proveedores->Resumen($_ID_Empresa);
		}
		$_data["code"]=0;
		$_data["ok"]="SUCCESS";
		$_data["result"]=$resumen;
		$data["response"]=$_data;
		$this->response($data);
	}
	public function getlista_post(){
		$datos=$this->post();
		$_ID_Empresa=$datos["IDEmpresa"];
		if($datos["tipo"]=="clientes"){
			$resumen=$this->Model_Clieprop->listaclientes($_ID_Empresa);
		}else{
			$resumen=$this->Model_Proveedores->listaproveedores($_ID_Empresa);
		}
		$_data["code"]=0;
		$_data["ok"]="SUCCESS";
		$_data["result"]=$resumen;
		$data["response"]=$_data;
		$this->response($data);
	}
	//funcion para filtar los clientes
	public function fillter_post(){
		$datos=$this->post();
		// primero vefico si se filtro por nombre
		if(isset($datos["filtros"]["nombre"])){
			$lista=$this->Model_Clieprop->listaclientespalabra($datos["filtros"],$datos["filtros"]["nombre"]);
		}
		vdebug($datos);
	}
}