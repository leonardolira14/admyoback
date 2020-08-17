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
	public function save($_ID_Empresa,$_Norma,$Fecha,$Calif,$_Archivo,$_Fecha_Vencimiento,$_Tipo,$_EmpresaCertificadoara,$_Clase)
	{
		$data=array(
			"Norma"=>$_Norma,
			"IDEmpresa"=>$_ID_Empresa,
			"Fecha"=>$Fecha,
			"Calif"=>$Calif,
			"Archivo"=>$_Archivo,
			"FechaVencimiento"=>$_Fecha_Vencimiento,
			"Tipo"=>$_Tipo,
			"EmpresaCertificadora"=> $_EmpresaCertificadoara,
			"Clase"=>$_Clase
		);
		return $this->db->insert("normascalidad",$data);
	}
	public function DelCert($cer){
		$this->db->where("IDNorma='$cer'");
		return $this->db->delete("normascalidad");
	}
	public function UpdateCert($cert,$norma,$fecha,$calif,$archivo,$_Fecha_Vencimiento,$_Tipo, $_EmpresaCertificadora,$_Clase){
		$data=array(
			"Norma"=>$norma,
			"Fecha"=>$fecha,
			"Calif"=>$calif,
			"Archivo"=>$archivo,
			"FechaVencimiento"=>$_Fecha_Vencimiento,
			"Tipo"=> $_Tipo,
			"EmpresaCertificadora"=> $_EmpresaCertificadora,
			"Clase"=>$_Clase
		);
		$this->db->where("IDNorma='$cert'");
		return $this->db->update("normascalidad",$data);
	}
}