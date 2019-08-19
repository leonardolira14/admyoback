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
	//funcion para obtener que  siguen esta empresa
	public function getAll_que_la_siguen($_ID_Empresa){
		$respuesta=$this->db->select('tb_follow_empresas.IDEmpresaA,tb_follow_empresas.IDFollow,tb_follow_empresas.IDEmpresaSeguida,empresa.Razon_Social,empresa.Logo,empresa.Nombre_Comer,empresa.RFC,empresa.Banner')->from("tb_follow_empresas")->join("empresa","empresa.IDEmpresa=tb_follow_empresas.IDEmpresaSeguida")->where("IDEmpresaSeguida='$_ID_Empresa' and Status='1'")->get();
		return $respuesta->result_array();
		
	}
	public function olvidar($_IDFollow){
		$this->db->where("IDFollow='$_IDFollow'")->delete('tb_follow_empresas');
	}
	public function get_num($IDEmpresa){
		$respuesta=$this->db->select('count(*) as num')->where("IDEmpresaA='$IDEmpresa'")->get("tb_follow_empresas");
		return $respuesta->row_array()["num"];
	}
	public function tb_follow_empresas($IDEmpresa,$IDEmpresaB){
		$respu=$this->db->select('*')->where("IDEmpresaA='$IDEmpresa' and IDEmpresaSeguida='$IDEmpresaB'")->get("tb_follow_empresas");
		if($respu->num_rows()===0){
			$array=array("IDEmpresaA"=>$IDEmpresa,"IDEmpresaSeguida"=>$IDEmpresaB,"Status"=>1);
			$this->db->insert('tb_follow_empresas',$array);
		}
		
	}
}