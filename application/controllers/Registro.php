<?


defined('BASEPATH') OR exit('No direct script access allowed');
require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;
/**
 * 
 */
class Registro extends REST_Controller
{
	
	function __construct()
	{
		header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
    	header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    	header("Access-Control-Allow-Origin: *");
    	parent::__construct();
    	$this->load->model("Model_Empresa");
    	$this->load->model("Model_Usuario");
    	$this->load->model("Model_QvalEmpresa");
		$this->load->model("Model_Email");
		$this->load->model("Model_Conecta_qval");
		$this->load->model("Model_Conecta_admyo");

	}
	public function valid_password($password = '')
	{
		$password = trim($password);
		$regex_lowercase = '/[a-z]/';
		$regex_uppercase = '/[A-Z]/';
		$regex_number = '/[0-9]/';
		$regex_special = '/[!@#$%^&*()\-_=+{};:,<.>§~]/';
		if (empty($password))
		{
			$this->form_validation->set_message('valid_password', 'El campo {field} es requerido.');
			return FALSE;
		}
		if (preg_match_all($regex_lowercase, $password) < 1)
		{
			$this->form_validation->set_message('valid_password', 'El campo {field} debe ser al menos una letra minúscula.');
			return FALSE;
		}
		if (preg_match_all($regex_uppercase, $password) < 1)
		{
			$this->form_validation->set_message('valid_password', 'El campo {field} debe ser al menos una letra mayúscula.');
			return FALSE;
		}
		if (preg_match_all($regex_number, $password) < 1)
		{
			$this->form_validation->set_message('valid_password', 'El campo {field} debe tener al menos un número.');
			return FALSE;
		}
		if (preg_match_all($regex_special, $password) < 1)
		{
			$this->form_validation->set_message('valid_password', 'El campo {field} debe tener al menos un carácter especial.' . ' ' . htmlentities('!@#$%^&*()\-_=+{};:,<.>§~'));
			return FALSE;
		}
		if (strlen($password) < 7)
		{
			$this->form_validation->set_message('valid_password', 'El campo {field} debe tener al menos 7 caracteres de longitud.');
			return FALSE;
		}
		if (strlen($password) > 32)
		{
			$this->form_validation->set_message('valid_password', 'El campo {field} no debe sobrepasar los 32 caracteres.');
			return FALSE;
		}
		return TRUE;
	}
	
