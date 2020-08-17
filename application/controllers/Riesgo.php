<?
defined('BASEPATH') OR exit('No direct script access allowed');
require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;
/**
 * 
 */
class Riesgo extends REST_Controller
{
	
	function __construct()
	{
		header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
    	header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    	header("Access-Control-Allow-Origin: *");
    	parent::__construct();
		$this->load->model("Model_Riesgo");
		$this->load->model("Model_Giros");
	}
	public function getriesgo_post(){
		$datos=$this->post();
	

		$data["Riesgo"]=$this->Model_Riesgo->obtenerrisgos($datos["IDEmpresa"],$datos["tiempo"],False,$datos["quienes"],$datos["subSector"],$datos["TipoPersonasComo"]);
		//necesito las ramas que tiene esta empresa
		$data["Ramas"]=$this->Model_Giros->getrama_empresa($datos["IDEmpresa"]);
		$_data["code"]=0;
		$_data["ok"]="SUCCES";
		$_data["response"]=array("result"=>$data);
		$this->response($_data);
		
	}
	//funcion para obtener el detalle del riesgo
	public function detalle_post(){
		$datos=$this->post();
		
		$data["datos"]=$this->Model_Riesgo->detalles_riesgo($datos["IDEmpresa"],$datos["tipo"],$datos["fecha"],$datos["rama"]);
		$data["Ramas"]=$this->Model_Giros->getrama_empresa($datos["IDEmpresa"]);
		$_data["code"]=0;
		$_data["ok"]="SUCCES";
		$_data["response"]=array("result"=>$data);
		$this->response($_data);
	}
	//funcion para obtener la lista de los clientes que han mejorado, emperorado, o mantenido en el riesgo
	public function listperson_post(){
		$_datos=$this->post();
		$data["Empresas"]=$this->Model_Riesgo->list_person($_datos["IDEmpresa"],$_datos["forma"],$_datos["tipo"],$_datos["persona"],$_datos["fecha"],$_datos["giro"]);
		
		$_data["response"]=array("result"=>$data);
		$this->response($_data);
	}
}