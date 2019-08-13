<?
defined('BASEPATH') OR exit('No direct script access allowed');
require_once( APPPATH.'/libraries/REST_Controller.php' );
use Restserver\libraries\REST_Controller;
/**
 * 
 */
class Notificaciones extends REST_Controller{
    function __construct()
	{
		header("Access-Control-Allow-Methods: GET,POST,PUT,DELETE");
		header("Access-Control-Allow-Headers: Content-Type, Content-Length, Accept-Encoding");
		header("Access-Control-Allow-Origin: *");
		parent::__construct();
        $this->load->model("Model_Notificaciones");
        $this->load->model("Model_Usuario");
		$this->load->model("Model_Empresa");
		
    }
    public function num_post(){
        $datos=$this->post();
        $notificaciones=$this->Model_Notificaciones->getnumten($datos["empresa"]);
        $_data["numnotificaciones"]=$notificaciones;
        $this->response($_data);
    }
    public function getnotification_post(){
        $datos=$this->post();
        $notificaciones=$this->Model_Notificaciones->getten($datos["empresa"]);
        
        //agrego los datos del usuario
        foreach($notificaciones as $key=>$notificacion){
            if($notificacion["IDUsuarioE"]==="0"){
                $Nombre_usuario="Sin Usuario";
            }else{
                $_datos_usuario=$this->Model_Usuario->DatosUsuario($notificacion["IDUsuarioE"]);
                $Nombre_usuario=$_datos_usuario["Nombre"]." ". $_datos_usuario["Apellidos"];
            }
            $notificaciones[$key]["Nombre_Usurio"]=$Nombre_usuario;

        }
        $_data["notificaciones"]=$notificaciones;
        $this->response($_data);
       
    }
    public function delete_post(){
        $datos=$this->post();
        $this->Model_Notificaciones->delete($datos["idnotificacion"]);
        $_data["ok"]="ok";
        $this->response($_data);
    }
    public function updateconfig_post(){
        $datos=$this->post();
        $this->Model_Empresa->update_alerta($datos["empresa"],$datos["alertas"]);
        $_data["ok"]="ok";
        $this->response($_data);
    }   
}