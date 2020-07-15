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
		$respuesta=$this->db->select("*")->where("IDEmpresa='$_ID_Empresa'")->get("giroempresa");
		$giros=$respuesta->result_array();
		foreach($giros as $key=>$item){
			$datos_rama=$this->getrama($item["IDGiro"]);
			$datos_subgiro=$this->getsubgiro($item["IDGiro2"]);
			$datos_sub=$this->getgiro($item["IDGiro3"]);
			$giros[$key]["giron1"]=$datos_rama["Giro"];
			$giros[$key]["giron2"]=$datos_subgiro["Giro"];
			$giros[$key]["giron3"]=$datos_sub["Giro"];
		}
		
	
		return $giros;
	}
	//FUNCION para obtner los giros de una emrpesa
	public function getGiroPrincipalEmpresa($_ID_Empresa){
		$respuesta=$this->db->select("*")->where("IDEmpresa='$_ID_Empresa' and Principal='1'")->get("giroempresa");
		$giros=$respuesta->result_array();
		foreach($giros as $key=>$item){
			$datos_rama=$this->getrama($item["IDGiro"]);
			$datos_subgiro=$this->getsubgiro($item["IDGiro2"]);
			$datos_sub=$this->getgiro($item["IDGiro3"]);
			$giros[$key]["giron1"]=$datos_rama["Giro"];
			$giros[$key]["giron2"]=$datos_subgiro["Giro"];
			$giros[$key]["giron3"]=$datos_sub["Giro"];
		}
		
	
		return $giros;
	}
	//funcion para agregar un nuevo giro a una empresa
	public function addgiro($_Empresa,$_giro,$_subGiro,$_Rama){
		$array=array("IDEmpresa"=>$_Empresa,"IDGiro"=>$_giro,"IDGiro2"=>$_subGiro,"IDGiro3"=>$_Rama);
		return $this->db->insert("giroempresa",$array);
	}

	//funcion para modificar  un nuevo giro a una empresa
	public function update($_IDGE,$_giro, $_subGiro, $_Rama,$_principal)
	{
		$array = array( "IDGiro" => $_giro, "IDGiro2" => $_subGiro, "IDGiro3" => $_Rama, "Principal"=> $_principal);
		return $this->db->where("IDGE='$_IDGE'")->update("giroempresa", $array);
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
	//funcion para obtener los datos de una rama
	public function getrama_empresa($IDEmpresa){
		$respuesta=$this->db->select('IDNivel2,Giro,Principal')
		->from("giroempresa")
		->join('gironivel2','gironivel2.IDNivel2=giroempresa.IDGiro2')
		->where("IDEmpresa='$IDEmpresa'")->get();
		return $respuesta->result_array();
	}
	//funcion para obtener los datos de una rama
	public function getrama($IDRama){
		$respuesta=$this->db->select('*')->where("IDNivel1='$IDRama'")->get("gironivel1");
		return $respuesta->row_array();
	}
	//funcion para obtener los datos de un subgiro
	public function getsubgiro($IDRama){
		$respuesta=$this->db->select('*')->where("IDNivel2='$IDRama'")->get("gironivel2");
		return $respuesta->row_array();
	}
	//funcion para obtener los datos de un subbgiro
	public function getgiro($IDRama){
		$respuesta=$this->db->select('*')->where("IDGiro3='$IDRama'")->get("gironivel3");
		return $respuesta->row_array();
	}
}