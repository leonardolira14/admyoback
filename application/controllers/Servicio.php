<?
defined('BASEPATH') OR exit('No direct script access allowed');
require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;
/**
 * 
 */
class Servicio extends REST_Controller
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
			$this->response($_data, REST_Controller::HTTP_NOT_FOUND);
		}else{
			$this->Model_Producto->delete($datos["IDProducto"]);
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$this->response($_data, REST_Controller::HTTP_OK);
		}
		
	}
	public function update_post(){
		$datos=$this->post();
		$_Token=$datos["token"];
		$_ID_Empresa=$datos["IDEmpresa"];
		$_ID_Producto = $datos["IDProducto"];
		$_Producto = $datos["Producto"];
		$_Descripcion = $datos["Descripcion"];
		
		if($this->checksession($_Token,$_ID_Empresa)===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de Sesion";
			$this->response($_data, REST_Controller::HTTP_NOT_FOUND);
		}else{
			$nombreactual=$datos["Foto"];
			if (count($_FILES) !== 0) {
				$_Imagen = $_FILES["Archivo"]["name"];
				$ruta = './assets/img/logoprod/';
				$rutatemporal = $_FILES["Archivo"]["tmp_name"];
				$nombreactual = $_FILES["Archivo"]["name"];
				try {
					if (!move_uploaded_file($rutatemporal, $ruta . $nombreactual)) {
						$_data["code"] = 1991;
						$_data["ok"] = "ERROR";
						$_data["result"] = "No se puede subir imagen";
						$this->response($_data, REST_Controller::HTTP_NOT_FOUND);
					}
				} catch (Exception $e) {
					$_data["code"] = 1991;
					$_data["ok"] = "ERROR";
					$_data["result"] = $e->getMessage();
					$this->response($_data, REST_Controller::HTTP_NOT_FOUND);
				}
			} else {
				$_data["code"] = 0;
				$_data["ok"] = "SUCCESS";
				$this->Model_Producto->update($_ID_Producto, $_Producto, $_Descripcion, $nombreactual);
				$this->response($_data, REST_Controller::HTTP_OK);
			}
			$_data["code"] = 0;
			$_data["ok"] = "SUCCESS";
			$this->Model_Producto->update($_ID_Producto, $_Producto, $_Descripcion, $nombreactual);
			$this->response($_data, REST_Controller::HTTP_OK);
	}
}
	public function save_post(){
		$datos=$this->post();
		$_Token=$datos["token"];
		$_ID_Empresa=$datos["IDEmpresa"];
		// vdebug($datos);
		if($this->checksession($_Token,$_ID_Empresa)===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de Sesion";
			$this->response($_data, REST_Controller::HTTP_NOT_FOUND);
		}else{
			$datos_empresa=$this->Model_Empresa->getempresa($_ID_Empresa);
			$num=$this->Model_Producto->getnum($_ID_Empresa);
			if($num==="2" && $datos_empresa["TipoCuenta"]){
				$_data["code"]=1990;
				$_data["ok"]="ERROR";
				$_data["result"]="plan_basico";
				$this->response($_data, REST_Controller::HTTP_OK);

			}
			//ahora guardo la imagen
			if (count($_FILES) !== 0) {
				$_Imagen = $_FILES["Archivo"]["name"];
				$ruta = './assets/img/logoprod/';
				$rutatemporal = $_FILES["Archivo"]["tmp_name"];
				$nombreactual = $_FILES["Archivo"]["name"];
				try {
					if (!move_uploaded_file($rutatemporal, $ruta . $nombreactual)) {
						$_data["code"] = 1991;
						$_data["ok"] = "ERROR";
						$_data["result"] = "No se puede subir imagen";
						$this->response($_data, REST_Controller::HTTP_NOT_FOUND);
					}
					//AHORA guardo el producto
					$this->Model_Producto->save($datos["IDEmpresa"], $datos["Producto"], $datos["Descripcion"], $nombreactual);
					$_data["code"] = 0;
					$_data["ok"] = "SUCCESS";
					$this->response($_data, REST_Controller::HTTP_OK);
					
				} catch (Exception $e) {
					$_data["code"] = 1991;
					$_data["ok"] = "ERROR";
					$_data["result"] = $e->getMessage();
					$this->response($_data, REST_Controller::HTTP_NOT_FOUND);
				}
			}else{
				$nombreactual='';
				
				//AHORA guardo el producto
				$this->Model_Producto->save($datos["IDEmpresa"], $datos["Producto"], $datos["Descripcion"], $nombreactual);
				$_data["code"] = 0;
				$_data["ok"] = "SUCCESS";
				$this->response($_data, REST_Controller::HTTP_OK);
			}

		}
		
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
