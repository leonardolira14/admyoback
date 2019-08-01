<?
/**
 * 
 */
class Pruebas extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->model("Model_Notificaciones");
		$this->load->model("Model_Email");
	}
	public function p1(){
		$IDEmpresaN='152';
		$Descripcion='crecibidas';
		$IDEmpresa='191';
		$IDUsuarioE='0';
		$this->Model_Notificaciones->add($IDEmpresaN,$Descripcion,$IDEmpresa,$IDUsuarioE);
	}
	public function prueba(){
		$toke="jdfgnsdj";
		$correo="test1@admyo.com";
		$nombre="nombre";
		$apellido='apellido';
		$_Tipo_Cuenta='basic';
		$clave='akjdfhk';
		$respuesta=$this->Model_Email->Activar_Usuario($toke,$correo,$nombre,$apellido,$correo,$clave);
		$respuesta=$this->Model_Email->baja_usuario_admin($correo,$correo);
		/*$respuesta=$this->Model_Email->Activar_Usuario_registro(
			$toke,
			$correo,
			$nombre,
			$apellido,
			$_Tipo_Cuenta,
			$correo,
			$clave
		);*/
		vdebug($respuesta);
	}
	
}