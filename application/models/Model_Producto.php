<?

/**
 * 
 */
class Model_Producto extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
	}
	//funcion para obtener los productos de una empresa
	public function getall($_ID_Empresa){
		$sql=$this->db->select("*")->where("IDEmpresa='$_ID_Empresa'")->get("productos");
		return $sql->result_array();
	}
	public function save($_ID_Empresa,$_Producto,$_Promocion,$_Descripcion,$_Logo){
		$array=array("IDEmpresa"=>$_ID_Empresa,"Producto"=>$_Producto,"Promocion"=>$_Promocion,"Descripcion"=>$_Descripcion,"Foto"=>$_Logo);
		return $this->db->insert("productos",$array);
	}
	public function update($_ID_Producto,$_Producto,$_Promocion,$_Descripcion,$_Logo){
		$array=array("Producto"=>$_Producto,"Promocion"=>$_Promocion,"Descripcion"=>$_Descripcion,"Foto"=>$_Logo);
		return $this->db->where("IDProducto='$_ID_Producto'")->update("productos",$array);
	}
	public function delete($_ID_Producto){
		return $this->db->where("IDProducto='$_ID_Producto'")->delete("productos");
	}
}