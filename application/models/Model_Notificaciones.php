<?

/**
 * 
 */
class Model_notificaciones extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	public function getnumten($_ID_Empresa){
		$respuesta=$this->db->select("IDNotificacion,IDEmpresaN,Razon_Social,fecha,Descript,IDUsuarioE,")->From("notificaciones")->join("empresa","empresa.IDEmpresa=notificaciones.IDEmpresaN")->where("notificaciones.IDEmpresa='$_ID_Empresa'")->get();
		return $respuesta->num_rows();
		
	}
	public function getten($_ID_Empresa){
		$respuesta=$this->db->select("IDNotificacion,IDEmpresaN,Razon_Social,fecha,Descript,IDUsuarioE,")->From("notificaciones")->join("empresa","empresa.IDEmpresa=notificaciones.IDEmpresaN")->where("notificaciones.IDEmpresa='$_ID_Empresa'")->get();
		$notificaciones=[];
		foreach ($respuesta->result_array() as $notificacion) {
			array_push($notificaciones,array("Descript"=>$notificacion["Descript"],"IDUsuarioE"=>$notificacion["IDUsuarioE"],"IDNotificacion"=>$notificacion["IDNotificacion"],"IDEmpresaN"=>$notificacion["IDEmpresaN"],"Razon_Social"=>$notificacion["Razon_Social"],"Fecha"=>$notificacion["fecha"]));
		}
		return $notificaciones;
	}
	public function delete($id){
		$this->db->where("IDNotificacion='$id'")->delete('notificaciones');
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
		$this->db->insert("notificaciones",$array);
	}
	public function getconfig($IDEmpresa){
		$respuesta=$this->db->select('Configaletas')->where("IDEmpresa='$IDEmpresa'")->get('empresa');
		return $respuesta->row_array();
	}


	public function filtro($IDEmpresa,$_Tipo,$_Fecha){
		$filtro='';
		$Fecha = '';
		
		switch($_Tipo){
			case 'crecibidas':
				$filtro= "and (Descript='calificacionrp' or Descript='calificacionrc') ";
				break;
			case 'crealizadas':
				$filtro = "and (Descript='calificacionp' or Descript='calificacionc')";
				break;
			case 'vista':
				$filtro = "and Descript='visitas' ";
				break;
			case 'riesgoc':
				$filtro = "and Descript='riesgoc' ";
				break;
			case 'riesgop':
				$filtro = " and Descript='riesgop' ";
				break;
			case 'follow':
				$filtro = "and Descript='Follow' ";
				break;
			case '':
				$filtro = "";
				break;
		}
		if($_Fecha===''){
			$Fecha='';
		}else{
			$Fecha=" and date(Fecha)='$_Fecha'";
		}

		$respuesta = $this->db->select('IDNotificacion,IDEmpresaN,Razon_Social,fecha,Descript,IDUsuarioE')->from('notificaciones')->join("empresa","empresa.IDEmpresa=notificaciones.IDEmpresaN")->where("notificaciones.IDEmpresa='$IDEmpresa'" . $filtro . $Fecha)->get();

		$notificaciones = [];
		foreach ($respuesta->result_array() as $notificacion) {
			array_push($notificaciones, array("Descript" => $notificacion["Descript"], "IDUsuarioE" => $notificacion["IDUsuarioE"], "IDNotificacion" => $notificacion["IDNotificacion"], "IDEmpresaN" => $notificacion["IDEmpresaN"], "Razon_Social" => $notificacion["Razon_Social"], "Fecha" => $notificacion["fecha"]));
		}
		return $notificaciones;
		
	}
}