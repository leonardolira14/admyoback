<?

/**
 * 
 */
class Model_QvalEmpresa extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->constante="FpgH456Gtdgh43i349gjsjf%ttt";
		
	}
	//funcin para registara una empresa 
	public function addempresa($_Razon_Social,$_Nombre_Comercial,$_Rfc,$_Tipo_Persona,$_Paquete,$_Licecias,$_ID_Admyo){
		$db_prueba = $this->load->database('qval', TRUE);
		$array=array("RazonSocial"=>$_Razon_Social,"NombreComercial"=>$_Nombre_Comercial,"RFC"=>$_Rfc,"TipoPersona"=>$_Tipo_Persona,"Paquete"=>$_Paquete,"Nolicencias"=>$_Licecias,"IDAdmyo"=>$_ID_Admyo);
		$db_prueba->insert("empresa",$array);
		$ultimoId = $db_prueba->insert_id();
		return $ultimoId;

	}
	public function addusuario($_ID_Empresa,$_Nombre,$_Apellidos,$_Puesto,$_ID_Config,$_Usuario,$_Correo,$_Clave)
	{
		$_Clave=md5($_Clave.$this->constante);
		$db_prueba = $this->load->database('qval', TRUE);
		$array=array("IDEmpresa"=>$_ID_Empresa,"Nombre"=>$_Nombre,"Apellidos"=>$_Apellidos,"Usuario"=>$_Correo,"Correo"=>$_Correo,"Clave"=>$_Clave,"Est"=>1,"Funciones"=>'["1","1","1","1","1","1","1","1","1"]');
		$db_prueba->insert("usuario",$array);

	}
}