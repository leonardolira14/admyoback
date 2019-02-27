<?

/**
 * 
 */
class Model_Giros extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	//funcion para obtener todos los giros de la empresa
	public function getAll(){
		$respuesta=$this->db->select("*")->get("giroempresa");
		return $respuesta->result_array();
	}
	//FUNCION para obtner los giros de una emrpesa
	public function getGirosEmpresa($_ID_Empresa){
		$respuesta=$this->db->select("Principal,IDGE,giroempresa.IDGiro,giroempresa.IDGiro2,giroempresa.IDGiro3,gironivel1.Giro as giron1 ,gironivel2.Giro as giron2,gironivel3.Giro as giron3 ")->from("giroempresa")->where("IDEmpresa='$_ID_Empresa'")->join("gironivel1","gironivel1.IDNivel1=giroempresa.IDGiro")->join("gironivel2","gironivel2.IDNivel2=giroempresa.IDGiro2")->join("gironivel3","gironivel3.IDGiro3=giroempresa.IDGiro3")->get();
		return $respuesta->result_array();
	}
	//funcion para agregar un nuevo giro a una empresa
	public function addgiro($_Empresa,$_giro,$_subGiro,$_Rama){
		$array=array("IDEmpresa"=>$_Empresa,"IDGiro"=>$_giro,"IDGiro2"=>$_subGiro,"IDGiro3"=>$_Rama);
		return $this->db->insert("giroempresa",$array);
	}
	//funcion para eliminar un giro de una empresa
	public function delete($_IDGiro){
		return $this->db->where("IDGE='$_IDGiro'")->delete("giroempresa");
	}
	//funcion para poner en principal un giro
	public function principal($_ID_Empresa,$_ID_Giro){
		$this->db->where("IDEmpresa='$_ID_Empresa'")->update("giroempresa",array("Principal"=>'0'));
		return $this->db->where("IDGE='$_ID_Giro'")->update("giroempresa",array("Principal"=>'1'));
	}
}