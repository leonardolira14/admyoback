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
        $notificaciones=$this->Model_Notificaciones->getten($datos["IDEmpresa"]);
        
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
        $this->response($_data,200);
       
    }
    public function delete_post(){
        $datos=$this->post();
        $this->Model_Notificaciones->delete($datos["IDNotificacion"]);
        $_data["ok"]="ok";
        $this->response($_data);
    }
    public function updateconfig_post(){
        $datos=$this->post();
       
        $_data['resp']=$this->Model_Empresa->update_alerta($datos["IDEmpresa"],$datos["alertas"]);
        $_data["ok"]="ok";
        $this->response($_data,200);
    }
    public function getconfignotification_post()
    {
        $datos = $this->post();
        $respuesta = $this->Model_Empresa->get_conf__alerta($datos["IDEmpresa"]);
        $_data["ok"] = "ok";
        $_data["config"] = $respuesta;
        $this->response($_data, 200);
    }
    public function filtro_post(){
        $datos = $this->post();
        $_Token = $datos["token"];
        $_ID_Empresa = $datos["IDEmpresa"];
        if ($this->checksession($_Token, $_ID_Empresa) === false) {
            $_data["code"] = 1990;
            $_data["ok"] = "ERROR";
            $_data["result"] = "Error de Sesion";
            $this->response($_data, 500);
        } else {
            if(!isset($datos['Filtro'])){
                $respuesta = $this->Model_Notificaciones->filtro($_ID_Empresa, '', $datos['Fecha']);
            }else{
                $respuesta =  $this->Model_Notificaciones->filtro($_ID_Empresa, $datos['Filtro'], $datos['Fecha']);
            }
            $_data["ok"] = "ok";
            //agrego los datos del usuario
            foreach ($respuesta as $key => $notificacion) {
                if ($notificacion["IDUsuarioE"] === "0") {
                    $Nombre_usuario = "Sin Usuario";
                } else {
                    $_datos_usuario = $this->Model_Usuario->DatosUsuario($notificacion["IDUsuarioE"]);
                    $Nombre_usuario = $_datos_usuario["Nombre"] . " " . $_datos_usuario["Apellidos"];
                }
                $respuesta[$key]["Nombre_Usurio"] = $Nombre_usuario;
            }
            $_data["notificaciones"] = $respuesta;
            $this->response($_data,200);
        }
    }
   

    function checksession($_Token, $_Empresa)
    {
        //primerocheco el token
        $_datos_Tocken = $this->Model_Usuario->checktoken($_Token);
        $_datos_empresa = $this->Model_Empresa->getempresa($_Empresa);
        if ($_datos_Tocken === false) {
            return false;
        } else if ($_datos_empresa === false) {
            return false;
        } else {
            return true;
        }
    }

     
}