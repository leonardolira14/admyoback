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
	
		$Razon_Social = $this->post('razon_social');
		$Nombre_Comercial = $this->post('nombre_comercial');
		$RFC = $this->post('rfc');
		$Tipopersona = $this->post('tipopersona');
		$Sector = $this->post('sector');
		$SubSector = $this->post('subsector');
		$Rama = $this->post('rama');
		$data_Usuario = $this->post('usuario');
		$Nombre = $data_Usuario['nombre'];
		$Apellidos = $data_Usuario['apellidos'];
		$Correo = $data_Usuario['correo'];
		$Clave = $data_Usuario['password'];
		$_Tipo_Cuenta="basic";
	
			// verifico si la empresa no esta registrada si es a si le mando un error
			$data_empresa = $this->Model_Empresa->getempresaRazon($Razon_Social);
			 
			if($data_empresa !== false ){
				$_data["code"]=0;
				$_data["ok"]=false;
				$_data["msj"]= "Esta Razón social ya esta registrada, solicite una aclaración con admyo";
				return $this->response(array("response"=>$_data),404);
			}

			// valido el RFC
			$data_empresa = $this->Model_Empresa->getempresaRFC($RFC);
			// vdebug($data_empresa);
			if($data_empresa !== false ){
				$_data["code"]=0;
				$_data["ok"]=false;
				$_data["msj"]="Este RFC ya esta registrado, solicite una aclaración con admyo";
				return $this->response(array("response"=>$_data),404);
			}

			// valido el usuario 
			$data_user = $this->Model_Usuario->DatosUsuarioCorreo($Correo);
			 //vdebug($data_user);
			if($data_user !== false ){
				$_data["code"]=0;
				$_data["ok"]=false;
				$_data["msj"]="Esta dirección de correo electrónico ya esta registrado, solicite una aclaración con admyo";
				return $this->response(array("response"=>$_data),404);
			}
			

			// sin todo salio bien agrego la empresa


			//ahora agrego la empresa en la base de datos de admyo
			$ID_Empresa_Admyo=$this->Model_Empresa->preaddempresa($Tipopersona,$Razon_Social,$Nombre_Comercial,$RFC,$_Tipo_Cuenta,"0");
			
			//agrego al usuario en la base de datos
			$_Token_Usuario=$this->Model_Usuario->addUsuario($ID_Empresa_Admyo,$Nombre,$Apellidos,$Correo,$Correo,$Clave,"Master",'0',"Master");
			$data_user = $this->Model_Usuario->DatosUsuarioCorreo($Correo);
			//ahora agrego el giro a la empresa
			$this->Model_Empresa->addgiros($ID_Empresa_Admyo,$Sector,$SubSector,$Rama);
			$this->Model_Email->Activar_Usuario_registro($_Token_Usuario,$Correo,$Nombre,$Apellidos,$_Tipo_Cuenta,$Correo,$Clave);

			//si traigo licencias de qval registro la empresa en qval
			/*if($_POST["PrecioQval"]!==0){
				$IDQval=$this->Model_QvalEmpresa->addempresa($_POST["Razon_Social"],$_POST["Nombre_Comercial"],$_POST["RFC"],$_POST["Tipo_Persona"],json_encode(array("Producto"=>$_POST["ProductoQval"],"Precio"=>$_POST["PrecioQval"])),$_POST["NlicenasQval"],$ID_Empresa_Admyo);
				//ahora inserto el usuario en la tabla de usauarios de qval
				$Token = $this->Model_QvalEmpresa->addusuario($IDQval,$_POST["Nombre"],$_POST["Apellidos"],'Usuario Master','0',$_POST["Correo1"],$_POST["Correo1"],$_POST["Clave1"]);
				$this->Model_Email->bienvenida_qval($_POST["Correo1"],$_POST["Nombre"],$_POST["Apellidos"],$_POST["Clave1"],$_POST["Correo1"],$Token);
			}*/
			//apartir de aqui mando los conrreo para qe se actiben las cuentas
			 // genero una sesion para esta cuenta
			 // genero un token para la sesion
			 $Token = md5($Correo.date('Y-m-d HH:ii:ss'));
			
			

			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$_data["result"]=$Token;

		
		$this->response(array("response"=>$_data),200);
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
		$_plan_admyo=$datos["pago"]["descripcion"];
		if(isset($datos["pago"]["tiempo"])){
			$_tiempo_admyo=$datos["pago"]["tiempo"];
		}else{
			$_tiempo_admyo=0;
		}
		
		if($datos["pago"]["metodo"]==='Tarjeta'){
			$_token_admyo=$datos["pago"]["token"];
			$respuesta=$this->Model_Conecta_admyo->Tarjeta($_nombre,$_correo,$_token_admyo,$_plan_admyo,$_precio_admyo,$_tel,$_tiempo_admyo);
			//vdebug($respuesta);
			if(isset($respuesta["status"])){
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
	//funcion para activar cuenta
	public function activarcuenta_post(){
		$datos=$this->post();
		$respuesta=$this->Model_Usuario->checktokenuser($datos["token"]);
		if($respuesta===false){
			$data["ok"]="error";
			$data["error"]="Token no valido";
		}else{
			
			$data["ok"]="ok";
			$data["mensaje"]="Cuenta activa";
		}
		$this->response($data);
	}
}