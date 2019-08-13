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
	public function getnumten($_ID_Empresa){
		$respuesta=$this->db->select("IDNotificacion,IDEmpresaN,Razon_Social,fecha,Descript,IDUsuarioE,")->From("Notificaciones")->join("empresa","empresa.IDEmpresa=Notificaciones.IDEmpresaN")->where("Notificaciones.IDEmpresa='$_ID_Empresa'")->get();
		return $respuesta->num_rows();
		
	}
	public function getten($_ID_Empresa){
		$respuesta=$this->db->select("IDNotificacion,IDEmpresaN,Razon_Social,fecha,Descript,IDUsuarioE,")->From("Notificaciones")->join("empresa","empresa.IDEmpresa=Notificaciones.IDEmpresaN")->where("Notificaciones.IDEmpresa='$_ID_Empresa'")->get();
		$notificaciones=[];
		foreach ($respuesta->result_array() as $notificacion) {
			array_push($notificaciones,array("Descript"=>$notificacion["Descript"],"IDUsuarioE"=>$notificacion["IDUsuarioE"],"IDNotificacion"=>$notificacion["IDNotificacion"],"IDEmpresaN"=>$notificacion["IDEmpresaN"],"Razon_Social"=>$notificacion["Razon_Social"],"Fecha"=>$notificacion["fecha"]));
		}
		return $notificaciones;
	}
	public function delete($id){
		$this->db->where("IDNotificacion='$id'")->delete('Notificaciones');
	}
	//funcion para agregar una notificacion
	public function add($IDEmpresaN,$Descripcion,$IDEmpresa,$IDUsuarioE,$tipo){
		$config=$this->getconfig($IDEmpresa);
		
		if($config["Configaletas"]!==''){
			$configuracion=json_decode($config["Configaletas"], True);
			if(!isset($configuracion[$tipo]) || $configuracion[$tipo]===0 ){
				return false;
			}
		}
		
		$array = array(
			"IDEmpresa"=>$IDEmpresa,
			"Descript"=>$Descripcion,
			"visto"=>1,
			"IDEmpresaN"=>$IDEmpresaN,
			"IDUsuarioE"=>$IDUsuarioE
		);
		$this->db->insert("Notificaciones",$array);
	}
	public function getconfig($IDEmpresa){
		$respuesta=$this->db->select('Configaletas')->where("IDEmpresa='$IDEmpresa'")->get('empresa');
		return $respuesta->row_array();
	}
}