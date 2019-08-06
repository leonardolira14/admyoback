<?
defined('BASEPATH') OR exit('No direct script access allowed');
require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;

/**
 * 
 */
class Marca extends REST_Controller
{
	
	function __construct()
	{
		header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
    	header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    	header("Access-Control-Allow-Origin: *");
    	parent::__construct();
    	$this->load->model("Model_Usuario");
		$this->load->model("Model_Empresa");
		$this->load->model("Model_Marcas");
	}
	//funcion para agregar una marca para una empresa
	public function add_post(){
		$datos=$this->post();
		$_Token=$datos["token"];
		$_ID_Empresa=$datos["empresa"];
		$_Marca=$datos["marca"];
		$bandera=false;
		$banderaimg=true;
		$_Imagen="";
		//primerocheco el token
		if($this->checksession($_Token,$_ID_Empresa)===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de empresa";
			$bandera=true;
		}else {
			//verifico que plan tiene
			$datos_empresa=$this->Model_Empresa->getempresa($_ID_Empresa);
			$num=$this->Model_Marcas->getnum($_ID_Empresa);
			
			if($num==="2" && $datos_empresa["TipoCuenta"]){
				$_data["code"]=1990;
				$_data["ok"]="ERROR";
				$banderaimg=false;
				$_data["result"]="plan_basico";
				$data["response"]=$_data;
				$this->response($data);
				return false;
			}
			if(count($_FILES)!==0){
				$_Imagen=$_FILES["logo"]["name"];	
				$ruta='./assets/img/logosmarcas/';
				$rutatemporal=$_FILES["logo"]["tmp_name"];
				$nombreactual=$_FILES["logo"]["name"];
				try {
					if(! move_uploaded_file($rutatemporal, $ruta.$nombreactual)){
						$_data["code"]=1991;
						$_data["ok"]="ERROR";
						$banderaimg=false;
						$_data["result"]="No se puede subir imagen";
					}
					$_data["code"]=0;
					$_data["ok"]="SUCCESS";
					$this->Model_Marcas->add($_Marca,$_ID_Empresa,$_Imagen);
					$_datas["Marcas"]=$this->Model_Marcas->getMarcasEmpresa($_ID_Empresa);
					$_data["result"]=$_datas;
					
				} catch (Exception $e) {
						$_data["code"]=1991;
						$_data["ok"]="ERROR";
						$banderaimg=false;
						$_data["result"]=$e->getMessage();
				}
		}
	}
		$data["response"]=$_data;
		$this->response($data);
		
	}
	//funcion para eliminar una marca
	public function delete_post(){
		$datos=$this->post();
		$_Token=$datos["token"];
		$_ID_Empresa=$datos["datos"]["empresa"];
		$_ID_Marca=$datos["datos"]["marca"];
		if($this->checksession($_Token,$_ID_Empresa)===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de Sesion";
		}else{
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$this->Model_Marcas->delete($_ID_Marca);
			$_datas["Marcas"]=$this->Model_Marcas->getMarcasEmpresa($_ID_Empresa);
			$_data["result"]=$_datas;
		}
		$data["response"]=$_data;
		$this->response($data);

	}
	public function update_post(){
		$datos=$this->post();
		//primero verifico si han cambiado la imagen
		$_Token=$datos["token"];
		$_ID_Empresa=$datos["empresa"];
		$_ID_Marca=$datos["idmarca"];
		$_Marca=$datos["marca"];
		$_Imagen=false;
		if(count($_FILES)!==0){
				$_Imagen=$_FILES["logo"]["name"];	
				$ruta='./assets/img/logosmarcas/';
				$rutatemporal=$_FILES["logo"]["tmp_name"];
				$nombreactual=$_FILES["logo"]["name"];
				try {
					if(! move_uploaded_file($rutatemporal, $ruta.$nombreactual)){
						$_data["code"]=1991;
						$_data["ok"]="ERROR";
						$banderaimg=false;
						$_data["result"]="No se puede subir imagen";
					}	
					$_data["code"]=0;
					$_data["ok"]="SUCCESS";
					$this->Model_Marcas->update($_ID_Marca,$_Marca,$_Imagen);
					$_datas["Marcas"]=$this->Model_Marcas->getMarcasEmpresa($_ID_Empresa);
					$_data["result"]=$_datas;				
				} catch (Exception $e) {
						$_data["code"]=1991;
						$_data["ok"]="ERROR";
						$banderaimg=false;
						$_data["result"]=$e->getMessage();
				}
			}else{
					$_data["code"]=0;
					$_data["ok"]="SUCCESS";
					$this->Model_Marcas->update($_ID_Marca,$_Marca,$_Imagen);
					$_datas["Marcas"]=$this->Model_Marcas->getMarcasEmpresa($_ID_Empresa);
					$_data["result"]=$_datas;	
			}
			$data["response"]=$_data;
		    $this->response($data);
	}
	//funcion para saber cuantos registros tiene esta empresa
	public function getnum($_Empresa){
		
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