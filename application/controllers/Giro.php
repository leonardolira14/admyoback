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
		$this->load->model("Model_General");
	}
	public function addnew_post(){
		$datos=$this->post();
		//vdebug($datos);
		$_Token=$datos["token"];
		$_datos_Tocken=$this->Model_Usuario->checktoken($_Token);

		if($_datos_Tocken===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de sesión";
			$this->response($_data, REST_Controller::HTTP_NOT_FOUND);
		}
		
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$this->Model_Giros->addgiro($datos["IDEmpresa"],$datos["IDGiro"],$datos["IDGiro2"],$datos["IDGiro3"]);
			$this->response($_data, REST_Controller::HTTP_OK);
	}
	// funcion para obtner todos los giros
	public function getallempresa_post(){
		$datos=$this->post();
		$_Token = $datos["token"];
		$_datos_Tocken = $this->Model_Usuario->checktoken($_Token);
		if ($_datos_Tocken === false) {
			$_data["code"] = 1990;
			$_data["ok"] = "ERROR";
			$_data["result"] = "Error de sesión";
			$this->response($_data, REST_Controller::HTTP_NOT_ACCEPTABLE);
		}
		$_data["giros"]=$this->Model_Giros->getGirosEmpresa($datos["IDEmpresa"]);
		$_data["allgiros"]=$this->Model_General->getAllsector();
		$this->response($_data, REST_Controller::HTTP_OK);
	}
	//funcion para eliminar un giro
	public function delete_post(){
		$datos=$this->post();
		$_Token=$datos["token"];
		$_datos_Tocken=$this->Model_Usuario->checktoken($_Token);
		
		if($_datos_Tocken===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de sesión";
			$this->response($_data, REST_Controller::HTTP_NOT_FOUND);
		}
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$this->Model_Giros->delete($datos["IDGiro"]);

			$_data["giros"]=$this->Model_Giros->getGirosEmpresa($datos["IDEmpresa"]);
			$this->response($_data, REST_Controller::HTTP_OK);
		
		
	}
	//funcion para eliminar un giro
	public function edit_post()
	{
		$datos = $this->post();
		//vdebug($datos);
		$_Token = $datos["token"];
		$_datos_Tocken = $this->Model_Usuario->checktoken($_Token);

		if ($_datos_Tocken === false) {
			$_data["code"] = 1990;
			$_data["ok"] = "ERROR";
			$_data["result"] = "Error de sesión";
			$this->response($_data, REST_Controller::HTTP_NOT_FOUND);
		}

		$_data["code"] = 0;
		$_data["ok"] = "SUCCESS";
		$this->Model_Giros->update($datos["IDGE"], $datos["IDGiro"], $datos["IDGiro2"], $datos["IDGiro3"], $datos["Principal"]);
		$this->response($_data, REST_Controller::HTTP_OK);
	}
	//funcion para eliminar un giro
	public function principal_post(){
		$datos=$this->post();
		$_Token=$datos["token"];
		
		$_datos_Tocken=$this->Model_Usuario->checktoken($_Token);

		if($_datos_Tocken===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de sesión";
			$this->response($_data, REST_Controller::HTTP_NOT_FOUND);
		}
		
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$this->Model_Giros->principal($datos["IDEmpresa"],$datos["IDGiro"]);
			$this->response($_data, REST_Controller::HTTP_OK);
		
		
	}
}