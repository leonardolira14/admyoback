<?

defined('BASEPATH') OR exit('No direct script access allowed');
require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;
/**
 * 
 */
class Giro extends REST_Controller
{
	
	function __construct()
	{
		header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
    	header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    	header("Access-Control-Allow-Origin: *");
    	parent::__construct();
    	$this->load->model("Model_Usuario");
    	$this->load->model("Model_Giros");
	}
	public function addnew_post(){
		$datos=$this->post();
		$_Token=$datos["token"];
		$_datos=$datos["datos"];
		$_datos_Tocken=$this->Model_Usuario->checktoken($_Token);
		$bandera=false;

		if($_datos_Tocken===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de sesión";
			$bandera=true;
		}
		if($bandera===false){
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$this->Model_Giros->addgiro($_datos["IDEmpresa"],$_datos["sector"],$_datos["subgiro"],$_datos["rama"]);
			$response["giros"]=$this->Model_Giros->getGirosEmpresa($_datos["IDEmpresa"]);
			$_data["result"]=$response;
		}
		$data["response"]=$_data;
		$this->response($data);
	}
	//funcion para eliminar un giro
	public function delete_post(){
		$datos=$this->post();
		$_Token=$datos["token"];
		$_datos=$datos["datos"];
		$_datos_Tocken=$this->Model_Usuario->checktoken($_Token);
		$bandera=false;
		
		if($_datos_Tocken===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de sesión";
			$bandera=true;
		}
		if($bandera===false){
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$this->Model_Giros->delete($_datos["giro"]);

			$response["giros"]=$this->Model_Giros->getGirosEmpresa($_datos["IDEmpresa"]);
			$_data["result"]=$response;
		}
		$data["response"]=$_data;
		$this->response($data);
	}
	//funcion para eliminar un giro
	public function principal_post(){
		$datos=$this->post();
		$_Token=$datos["token"];
		$_datos=$datos["datos"];
		$_datos_Tocken=$this->Model_Usuario->checktoken($_Token);
		$bandera=false;

		if($_datos_Tocken===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de sesión";
			$bandera=true;
		}
		if($bandera===false){
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$this->Model_Giros->principal($_datos["IDEmpresa"],$_datos["giro"]);
			$response["giros"]=$this->Model_Giros->getGirosEmpresa($_datos["IDEmpresa"]);
			$_data["result"]=$response;
		}
		$data["response"]=$_data;
		$this->response($data);
	}
}