<?
defined('BASEPATH') OR exit('No direct script access allowed');
require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;
/**
 * 
 */
class Visitas extends REST_Controller
{
	
	function __construct()
	{
		header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
		header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
		header("Access-Control-Allow-Origin: *");
		parent::__construct();
		$this->load->model("Model_Usuario");
		$this->load->model("Model_Empresa");
		$this->load->model("Model_Visitas");
	}
	public function Visitasg_post(){
		$datos=$this->post();
		//vdebug($datos);
		$_Token=$datos["token"];
		$_ID_Empresa=$datos["IDEmpresa"];
		
		if($this->checksession($_Token,$_ID_Empresa)===false){
			$_data["code"]=1990;
			$_data["ok"]="ERROR";
			$_data["result"]="Error de Sesion";
		}else{
			$anio=$datos["Anio"];
			$dat["datos"]=$this->Model_Visitas->VisitasGeneral($_ID_Empresa,$anio);
			$_data["code"]=0;
			$_data["ok"]="SUCCESS";
			$_data["result"]=$dat["datos"];
		}
		$data["response"]=$_data;
		$this->response($data);
			
		
	}
	public function Visitasv2($met){
		if($this->session->userdata('logueado')){
			if($this->session->userdata('IDEmpresa')){
				$IDEmpresa=$this->session->userdata('IDEmpresa');
			}else{
				$IDEmpresa=$datos->IDEmpresa;
			}
			$n["notif"]=$this->Model_Notificaciones->NumNot($IDEmpresa);
			$this->load->view('head/head_profile',$n);

			if($met==="M"){
				$datar["rec"]=json_encode($this->Model_Visitas->VisitasGeneral($IDEmpresa,'M'));
				$datar["recc"]=json_encode($this->Model_Visitas->detalles($IDEmpresa,'A'));
			}else if($met==="A"){
				$datar["rec"]=json_encode($this->Model_Visitas->VisitasGeneral($IDEmpresa,'A'));
				$datar["recc"]=json_encode($this->Model_Visitas->detalles($IDEmpresa,'A'));
			}
			$datar["tip"]=$met;
			$this->load->view("views/newvista/visitas",$datar);
			$this->load->view('footer');

		}else{
			redirect('');
		}
	}
	//funcion para los detalles de visitas
	public function detallesvisitas($met){
		if($this->session->userdata('logueado')){
			if($this->session->userdata('IDEmpresa')){
				$IDEmpresa=$this->session->userdata('IDEmpresa');
			}else{
				$IDEmpresa=$datos->IDEmpresa;
			}
			if($met==="M"){
				
			}else if($met==="A"){
				$datar["rec"]=json_encode($this->Model_Visitas->detalles($IDEmpresa,'A'));
			}
			$datar["tip"]=$met;
			$this->load->view("views/newvista/master");
			$this->load->view("views/newvista/detallevisita",$datar);
		
		}else{
			echo false;
		}
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