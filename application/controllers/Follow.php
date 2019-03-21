<?
defined('BASEPATH') OR exit('No direct script access allowed');
require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;
/**
 * 
 */
class Follow extends REST_Controller
{
	
	function __construct()
	{
		header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
    	header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    	header("Access-Control-Allow-Origin: *");
    	parent::__construct();
    	$this->load->model("Model_Usuario");
    	$this->load->model("Model_Empresa");
    	$this->load->model("Model_Follow");
	}
	//funcion para obtener tdos loas empresas seguidas
	public function getallfollow_post(){
		$datos=$this->post();
		$datos=$this->post();
		//vdebug($datos);
		$_Token=$datos["token"];
		$_ID_Empresa=$datos["IDEmpresa"];
		
		if($this->checksession($_Token,$_ID_Empresa)===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de Sesion";
		}else{
			$dat["datos"]=$this->Model_Follow->getAll($_ID_Empresa);
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$_data["result"]=$dat["datos"];
		}
		$data["response"]=$_data;
		$this->response($data);
	}
	public function olvidarfollow_post(){
		$datos=$this->post();
		
		//vdebug($datos);
		$_Token=$datos["token"];
		$_ID_Empresa=$datos["IDEmpresa"];
		
		if($this->checksession($_Token,$_ID_Empresa)===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de Sesion";
		}else{
			//quito la empresa que estoy siguiendo
			$this->Model_Follow->olvidar($datos["IDFollow"]);
			$dat["datos"]=$this->Model_Follow->getAll($_ID_Empresa);
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$_data["result"]=$dat["datos"];
		}
		$data["response"]=$_data;
		$this->response($data);
	}
	public function addfllow_post(){
		$datos=$this->post();
		$_Empresa=$datos["IDEmpresa"];
		$_EmpresaB=$datos["IDEmpresaB"];
		//primero traigo los datos de la empresa
		$_datos_empresa=$this->Model_Empresa->getempresa($_Empresa);
		//ahora traigo los datos del numero de seguidas
		$_num=$this->Model_Follow->get_num($_Empresa);
		if($_Empresa===$_EmpresaB){
			$_data["code"]=2;
			$dat["datos"]="iguales";
		}else if($_datos_empresa["TipoCuenta"]==="basic" && $_num<=10){
			$_data["code"]=0;
			$dat["datos"]=$this->Model_Follow->tb_follow_empresas($_Empresa,$_EmpresaB);
		}else{
			$_data["code"]=1;
			$dat["datos"]="Aumentar";
		}

			
			$_data["ok"]="SUCCESS";
			$_data["result"]=$dat;
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