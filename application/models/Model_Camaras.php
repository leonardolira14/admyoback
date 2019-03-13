<?

/**
 * 
 */
class Model_Camaras extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	public function  getall($IDEmpresa){
		$sql=$this->db->select('*')->where("IDEmpresa='$IDEmpresa'")->get("asociaciones");
		return $sql->result_array();
	}
	public function save($idempresa,$nombre,$web){
		$data=array("IDEmpresa"=>$idempresa,"Asociacion"=>$nombre,"Web"=>$web);
		return $this->db->insert("asociaciones",$data);
	}
	public function update($asocia,$nombre,$web){
		$data=array("Asociacion"=>$nombre,"Web"=>$web);
		$this->db->where("IDAsocia='$asocia'");
		return $this->db->update("asociaciones",$data);
	}
	public function delete($asocia){
		$this->db->where("IDAsocia='$asocia'");
		return $this->db->delete("asociaciones");
	}
}