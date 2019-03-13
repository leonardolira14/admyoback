<?
defined('BASEPATH') OR exit('No direct script access allowed');
require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;
/**
 * 
 */
class Busqueda extends REST_Controller
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
        $this->load->model("Model_Visitas");
        $this->load->model("Model_Buscar");
        $this->load->model("Model_Norma");
        $this->load->model("Model_Marcas");
        $this->load->model("Model_Giros");
        $this->load->model("Model_Producto");
        $this->load->model("Model_Imagen");

	}



    //funcion para buscar
    public function perfil_post(){
        $datos=$this->post();
        
        $_Empresa_Emisora=$this->Model_Empresa->getempresa($datos["IDEmpresaEmisora"]);
        $_Empresa_Receptora=$this->Model_Empresa->getempresa($datos["IDEmpresa"]);

        $_ID_Empresa=$datos["IDEmpresa"];
        //primero verifico si la misma empresa se esta buscando a si misma no agrego la visita

        if($datos["IDEmpresa"]!==$datos["IDEmpresaEmisora"]){
            $this->Model_Visitas->Addvisita($datos["IDEmpresa"],$datos["IDEmpresaEmisora"]);

            //ahora obtengo los correos de los usuarios master
            $_Datos_Usuario_Receptor=$this->Model_Usuario->GetMaster($datos["IDEmpresa"]);
            //envio un correo a la empresa que buscaron avisandole que lo han buscado

            if(count($_Datos_Usuario_Receptor)!=0){
                $this->Model_Email->visita($_Empresa_Receptora["IDEmpresa"], $_Datos_Usuario_Receptor[0]["Correo"]);
            }
            
        }
        $dat["datosempresa"]=$_Empresa_Receptora;
        $dat["usuarios"]=$this->Model_Usuario->getAlluser($_ID_Empresa);
        $dat["marcas"]=$this->Model_Marcas->getMarcasEmpresa($_ID_Empresa);
        $dat["giros"]=$this->Model_Giros->getGirosEmpresa($_ID_Empresa);
        $dat["Normas"]=$this->Model_Norma->getall($_ID_Empresa);
        $dat["Productos"]=$this->Model_Producto->getall($_ID_Empresa);
        $dat["telefonos"]=$this->Model_Empresa->getTels($_ID_Empresa);
        $dat["ImagenCliente"]=$this->Model_Imagen->imgcliente($_ID_Empresa,'A','Cliente',$resumen=FALSE);
        $dat["ImagenProveedor"]=$this->Model_Imagen->imgcliente($_ID_Empresa,'A','Proveedor',$resumen=FALSE);


        $_data["code"]=0;
        $_data["ok"]="SUCCESS";
        $_data["result"]=$dat;
        
        $this->response(array("response"=>$_data));   

       
    }
}