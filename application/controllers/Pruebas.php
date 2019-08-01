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
	}
	public function p1(){
		$IDEmpresaN='152';
		$Descripcion='crecibidas';
		$IDEmpresa='191';
		$IDUsuarioE='0';
		$this->Model_Notificaciones->add($IDEmpresaN,$Descripcion,$IDEmpresa,$IDUsuarioE);
	}
	
}