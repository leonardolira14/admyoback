<?

/**
 * 
 * vdebug($_Calificaciones);
 */
class Model_Buscar extends CI_Model
{
	
	function __construct()
	{
		$this->load->database();
	}
	//function para buscar una empresa
	public function busquda_Filtro($_Palabra,$_Orden,$_Estado,$_Calificaciones,$_Certificaciones,$_Asociaciones){
		//primero verifico en que ordern las voy a solicitar
		if($_Orden===''){
			$_Orden="";
		}else if($_Orden==='az'){
			$_Orden=" order by Razon_Social asc";
		}else if($_Orden==='za'){
			$_Orden=" order by Razon_Social desc";
		}
		$_Resultadosr=$this->db->query("SELECT IDEmpresa FROM  empresa WHERE Razon_Social LIKE '%$_Palabra%'");
		$_Resultadosr=$_Resultadosr->result_array();
		
		//busco en su nombre comercial
		$_Resultadosnc=$this->db->query("SELECT IDEmpresa FROM  empresa WHERE Nombre_Comer LIKE '%$_Palabra%'");
		$_Resultadosnc=$_Resultadosnc->result_array();

		//busco por su rfc
		$_Resultadosrfc=$this->db->query("SELECT IDEmpresa FROM  empresa WHERE RFC LIKE '%$_Palabra%' ");
		$_Resultadosrfc=$_Resultadosrfc->result_array();

		//busco por su descripcion
		$_Resultadosprefil=$this->db->query("SELECT IDEmpresa FROM  empresa WHERE Perfil LIKE '%$_Palabra%' ");
		$_Resultadosprefil=$_Resultadosprefil->result_array();
		
		//buscos por sus productos y servicios
		$_Resultadospprod=$this->db->query("SELECT IDEmpresa FROM  productos WHERE Producto LIKE '%$_Palabra%'");
		$_Resultadospprod=$_Resultadospprod->result_array();
		

		//buscos por sus productos y servicios de su descripcion
		$_Resultadospprodd=$this->db->query("SELECT IDEmpresa FROM  productos WHERE Descripcion LIKE '%$_Palabra%'");
		$_Resultadospprodd=$_Resultadospprodd->result_array();
		

		//buscos por sus productos y servicios de su promocion
		$_Resultadospprodp=$this->db->query("SELECT IDEmpresa FROM  productos WHERE Promocion LIKE '%$_Palabra%'");
		$_Resultadospprodp=$_Resultadospprodp->result_array();
	

		//buscos por sus marca
		$_Resultadosmarcas=$this->db->query("SELECT IDEmpresa FROM  marcas WHERE Marca LIKE '%$_Palabra%'");
		$_Resultadosmarcas=$_Resultadosmarcas->result_array();

		//buscos por sus normas de calidad
		$_Resultadosnormas=$this->db->query("SELECT IDEmpresa FROM  normascalidad WHERE Norma LIKE '%$_Palabra%'");
		$_Resultadosnormas=$_Resultadosnormas->result_array();

		// ahohora tengo que unir todos los resultados en un solo array para obtener sus datos
		$todos=array_merge($_Resultadosr, $_Resultadosnc,$_Resultadosrfc,$_Resultadosprefil,$_Resultadospprod,$_Resultadospprodd,$_Resultadospprodp,$_Resultadosmarcas,$_Resultadosnormas);
		
		$_Resultados=[];
		//ahora elimino los repetidos
		foreach($todos as $empresa){
			$bandera=false;
			$resulta=in_array_r($empresa["IDEmpresa"],$_Resultados);
			if($resulta===false){
				array_push($_Resultados,array("IDEmpresa"=>$empresa["IDEmpresa"]));
			}			
		}
		
		// ahora obtengo el filtro de estados
		if($_Estado!==''){
			foreach($_Resultados as $keys=>$_empresa){
				$_datos_empresa=$this->Datos_Empresa($_empresa["IDEmpresa"]);
				if($_datos_empresa["Estado"]!==$_Estado){
					unset($_Resultados[$keys]);
				}
			}
			
		}
		$_Resultados = array_values($_Resultados);

		
		// ahora obtengo el filtro de Certificaciones
		if($_Certificaciones===TRUE){
			foreach($_Resultados as $keys=>$_empresa){
				$_datos_empresa=$this->Datos_certificaciones($_empresa["IDEmpresa"]);
				if(count($_datos_empresa)===0){
					unset($_Resultados[$keys]);
				}
			}
			
		}
		$_Resultados = array_values($_Resultados);

		
		// ahora obtengo el filtro de Asociaciones
		if($_Asociaciones===TRUE){
			foreach($_Resultados as $keys=>$_empresa){
				$_datos_empresa=$this->Datos_Asociaciones($_empresa["IDEmpresa"]);
				if(count($_datos_empresa)===0){
					unset($_Resultados[$keys]);
				}
			}
			
		}
		$_Resultados = array_values($_Resultados);
	
		$_Datos=[];
		
		if($_Calificaciones!==""){
			$_DatosCalificaciones=explode('-',$_Calificaciones);
			
			foreach($_Resultados as $_Empresa){
				$_DatosEmpresa=$this->Datos_Empresa($_Empresa["IDEmpresa"]);
				$_total=$this->db->query("SELECT COUNT('*') AS total FROM tbcalificaciones where IDEmpresaReceptor='".$_Empresa["IDEmpresa"]."'");
				$_total=$_total->row_array();
				if((int)$_total["total"]>=(int)$_DatosCalificaciones[0] && (int)$_total["total"]<=(int)$_DatosCalificaciones[1] ){
					array_push($_Datos,array("IDEmpresa"=>$_DatosEmpresa["IDEmpresa"],"Razon_Social"=>$_DatosEmpresa["Razon_Social"],"RFC"=>$_DatosEmpresa["RFC"],"Nombre_Comer"=>$_DatosEmpresa["Nombre_Comer"],"Perfil"=>$_DatosEmpresa["Perfil"],"Logo"=>$_DatosEmpresa["Logo"],"Banner"=>$_DatosEmpresa["Banner"]));
				}
				
			}

		}else{
			foreach($_Resultados as $_Empresa){
				
				$_DatosEmpresa=$this->Datos_Empresa($_Empresa["IDEmpresa"]);
				$_total=$this->db->query("SELECT COUNT('*') AS total FROM tbcalificaciones where IDEmpresaReceptor='".$_Empresa["IDEmpresa"]."'");
				array_push($_Datos,array("IDEmpresa"=>$_DatosEmpresa["IDEmpresa"],"Razon_Social"=>$_DatosEmpresa["Razon_Social"],"RFC"=>$_DatosEmpresa["RFC"],"Nombre_Comer"=>$_DatosEmpresa["Nombre_Comer"],"Perfil"=>$_DatosEmpresa["Perfil"],"Logo"=>$_DatosEmpresa["Logo"],"Banner"=>$_DatosEmpresa["Banner"]));
							
			}
		}
		return $_Datos;		
	}

	public function Datos_Empresa($_IDEmpresa){
		
		$_respuesta=$this->db->select('*')->where("IDEmpresa='$_IDEmpresa'")->get('empresa');
		return $_respuesta->row_array();
	}

	public function Datos_certificaciones($_IDEmpresa){
		
		$_respuesta=$this->db->select('*')->where("IDEmpresa='$_IDEmpresa'")->get('normascalidad');
		return $_respuesta->result_array();
	}
	public function Datos_Asociaciones($_IDEmpresa){
		
		$_respuesta=$this->db->select('*')->where("IDEmpresa='$_IDEmpresa'")->get('asociaciones');
		return $_respuesta->result_array();
	}
	
}