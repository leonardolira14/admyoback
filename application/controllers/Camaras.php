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
		if(isset($datos["datosasociacion"])){
			$_POST = json_decode($datos["datosasociacion"], true);
			//obtengo los datos para guardarlos
			$_ID_Empresa=$_POST["IDEmpresa"];
			$_Nombre=$_POST["Nombre"];
			$_Siglas=$_POST["Siglas"];;
			$_Web=$_POST["Web"];
			(isset($_POST["Direccion"]))?$_Direccion=$_POST["Direccion"] : $_Direccion="";
			(isset($_POST["Colonia"]))?$_Colonia=$_POST["Colonia"] : $_Colonia="";
			(isset($_POST["Municipio"]))?$_Municipio=$_POST["Municipio"] : $_Municipio="";
			(isset($_POST["Estado"]))?$_Estado=$_POST["Estado"] : $_Estado="";
			(isset($_POST["CP"]))?$_CP=$_POST["CP"] : $_CP="";
			(isset($_POST["Telefono"]))?$_Telefono=$_POST["Telefono"] : $_Telefono="";
			
			
			
			if(count($_FILES)!==0){
				$_Imagen=$_FILES["logo"]["name"];	
				$ruta='./assets/img/asociaciones/';
				$rutatemporal=$_FILES["logo"]["tmp_name"];
				$nombreactual=$_FILES["logo"]["name"];
				try {
					if(! move_uploaded_file($rutatemporal, $ruta.$nombreactual)){
						$_data["code"]=1991;
						$_data["ok"]="ERROR";
						$banderaimg=false;
						$_data["result"]="No se puede subir imagen";
					}
					$_IDAsociacion=$this->Model_Camaras->addlistasociacion(
						$_Nombre,
						$_Siglas,
						$nombreactual,
						$_Web,
						$_Estado,
						$_Municipio,
						$_Colonia,				
						$_CP,
						$_Direccion,
						$_Telefono
					);
					$this->Model_Camaras->addrelacion($_ID_Empresa,$_IDAsociacion);
					$_data["result"]=$this->Model_Camaras->getall($_ID_Empresa);				
					$_data["code"]=0;
					$_data["ok"]="SUCCESS";
					
					
				} catch (Exception $e) {
						$_data["code"]=1991;
						$_data["ok"]="ERROR";
						$banderaimg=false;
						$_data["result"]=$e->getMessage();
				}
			}
		}else{
			
			$_ID_Empresa=$datos["IDEmpresa"];
			$_IDAsociacion=$datos["IDAsociasiones"];
			// ahora solo guardo la relacion de camaras
			$this->Model_Camaras->addrelacion($_ID_Empresa,$_IDAsociacion);
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
			$_data["data"]=$this->Model_Camaras->getall_list();
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