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
}