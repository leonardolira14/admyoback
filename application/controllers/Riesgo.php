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
		$this->load->model("Model_RiesgoN");
		$this->load->model("Model_Giros");
	}
	public function getriesgo_post(){
		$datos=$this->post();
		
		$data["Riesgo"]=$this->Model_RiesgoN->RiesgoGen(
			$datos["IDEmpresa"],
			$datos["IDGiro"],
			$datos["Periodo"],
			$datos["Quienes"],
			$datos["comoQue"]
		);
		
		$_data["code"]=0;
		$_data["ok"]="SUCCES";
		$_data["response"]=$data["Riesgo"];
		$this->response($_data,200);
		
	}
	//funcion para obtener el detalle del riesgo
	public function detalle_post(){
		$datos=$this->post();
		
		$data["datos"]=$this->Model_RiesgoN->detalleRiesgo($datos["IDEmpresa"],$datos["IDSector"],$datos["Quienes"],$datos["ComoQue"],$datos["Periodo"]);
		
		$_data["code"]=0;
		$_data["ok"]="SUCCES";
		$_data["response"]=$data;
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