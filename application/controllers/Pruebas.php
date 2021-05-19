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
		$this->load->model("Model_Imagen");
	}
	// funcion para enviar los correos para pruebas
	public function pruebacorreos(){
		// primero para activar un usuario 
		$respuestaEmail = $this->Model_Email->Activar_Usuario('Token para activar','lira053@gmail.com', 'Leonardo','Lira ', 'usuarioenadmyo', 'passwprd');
		var_dump($respuestaEmail);
	}
	public function prueva_correo_activar_usuario(){
		$categorias = ['basic', 'micro', 'micro_anual', 'empresa', 'empresa_anual'];
		$correos = ['bernardo@admyo.com','lira053@gmail.com','belen.roldal@admyo.com'];
		foreach($categorias as $categoria){
			//var_dump($categoria);
			$this->Model_Email->Activar_Usuario_registro('Token para activar', $correos, 'Leonardo', 'Lira ', $categoria, 'usuarioenadmyo', 'passwprd');
		}
		echo "ya";
		
	}
}