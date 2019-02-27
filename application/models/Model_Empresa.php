<?

/**
 * 
 */
class Model_Empresa extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();

	}
	//funcion para obter los daros de la empresa 
	public function getempresa($_ID_Empresa){
		$respuesta=$this->db->select("*")->where("IDEmpresa='$_ID_Empresa'")->get("empresa");
		if($respuesta->num_rows()===0){
			return false;
		}else{
			return $respuesta->row_array();
		}

	}
	//funcion para agregar una empresa en la tabla de admyo
	public function preaddempresa($_Tipo_Persona,$_Razon_Social,$_Nombre_Comercial,$_RFC,$_Tipo_Cuenta,$_Status){
		$tokenApi=md5($_Razon_Social.$_Nombre_Comercial.$_RFC);
		$array=array("Persona"=>$_Tipo_Persona,"Razon_Social"=>$_Razon_Social,"Nombre_Comer"=>$_Nombre_Comercial,"RFC"=>$_RFC,"TipoCuenta"=>$_Tipo_Cuenta,"DiasPago"=>date('d'),"Esta"=>$_Status,"Token_API"=>$tokenApi);
		$this->db->insert("empresa",$array);
		return $this->db->insert_id();
	}
	public function addgiros($_ID_Empresa,$_ID_Giro1,$_ID_Giro2,$_ID_Giro3)
	{
		
		$array=array("IDEmpresa"=>$_ID_Empresa,"Principal"=>1,"IDGiro"=>$_ID_Giro1,"IDGiro2"=>$_ID_Giro2,"IDGiro3"=>$_ID_Giro3);
		$this->db->insert("giroempresa",$array);
	}
	public function update($_ID_Empresa,$_Razon_Social,$Nombre_Comercial,$rfc,$T_empresa,$_Empleados,$_Fac_Anual,$_perfil,$logo,$banner){
		$array=array("Razon_Social"=>$_Razon_Social,"Nombre_Comer"=>$Nombre_Comercial,"RFC"=>$rfc,"Perfil"=>$_perfil,"TipoEmpresa"=>$T_empresa,"NoEmpleados"=>$_Empleados,"FacAnual"=>$_Fac_Anual);	
		if($logo!==false){
			$array["Logo"]=$logo;
		}
		if($banner!==false){
			$array["banner"]=$banner;
		}
		$this->db->where("IDEmpresa='$_ID_Empresa'")->update("empresa",$array);
		
	}
	public function updatecontacto($_ID_Empresa,$_Pagina_Web,$Direc_Fiscal,$Colonia,$Deleg_Mpo,$Estado,$Codigo_Postal){
		$array=array("Codigo_Postal"=>$Codigo_Postal,"Sitio_Web"=>$_Pagina_Web,"Direc_Fiscal"=>$Direc_Fiscal,"Colonia"=>$Colonia,"Deleg_Mpo"=>$Deleg_Mpo,"Estado"=>$Estado,"Codigo_Postal"=>$Codigo_Postal);
		$this->db->where("IDEmpresa='$_ID_Empresa'")->update("empresa",$array);

	}
	public function getTels($_ID_Empresa){
		$sql=$this->db->select("*")->where("IDEmpresa='$_ID_Empresa'")->get("telefonos");
		if($sql->num_rows()===0){
			return false;
		}else{
			return $sql->result_array();
		}
	}
	public function addtel($_ID_Empresa,$numero,$tipo){
		$array=array("IDEmpresa"=>$_ID_Empresa,"Numero"=>$numero,"Tipo_Numero"=>$tipo);
		$this->db->insert("telefonos",$array);
	}
	public function delete_tel($_ID_Tel){
		$this->db->where("IDTel='$_ID_Tel'")->delete("telefonos");
	}
	public function update_tel($_ID_Tel,$numero,$tipo){
		$array=array("Numero"=>$numero,"Tipo_Numero"=>$tipo);
		$this->db->where("IDTel='$_ID_Tel'")->update("telefonos",$array);
	}

	
}