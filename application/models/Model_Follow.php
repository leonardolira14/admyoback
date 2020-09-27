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
		$this->load->model("Model_Imagen");
		$this->load->model("Model_Empresa");
	}

	//funcion para obtener las empresa que siguen 
	public function getAll($_ID_Empresa){
		$respuesta=$this->db->select('tb_follow_empresas.IDFollow,tb_follow_empresas.IDEmpresaSeguida,empresa.Razon_Social,empresa.Logo,empresa.Nombre_Comer,empresa.RFC,empresa.Banner')->from("tb_follow_empresas")->join("empresa","empresa.IDEmpresa=tb_follow_empresas.IDEmpresaSeguida")->where("IDEmpresaA='$_ID_Empresa' and Status='1'")->get();
		$resultados=$respuesta->result_array();
		foreach ($resultados as $key => $value) {
			
			$resultados[$key]["imagencliente"]=$this->Model_Imagen->imgcliente($value["IDEmpresaSeguida"],"A","cliente",$resumen=FALSE);
			$resultados[$key]["imagenproveedor"]=$this->Model_Imagen->imgcliente($value["IDEmpresaSeguida"],"A","proveedor",$resumen=FALSE);
		}
		return $resultados;
		
	}
	//filtro especial 
	public function filtro_especial($_Rango,$_IDEmpresa,$_Estado){
		$Lista_return=[];
		$lista_empresas_seguidas=$this->getAll($_IDEmpresa);
		if($_Rango!==''){
			$_DatosCalificaciones=explode('-',$_Rango);
			foreach ($lista_empresas_seguidas as $key => $value) {
				$respueta_rango=$this->get_rangoCalifaciones($_DatosCalificaciones[0],$_DatosCalificaciones[1],$value["IDEmpresaSeguida"]);
				if($respueta_rango){
					array_push($Lista_return,$value);
				}
			}
			
		}else{
			$Lista_return=$lista_empresas_seguidas;
		}
		//ahora vamos a filtrar por estado
		if($_Estado!==''){
			foreach($Lista_return as $keys=>$_empresa){
				$_datos_empresa=$this->Model_Empresa->getempresa($_empresa["IDEmpresaSeguida"]);
				if($_datos_empresa["Estado"]!==$_Estado){
					unset($Lista_return[$keys]);
				}
			}
			
		}
		$Lista_return = array_values($Lista_return);
		return  $Lista_return;
		
	}

	//funcion para obtener que  siguen esta empresa
	public function getAll_que_la_siguen($_ID_Empresa){
		$respuesta=$this->db->select('tb_follow_empresas.IDEmpresaA,tb_follow_empresas.IDFollow,tb_follow_empresas.IDEmpresaSeguida,empresa.Razon_Social,empresa.Logo,empresa.Nombre_Comer,empresa.RFC,empresa.Banner')->from("tb_follow_empresas")->join("empresa","empresa.IDEmpresa=tb_follow_empresas.IDEmpresaSeguida")->where("IDEmpresaSeguida='$_ID_Empresa' and Status='1'")->get();
		
		return $respuesta->result_array();
		
	}
	function get_rangoCalifaciones($_Rango_A,$_Rango_B,$_Empresa){
		$_total=$this->db->select("COUNT('*') AS total")->where("IDEmpresaReceptor='".$_Empresa."'")->get("tbcalificaciones");
		$_total=$_total->row_array();
		
		if((int)$_total["total"]>=(int)$_Rango_A && (int)$_total["total"]<=(int)$_Rango_B ){
			return true;
		}else {
			return false;
		}
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

	// funcion para saver si sigo o no a esta emprsa
	public function getfollowtrue($IDEmpresa,$IDEmpresaseguida){
		$respuesta = $this->db->select('*')->where("IDEmpresaA='$IDEmpresa' AND IDEmpresaSeguida='$IDEmpresaseguida'")->get('tb_follow_empresas');
		
		if($respuesta->num_rows()===0){
			return false;
		}
			return true;
	}
}