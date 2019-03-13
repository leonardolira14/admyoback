<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_Clieprop extends CI_Model{
	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->helper('selec_Titulo');
		$this->constant="vkq4suQesgv6FVvfcWgc2TRQCmAc80iE";
	}
	public function DatosEmpresa($IDEmpresa){
		$this->db->select('*');
		$this->db->from('empresa');
		$this->db->where('IDEmpresa',$IDEmpresa);	
		$respu=$this->db->get();
		if($respu->num_rows()==0){
			return false;
		}else{
			return $respu->result();
		}
		
	}
	//<=========nuevas funcion para admyo en  vercion 2 ======>>
	//funcion para enlistar los proveedores
	public function listaclientes($IDEmpresa){
		$listaproveedores=[];
		$lis=$this->ObtenerClientes($IDEmpresa);
		foreach ($lis as $proveedor) {
			$datos=$this->DatosEmpresa($proveedor["num"]);
			array_push($listaproveedores,array("num"=>$datos[0]->IDEmpresa,"Razon_Social"=>$datos[0]->Razon_Social,"Nombre_Comer"=>$datos[0]->Nombre_Comer,"RFC"=>$datos[0]->RFC,"Logo"=>$datos[0]->Logo,"Visible"=>"Invisible","Banner"=>$datos[0]->Banner));
		}
		return $listaproveedores;
	}
	//funcion para obtener los clientes
	public function ObtenerClientes($idempresa){
		$clientes1=[];
		//esta relacion es para obtener en la tabla tbrelacion las que esten como IDEmpresaPque es la principal
		$sql=$this->db->select('*')->where("IDEmpresaP='$idempresa' and Tipo='cliente'")->get("tbrelacion");
		if($sql->num_rows()!=0){	
			foreach ($sql->result() as $provedor) {
				array_push($clientes1,array("num"=>$provedor->IDEmpresaB));
			}
		}
		//ahora obtengo las que estan en la IDEmpresaB pero como cliente
		$sql=$this->db->select('*')->where("IDEmpresaB='$idempresa' and Tipo='proveedor'")->get("tbrelacion");
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
		$resp1=$this->cuantoscalif($IDEmpresa,$mes1,$anio1,'Cliente','Realizada');
		$resp2=$this->cuantoscalif($IDEmpresa,$mes2,$anio2,'Cliente','Realizada');
		$resp3=$this->cuantoscalif($IDEmpresa,$mes3,$anio3,'Cliente','Realizada');
		//serie grafica 1
		$seriebarraslabel=[$anio1."-".da_mes($mes1),$anio2."-".da_mes($mes2),$anio3."-".da_mes($mes3)];
		$seriebarras=array(array("data"=>[$resp1["totalmay8"],$resp2["totalmay8"],$resp3["totalmay8"]],"label"=>"Mayores de 8"),array("data"=>[$resp1["totalmen8"],$resp2["totalmen8"],$resp3["totalmen8"]],"label"=>"Menores de 8"),array("data"=>[$resp1["nocalificacados"],$resp2["nocalificacados"],$resp3["nocalificacados"]],"label"=>"No Calificados"));
		
		$data["seriecul"]=array("serie"=>$seriebarras,"labels"=>$seriebarraslabel);
		//grafica nuemero de clientes registrados por mes
		for($i=1;$i<=date('m');$i++){
			$sql=$this->db->select("count(*) as total")->where("IDEmpresaP='$IDEmpresa' and Tipo='Cliente' and DATE(FechaRelacion) between '".date('Y')."-$i-01' and '".date('Y')."-$i-31' group by(IDEmpresaP)")->get("tbrelacion");
			if($sql->num_rows()===0){
				$num=0;
			}else{
				$num=$sql->result()[0]->total;
			}
			array_push($serieclientespormeslabel,da_mes($i));
			array_push($serieclientespormes,$num);
			//grafica Número de calificaciones realizadas a Clientes
			$sql=$this->db->select("count(*) as total")->where("IDEmpresaEmisor='$IDEmpresa' and Emitidopara='Cliente' and Date(FechaRealizada) between '".date('Y')."-$i-01' and '".date('Y')."-$i-31'")->get('tbcalificaciones');
			$num=(int)$sql->result()[0]->total;
			$TcM=$this->db->select("(sum(tbdetallescalificaciones.PuntosObtenidos)/sum(tbdetallescalificaciones.PuntosPosibles)*10) as promedio ")->join('tbdetallescalificaciones','tbdetallescalificaciones.IDCalificacion=tbcalificaciones.IDCalificacion')->where("IDEmpresaEmisor='$IDEmpresa' and Emitidopara='Cliente' and Date(FechaRealizada) between '".date('Y')."-$i-01' and '".date('Y')."-$i-31'")->get('tbcalificaciones');
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
				$sql=$this->db->select("(sum(tbdetallescalificaciones.PuntosObtenidos)/sum(tbdetallescalificaciones.PuntosPosibles)*10) as Calificacion")->where("IDCalificacion='$valoracion->IDCalificacion'")->get("tbdetallescalificaciones");
				if($valoracion->IDCalificacion!=""){
				if(count($calificacados)!=0){
						foreach ($calificacados as $key=>$empresa) {
							if($empresa["num"]===$valoracion->IDEmpresa){
								$calif=((float)$empresa["calificacion"]+(float)$sql->result()[0]->Calificacion)/2;
								$calificacados[$key]["calificacion"]=round($calif,2);
								$bandera10=true;
								break;
							}else{
								$bandera10=false;
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






	//funcion para obtener los clientes por anio
	public function ObtenerClientesAnioymes($idempresa,$anio,$mes){
		$sql="IDEmpresa='$idempresa' and Forma='Realizada' and Tipo='Proveedor' and Status_Val='Activa'  order by Fecha asc limit 1";
		$this->db->select('Fecha');
		$this->db->from('calificaciones');
		$this->db->where($sql);	
		$prove=$this->db->get();
		if($prove->num_rows()==0){
			$fecha='$anio-$mes-01';
		}else{
			$fecha=$prove->result()[0]->Fecha;
		}
		$sql="IDEmpresa='$idempresa' and Forma='Realizada' and Tipo='Proveedor' and Status_valora='ACTIVA' and date(Fecha) between '$fecha' and '$anio-$mes-31' group by IDEmpresa_Valorada";
		$this->db->select('IDEmpresa_Valorada');
		$this->db->from('calificaciones');
		$this->db->join('valoraciones','valoraciones.IDValora=calificaciones.IDValora');
		$this->db->where($sql);	
		$prove=$this->db->get();
		$prove1=[];
		if($prove->num_rows()!=0){
			foreach ($prove->result() as $provedor) {
				array_push($prove1,array("num"=>$provedor->IDEmpresa_Valorada));
			}
		}
			$sql="IDEmpresa='$idempresa' and Forma='Recibida' and Tipo='Cliente' and Status_Val='Activa'  order by Fecha asc limit 1";
		$this->db->select('Fecha');
		$this->db->from('calificaciones');
		$this->db->where($sql);	
		$prove=$this->db->get();
		if($prove->num_rows()==0){
			$fecha='$anio-$mes-01';
		}else{
			$fecha=$prove->result()[0]->Fecha;
		}
		$sql="IDEmpresa='$idempresa' and Forma='Recibida' and Tipo='Cliente' and Status_valora='ACTIVA' and date(Fecha) between '$fecha' and '$anio-$mes-31' group by IDEmpresa_Valoradora";
		$this->db->select('IDEmpresa_Valoradora');
		$this->db->from('calificaciones');
		$this->db->join('valoraciones','valoraciones.IDValora=calificaciones.IDValora');
		$this->db->where($sql);	
		$prove=$this->db->get();
		$prove2=[];
		if($prove->num_rows()!=0){
			
			foreach ($prove->result() as $provedor) {
				array_push($prove2,array("num"=>$provedor->IDEmpresa_Valoradora));
			}
		}
		
		$clientes=array_merge($prove1,$prove2);
		$clientes = array_map('unserialize', array_unique(array_map('serialize', $clientes)));

		return $clientes;
	}
	public function ObtenerReputacionClientes($IDEmpresa,$anio){
		$data=[];
		$met=[];
		$numclientes=[];
		$clientesacum=[];
		$cli=[];
		$ma=[];
		$me=[];
		$em=[];
		if($anio!=date('Y')){
			$mesultimo=12;
		}else{
			$mesultimo=date('m');
		}
		for($i=1;$i<=$mesultimo;$i++){
			array_push($met,da_mes($i));
			if($i<10){
				$mes="0".$i;
			}
			$mantenido=0;
			$mejorado=0;
			$empeorado=0;
			
			$clientes=$this->ObtenerClientesAnioymes($IDEmpresa,$anio,$mes);
			$clientes=array_merge($clientesacum,$clientes);
			$clientes = array_map('unserialize', array_unique(array_map('serialize', $clientes)));
			$clientesacum=$clientes;
			array_push($numclientes,count($clientes));
			foreach($clientes as $key){
				
				if($key["num"]!=$IDEmpresa ){
					$rep=$this->ReputaciondeUnaEmpresa($key["num"],$mes,$anio);
					if($rep["puesto"]=="Em"){
						$empeorado++;
					}else if($rep["puesto"]=="Me"){
						$mejorado++;
					}else if($rep["puesto"]=="Ma"){
						$mantenido++;
					}
				}
			}
			array_push($ma,$mantenido);
			array_push($me,$mejorado);
			array_push($em, $empeorado);
			$total=intval($mantenido)+intval($mejorado)+intval($empeorado);
		}
		$clientesacum=[];
		foreach($clientes as $key){
			
			if($key["num"]!=$IDEmpresa ){
				$rep=$this->ReputaciondeUnaEmpresa($key["num"],$mes,$anio);
				$dat=$this->DatosEmpresa($key["num"]);
				if($dat[0]!=null){
					array_push($clientesacum,array("num"=>$key["num"],"Logo"=>$dat[0]->Logo,"Razon_Social"=>$dat[0]->Razon_Social,"Nombre_Comer"=>$dat[0]->Nombre_Comer,"RFC"=>$dat[0]->RFC,"leyenda"=>$rep["leyenda"],"class"=>$rep["class"]));
				}
			
				
			}
		}
		$data["TotalClientes"]=array("Empeorado"=>$empeorado,"Mantenido"=>$mantenido,"Mejorado"=>$mejorado);
		$data["Porcentajes"]=array("PorEmpeorado"=>porcentaje($total,$empeorado),"PorMantenido"=>porcentaje($total,$mantenido),"PorMejorado"=>porcentaje($total,$mejorado));
		$data["reputacion"]=array(array("name"=>"Mejorado","data"=>$me,"color"=>"#00C331"),array("name"=>"Mantenido","data"=>$ma,"color"=>"#008CC3"),array("name"=>"Empeorado","data"=>$em,"color"=>"#C30000"));
		$data["Catego"]=$met;
		$data["clientes"]=$clientesacum;

		return $data;
	}
	//funcion para obtener la reputacion
	public function ReputaciondeUnaEmpresa($idempresa,$mes2,$anio2){
		$arriba=0;
		$abajo=0;
		if($mes2=="01"){
			$mes1=12;
			$anio1=$anio2-1;
		}else{
			$mes1=$mes2-1;
			$anio1=$anio2;
		}


		$sql=" IDEmpresa='$idempresa' and Forma='Recibida' and Status_Val='Activa' and date(Fecha) between '$anio1-$mes1-01' and  '$anio1-$mes1-31'";
		$this->db->select('Calificacion');
		$this->db->from('calificaciones');
		$this->db->where($sql);	
		$respu=$this->db->get();
		if($respu->num_rows()!=0){
			foreach ($respu->result() as $key) {
				$arriba=$arriba+$key->Calificacion;
			}
			$abajo=$respu->num_rows();
			$promedio=$arriba/$abajo;
		}else{
			$promedio=0;
		}
		$sql=" IDEmpresa='$idempresa' and Forma='Recibida' and Status_Val='Activa' and date(Fecha) between '$anio2-$mes2-01' and  '$anio2-$mes2-31'";
		$this->db->select('Calificacion');
		$this->db->from('calificaciones');
		$this->db->where($sql);	
		$respu=$this->db->get();
		if($respu->num_rows()!=0){
			foreach ($respu->result() as $key) {
				$arriba=$arriba+$key->Calificacion;
			}
			$abajo=$respu->num_rows();
			$promedio2=$arriba/$abajo;
		}else{
			$promedio2=0;
		}
		if($promedio>$promedio2){
			$data["puesto"]="Em";
			$data["diferencia"]=round($promedio-$promedio2,2);
			$data["leyenda"]="La empresa ha empeorado su reputación.";
			$data["class"]="colorrojo";

		}else if($promedio<$promedio2){
			$data["puesto"]="Me";
			$data["diferencia"]=round($promedio2-$promedio,2);
			$data["leyenda"]="La empresa ha mejorado su reputación.";
			$data["class"]="colorverde";
		}else{
			$data["puesto"]="Ma";
			$data["diferencia"]=round($promedio2);
			$data["leyenda"]="La empresa ha mantenido su calificación.";
			$data["class"]="colorazul";
		}
		return $data;

	}
	//////////////////////////////////////////////////====esto es para los proveedores========///////////////
	
	//funcion para obtener los proveedores por anio
	public function ObtenerProveeAnioymes($idempresa,$anio,$mes){
		$sql="IDEmpresa='$idempresa' and Forma='Realizada' and Tipo='Cliente' and Status_Val='Activa' order by Fecha asc limit 1";
		$this->db->select('Fecha');
		$this->db->from('calificaciones');
		$this->db->where($sql);	
		$prove=$this->db->get();
		if($prove->num_rows()==0){
			$fecha='$anio-$mes-01';
		}else{
			$fecha=$prove->result()[0]->Fecha;
		}
		$sql="IDEmpresa='$idempresa' and Forma='Realizada' and Tipo='Cliente' and Status_valora='ACTIVA' and date(Fecha) between '$fecha' and '$anio-$mes-31' group by IDEmpresa_Valorada";
		$this->db->select('IDEmpresa_Valorada');
		$this->db->from('calificaciones');
		$this->db->join('valoraciones','valoraciones.IDValora=calificaciones.IDValora');
		$this->db->where($sql);	
		$prove=$this->db->get();
		$prove1=[];
		if($prove->num_rows()!=0){
			
			foreach ($prove->result() as $provedor) {
				array_push($prove1,array("num"=>$provedor->IDEmpresa_Valorada));
			}
		}
		$sql="IDEmpresa='$idempresa' and Forma='Recibida' and Tipo='Proveedor' and Status_Val='Activa'  order by Fecha asc limit 1";
		$this->db->select('Fecha');
		$this->db->from('calificaciones');
		$this->db->where($sql);	
		$prove=$this->db->get();
		if($prove->num_rows()==0){
			$fecha='$anio-$mes-01';
		}else{
			$fecha=$prove->result()[0]->Fecha;
		}
		$sql="IDEmpresa='$idempresa' and Forma='Recibida' and Tipo='Proveedor' and Status_valora='ACTIVA' and date(Fecha) between '$fecha' and '$anio-$mes-31' group by IDEmpresa_Valoradora";
		$this->db->select('IDEmpresa_Valoradora');
		$this->db->from('calificaciones');
		$this->db->join('valoraciones','valoraciones.IDValora=calificaciones.IDValora');
		$this->db->where($sql);	
		$prove=$this->db->get();
		$prove2=[];
		if($prove->num_rows()!=0){
			
			foreach ($prove->result() as $provedor) {
				array_push($prove2,array("num"=>$provedor->IDEmpresa_Valoradora));
			}
		}
		
		$clientes=array_merge($prove1,$prove2);
		$clientes = array_map('unserialize', array_unique(array_map('serialize', $clientes)));

		return $clientes;
	}
	public function ObtenerReputacionProveedores($IDEmpresa,$anio){
		$data=[];
		$met=[];
		$numclientes=[];
		$clientesacum=[];
		$cli=[];
		$ma=[];
		$me=[];
		$em=[];
		if($anio!=date('Y')){
			$mesultimo=12;
		}else{
			$mesultimo=date('m');
		}
		for($i=1;$i<=$mesultimo;$i++){
			array_push($met,da_mes($i));
			if($i<10){
				$mes="0".$i;
			}
			$mantenido=0;
			$mejorado=0;
			$empeorado=0;
			
			$clientes=$this->ObtenerProveeAnioymes($IDEmpresa,$anio,$mes);
			$clientes=array_merge($clientesacum,$clientes);
			$clientes = array_map('unserialize', array_unique(array_map('serialize', $clientes)));
			$clientesacum=$clientes;
			array_push($numclientes,count($clientes));
			foreach($clientes as $key){
				if($key["num"]!=$IDEmpresa ){
					$rep=$this->ReputaciondeUnaEmpresa($key["num"],$mes,$anio);
					if($rep["puesto"]=="Em"){
						$empeorado++;
					}else if($rep["puesto"]=="Me"){
						$mejorado++;
					}else if($rep["puesto"]=="Ma"){
						$mantenido++;
					}
				}
			}
			array_push($ma,$mantenido);
			array_push($me,$mejorado);
			array_push($em, $empeorado);
			$total=intval($mantenido)+intval($mejorado)+intval($empeorado);
		}
		$clientesacum=[];
		foreach($clientes as $key){
			if($key["num"]!=$IDEmpresa ){
				$rep=$this->ReputaciondeUnaEmpresa($key["num"],$mes,$anio);
				$dat=$this->DatosEmpresa($key["num"]);
				if($dat[0]!=null){
					array_push($clientesacum,array("num"=>$key["num"],"Logo"=>$dat[0]->Logo,"Razon_Social"=>$dat[0]->Razon_Social,"Nombre_Comer"=>$dat[0]->Nombre_Comer,"RFC"=>$dat[0]->RFC,"leyenda"=>$rep["leyenda"],"class"=>$rep["class"]));
				}
			
				
			}
		}
		$data["TotalClientes"]=array("Empeorado"=>$empeorado,"Mantenido"=>$mantenido,"Mejorado"=>$mejorado);
		$data["Porcentajes"]=array("PorEmpeorado"=>porcentaje($total,$empeorado),"PorMantenido"=>porcentaje($total,$mantenido),"PorMejorado"=>porcentaje($total,$mejorado));
		$data["reputacion"]=array(array("name"=>"Mejorado","data"=>$me,"color"=>"#00C331"),array("name"=>"Mantenido","data"=>$ma,"color"=>"#008CC3"),array("name"=>"Empeorado","data"=>$em,"color"=>"#C30000"));
		$data["Catego"]=$met;
		$data["clientes"]=$clientesacum;
		return $data;
	}
}