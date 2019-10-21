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
		if($datos["tipo"]=="clientes"){
			$resumen=$this->Model_Clieprop->listaclientes($_ID_Empresa);
		}else{
			$resumen=$this->Model_Proveedores->listaproveedores($_ID_Empresa);
		}
		$_data["code"]=0;
		$_data["ok"]="SUCCESS";
		$_data["result"]=$resumen;
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
}