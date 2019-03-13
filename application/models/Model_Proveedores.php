<?
/**
 * 
 */
class Model_Proveedores extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('selec_Titulo');
		$this->constant="vkq4suQesgv6FVvfcWgc2TRQCmAc80iE";
	}
	//<=========nuevas funcion para admyo en  vercion 2 ======>>
	//funcion para obtener los clientes
	public function ObtenerClientes($idempresa){
		$clientes1=[];
		//esta relacion es para obtener en la tabla tbrelacion las que esten como IDEmpresaPque es la principal
		$sql=$this->db->select('*')->where("IDEmpresaP='$idempresa' and Tipo='proveedor'")->get("tbrelacion");
		if($sql->num_rows()!=0){	
			foreach ($sql->result() as $provedor) {

				array_push($clientes1,array("num"=>$provedor->IDEmpresaB));
			}
		}

		//ahora obtengo las que estan en la IDEmpresaB pero como cliente
		$sql=$this->db->select('*')->where("IDEmpresaB='$idempresa' and Tipo='cliente'")->get("tbrelacion");
		$clientes2=[];
		if($sql->num_rows()!=0){
			foreach ($sql->result() as $provedor) {
				array_push($clientes2,array("num"=>$provedor->IDEmpresaP));
			}
		}

		$clientes=array_merge($clientes1,$clientes2);
		$clientes = array_map('unserialize', array_unique(array_map('serialize', $clientes)));
		
		return $clientes;
	}
	public function DatosEmpresa($IDEmpresa){
		$this->db->select('*');
		$this->db->from('empresa');
		$this->db->where('IDEmpresa',$IDEmpresa);	
		$respu=$this->db->get();
		if($respu->num_rows()==0){
			return false;
		}else{
			return $respu->result()[0];
		}
		
	}
	//funcion para enlistar los proveedores
	public function listaproveedores($IDEmpresa){
		$listaproveedores=[];
		$lis=$this->ObtenerClientes($IDEmpresa);
		foreach ($lis as $proveedor) {
			$datos=$this->DatosEmpresa($proveedor["num"]);
			array_push($listaproveedores,array("num"=>$datos->IDEmpresa,"Razon_Social"=>$datos->Razon_Social,"Nombre_Comer"=>$datos->Nombre_Comer,"RFC"=>$datos->RFC,"Logo"=>$datos->Logo,"Visible"=>"Invisible","Banner"=>$datos->Banner));
		}
		return $listaproveedores;
	}
	//funcion para el resumen
	public function Resumen($IDEmpresa){
		//ahora reccorro cada uno delos clientes y veo que promedio le he dado en los meses
		//primero obtengo en el mes que estoy menos 2
		$serieclientespormes=[];
		$serieclientespormeslabel=[];
		$calificacionespormeslabel=[];
		$calificacionespormes=[];
		$promediopormeslabel=[];
		$promediopormes=[];
		if(date('m')==="01"){
			$mes1="11";
			$anio1=date('Y')-1;
			$mes2="12";
			$anio2=$anio1;
			$mes3=date("m");
			$anio3=date('Y');
		}else if(date('m')==="02"){
			$mes1="12";
			$anio1=date('Y')-1;
			$mes2="01";
			$anio2=date('Y');
			$mes3=date("m");
			$anio3=date('Y');
		}else{
			$mes1=date("m")-2;
			$anio1=date('Y');
			$mes2=date("m")-1;
			$anio2=date('Y');
			$mes3=date("m");
			$anio3=date('Y');
		}
		//ahora tengo las variables
		$resp1=$this->cuantoscalif($IDEmpresa,$mes1,$anio1,'Proveedor','Realizada');
		$resp2=$this->cuantoscalif($IDEmpresa,$mes2,$anio2,'Proveedor','Realizada');
		$resp3=$this->cuantoscalif($IDEmpresa,$mes3,$anio3,'Proveedor','Realizada');
		//serie grafica 1
		$seriebarraslabel=[$anio1."-".da_mes($mes1),$anio2."-".da_mes($mes2),$anio3."-".da_mes($mes3)];
		$seriebarras=array(array("data"=>[$resp1["totalmay8"],$resp2["totalmay8"],$resp3["totalmay8"]],"label"=>"Mayores de 8"),array("data"=>[$resp1["totalmen8"],$resp2["totalmen8"],$resp3["totalmen8"]],"label"=>"Menores de 8"),array("data"=>[$resp1["nocalificacados"],$resp2["nocalificacados"],$resp3["nocalificacados"]],"label"=>"No Calificados"));
		$data["seriecul"]=array("serie"=>$seriebarras,"labels"=>$seriebarraslabel);
		//grafica nuemero de clientes registrados por mes
		for($i=1;$i<=date('m');$i++){
			$sql=$this->db->select("count(*) as total")->where("IDEmpresaP='$IDEmpresa' and Tipo='Proveedor' and DATE(FechaRelacion) between '".date('Y')."-$i-01' and '".date('Y')."-$i-31' group by(IDEmpresaP)")->get("tbrelacion");
			if($sql->num_rows()===0){
				$num=0;
			}else{
				$num=$sql->result()[0]->total;
			}
			array_push($serieclientespormeslabel,da_mes($i));
			array_push($serieclientespormes,$num);
			//grafica Número de calificaciones realizadas a Clientes
			$sql=$this->db->select("count(*) as total")->where("IDEmpresaEmisor='$IDEmpresa' and Emitidopara='Proveedor' and Date(FechaRealizada) between '".date('Y')."-$i-01' and '".date('Y')."-$i-31'")->get('tbcalificaciones');
			$num=(int)$sql->result()[0]->total;
			$TcM=$this->db->select("(sum(tbdetallescalificaciones.PuntosObtenidos)/sum(tbdetallescalificaciones.PuntosPosibles)*10) as promedio ")->join('tbdetallescalificaciones','tbdetallescalificaciones.IDCalificacion=tbcalificaciones.IDCalificacion')->where("IDEmpresaEmisor='$IDEmpresa' and Emitidopara='Proveedor' and Date(FechaRealizada) between '".date('Y')."-$i-01' and '".date('Y')."-$i-31'")->get('tbcalificaciones');
			if($TcM->num_rows()===0){
				$promedio=0;
			}else{
				$promedio=round((float)$TcM->result()[0]->promedio,2);
			}
				array_push($calificacionespormeslabel,da_mes($i));
				array_push($calificacionespormes,$num);

				array_push($promediopormes,$promedio);
				array_push($promediopormeslabel,da_mes($i));
		}

		$data["serieclientes"]=array("datos"=>[array("data"=>$serieclientespormes,"label"=>'Numero de Clientes')],"labels"=>$serieclientespormeslabel);
		
		$data["serienumerodecalifmes"]=array("datos"=>[array("data"=>$calificacionespormes,"label"=>'Numero de Calificaciones')],"labels"=>$calificacionespormeslabel);
		
		$data["promediopormes"]=array("datos"=>[array("data"=>$promediopormes,"label"=>"Promedio de Calificaciones")],"labels"=>$promediopormeslabel);
		return $data;
	}
	//funcion para saber cuantos tengo calificacados y cuantos no 
	public function cuantoscalif($IDEmpresa,$mes,$anio,$tipo,$forma){

		$clientes=$this->ObtenerClientes($IDEmpresa);
		
		$mayoresde8=0;
		$menoresde8=0;
		$nocalificados=0;
		$calificacados=[];
		$bandera10=false;
		//ahora obtengo el totaal de calificaciones en  ese mes
		if($forma==="Realizada")
		{
			$TcM=$this->db->select("IDCalificacion,IDEmpresaReceptor as IDEmpresa")->where("IDEmpresaEmisor='$IDEmpresa' and Emitidopara='$tipo' and Date(FechaRealizada) between '$anio-$mes-01' and '$anio-$mes-31'")->get('tbcalificaciones');

		}
		else
		{
			$TcM=$this->db->select("tbcalificaciones.IDCalificacion,IDEmpresaEmisor as IDEmpresa,(sum(tbdetallescalificaciones.PuntosObtenidos)/sum(tbdetallescalificaciones.PuntosPosibles)*10) as Calificacion ")->join('tbdetallescalificaciones','tbdetallescalificaciones.IDCalificacion=tbcalificaciones.IDCalificacion')->where("IDEmpresaReceptor='$IDEmpresa' and Emitidopara='$tipo' and Date(FechaRealizada) between '$anio-$mes-01' and '$anio-$mes-31'")->get('tbcalificaciones');
		}
		
	
			foreach ($TcM->result() as $valoracion) {

				if($valoracion->IDCalificacion!=""){
					$sql=$this->db->select("(sum(tbdetallescalificaciones.PuntosObtenidos)/sum(tbdetallescalificaciones.PuntosPosibles)*10) as Calificacion")->where("IDCalificacion='$valoracion->IDCalificacion'")->get("tbdetallescalificaciones");
					if(count($calificacados)!=0){
							foreach ($calificacados as $key=>$empresa) {
								if($empresa["num"]===$valoracion->IDEmpresa){
									$calif=((float)$empresa["calificacion"]+(float)$sql->result()[0]->Calificacion)/2;
									$calificacados[$key]["calificacion"]=round($calif,2);
									$bandera10=true;
									break;
								}else{
									array_push($calificacados,array("num"=>$valoracion->IDEmpresa,"calificacion"=>round($sql->result()[0]->Calificacion,2)));
									break;
								}
							}
					}
					if($bandera10===false){
						array_push($calificacados,array("num"=>$valoracion->IDEmpresa,"calificacion"=>round($sql->result()[0]->Calificacion,2)));
						$bandera10=false;
					}
				}	
			}//fin del foreach principal

		if(count($calificacados)!=0){

			foreach ($calificacados as $calificado)
			{
				if($calificado["calificacion"]>=8){
					$mayoresde8++;
				}else if($calificado["calificacion"]<8){
					$menoresde8++;
				}			
			}
			$nocalificados=count($clientes)-($mayoresde8+$menoresde8);
			($nocalificados<0)?	$nocalificados=0:$nocalificados=$nocalificados ;
		}else{
			$nocalificados=count($clientes);
		}
		$data["totalmay8"]=$mayoresde8;
		$data["totalmen8"]=$menoresde8;
		$data["nocalificacados"]=$nocalificados;
		
		//$data["clientes"]=$clientes;
		return $data;
	}//fin de la funcion  cuantoscalif

	//<===fin para las funciones para vercion2====>>>
	
}