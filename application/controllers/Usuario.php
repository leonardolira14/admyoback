<?
defined('BASEPATH') OR exit('No direct script access allowed');
require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;
/**
 * 
 */
class Usuario extends REST_Controller
{
	
	function __construct()
	{
		header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
    	header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    	header("Access-Control-Allow-Origin: *");
    	parent::__construct();
    	$this->load->model("Model_Usuario");
    	$this->load->model("Model_Empresa");
    	$this->load->model("Model_Email");

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
	//funcion para guadar un usuario
	public function saveususer_post(){
		$datos=$this->post();

		$_Token=$datos["token"];
		$_ID_Empresa=$datos["IDEmpresa"];
		if($this->checksession($_Token,$_ID_Empresa)===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de Sesion";
		}else{
			$_POST = json_decode(file_get_contents("php://input"), true);
			$config=array( 
		array(
			'field'=>'Nombre', 
			'label'=>'Nombre', 
			'rules'=>'trim|required|xss_clean'					
		),array(
			'field'=>'Apellidos', 
			'label'=>'Apellidos', 
			'rules'=>'trim|xss_clean'					
		),array(
			'field'=>'Correo', 
			'label'=>'Correo Electrónico', 
			'rules'=>'trim|required|xss_clean|is_unique[usuarios.Correo]'					
		)
		,array(
			'field'=>'Puesto', 
			'label'=>'Puesto', 
			'rules'=>'trim|xss_clean'					
		));
		$this->form_validation->set_error_delimiters('', ',');
		$this->form_validation->set_rules($config);
			$array=array("required"=>'El campo %s es obligatorio',"valid_email"=>'El campo %s no es valido',"min_length[3]"=>'El campo %s debe ser mayor a 3 Digitos',"min_length[10]"=>'El campo %s debe ser mayor a 10 Digitos','alpha'=>'El campo %s debe estar compuesto solo por letras',"matches"=>"Las contraseñas no coinciden",'is_unique'=>'El contenido del campo %s ya esta registrado');
		$this->form_validation->set_message($array);
		if($this->form_validation->run() !=false){
			$clave=generate_clave();
			$_datos_empresa=$this->Model_Empresa->getempresa($_ID_Empresa);
			$num=$this->Model_Usuario->getAlluser($_ID_Empresa);
			$num=count($num);
			if($num===2 && $_datos_empresa["TipoCuenta"]){
				$_data["code"]=1991;
				$_data["ok"]="ERROR";
				$_data["result"]="plan_basico";
				
			}else{
				$_Token_Usuario=$this->Model_Usuario->addUsuario($_ID_Empresa,$_POST["Nombre"],$_POST["Apellidos"],$_POST["Correo"],$_POST["Correo"],$clave,$_POST["Puesto"],'0',"");
				$this->Model_Email->Activar_Usuario($_Token_Usuario,$_POST["Correo"],$_POST["Nombre"],$_POST["Apellidos"],$_POST["Correo"],$clave);


				$respuesta=$this->Model_Usuario->getAlluser($_ID_Empresa);
				$_data["code"]=0;
				$_data["ok"]="SUCCESS";
				$_data["result"]=$respuesta;
			}

			
		}else{
			$_data["code"]=1990;
			$_data["ok"]="Error";
			$_data["result"]=validation_errors();
		}
			//actualizo los datos del usuario
			
		}
		$data["response"]=$_data;
		$this->response($data);
	}
	//funcion para desactivar los usuarios
	public function delete_post(){
		$datos=$this->post();
		$_Token=$datos["token"];
		$_ID_Empresa=$datos["datos"]["IDEmpresa"];
		$_ID_Usuario=$datos["datos"]["IDUsuario"];
		$_Status=$datos["datos"]["Status"];
		if($this->checksession($_Token,$_ID_Empresa)===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de Sesion";
		}else{
			//obtengo el usuario master para avisarle
			$_datos_usuario_master=$this->Model_Usuario->GetMaster($_ID_Empresa);
			//obtengo los datos del correo del usario que se dio de baja
			$_datos_usuario_baja=$this->Model_Usuario->DatosUsuario($_ID_Usuario);
			//actualizo los datos del usuario
			$this->Model_Usuario->updatestatus($_ID_Usuario,$_Status);
			$respuesta=$this->Model_Usuario->getAlluser($_ID_Empresa);
			$this->Model_Email->baja_usuario_admin($_datos_usuario_master["Correo"],$_datos_usuario_baja["Correo"]);
			$this->Model_Email->baja_usuario($_datos_usuario_baja["Correo"]);
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$_data["result"]=$respuesta;
		}
		$data["response"]=$_data;
		$this->response($data);
	} 
	//funcion para obtener los usarios de una empresa
	public function getAlluser_post(){
		$datos=$this->post();
		$_Token=$datos["token"];
		$_ID_Empresa=$datos["datos"];
		
		if($this->checksession($_Token,$_ID_Empresa)===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de Sesion";
		}else{
			//actualizo los datos del usuario
			$respuesta=$this->Model_Usuario->getAlluser($_ID_Empresa);
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$_data["result"]=$respuesta;
		}
		$data["response"]=$_data;
		$this->response($data);
	}
	//funcion para login
	public function login_post()
	{
		$datos=$this->post();
		$_user=$datos["user"];
		$_password=$datos["password"];
		$_POST = json_decode(file_get_contents("php://input"), true);
		$config=array( array(
			'field'=>'user', 
			'label'=>'Usuario', 
			'rules'=>'trim|required|xss_clean'					
		),array(
			'field'=>'password', 
			'label'=>'Contraseña', 
			'rules'=>'trim|required|xss_clean'					
		));
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		$this->form_validation->set_rules($config);
			$array=array("required"=>'El campo %s es obligatorio',"valid_email"=>'El campo %s no es valido',"min_length[3]"=>'El campo %s debe ser mayor a 3 Digitos',"min_length[10]"=>'El campo %s debe ser mayor a 10 Digitos','alpha'=>'El campo %s debe estar compuesto solo por letras',"matches"=>"Las contraseñas no coinciden",'is_unique'=>'El contenido del campo %s ya esta registrado');
		$this->form_validation->set_message($array);
		if($this->form_validation->run() !=false){
			$respuesta=$this->Model_Usuario->login($_user,$_password);
			if($respuesta===false){
				$_data["code"]=1990;
				$_data["ok"]="Error";
				$_data["result"]="Usuario y/o Contraseña no validos";

			}else{
				//primero verifico que su cuenta este activa si no le mando un mail para activarla y no le doy acceso

				//agregamos el token en accesos
				$token=$this->Model_Usuario->addacceso($respuesta["IDUsuario"],date('Y-m-d'),1);
				
				$empresa=$this->Model_Empresa->getempresa($respuesta["IDEmpresa"]);
				
				if($respuesta["Status"]==="0"){
					$this->Model_Email->Activar_Usuario($respuesta["Token_Activar"],$respuesta["Correo"],$respuesta["Nombre"],$respuesta["Apellidos"],$respuesta["Correo"],"");
					$_data["code"]=1990;
					$_data["ok"]="Error";
					$_data["result"]="Cuenta no activada, se han enviado un email las instrucciones para activar su cuenta.";
				}else{
					$_data["code"]=0;
					$_data["ok"]="SUCCESS";
					$_data["datosusuario"]=$respuesta;
					$_data["empresa"]=$empresa;
					$_data["Token"]=$token;
				}
				
				
			}
		}else{
			$_data["code"]=1990;
			$_data["ok"]="Error";
			$_data["result"]=validation_errors();
		}
		
		$this->response(array("response"=>$_data));
	}
	//funcion para actualizar los datos de un usuario
	public function update_post(){
		$datos=$this->post();
		$_Token=$datos["token"];
		$_ID_Empresa=$datos["datos"]["IDEmpresa"];
		
		if($this->checksession($_Token,$_ID_Empresa)===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de Sesion";
		}else{
			//actualizo los datos del usuario
			$respuesta=$this->Model_Usuario->update($datos["datos"]["IDUsuario"],$datos["datos"]["Nombre"],$datos["datos"]["Apellidos"],$datos["datos"]["Puesto"],$datos["datos"]["Correo"],$datos["datos"]["Visible"]);
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$_data["result"]=$this->Model_Usuario->getAlluser($_ID_Empresa);;
		}
		$data["response"]=$_data;
		$this->response($data);
	}
	public function master_post(){
		$datos=$this->post();
		//vdebug($datos);
		$_Token=$datos["token"];
		$_ID_Empresa=$datos["IDEmpresa"];
		if($this->checksession($_Token,$_ID_Empresa)===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de Sesion";
		}else{
			$this->Model_Usuario->Master($_ID_Empresa,$datos["usuario"]);
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$_data["result"]=$this->Model_Usuario->getAlluser($_ID_Empresa);
		}
		$data["response"]=$_data;
		$this->response($data);
	}
	public function updateclave_post(){
		$datos=$this->post();
		//vdebug($datos);
		$_Token=$datos["token"];
		$_ID_Empresa=$datos["IDEmpresa"];
		
		if($this->checksession($_Token,$_ID_Empresa)===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de Sesion";
		}else{
			$_POST = json_decode(file_get_contents("php://input"), true);
			$config=array( array(
			'field'=>'actual', 
			'label'=>'Contraseña Actual', 
			'rules'=>'trim|required|xss_clean'					
			),array(
				'field'=>'nueva', 
				'label'=>'Contraseña Nueva', 
				'rules'=>'callback_valid_password'						
			),array(
				'field'=>'repetir', 
				'label'=>'Confirmar Contraseña', 
				'rules'=>'matches[nueva]'						
			));
			//actualizo los datos del usuario
			$this->form_validation->set_error_delimiters('', ',');
			$this->form_validation->set_rules($config);
				$array=array("required"=>'El campo %s es obligatorio',"valid_email"=>'El campo %s no es valido',"min_length[3]"=>'El campo %s debe ser mayor a 3 Digitos',"min_length[10]"=>'El campo %s debe ser mayor a 10 Digitos','alpha'=>'El campo %s debe estar compuesto solo por letras',"matches"=>"Las contraseñas no coinciden",'is_unique'=>'El contenido del campo %s ya esta registrado');
			$this->form_validation->set_message($array);
			if($this->form_validation->run() !=false){
				$respuesta=$this->Model_Usuario->updateclave($_POST["IDUsuario"],$_POST["nueva"]);
				$_data["code"]=0;
				$_data["ok"]="SUCCESS";
				$_data["result"]=$respuesta;
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