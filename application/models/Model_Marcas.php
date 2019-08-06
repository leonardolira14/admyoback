<?

/**
 * 
 */
class Model_Marcas extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	//funcion para obtner las marcas de una empresa
	public function getMarcasEmpresa($_ID_Empresa){
		$respuesta=$this->db->select("*")->where("IDEmpresa='$_ID_Empresa'")->get("marcas");
		return $respuesta->result_array();
	}
	//FUNCION para agregar una nueva marca
	public function add($_Marca,$_ID_Empresa,$_Logo){
		$array=array("Marca"=>$_Marca,"IDEmpresa"=>$_ID_Empresa,"logo"=>$_Logo);
		$this->db->insert("marcas",$array);

	}
	public function delete($_Marca){
		$this->db->where("IDMarca='$_Marca'")->delete("marcas");
	}
	public function update($_ID_Marca,$_Marca,$_Logo){
		if($_Logo===false){
			$array=array("Marca"=>$_Marca);
		}else{
			$array=array("Marca"=>$_Marca,"logo"=>$_Logo);	
		}
		$this->db->where("IDMarca='$_ID_Marca'")->update("marcas",$array);
	}
	public function getnum($_Empresa){
		$respuesta=$this->db->select('COUNT(*) as num')->where("IDEmpresa='$_Empresa'")->get("marcas");
		return $respuesta->row_array()["num"];
	}
}