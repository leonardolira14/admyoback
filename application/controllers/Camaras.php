<?
defined('BASEPATH') OR exit('No direct script access allowed');
require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;
/**
 * 
 */
class Camaras extends REST_Controller
{
	
	function __construct()
	{
		header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
		header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
		header("Access-Control-Allow-Origin: *");
		parent::__construct();
		$this->load->model("Model_Usuario");
		$this->load->model("Model_Empresa");
		$this->load->model("Model_Camaras");
	}
	public function delete_post(){
		
		$datos=$this->post();
		
		$_Token=$datos["token"];
		$_ID_Empresa=$datos["IDEmpresa"];
		
		if($this->checksession($_Token,$_ID_Empresa)===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de Sesion";
		}else{
			$this->Model_Camaras->delete($datos["IDAsocia"]);
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$_data["result"]=$this->Model_Camaras->getall($_ID_Empresa);
		}
		$data["response"]=$_data;
		$this->response($data);
	}
	public function update_post(){
		
		$datos=$this->post();
		
		$_Token=$datos["token"];
		$_ID_Empresa=$datos["IDEmpresa"];
		
		if($this->checksession($_Token,$_ID_Empresa)===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de Sesion";
		}else{
			$this->Model_Camaras->update($datos["IDAsocia"],$datos["Asociacion"],$datos["Web"]);
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$_data["result"]=$this->Model_Camaras->getall($_ID_Empresa);
		}
		$data["response"]=$_data;
		$this->response($data);
	}
	public function save_post(){
		
		$datos=$this->post();
		
		$_Token=$datos["token"];
		$_ID_Empresa=$datos["IDEmpresa"];
		
		if($this->checksession($_Token,$_ID_Empresa)===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de Sesion";
		}else{
			$this->Model_Camaras->save($_ID_Empresa,$datos["Asociacion"],$datos["Web"]);
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$_data["result"]=$this->Model_Camaras->getall($_ID_Empresa);
		}
		$data["response"]=$_data;
		$this->response($data);
	}
	public function getall_post(){
		
		$datos=$this->post();
		//vdebug($datos);
		$_Token=$datos["token"];
		$_ID_Empresa=$datos["IDEmpresa"];
		
		if($this->checksession($_Token,$_ID_Empresa)===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de Sesion";
		}else{
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$_data["result"]=$this->Model_Camaras->getall($_ID_Empresa);
		}
		$data["response"]=$_data;
		$this->response($data);
	}
	function checksession($_Token,$_Empresa){
		//primerocheco el token
		$_datos_Tocken=$this->Model_Usuario->checktoken($_Token);
		$_datos_empresa=$this->Model_Empresa->getempresa($_Empresa);
		if($_datos_Tocken===false){
			return false;
		}else if($_datos_empresa===false){
			return false;
		}else{
			return true;
		}
	}
}