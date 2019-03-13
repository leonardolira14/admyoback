<?

/**
 * 
 */
class Model_Follow extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}

	//funcion para obtener las empresa que siguen 
	public function getAll($_ID_Empresa){
		$respuesta=$this->db->select('tb_follow_empresas.IDFollow,tb_follow_empresas.IDEmpresaSeguida,empresa.Razon_Social,empresa.Logo,empresa.Nombre_Comer,empresa.RFC,empresa.Banner')->from("tb_follow_empresas")->join("empresa","empresa.IDEmpresa=tb_follow_empresas.IDEmpresaSeguida")->where("IDEmpresaA='$_ID_Empresa' and Status='1'")->get();
		return $respuesta->result_array();
		
	}
	public function olvidar($_IDFollow){
		$this->db->where("IDFollow='$_IDFollow'")->delete('tb_follow_empresas');
	}
}