<?
defined('BASEPATH') OR exit('No direct script access allowed');
require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;
/**
 * 
 */
class Norma extends REST_Controller
{
	
	function __construct()
	{
		header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
		header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
		header("Access-Control-Allow-Origin: *");
		parent::__construct();
		$this->load->model("Model_Usuario");
		$this->load->model("Model_Empresa");
		$this->load->model("Model_Norma");
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
			$_data["result"]=$this->Model_Norma->getall($_ID_Empresa);
		}
		$data["response"]=$_data;
		$this->response($data);
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
			$this->Model_Norma->DelCert($datos["IDNorma"]);
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$_data["result"]=$this->Model_Norma->getall($_ID_Empresa);
		}
		$data["response"]=$_data;
		$this->response($data);
	}
	public function update_post(){
		$datos=$this->post();
		$_Token=$datos["token"];
		$_ID_Empresa=$datos["IDEmpresa"];
		$nombreactual=$datos["Archivo"];
		if($this->checksession($_Token,$_ID_Empresa)===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de Sesion";
		}else{
			
			//ahora guardo la norma 
			if(count($_FILES)>0){
					$ruta="assets/certificaciones/";
					$rutatemporal=$_FILES[0]["tmp_name"];
					$nombreactual=$_FILES[0]["name"];
					move_uploaded_file($rutatemporal, $ruta.$nombreactual);
			}
			//ahora reviso que todo llego bien
			
		$config=array( array(
			'field'=>'Norma', 
			'label'=>'Norma', 
			'rules'=>'trim|required|xss_clean'					
		),array(
			'field'=>'Fecha', 
			'label'=>'Fecha de Certificacion', 
			'rules'=>'trim|required|xss_clean'					
		),array(
			'field'=>'FechaVencimiento', 
			'label'=>'Fecha de Vencimiento', 
			'rules'=>'trim|xss_clean'					
		),array(
			'field'=>'Calificacion', 
			'label'=>'Calificación', 
			'rules'=>'trim|xss_clean'					
		));
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		$this->form_validation->set_rules($config);
			$array=array("required"=>'El campo %s es obligatorio',"valid_email"=>'El campo %s no es valido',"min_length[3]"=>'El campo %s debe ser mayor a 3 Digitos',"min_length[10]"=>'El campo %s debe ser mayor a 10 Digitos','alpha'=>'El campo %s debe estar compuesto solo por letras',"matches"=>"Las contraseñas no coinciden",'is_unique'=>'El contenido del campo %s ya esta registrado');
		$this->form_validation->set_message($array);
			if($this->form_validation->run() !=false){
				//ahora guardo la norma
				$this->Model_Norma->UpdateCert($_POST["IDNorma"],$_POST["Norma"],$_POST["Fecha"],$_POST["Calificacion"],$nombreactual,$_POST["FechaVencimiento"]);
				$_data["code"]=0;
				$_data["ok"]="SUCCESS";
				$_data["result"]=$this->Model_Norma->getall($_ID_Empresa);
			}else{
				$_data["code"]=1990;
				$_data["ok"]="Error";
				$_data["result"]=validation_errors();
			}
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
			$nombreactual="";
			//ahora guardo la norma 
			if(count($_FILES)>0){
					$ruta="assets/certificaciones/";
					$rutatemporal=$_FILES["Archivo"]["tmp_name"];
					$nombreactual=$_FILES["Archivo"]["name"];
					move_uploaded_file($rutatemporal, $ruta.$nombreactual);
			}
			//ahora reviso que todo llego bien
			
			$config=array( array(
				'field'=>'Norma', 
				'label'=>'Norma', 
				'rules'=>'trim|required|xss_clean'					
			),array(
				'field'=>'Fecha', 
				'label'=>'Fecha de Certificacion', 
				'rules'=>'trim|required|xss_clean'					
			),array(
				'field'=>'FechaVencimiento', 
				'label'=>'Fecha de Vencimiento', 
				'rules'=>'trim|xss_clean'					
			),array(
				'field'=>'Calificacion', 
				'label'=>'Calificación', 
				'rules'=>'trim|xss_clean'					
			));
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		$this->form_validation->set_rules($config);
			$array=array("required"=>'El campo %s es obligatorio',"valid_email"=>'El campo %s no es valido',"min_length[3]"=>'El campo %s debe ser mayor a 3 Digitos',"min_length[10]"=>'El campo %s debe ser mayor a 10 Digitos','alpha'=>'El campo %s debe estar compuesto solo por letras',"matches"=>"Las contraseñas no coinciden",'is_unique'=>'El contenido del campo %s ya esta registrado');
		$this->form_validation->set_message($array);
			if($this->form_validation->run() !=false){
				//ahora guardo la norma
				$this->Model_Norma->save($_ID_Empresa,$_POST["Norma"],$_POST["Fecha"],$_POST["Calificacion"],$nombreactual,$_POST["FechaVencimiento"]);
				$_data["code"]=0;
				$_data["ok"]="SUCCESS";
				$_data["result"]=$this->Model_Norma->getall($_ID_Empresa);
			}else{
				$_data["code"]=1990;
				$_data["ok"]="Error";
				$_data["result"]=validation_errors();
			}
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