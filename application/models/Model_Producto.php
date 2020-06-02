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
	public function save($_ID_Empresa,$_Producto,$_Descripcion,$_Logo,$_Clave){
		$array=array(
			"IDEmpresa"=>$_ID_Empresa,
			"Producto"=>$_Producto,
			"Descripcion"=>$_Descripcion,
			"Clave"=>$_Clave,
			"Foto"=>$_Logo);
		return $this->db->insert("productos",$array);
	}
	public function update($_ID_Producto,$_Producto,$_Descripcion,$_Logo,$_Clave){
		$array=array(
			"Producto"=>$_Producto,
			"Descripcion"=>$_Descripcion,
			"Clave"=>$_Clave,
			"Foto"=>$_Logo
		);
		return $this->db->where("IDProducto='$_ID_Producto'")->update("productos",$array);
	}
	public function delete($_ID_Producto){
		return $this->db->where("IDProducto='$_ID_Producto'")->delete("productos");
	}
	public function getnum($_Empresa){
		$respuesta=$this->db->select('COUNT(*) as num')->where("IDEmpresa='$_Empresa'")->get("productos");
		return $respuesta->row_array()["num"];
	}
}