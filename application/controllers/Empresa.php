<?
defined('BASEPATH') OR exit('No direct script access allowed');
require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;
/**
 * 
 */
class Empresa extends REST_Controller
{
	
	function __construct()
	{
		header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
    	header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    	header("Access-Control-Allow-Origin: *");
    	parent::__construct();
    	$this->load->model("Model_Usuario");
		$this->load->model("Model_Empresa");
		$this->load->model("Model_Conecta_admyo");
	}
	public function updatedatgen_post(){
		$datos=$this->post();
		$logo=false;
		$banner=false;
		$_Token=$datos["token"];
		$_ID_Empresa=$datos["IDEmpresa"];
		$_Razon_Social=$datos["razon_social"];
		$Nombre_Comercial=$datos["nombre_comercial"];
		$rfc=$datos["rfc"];
		$T_empresa=$datos["tempresa"];
		$_Empleados=$datos["nempleados"];
		$_Fac_Anual=$datos["facanual"];
		$_dias_pago_empresa=$datos["diaspagoempresa"];
		$_perfil=$datos["perfil"];
		if($this->checksession($_Token,$_ID_Empresa)===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de Sesion";
		}else{
			//primero verifico si cambiaron el logo o el banner
			if(count($_FILES)>0){
				foreach ($_FILES as $archivo=>$key) {
					if($archivo==="logo"){
						$ruta="assets/img/logosEmpresas/";
						$logo=$key["name"];
					}
					if($archivo==="banner"){
						$ruta="assets/img/banners/";
						$banner=$key["name"];
					}
					$_Imagen=$key["name"];	
					$rutatemporal=$key["tmp_name"];
					$nombreactual=$key["name"];
					move_uploaded_file($rutatemporal, $ruta.$nombreactual);
				}
			}
			//ahora actualizo los datos
			$this->Model_Empresa->update($_ID_Empresa,$_Razon_Social,$Nombre_Comercial,$rfc,$T_empresa,$_Empleados,$_Fac_Anual,$_perfil,$logo,$banner,$_dias_pago_empresa);
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$datas["empresa"]=$this->Model_Empresa->getempresa($_ID_Empresa);
			$_data["result"]=$datas;
		}
		$data["response"]=$_data;
		$this->response($data);
	}
	//funcion para actualizar los datos de contacto
	public function updatecontacto_post(){
		$datos=$this->post();
		$_Token=$datos["token"];
		$_ID_Empresa=$datos["datos"]["IDEmpresa"];
		if($this->checksession($_Token,$_ID_Empresa)===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de Sesion";
		}else{
			$this->Model_Empresa->updatecontacto($datos["datos"]["IDEmpresa"],$datos["datos"]["Sitio_Web"],$datos["datos"]["Direc_Fiscal"],$datos["datos"]["Colonia"],$datos["datos"]["Deleg_Mpo"],$datos["datos"]["Estado"],$datos["datos"]["Codigo_Postal"]);
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$datas["empresa"]=$this->Model_Empresa->getempresa($_ID_Empresa);
			$_data["result"]=$datas;
		}
		$data["response"]=$_data;
		$this->response($data);
	}
	//funcion para obtener los telefonos
	public function gettels_post(){
		$datos=$this->post();
		$_Token=$datos["token"];
		$_ID_Empresa=$datos["datos"];
		if($this->checksession($_Token,$_ID_Empresa)===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de Sesion";
		}else{
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$datas["telefonos"]=$this->Model_Empresa->getTels($_ID_Empresa);
			$_data["result"]=$datas;
		}
		$data["response"]=$_data;
		$this->response($data);
		
	}
	//funcion para agregar un telefono
	public function addtel_post(){
		$datos=$this->post();
		$_Token=$datos["token"];
		$_ID_Empresa=$datos["datos"]["IDEmpresa"];
		
		if($this->checksession($_Token,$_ID_Empresa)===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de Sesion";
		}else{
			//ahora agrego el teledno 
			$this->Model_Empresa->addtel($_ID_Empresa,$datos["datos"]["Numero"],$datos["datos"]["Tipo_Numero"]);
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$datas["telefonos"]=$this->Model_Empresa->getTels($_ID_Empresa);
			$_data["result"]=$datas;
		}
		$data["response"]=$_data;
		$this->response($data);
	}
	public function deletetel_post(){
		$datos=$this->post();
		$_Token=$datos["token"];
		$_ID_Empresa=$datos["datos"]["IDEmpresa"];
		
		if($this->checksession($_Token,$_ID_Empresa)===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de Sesion";
		}else{
			//ahora agrego el teledno 
			$this->Model_Empresa->delete_tel($datos["datos"]["IDTel"]);
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$datas["telefonos"]=$this->Model_Empresa->getTels($_ID_Empresa);
			$_data["result"]=$datas;
		}
		$data["response"]=$_data;
		$this->response($data);
	}
	public function updatetel_post(){
		$datos=$this->post();
		$_Token=$datos["token"];
		$_ID_Empresa=$datos["datos"]["IDEmpresa"];
		
		if($this->checksession($_Token,$_ID_Empresa)===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de Sesion";
		}else{
			//ahora agrego el teledno 
			$this->Model_Empresa->update_tel($datos["datos"]["IDTel"],$datos["datos"]["Numero"],$datos["datos"]["Tipo_Numero"]);
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$datas["telefonos"]=$this->Model_Empresa->getTels($_ID_Empresa);
			$_data["result"]=$datas;
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
	//funcion para obtener todas las empresas para calificarlas
	public function getall_get(){
		
		$_data["code"]=0;
		$_data["ok"]="SUCCESS";
		$_data["result"]=$this->Model_Empresa->getempresa_calificar();
		$data["response"]=$_data;
		$this->response($data);
	}
	public function addRelacion($IDEmpresaP,$IDEmpresaB,$Tipo){
		$tp=$this->db->select('*')->where("IDEmpresaB=$IDEmpresaB and IDEmpresaP=$IDEmpresaP and Tipo='".$Tipo."'")->get("tbrelacion");
		if($tp->num_rows()===0){
			$datos= array('IDEmpresaP' =>$IDEmpresaP,"IDEmpresaB"=>$IDEmpresaB,"Tipo"=>$Tipo);
			$this->db->insert("tbrelacion",$datos);
		}
		
	}
	// funcion para dar de baja una relacion
	public function bajarelacion_post(){
		$datos=$this->post();
		$this->Model_Empresa->update_relacion($datos["relacion"],'0');
		$_data["code"]=0;
		$_data["ok"]="SUCCESS";
		$data["response"]=$_data;
		$this->response($data);
	}
	// funcion para solictar a conecta los datos de un cliente
	public function getdataconecta_post(){
		$datos=$this->post();
		//funcion para obtener los datos de id de para conecta
		$datos_pago=$this->Model_Empresa->getdata_pago($datos["empresa"]);
		$fecha = new DateTime();
		$datos_customer=$this->Model_Conecta_admyo->obtner_info($datos_pago["Customer_id"]);
		$data_response["plan_id"]=$datos_customer["plan_id"];
		$data_response["customer_id"]=$datos_customer["customer_id"];
		$fecha->setTimestamp($datos_customer["billing_cycle_end"]);
		$fecha_cambio=explode("-",$fecha->format('Y-m-d'));
		$data_response["fecha_proximo_cargo"]=$fecha_cambio[2]."-".dame_mes($fecha_cambio[1])."-".$fecha_cambio[0];
		$_data["code"]=0;
		$_data["ok"]="SUCCESS";
		$data["response"]=$data_response;
		$this->response($data);
	}
	//funcion para cargar plan
	public function updateplan_post(){
		$datos=$this->post();
		
		// si el plan es gratis solo cambio a gratis la empresa y elimino el plan si es que esta subscrito
		if($datos["plan_selec"]==="gratis"){
			$this->Model_Empresa->update_plan($datos["IDEmpresa"],"basic");
			 // ahora elimino el plan si es que tiene plan
			 if(isset($datos["datos_cargo"]["plan_id"])){

			 }
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			
		}else{
			vdebug($datos);
		}
		$data["response"]=$_data;
		$this->response($data);
	}
}