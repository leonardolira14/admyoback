<?

/**
 * 
 */
class Model_Usuario extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->constant="vkq4suQesgv6FVvfcWgc2TRQCmAc80iE";
	}
	//funcion para agregar un usuario de una empresa
	public function addUsuario($_ID_Empresa,$_Nombre,$_Apellidos,$_Correo,$_Usuario,$_Clave,$_Puesto,$_Visible,$_Tipo_Usuario)
	{
		$TokenActivar=md5($_Nombre.$_Apellidos.$_Correo.date('d/m/Y H:i:s'));
		$clave=md5($_Clave.$this->constant).":".$this->constant;
		$data=array(
				"Nombre"=>$_Nombre,
				"Apellidos"=>$_Apellidos,
				"Puesto"=>$_Puesto,
				"Correo"=>$_Correo,
				"password"=>$clave,
				"visible"=>$_Visible,
				"Status"=>1,
				"Fecha_Alta"=>date('Y-m-d H:i:s'),
				"Token_Activar"=>$TokenActivar,
				"IDEmpresa"=>$_ID_Empresa,
				"Tipo_Usuario"=>$_Tipo_Usuario
				);
		$this->db->insert("usuarios",$data);
		return $TokenActivar;
	}
	//funcion para el ligin
	public function login($_user,$_password){
		$clave=md5($_password.$this->constant).":".$this->constant;
		$respuesta=$this->db->select("*")->where("Correo='$_user' and password='$clave'")->get("usuarios");
		if($respuesta->num_rows()===0){
			return false;
		}else{
			return $respuesta->row_array();
		}
	}
	public function addacceso($_ID_Usuario,$_Fecha,$Estatus){

		$respuesta=$this->db->select("*")->where("IDUsuario='$_ID_Usuario' and Estatus='1'")->get("accesos");
		if($respuesta->num_rows()===0){
			$Token=md5($_ID_Usuario.date('Y-m-d'));
			$array=array("Token"=>$Token,"IDUsuario"=>$_ID_Usuario,"FechaAcceso"=>$_Fecha,"Estatus"=>$Estatus);
			$this->db->insert("accesos",$array);
		}else{
			$Token=$respuesta->row_array()["Token"];
		}
		
		return $Token;
		
	}
	//funcion para revisar un token
	public function checktoken($token){
		$respuesta=$this->db->select("*")->where("Token='$token' and Estatus='1'")->get('accesos');
		if($respuesta->num_rows===0){
			return false;
		}else{
			return $respuesta->row_array();
		}
	}
	public function update($_ID_Usuario,$_Nombre,$_Apellidos,$_Puesto,$_Correo,$_Visible){
		$array=array("Nombre"=>$_Nombre,"Apellidos"=>$_Apellidos,"Puesto"=>$_Puesto,"Correo"=>$_Correo,"Visible"=>$_Visible);
		return $this->db->where("IDUsuario='$_ID_Usuario'")->update("usuarios",$array);
	}
	public function updateclave($_ID_Usuario,$_Clave){
		$clave=md5($_Clave.$this->constant).":".$this->constant;
		$array=array("password"=>$clave);
		return $this->db->where("IDUsuario='$_ID_Usuario'")->update("usuarios",$array);
	}
	public function getAlluser($_ID_Empresa){
		$sql=$this->db->select("*")->where("IDEmpresa='$_ID_Empresa'")->get("usuarios");
		return $sql->result_array();
	}
	public function updatestatus($_ID_Usuario,$_Status){
		$array=array("Status"=>$_Status);
		$this->db->where("IDUsuario='$_ID_Usuario'")->update("usuarios",$array);
	}
	public function Master($_ID_Empresa,$_ID_Usuario){
		$array=array("Tipo_Usuario"=>"");
		$this->db->where("IDEmpresa='$_ID_Empresa'")->update("usuarios",$array);
		$array=array("Tipo_Usuario"=>'Master');
		$this->db->where("IDUsuario='$_ID_Usuario'")->update("usuarios",$array);

	}
	public function GetMaster($_ID_Empresa){
		$sql=$this->db->select("*")->where("Tipo_Usuario='Master' and IDEmpresa='$_ID_Empresa'")->get("usuarios");
		return $sql->result_array();
	}
	public function cerrar($_Token){
		$array=array("Estatus"=>1,"FechaFin"=>date('Y-m-d'));
		$sql=$this->db->where("Token='$_Token'")->get("accesos");
	
	}

}