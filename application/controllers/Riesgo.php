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
	}
	public function getriesgo_post(){
		$datos=$this->post();
		
		$data=$this->Model_Riesgo->obtenerrisgos($datos["IDEmpresa"],$datos["tipo"],$datos["fecha"],False,$datos["Tipo_Persona"]);
		$_data["code"]=0;
		$_data["ok"]="SUCCES";
		$_data["response"]=array("result"=>$data);
		$this->response($_data);
		
	}
	//funcion para obtener el detalle del riesgo
	public function detalle_post(){
		$datos=$this->post();
		$data=$this->Model_Riesgo->detalles_riesgo($datos["IDEmpresa"],$datos["tipo"],$datos["fecha"],$datos["rama"]);
		$_data["code"]=0;
		$_data["ok"]="SUCCES";
		$_data["response"]=array("result"=>$data);
		$this->response($_data);
	}
}