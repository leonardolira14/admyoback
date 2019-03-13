<?

/**
 * 
 */
class Model_Norma extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	//funcion para obtener los productos de una empresa
	public function getall($_ID_Empresa){
		$sql=$this->db->select("*")->where("IDEmpresa='$_ID_Empresa'")->get("normascalidad");
		return $sql->result_array();
	}
	public function save($_ID_Empresa,$_Norma,$Fecha,$Calif,$_Archivo)
	{
		$data=array("Norma"=>$_Norma,"IDEmpresa"=>$_ID_Empresa,"Fecha"=>$Fecha,"Calif"=>$Calif,"Archivo"=>$_Archivo);
		return $this->db->insert("normascalidad",$data);
	}
	public function DelCert($cer){
		$this->db->where("IDNorma='$cer'");
		return $this->db->delete("normascalidad");
	}
	public function UpdateCert($cert,$norma,$fecha,$calif,$archivo){
		$data=array(
			"Norma"=>$norma,
			"Fecha"=>$fecha,
			"Calif"=>$calif,
			"Archivo"=>$archivo
		);
		$this->db->where("IDNorma='$cert'");
		return $this->db->update("normascalidad",$data);
	}
}