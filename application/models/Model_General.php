<?
/**
 * 
 */
class Model_General extends CI_Model
{
	
	function __construct()
	{
			parent::__construct();
			$this->load->database();
	}
	//funcion para obtener los sectores
	public function getAllsector(){
		$sql=$this->db->select("*")->order_by("Giro",'ASC')->get("gironivel1");
		return $sql->result_array();
	}
	public function getSubsector($ID_Giro){
		$sql=$this->db->select("*")->where("IDNivel1='$ID_Giro'")->order_by("Giro",'ASC')->get("gironivel2");
		
		return $sql->result_array();

	}
	public function getRama($ID_Giro){
		$sql=$this->db->select("*")->where("IDNivel2='$ID_Giro'")->order_by("Giro",'ASC')->get("gironivel3");
		return $sql->result_array();
	}
	public function getempleados(){
		$sql=$this->db->select("*")->get("numempleados");
		return $sql->result_array();
	}
	public function getfactanual(){
		$sql=$this->db->select("*")->get("facanual");
		return $sql->result_array();
	}
	public function gettipoempresas(){
		$sql=$this->db->select("*")->get("tipoempresa");
		return $sql->result_array();
	}
	public function getEstados($_ID_Estado){
		$sql=$this->db->select("*")->where("ubicacionpaisid='$_ID_Estado'")->get("estado");
		return $sql->result_array();
	}
}