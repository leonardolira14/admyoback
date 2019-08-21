<?
defined('BASEPATH') OR exit('No direct script access allowed');
require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;
/**
 * 
 */
class Imagen extends REST_Controller
{
	
	function __construct()
	{
		header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
    	header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
    	header("Access-Control-Allow-Origin: *");
    	parent::__construct();
    	$this->load->model("Model_Usuario");
		$this->load->model("Model_Empresa");
		$this->load->model("Model_Imagen");
	}
	public function getImagen_post(){
		$datos=$this->post();
		//vdebug($datos);
		$_ID_Empresa=$datos["IDEmpresa"];
		$_fecha=$datos["fecha"];
		$_Tipo=$datos["tipo"];
		$datoss["imagen"]=$this->Model_Imagen->imgcliente($_ID_Empresa,$_fecha,$_Tipo,$resumen=FALSE);
		$_data["code"]=0;
		$_data["ok"]="SUCCESS";
		$_data["result"]=$datoss;
		$data["response"]=$_data;
		$this->response($data);
	}
	public function detalle_post(){
		$datos=$this->post();
		//vdebug($datos);
		$_ID_Empresa=$datos["IDEmpresa"];
		$_fecha=$datos["fecha"];
		$_Tipo=$datos["tipo"];
		$datoss["imagen"]=$this->Model_Imagen->detalleImagen($_Tipo,$_ID_Empresa,$_fecha);
		$_data["code"]=0;
		$_data["ok"]="SUCCESS";
		$_data["result"]=$datoss;
		$data["response"]=$_data;
		$this->response($data);
	}
}