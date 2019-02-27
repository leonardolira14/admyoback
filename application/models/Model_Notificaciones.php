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
		$respuesta=$this->db->select("IDEmpresaN,Razon_Social,fecha,Descript")->From("Notificaciones")->join("empresa","empresa.IDEmpresa=Notificaciones.IDEmpresaN")->where("Notificaciones.IDEmpresa='$_ID_Empresa'")->get();
		if($respuesta->num_rows()===0){
			return false;
		}else{
			$notificaciones=[];
			foreach ($respuesta->result_array() as $notificacion) {
				array_push($notificaciones,array("Razon_Social"=>$notificacion["Razon_Social"],"Fecha"=>$notificacion["fecha"],"descripcion"=>comentario_notificaciones($notificacion["Descript"],$notificacion["IDEmpresaN"],$notificacion["Razon_Social"])));
			}
			return $notificaciones;
		}
	}
}