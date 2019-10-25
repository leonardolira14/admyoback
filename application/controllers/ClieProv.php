<?
defined('BASEPATH') OR exit('No direct script access allowed');
require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;
/**
 * 
 */
class ClieProv extends REST_Controller
{
	
	function __construct()
	{
		header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
    	header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    	header("Access-Control-Allow-Origin: *");
    	parent::__construct();
    	$this->load->model("Model_Clieprop");
		$this->load->model("Model_Proveedores");
		$this->load->model("Model_General");
		$this->load->model("Model_Empresa");
		$this->load->model("Model_Usuario");
		$this->load->model("Model_Email");
	}

	public function getaresumen_post(){
		$datos=$this->post();
		$_ID_Empresa=$datos["IDEmpresa"];
		if($datos["tipo"]=="clientes"){
			$resumen=$this->Model_Clieprop->Resumen($_ID_Empresa);
		}else{
			$resumen=$this->Model_Proveedores->Resumen($_ID_Empresa);
		}
		$_data["code"]=0;
		$_data["ok"]="SUCCESS";
		$_data["result"]=$resumen;
		$data["response"]=$_data;
		$this->response($data);
	}
	public function getlista_post(){
		$datos=$this->post();
		$_ID_Empresa=$datos["IDEmpresa"];
		
		if($datos["tipo"]==="clientes"){
			$resumen=$this->Model_Clieprop->listaclientes($_ID_Empresa);
		}else{
			$resumen=$this->Model_Proveedores->listaproveedores($_ID_Empresa);
		}
		
		$estados=$this->Model_General->getEstados('42');
		$_data["code"]=0;
		$_data["ok"]="SUCCESS";
		$_data["result"]=$resumen;
		$_data["estados"]=$estados;
		$data["response"]=$_data;
		$this->response($data);
	}
	//funcion para filtar los clientes
	public function fillter_post(){
		$datos=$this->post();
		$_ID_Empresa=$datos["IDEmpresa"];
		// primero vefico si se filtro por nombre
		if(!isset($datos["filtros"]["nombre"]) || $datos["filtros"]["nombre"]===''){
			if($datos["tipo"]==="clientes"){
				$lista=$this->Model_Clieprop->listaclientes($_ID_Empresa);
			}else{
				$lista=$this->Model_Proveedores->listaproveedores($_ID_Empresa);	
			}
			
		}else{
			if($datos["tipo"]==="clientes"){
				$lista=$this->Model_Clieprop->listaclientespalabra($_ID_Empresa,$datos["filtros"]["nombre"]);
			}else{
				$lista=$this->Model_Proveedores->listaproveedorespalabra($_ID_Empresa,$datos["filtros"]["nombre"]);	
			}

		}
		$lista2=[];
		// vefico si se filtro por status
		if(!isset($datos["filtros"]["status"]) || $datos["filtros"]["status"]==='sn'){
			$lista2=$lista;
		}else{
			
			foreach ($lista as  $item) {

				if($item["status_relacion"]===$datos["filtros"]["status"]){
					array_push($lista2,$item);
				}
				
			}	
		}
		$lista3=[];
		
		// vefico si se filtro por status
		if(!isset($datos["filtros"]["validado"]) || $datos["filtros"]["validado"]==='sn'){
			$lista3=$lista2;
		}else{
			
			foreach ($lista as  $item) {
				if($datos["filtros"]["validado"]==="1"){
					if($item["CerA"]==='1'){
						array_push($lista3,$item);	
					}
					
				}
				if($datos["filtros"]["validado"]==="2"){
					if($item["CerB"]==='1'){
						array_push($lista3,$item);	
					}
					
				}
				if($datos["filtros"]["validado"]==="3"){
					
					if($item["CerB"]==='0' && $item["CerA"]==='0'){
						array_push($lista3,$item);	
					}
					
				}
			}	
		}
		$_data["code"]=0;
		$_data["ok"]="SUCCESS";
		$_data["result"]=$lista3;
		$data["response"]=$_data;
		$this->response($data);
		
	}
	//funcion para registrar un cliente o proveedor 
	public function add_post(){
		$datos=$this->post();
		
		$_POST = json_decode($datos["datos"], true);
		$config=array( array(
			'field'=>'razon', 
			'label'=>'Razón Social', 
			'rules'=>'trim|required|xss_clean|is_unique[empresa.Razon_Social]'					
		),array(
			'field'=>'nombrecomercial', 
			'label'=>'Nombre Comercial', 
			'rules'=>'trim|required|xss_clean'					
		),array(
			'field'=>'rfc', 
			'label'=>'RFC', 
			'rules'=>'trim|required|xss_clean|is_unique[empresa.RFC]'					
		),array(
			'field'=>'tipopersona', 
			'label'=>'Tipo Persona', 
			'rules'=>'trim|required|xss_clean'					
		),array(
			'field'=>'nombre', 
			'label'=>'Nombre', 
			'rules'=>'trim|required|xss_clean'					
		),array(
			'field'=>'apellidos', 
			'label'=>'Apellidos', 
			'rules'=>'trim|xss_clean'					
		),array(
			'field'=>'correo', 
			'label'=>'Correo Electrónico', 
			'rules'=>'trim|required|xss_clean|is_unique[usuarios.Correo]'					
		),array(
			'field'=>'municipio', 
			'label'=>'Municipio', 
			'rules'=>'trim|xss_clean'					
		),array(
			'field'=>'calle', 
			'label'=>'Calle y Número', 
			'rules'=>'trim|xss_clean'					
		),array(
			'field'=>'telefono', 
			'label'=>'Teléfono', 
			'rules'=>'trim|xss_clean'					
		));
		$this->form_validation->set_error_delimiters('<li>', '</li>');
		$this->form_validation->set_rules($config);
			$array=array(
				"required"=>'El campo %s es obligatorio',
				"valid_email"=>'El campo %s no es valido',
				"min_length[3]"=>'El campo %s debe ser mayor a 3 Digitos',
				"min_length[10]"=>'El campo %s debe ser mayor a 10 Digitos',
				'alpha'=>'El campo %s debe estar compuesto solo por letras',
				"matches"=>"El campo %s no coinciden",
				'is_unique'=>'El contenido del campo %s ya esta registrado');
		$this->form_validation->set_message($array);
		
		if($this->form_validation->run() !=false){

			$_Tipo_Cuenta="basic";

			//ahora agrego la empresa en la base de datos de admyo
			$ID_Empresa_Admyo=$this->Model_Empresa->preaddempresa($_POST["tipopersona"],$_POST["razon"],$_POST["nombrecomercial"],$_POST["rfc"],$_Tipo_Cuenta,"0");
			//agrego al usuario en la base de datos
			$clave=generate_clave();
			$_Token_Usuario=$this->Model_Usuario->addUsuario($ID_Empresa_Admyo,$_POST["nombre"],$_POST["apellidos"],$_POST["correo"],$_POST["correo"],$clave,"Master",'0',"Master");
			
			$this->Model_Email->Activar_Usuario_registro($_Token_Usuario,$_POST["correo"],$_POST["nombre"],$_POST["apellidos"],$_Tipo_Cuenta,$_POST["correo"],$clave);
			// ahora agrego la relacion
			$_ID_Empresa_emisora=$datos["IDEmpresa"];
			$this->Model_Empresa->addRelacion($_ID_Empresa_emisora,$ID_Empresa_Admyo,ucwords($datos["tiporegistro"]));
			
			//ahora cambio el logo
			if(count($_FILES)!==0){
				$_Imagen=$_FILES["Imagen"]["name"];	
				$ruta="assets/img/logosEmpresas/";
				$rutatemporal=$_FILES["Imagen"]["tmp_name"];
				$nombreactual=$_FILES["Imagen"]["name"];
				try {
					if(! move_uploaded_file($rutatemporal, $ruta.$nombreactual)){
						$_data["code"]=1991;
						$_data["ok"]="ERROR";
						$banderaimg=false;
						$_data["result"]="No se puede subir imagen";
					}
					$_data["code"]=0;
					$_data["ok"]="SUCCESS";
					$this->Model_Empresa->updatelogo($_ID_Empresa_emisora,$nombreactual);
					$_data["result"]=$_data;
					
				} catch (Exception $e) {
						$_data["code"]=1991;
						$_data["ok"]="ERROR";
						$banderaimg=false;
						$_data["result"]=$e->getMessage();
				}
		}else{
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$_data["result"]=$ID_Empresa_Admyo;
		}
			
			
			
		}else{
			$_data["code"]=1990;
			$_data["ok"]="Error";
			$_data["result"]=validation_errors();
		}
		$this->response(array("response"=>$_data));
	}
}