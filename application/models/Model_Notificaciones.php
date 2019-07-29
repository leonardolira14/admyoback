<?

/**
 * 
 */
class Model_Notificaciones extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	public function getten($_ID_Empresa){
		$respuesta=$this->db->select("IDNotificacion,IDEmpresaN,Razon_Social,fecha,Descript,IDUsuarioE,")->From("Notificaciones")->join("empresa","empresa.IDEmpresa=Notificaciones.IDEmpresaN")->where("Notificaciones.IDEmpresa='$_ID_Empresa'")->get();
		if($respuesta->num_rows()===0){
			return false;
		}else{
			$notificaciones=[];
			foreach ($respuesta->result_array() as $notificacion) {
				array_push($notificaciones,array("Descript"=>$notificacion["Descript"],"IDUsuarioE"=>$notificacion["IDUsuarioE"],"IDNotificacion"=>$notificacion["IDNotificacion"],"IDEmpresaN"=>$notificacion["IDEmpresaN"],"Razon_Social"=>$notificacion["Razon_Social"],"Fecha"=>$notificacion["fecha"]));
			}
			return $notificaciones;
		}
	}
}