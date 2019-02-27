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
		$respuesta=$this->db->select('tb_follow_empresas.IDEmpresaSeguida,empresa.Razon_Social')->from("tb_follow_empresas")->join("empresa","empresa.IDEmpresa=tb_follow_empresas.IDEmpresaSeguida")->where("IDEmpresaA='$_ID_Empresa' and Status='1'")->get();
		if($respuesta->num_rows()===0){
			return false;
		}else{
			return $respuesta->result_array();
		}
		
	}
}