	//funcion para agregar una empresa
	public function addempresa_post(){
	
		$_POST = json_decode(file_get_contents("php://input"), true);
		
		//vdebug($_POST);
		$config=array( array(
			'field'=>'Razon_Social', 
			'label'=>'Razón Social', 
			'rules'=>'trim|required|xss_clean|is_unique[empresa.Razon_Social]'					
		),array(
			'field'=>'Nombre_Comercial', 
			'label'=>'Nombre Comercial', 
			'rules'=>'trim|required|xss_clean'					
		),array(
			'field'=>'RFC', 
			'label'=>'RFC', 
			'rules'=>'trim|required|xss_clean|is_unique[empresa.RFC]'					
		),array(
			'field'=>'Tipo_Persona', 
			'label'=>'Tipo Persona', 
			'rules'=>'trim|required|xss_clean'					
		),array(
			'field'=>'Sector', 
			'label'=>'Sector', 
			'rules'=>'trim|required|xss_clean'					
		),array(
			'field'=>'SubSector', 
			'label'=>'SubSector', 
			'rules'=>'trim|xss_clean'					
		),array(
			'field'=>'Rama', 
			'label'=>'Rama', 
			'rules'=>'trim|xss_clean'					
		),array(
			'field'=>'Nombre', 
			'label'=>'Nombre', 
			'rules'=>'trim|required|xss_clean'					
		),array(
			'field'=>'Apellidos', 
			'label'=>'Apellidos', 
			'rules'=>'trim|xss_clean'					
		),array(
			'field'=>'Correo1', 
			'label'=>'Correo Electrónico', 
			'rules'=>'trim|required|xss_clean|is_unique[usuarios.Correo]'					
		),array(
			'field'=>'Correo2', 
			'label'=>'Confirmar Correo Electrónico', 
			'rules'=>'trim|required|xss_clean'					
		),array(
			'field'=>'Clave1', 
			'label'=>'Contraseña', 
			'rules'=>'callback_valid_password'					
		),array(
			'field'=>'Clave2', 
			'label'=>'Confirmar Contraseña', 
			'rules'=>'matches[Clave1]'					
		));
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		$this->form_validation->set_rules($config);
			$array=array("required"=>'El campo %s es obligatorio',"valid_email"=>'El campo %s no es valido',"min_length[3]"=>'El campo %s debe ser mayor a 3 Digitos',"min_length[10]"=>'El campo %s debe ser mayor a 10 Digitos','alpha'=>'El campo %s debe estar compuesto solo por letras',"matches"=>"Las contraseñas no coinciden",'is_unique'=>'El contenido del campo %s ya esta registrado');
		$this->form_validation->set_message($array);
		if($this->form_validation->run() !=false){
			//;
			if($_POST["Productoadmyo"]==="gratis"){
				$_Tipo_Cuenta="basic";
			}else{
				$_Tipo_Cuenta=$_POST["Productoadmyo"];
			}

			//ahora agrego la empresa en la base de datos de admyo
			$ID_Empresa_Admyo=$this->Model_Empresa->preaddempresa($_POST["Tipo_Persona"],$_POST["Razon_Social"],$_POST["Nombre_Comercial"],$_POST["RFC"],$_Tipo_Cuenta,"0");
			//agrego al usuario en la base de datos
			$_Token_Usuario=$this->Model_Usuario->addUsuario($ID_Empresa_Admyo,$_POST["Nombre"],$_POST["Apellidos"],$_POST["Correo1"],$_POST["Correo1"],$_POST["Clave1"],"Master",'0',"Master");
			//ahora agrego el giro a la empresa
			$this->Model_Empresa->addgiros($ID_Empresa_Admyo,$_POST["Sector"],$_POST["SubSector"],$_POST["Rama"]);


			//si traigo licencias de qval registro la empresa en qval
			if($_POST["PrecioQval"]!==0){
				$IDQval=$this->Model_QvalEmpresa->addempresa($_POST["Razon_Social"],$_POST["Nombre_Comercial"],$_POST["RFC"],$_POST["Tipo_Persona"],json_encode(array("Producto"=>$_POST["ProductoQval"],"Precio"=>$_POST["PrecioQval"])),$_POST["NlicenasQval"],$ID_Empresa_Admyo);
				//ahora inserto el usuario en la tabla de usauarios de qval
				$this->Model_QvalEmpresa->addusuario($IDQval,$_POST["Nombre"],$_POST["Apellidos"],'Usuario Master','0',$_POST["Correo1"],$_POST["Correo1"],$_POST["Clave1"]);
			}
			//apartir de aqui mando los conrreo para qe se actiben las cuentas
			$this->Model_Email->Activar_Usuario($_Token_Usuario,$_POST["Correo1"],$_POST["Nombre"],$_POST["Apellidos"],$_POST["Razon_Social"]);
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$_data["result"]=$ID_Empresa_Admyo;

		}else{
			$_data["code"]=1990;
			$_data["ok"]="Error";
			$_data["result"]=validation_errors();
		}
		$this->response(array("response"=>$_data));
	}
	public function pago_post(){
		$datos=$this->post();
		//$datos= json_decode(file_get_contents("php://input"), true);
		//vdebug($datos);
		$_nombre=$datos["pago"]["nombre"];
		$_correo=$datos["pago"]["correo"];
		$_tel=$datos["pago"]["tel"];
		$_ID_Empresa=$datos["datosempresa"];
		$_precio_admyo=$datos["pago"]["total"];
		$_tiempo_admyo=$datos["pago"]["tiempo"];
		$_plan_admyo=$datos["pago"]["descripcion"];
		if($_tiempo_admyo===FALSE){
			$_tiempo_admyo=0;
		}
		
		if($datos["pago"]["metodo"]==='Tarjeta'){
			$_token_admyo=$datos["pago"]["token"];
			$respuesta=$this->Model_Conecta_admyo->Tarjeta($_nombre,$_correo,$_token_admyo,$_plan_admyo,$_precio_admyo,$_tel,$_tiempo_admyo);
			
			if($respuesta["status"]==="active"){
				if($datos["pago"]["para"]==='admyo'){
					$this->Model_Empresa->update_datos_conecta('admyo',$_ID_Empresa,$respuesta["customer_id"],$respuesta["plan_id"]);
				}
				if($datos["pago"]["para"]==='qval'){
					$this->Model_Empresa->update_datos_conecta('qval',$_ID_Empresa,$respuesta["customer_id"],$respuesta["plan_id"]);
				}
				$this->Model_Conecta_admyo->save_pago($_ID_Empresa,$datos["pago"]["para"],$_precio_admyo,$respuesta["status"],$respuesta["customer_id"],$respuesta["plan_id"],"Tarjeta");
				$data["ok"]="succes";
				$data["status"]=$respuesta["status"];
			}else{
				$data["ok"]="error";
				$data["status"]=$respuesta;
			}
			$data["pago"]="Tarjeta";
			

		}else{
			$respuesta=$this->Model_Conecta_admyo->tranfer($_precio_admyo, $_nombre, $_correo, $_tel, $_plan_admyo);
			$data["pago"]="Transferencia";
			$data["ID_orden"]=$respuesta->id;
			$data["Bank"]=$respuesta->charges[0]->payment_method->receiving_account_bank;
			$data["CLABE"]=$respuesta->charges[0]->payment_method->receiving_account_number;
			$data["Cantidad"]=$_precio_admyo;
			$data["descripcion"]=$_plan_admyo;
			$data["ok"]="succes";
			$this->Model_Conecta_admyo->save_pago($_ID_Empresa,$datos["pago"]["para"],$_precio_admyo,'espera',$data["ID_orden"],'',"Transferencia");		
		}
		
		$this->response($data);
	}
	//funcion para activar pago 
	public function activarpago_get(){
		$result = @file_get_contents('php://input');
		$fp = fopen('acceso.txt', 'w+');
		fwrite($fp, $result);
		$data = json_decode($result);
		http_response_code(200);
		if(isset($data)){
			if($data->type=='charge.paid'){
				$numreferencia=$data->data->object->order_id;
				$this->Model_Conecta_admyo->activarpago($numreferencia);
			}
		}
		$this->response("echo");
	}
}