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

	//funcion para validar la empresa por la razon social
	public function getempresaRazon($_Razon_Social){
		$respuesta=$this->db->select("*")->where("Razon_Social='$_Razon_Social'")->get("empresa");
		if($respuesta->num_rows()===0){
			return false;
		}else{
			return $respuesta->row_array();
		}
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
	//funcion para obter los daros de la empresa 
	public function getempresaRFC($RFC_Empresa){
		$respuesta=$this->db->select("*")->where("RFC='$RFC_Empresa'")->get("empresa");
		if($respuesta->num_rows()===0){
			return false;
		}else{
			return $respuesta->row_array();
		}

	}
	//funcion para agregar una empresa en la tabla de admyo
	public function preaddempresa($_Tipo_Persona,$_Razon_Social,$_Nombre_Comercial,$_RFC,$_Tipo_Cuenta,$_Status){
		$tokenApi=md5($_Razon_Social.$_Nombre_Comercial.$_RFC);
		$array=array("Persona"=>$_Tipo_Persona,"Razon_Social"=>$_Razon_Social,"Nombre_Comer"=>$_Nombre_Comercial,"RFC"=>$_RFC,"TipoCuenta"=>$_Tipo_Cuenta,"DiasPago"=>date('d/m/Y'),"Esta"=>$_Status,"Token_API"=>$tokenApi);
		$this->db->insert("empresa",$array);
		return $this->db->insert_id();
	}
	public function addgiros($_ID_Empresa,$_ID_Giro1,$_ID_Giro2,$_ID_Giro3)
	{
		
		$array=array("IDEmpresa"=>$_ID_Empresa,"Principal"=>1,"IDGiro"=>$_ID_Giro1,"IDGiro2"=>$_ID_Giro2,"IDGiro3"=>$_ID_Giro3);
		$this->db->insert("giroempresa",$array);
	}
	public function update(
		$_ID_Empresa,
		$_Razon_Social,
		$Nombre_Comercial,
		$rfc,
		$T_empresa,
		$_Empleados,
		$_Fac_Anual,
		$_perfil,
		$logo,
		$banner,
		$dias_pago_empresa,
		$Tel1,
		$Tel2,
		$Tel3
		){
		$array=array(
			"Razon_Social"=>$_Razon_Social,
			"Nombre_Comer"=>$Nombre_Comercial,
			"RFC"=>$rfc,"Perfil"=>$_perfil,
			"TipoEmpresa"=>$T_empresa,
			"NoEmpleados"=>$_Empleados,
			"FacAnual"=>$_Fac_Anual,
			"DiasPagoEmpresa"=>$dias_pago_empresa,
			"Tel1"=>$Tel1,
			"Tel2"=>$Tel2,
			"Tel3"=>$Tel3
		);	
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
	//funcion para obtener las empresas para calificarlas
	public function getempresa_calificar(){
		$emrpesas=$this->db->select("IDEmpresa,Razon_Social,RFC")->get("empresa");
		return $emrpesas->result_array();
	}
	public function datosRFCEm($rfc){
		$respu=$this->db->select('*')->where('RFC',$rfc)->get('empresa');
		if($respu->num_rows()==0){
			return false;
		}else{
			return $respu->row_array();
		}
		
	}
	//funcion para saber el giro principal de la empresa
	public function Get_Giro_Principal($_ID_Empresa){
		$_registro=$this->db->select("IDGiro,IDGiro2,IDGiro3")->where("IDEmpresa=$_ID_Empresa and Principal='1'")->get("giroempresa");
		return $_registro->row_array();

	}
	public function addRelacion($IDEmpresaP,$IDEmpresaB,$Tipo){
		$tp=$this->db->select('*')->where("IDEmpresaB=$IDEmpresaB and IDEmpresaP=$IDEmpresaP and Tipo='".$Tipo."'")->get("tbrelacion");
		if($tp->num_rows()===0){
			$datos= array('IDEmpresaP' =>$IDEmpresaP,"IDEmpresaB"=>$IDEmpresaB,"Tipo"=>$Tipo);
			$this->db->insert("tbrelacion",$datos);
		}
		
	}
	public function  AddEmpresa($persona='PFAE',$rz,$nc,$rfc,$n1,$n2,$n3,$estado="",$tipocuenta='basic',$esta='3',$idioma='esl',$pais="MX"){
		
		$array=array("Razon_Social"=>$rz,"RFC"=>$rfc,"Persona"=>$persona,
			"Estado"=>$estado,
			"Nombre_Comer"=>$nc,
			"Esta"=>$esta,
			"TipoCuenta"=>$tipocuenta,
			"Idioma"=>$idioma,
			"Pais"=>$pais
		);
		$this->db->insert("empresa",$array);
		$select="RFC='$rfc'";
		$this->db->where($select);
		$resp=$this->db->get("empresa");
		$IDempresa= $resp->result()[0]->IDEmpresa; 
		$this->AddGiro($IDempresa,$n1,$n2,$n3);
		return $IDempresa;
	}
	public function AddGiro($IDEmpresa,$n1,$n2,$n3){
		$array=array("IDEmpresa"=>$IDEmpresa,"IDGiro"=>$n1,"Principal"=>'0',"IDGiro2"=>$n2,"IDGiro3"=>$n3);
		return $this->db->insert('giroempresa', $array);
	}
	 
	//funcion para cambiar el logo de la empresa
	public function updatelogo($IDEmpresa,$logo){
		return $this->db->where("IDEmpresa='$IDEmpresa'")->update("empresa",array("Logo"=>$logo));
	}
	
	//funccion para modificar el pago el id y el pago de conecta 
	public function update_datos_conecta($IDEmpresa,$IDCustomer,$PlanID){
		$array=array("Customer_id"=>$IDCustomer,"Plan_ID"=>$PlanID);
		$this->db->where("IDEmpresa='$IDEmpresa'")->update("empresa",$array);
	}
	public function update_plan($IDEmpresa,$plan){
		$array=array("TipoCuenta"=>$plan);
		$this->db->where("IDEmpresa='$IDEmpresa'")->update("empresa",$array);
	}	
	/*
	//
	/
	/
	/
	//funcion para actualizar la configuaracion de alertas
	/
	/
	/
	*/
	public function update_alerta($IDEmpresa,$_config_Alertas){
		$respuesta = $this->db->where("IDEmpresa='$IDEmpresa'")->update("empresa",array("Configaletas"=>json_encode($_config_Alertas)));
		
		return $respuesta;
	}
	public function get_conf__alerta($IDEmpresa)
	{
		$respuesta =  $this->db->select('Configaletas')->where("IDEmpresa='$IDEmpresa'")->get("empresa");
		return $respuesta->row_array();
	}	

	//funcion para dar de baja una relacion
	public function update_relacion($IDRealcion,$status){
		$this->db->where("IDRelacion='$IDRealcion'")->update("tbrelacion",array("Status"=>$status));

	}
	// funcion para obtener los datos de pago
	public function getdata_pago($IDEmpresa){
		$respuesta=$this->db->select('*')->where("IDEmpresa='$IDEmpresa'")->get('pagos');
		return $respuesta->row_array();
	}
}