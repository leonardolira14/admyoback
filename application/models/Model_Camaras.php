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
		$relaciones=$sql->result_array();
		foreach($relaciones as  $key => $relacion){
			$datos_asociacion=$this->getdata_asociacion($relacion["IDAsociacion"]);
			$relaciones[$key]["Nombre"]=$datos_asociacion["Nombre"];
			$relaciones[$key]["Siglas"]=$datos_asociacion["Siglas"];
			$relaciones[$key]["Web"]=$datos_asociacion["Web"];
			$relaciones[$key]["Imagen"]=$datos_asociacion["Imagen"];
			$relaciones[$key]["Direccion"]=$datos_asociacion["Direccion"];
			$relaciones[$key]["Estado"]=$datos_asociacion["Estado"];
			$relaciones[$key]["Municipio"]=$datos_asociacion["Municipio"];
			$relaciones[$key]["Colonia"]=$datos_asociacion["Colonia"];
			$relaciones[$key]["Telefono"]=$datos_asociacion["Telefono"];
		}
		return $relaciones;
		
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
	public function update_camara($datos){
		vdebug($datos);
	}
	public function delete($asocia){
		$this->db->where("IDAsocia='$asocia'");
		return $this->db->delete("asociaciones");
	}
	//funcion para generar una relacion de asociciones
	public function addrelacion($_IDEmpresa,$_IDAsociacion){
		$array=array(
			"IDEmpresa"=>$_IDEmpresa,
			"IDAsociacion"=>$_IDAsociacion
		);
		$this->db->insert("asociaciones",$array);
	}
	//funcion para traer la lista de asociaciones y camaras
	public function getall_list(){
		$respuesta=$this->db->select('*')->get('tblistaasociaciones');
		return $respuesta->result_array();
	}
	//funcion para agregar una asociacion a la base de datos
	public function addlistasociacion(
		$Nombre,
		$_Siglas,
		$_Imagen,
		$_Web,
		$_Estado,
		$_Municipio,
		$_Colonia,
		$_CP,
		$_Direccion,
		$_Telefono
		){
		$array=array(
			"Nombre"=>$Nombre,
			"Siglas"=>$_Siglas,
			"Imagen"=>$_Imagen,
			"Web"=>$_Web,
			"Estado"=>$_Estado,
			"Municipio"=>$_Municipio,
			"Colonia"=>$_Colonia,
			"CP"=>$_CP,
			"Direccion"=>$_Direccion,
			"Telefono"=>$_Telefono
		);
		$this->db->insert("tblistaasociaciones",$array);
		return $this->db->insert_id();
	}
	// funcion para obtener los datos de una asociacion
	public function getdata_asociacion($_IDAsociacion){
		$respuesta=$this->db->select('*')->where("IDAsociasiones='$_IDAsociacion'")->get("tblistaasociaciones");
		return $respuesta->row_array();
	}
}