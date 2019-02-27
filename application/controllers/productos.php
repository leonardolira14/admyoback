<?
defined('BASEPATH') OR exit('No direct script access allowed');
require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;
/**
 * 
 */
class Productos extends REST_Controller
{
	
	function __construct()
	{
		header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
		header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
		header("Access-Control-Allow-Origin: *");
		parent::__construct();
		$this->load->model("Model_Usuario");
		$this->load->model("Model_Empresa");
		$this->load->model("Model_Producto");
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
			
			$this->Model_Producto->delete($datos["IDProducto"]);
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$_data["result"]=$this->Model_Producto->getall($_ID_Empresa);
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
			$nombreactual=$datos["Foto"];
			//ahora guardo la imagen
			if(count($_FILES)>0){
				foreach ($_FILES as $archivo=>$key) {
					if($archivo==="logo"){
						$ruta="assets/img/logoprod/";
						$logo=$key["name"];
					}
					
					
					$rutatemporal=$key["tmp_name"];
					$nombreactual=$key["name"];
					move_uploaded_file($rutatemporal, $ruta.$nombreactual);
				}
			}
			//AHORA guardo el producto
			$this->Model_Producto->update($datos["IDProducto"],$datos["Producto"],$datos["Promocion"],$datos["Descripcion"],$nombreactual);
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$_data["result"]=$this->Model_Producto->getall($_ID_Empresa);
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
			//ahora guardo la imagen
			if(count($_FILES)>0){
				foreach ($_FILES as $archivo=>$key) {
					if($archivo==="logo"){
						$ruta="assets/img/logoprod/";
						$logo=$key["name"];
					}
					
					
					$rutatemporal=$key["tmp_name"];
					$nombreactual=$key["name"];
					move_uploaded_file($rutatemporal, $ruta.$nombreactual);
				}
			}
			//AHORA guardo el producto
			$this->Model_Producto->save($datos["IDEmpresa"],$datos["Producto"],$datos["Promocion"],$datos["Descripcion"],$nombreactual);
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$_data["result"]=$this->Model_Producto->getall($_ID_Empresa);
		}
		$data["response"]=$_data;
		$this->response($data);
		
	}
	//funcion para obtener los productos o serviciode una empresa
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
			$_data["result"]=$this->Model_Producto->getall($_ID_Empresa);
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
