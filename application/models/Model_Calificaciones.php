<?php 
defined('BASEPATH') OR exit('No direct script access allowed');
class Model_Calificaciones extends CI_Model{
	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->load->helper('selec_Titulo');
	} 
	public function devcorreoempresa($IDEmpresa){
		$sql="IDEmpresa='$IDEmpresa' and Tipo_Usuario='Master'";
		$this->db->select('Correo');
		$this->db->where($sql);
		$this->db->from('usuarios');
		$resp=$this->db->get();
		if($resp->num_rows()==0){
		$sql="IDEmpresa='$IDEmpresa' limit 1";
		$this->db->select('Correo');
		$this->db->where($sql);
		$this->db->from('usuarios');
		$resp=$this->db->get();
		return $resp->result()[0]->Correo;
		}else{
			return $resp->result()[0]->Correo;
		}
	}
	public function CdatosUsuario($usuario){
		$sql="IDUsuario='$usuario'";
		$this->db->select('Correo');
		$this->db->where($sql);
		$this->db->from('usuarios');
		$resp=$this->db->get();
		return $resp->result()[0]->Correo;
	}
	public function DatosUsuario($IDUsuario){
		$sql=$this->db->select("*")->where("IDUsuario='$IDUsuario'")->get("usuarios");
		return $sql->row(); 
	}
	//funcion para obtener las calificaciones en un año
	public function CalificacionesA($IDEmpresa,$Forma,$Tipo,$anio,$status){
		if($status=="Pendiente"){
			$sql="IDEmpresa='$IDEmpresa' and Forma='$Forma' and Tipo='$Tipo' and date(Fecha) between '".$anio."-01-01' and '".$anio."-12-31' and Status_Val like '%$status%'";
		}else{
			$sql="IDEmpresa='$IDEmpresa' and Forma='$Forma' and Tipo='$Tipo' and date(Fecha) between '".$anio."-01-01' and '".$anio."-12-31' and Status_Val='$status'";
		}
		
		$this->db->select('*');
		$this->db->from('calificaciones');
		$this->db->where($sql);
		$respu=$this->db->get();
		if($respu->num_rows()==0){
		$data["NumeroCalif"]=0;
		$data["promedio"]=0;
		}else{
			$data["NumeroCalif"]=$respu->num_rows();
			$arriba=0;
			foreach ($respu->result() as $key) {
				$arriba=$arriba+$key->Calificacion;
			}
			$promedio=$arriba/$data["NumeroCalif"];
			$data["promedio"]=round($promedio,2);
		}
		return $data;
	}
	public function CalificacionesdeunAnio($IDEmpresa,$Forma,$Tipo,$anio){
		$data=[];
		$data2=[];
		$data3=[];
		$catego=[];	
		if($anio!=date("Y")){
			$mest=12;
		}else{
			$mest=date("m");
		}
		for($i=1;$i<=$mest;$i++){
			array_push($catego,da_mes($i));
			if($i<10){
				$mes="0".$i;
			}else{
				$mes=$i;
			}
		$sql="IDEmpresa='$IDEmpresa' and Forma='$Forma' and Tipo='$Tipo' and date(Fecha) between '".$anio."-$mes-01' and '".$anio."-$mes-31' and Status_Val='Activa'";
		
		$this->db->select('*');
		$this->db->from('calificaciones');
		$this->db->where($sql);
		$respu=$this->db->get();
		if($respu->num_rows()==0){
		$num=0;
		$promedio=0;
		}else{
			$num=$respu->num_rows();
			$arriba=0;
			foreach ($respu->result() as $key) {
				$arriba=$arriba+$key->Calificacion;
			}
			$promedio=$arriba/$num;
			$promedio=round($promedio,2);
		}
		array_push($data,$num);
		array_push($data2,$promedio);
		
		$sql="IDEmpresa='$IDEmpresa' and Forma='$Forma' and Tipo='$Tipo' and date(Fecha) between '".$anio."-$mes-01' and '".$anio."-$mes-31' and Status_Val like '%Pendiente%'";
		$this->db->select('*');
		$this->db->from('calificaciones');
		$this->db->where($sql);
		$respu=$this->db->get();
		if($respu->num_rows()==0){
		$nump=0;
		$promediop=0;
		}else{
			$nump=$respu->num_rows();
			$arriba=0;
			foreach ($respu->result() as $key) {
				$arriba=$arriba+$key->Calificacion;
			}
			$promediop=$arriba/$nump;
			$promediop=round($promediop,2);
		}
		array_push($data3,$nump);
		}
		$datos["meses"]=[array("name"=>"Total de calificaciones","data"=>$data,"color"=>"#00F265"),array("name"=>"Pendientes de Resolucion","data"=>$data3,"color"=>"#f45b5b")];
			$datos["medias"]=[array("name"=>"Promedio","data"=>$data2,"color"=>"#00F265")];
		$datos["catego"]=$catego;
		return $datos;
		
	}
	public function CalificacionesAcumuladasBruto($IDEmpresa,$Forma,$Tipo,$status,$fecha1,$fecha2,$cliente){
		if($fecha1!=""){
			$rango="and date(FechaRealizada) between '$fecha1' and '$fecha2'";
		}else{
			$rango="";
		}
		if($status!=""){
			$status="and Status='$status'";
		}

		if($cliente!="" && $Forma==="Recibida"){
			$cliente="and tbcalificaciones.IDEmpresaEmisor='$cliente'";
		}else if($cliente!="" and $Forma=="Realizada"){
			$cliente="and tbcalificaciones.IDEmpresaReceptor='$cliente'";
		}
		$listascalificaciones=[];

	 	//primero obtengo las calificaciones en bruto
	 	if($Forma==="Recibida"){
	 		$sql=$this->db->select("*")->where("tbcalificaciones.IDEmpresaReceptor='$IDEmpresa' $rango $status $cliente and Emitidopara='$Tipo'")->get("tbcalificaciones");
	 		
	 	}else{
	 		$sql=$this->db->select("*")->where("tbcalificaciones.IDEmpresaEmisor='$IDEmpresa' $rango $status $cliente and Emitidopara='$Tipo'")->get("tbcalificaciones");
	 	
	 			foreach ($sql->result() as $valoracion)
	 			{
	 				
	 				//Datos de la empresa
	 				 $datosempresa=$this->DatosEmpresa($valoracion->IDEmpresaReceptor);
	 				//datos del usuario receptor
	 				 $datosuario=$this->DatosUsuario($valoracion->IDUsuarioReceptor);
	 				 //datos del usuario emisor
	 				 $datosuario2=$this->DatosUsuario($valoracion->IDUsuarioEmisor);
	 				 
	 				 ($valoracion->FechaModificacion==="0000-00-00") ? $fechamod="-" :$fechamod=$valoracion->FechaModificacion;
	 				($valoracion->FechaPuesta==="0000-00-00") ? $fechapuesta="-" : $fechapuesta=$valoracion->FechaPuesta;
	 				array_push($listascalificaciones,array("IDValora"=>$valoracion->IDCalificacion,"num_empresa_receptora"=>$datosempresa->IDEmpresa,"Logo"=>$datosempresa->Logo,"Nombre_comer"=>$datosempresa->Nombre_Comer,'Razon_Social' =>$datosempresa->Razon_Social,"UsuarioReceptor"=>$datosuario->Nombre." ".$datosuario->Apellidos,"CorreoReceptor"=>$datosuario->Correo,"UsuarioEmisor"=>$datosuario2->Nombre." ".$datosuario2->Apellidos,"CorreoEmisor"=>$datosuario2->Correo,"Status"=>$valoracion->Status,"Fecha"=>$valoracion->FechaRealizada,"FechaModificacion"=>$fechamod,"FechaPuesta"=>$fechapuesta));
	 				
	 			}
	 			
	 			
	 		
	 		
	 		
	 	}
	 	return $listascalificaciones;
		
	}
	public function cuestionario($Tipo,$_ID_Empresa_emisora,$_ID_Empresa_receptor){
		$sql="IDNivel1='1'";
		$this->db->select($Tipo);
		$this->db->from('GiroNivel1');
		$this->db->where($sql);
		$respu=$this->db->get();
		if($Tipo=="cliente"){
			$cadena=$respu->result()[0]->cliente;
		}else{
			$cadena=$respu->result()[0]->proveedor;
		}
		$cuestionario=[];
		$total=0;
		$nomenltura=explode(',',$cadena);
		$_listas_dependencias=[];
		$flag=TRUE;
		foreach ($nomenltura as $key) {
			$respu=$this->datpreguntaN($key);
			if($respu->IDPregunta=="153" || $respu->IDPregunta=="145"){
				//verificamos que no se haya realizado esta pregunta que son las de recomendacion
				$sql=$this->db->select("*")->from("tbcalificaciones")->join("tbdetallescalificaciones","tbdetallescalificaciones.IDCalificacion=tbcalificaciones.IDCalificacion")->where("IDEmpresaEmisor='$_ID_Empresa_emisora' and IDEmpresaReceptor='$_ID_Empresa_receptor' and IDPregunta='$respu->IDPregunta'")->get();
				if($sql->num_rows()!=0){
					$flag=FALSE;
				}
			}
			
			if($respu->IDPregunta==="137" || $respu->IDPregunta==="116"){

				//verificamos que no se haya realizado esta pregunta que son las de recomendacion
				$sql=$this->db->select("*")->from("tbcalificaciones")->join("tbdetallescalificaciones","tbdetallescalificaciones.IDCalificacion=tbcalificaciones.IDCalificacion")->where("IDEmpresaEmisor='$_ID_Empresa_emisora' and IDEmpresaReceptor='$_ID_Empresa_receptor' and IDPregunta='$respu->IDPregunta'")->get();
				
				if($sql->num_rows()!=0)
				{
					(restar_fecha(date("Y-m-d"),$sql->row()->FechaRealizada)<=31) ? $flag=FALSE : $flag=TRUE;	
				}
			}
			if($flag===TRUE){
				array_push($cuestionario,array("Pregunta"=>$respu->Pregunta,"Forma"=>$respu->Forma,"Nump"=>$respu->IDPregunta,"respuesta"=>$respu->Condicion,"dependecia"=>$respu->Dependencia,"PreguntaDependencia"=>$respu->PreguntaDependencia,"RespuestaDepen"=>$respu->RespuestaDepen));
				if($respu->Dependencia==="Si"){
					array_push($_listas_dependencias,array("ID_Pregunta"=>$respu->PreguntaDependencia,"Respuesta"=>$respu->RespuestaDepen,"S_ID_Pregunta"=>$respu->IDPregunta));
				}	
			}
			$flag=TRUE;
		
			//$total=$total+$respu->result()[0]->PorTotal;
		}
		$data["Preguntas"]=$cuestionario;
		$data["listas_dependencias"]=$_listas_dependencias;
		return $data;
		
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
	//funcion para los datoa de pregunta segun la nomenclatura
	public function datpreguntaN($nomenclatura){
		$sql=$this->db->select('*')->where("Nomenclatura='$nomenclatura'")->get('preguntas_val');
		return $sql->result()[0];
	}
	//funcion para los datos de preguna segun el id 
	public function detpreguntas($numPregunta){
		$sql="IDPregunta='$numPregunta'";
		$this->db->from('preguntas_val');
		$this->db->where($sql);
		$respu=$this->db->get();
		return $respu->result()[0];

	}
	public function ultimavaloracion(){
		$this->db->select('IDCalificacion');
		$this->db->where("1 order by IDCalificacion desc Limit 1");
		$this->db->from('valoraciones');
		$respu=$this->db->get();
		return $respu->result()[0]->IDValora;
	}
	public function AddDetalleValoracion2($_ID_Valoracion,$_ID_Pregunta,$_Respuesta){
		$_Datos_pregunta=$this->detpreguntas($_ID_Pregunta);
		$_Respuesta_correcta=$_Datos_pregunta->Condicion;
		$_Puntos_Posibles=$_Datos_pregunta->PorTotal;
		$_Puntos_obtenidos=0;
		if($_Puntos_Posibles!="F1")
		{
			if($_Respuesta_correcta===$_Respuesta){
				$_Puntos_obtenidos=$_Puntos_Posibles;
			}
		}
		else
		{
			$_Puntos_Posibles=0;
			$_Puntos_obtenidos=$_Datos_pregunta->PorDesc/(pow(arg(3.005),$_Respuesta));
		}
		$_array=array(
			"IDCalificacion"=>$_ID_Valoracion,
			"IDPregunta"=>$_ID_Pregunta,
			"Respuesta"=>$_Respuesta,
			"PuntosObtenidos"=>$_Puntos_obtenidos,
			"PuntosPosibles"=>$_Puntos_Posibles
		);
		$_data["PuntosPosibles"]=$_Puntos_Posibles;
		$_data["PuntosObtenidos"]=$_Puntos_obtenidos;
		$_data["Pregunta"]=$_Datos_pregunta->Pregunta;
		$_data["Respuesta"]=$_Respuesta;
		$this->db->insert("tbdetallescalificaciones",$_array);
		return $_data;
	}
	public function AddValoracion($_ID_Usuario_Emisor,$_ID_Empresa_emisora,$_ID_Giro_emisor,$_ID_Usuario_receptor,$_ID_Empresa_receptor,$_ID_Giro_receptor,$_Status,$_Emitido_para){
		$_array=array(
			"IDUsuarioEmisor"=>$_ID_Usuario_Emisor,
			"IDEmpresaEmisor"=>$_ID_Empresa_emisora,
			"IDGrioEmisor"=>$_ID_Giro_emisor,
			"IDUsuarioReceptor"=>$_ID_Usuario_receptor,
			"IDEmpresaReceptor"=>$_ID_Empresa_receptor,
			"IDGiroReceptor"=>$_ID_Giro_receptor,
			"Status"=>$_Status,
			"Emitidopara"=>$_Emitido_para
		);

		$this->db->insert("tbcalificaciones",$_array);
		return $this->db->insert_id();
	}
	public function addDetallevaloracion($tipo,$respuestas,$IDEmpresa,$IDEmpresa_Valorada,$correo,$IDUsuario,$IDGiro){
		$resp=$this->cuestionario($tipo);
		$existe=[];
		 $valoracion=$this->ultimavaloracion()+1;
	$preguntas="";
		if(count($resp["Preguntas"])!=count($respuestas)){
			foreach ($resp["Preguntas"] as $key) {
				foreach ($respuestas as $keys) {
					$num=explode("|",$keys);
					if(in_array($num[0],$key)){
						$pasa=0;
					}else{
						$pasa=1;	
					}
				}
				if($pasa!=1){
					array_push($respuestas,$key["Nump"]."|"."NA");
				}
			}
		}
		
		$valt=0;
		$int=1;
		$pt=(float)$resp["Total"];
		foreach ($respuestas as $key) {
			$num=explode("|",$key);
			if($num[1]=="NA" || $num[1]=="NS" || $num[1]=="NT"){
				$dtp=$this->detpreguntas($num[0]);
				$pt=$pt-(float)$dtp->PorTotal;
			}
		}

		foreach ($respuestas as $keys) {
				$num=explode("|",$keys);
				$dtp=$this->detpreguntas($num[0]);
				$peso=($dtp->PorTotal/$pt)*100;
				if(is_numeric($num[1])){
					if($num[1]==0){
						$cal=10;
						$Val=$dtp->PorTotal;
					}else{
						$cal=$peso-(((int)$num[1]/30)*$peso);
						$Val = $peso-(((int)$num[1]/30)*$peso);
					}
				}else{
					if($num[1]==$dtp->Condicion){
						$cal=10;
						$Val=$peso;
					}else{
						$cal=0;
						$Val=0;
					}
				}
				$preguntas=$preguntas."|*|".$dtp->Pregunta."|".$cal;
				$valt = $valt + $Val;
				$this->insertDetalle($valoracion,$IDEmpresa_Valorada,$IDEmpresa,$IDUsuario,$num[0],$num[1],round($cal,2),$correo);
		}
		if($tipo=="cliente"){
			$tipo="Cliente";
		}else{
			$tipo="Proveedor";
		}

		$this->insertcalificaion($valoracion,$IDEmpresa_Valorada,$IDGiro,round($valt/10,2),$tipo,"Recibida");
		if($tipo=="Cliente"){
			$tipo="Proveedor";
		}else{
			$tipo="Cliente";
		}
		$tipo2=$tipo;
		$this->insertcalificaion($valoracion,$IDEmpresa,$IDGiro,round($valt/10,2),$tipo,"Realizada");
		//obtengo los datos de cada empresa
		$dt=$this->DatosEmpresa($IDEmpresa);
		$dt2=$this->DatosEmpresa($IDEmpresa_Valorada);
		ms_EnvioValoracion($dt->Razon_Social,$dt2->Razon_Social,round($valt/10,2),$tipo2,$correo,$tipo,$preguntas);
		return round($valt/10,2);
	}
	public function insertDetalle($valoracion,$IDEmpresa_Valorada,$IDEmpresa_Valoradora,$IDUsuario,$IDPregunta,$respuesta,$calificacion,$correo){
		$array=array(
			"IDValora"=>$valoracion,
			"IDEmpresa_Valorada"=>$IDEmpresa_Valorada,
			"IDEmpresa_Valoradora"=>$IDEmpresa_Valoradora,
			"IDUsuario"=>$IDUsuario,
			"IDPregunta"=>$IDPregunta,
			"valoracion"=>$respuesta,
			"Calificacion"=>$calificacion,
			"Status_Valora"=>"ACTIVA",
			"correo_calificador"=>$correo,
			"Fecha_Valora"=>date('Y-m-d')
		);
		$this->db->insert("valoraciones",$array);
	}
	public function insertcalificaion($IDValora,$IDEmpresa,$IDGiro,$calificacion,$Tipo,$Forma){
		$array=array("IDValora"=>$IDValora,"IDEmpresa"=>$IDEmpresa,"IDGiro"=>$IDGiro,"Calificacion"=>$calificacion,"Tipo"=>$Tipo,"Forma"=>$Forma,"Status_Val"=>"Activa","Localidad"=>"Local","Post"=>"Posteado","Fecha"=>date('Y-m-d'));
		$this->db->insert("calificaciones",$array);
	}
	public function cambioestado($num,$tipo){
		$sql="Status_val='Activa' and IDValora='$num'";
		$this->db->where($sql);
		$resp=$this->db->get("calificaciones");
		if($resp->num_rows()=="0"){
			$data["pass"]=0;
			$data["mensaje"]="Esta calificación ya esta en proceso";
		}else{
			$sql="IDValora='$num' limit 1";
			$this->db->where($sql);
			$datosvaloracion=$this->db->get("valoraciones");
			
			//obtengo los datos de las empresas qe estan en esa valoracion
			$datvalorada=$this->DatosEmpresa($datosvaloracion->result()[0]->IDEmpresa_Valorada);
			if($datosvaloracion->result()[0]->correo_calificador=="-"){
				$correovalora=$this->devcorreoempresa($datosvaloracion->result()[0]->IDEmpresa_Valorada);	
			}else{
				$correovalora=$datosvaloracion->result()[0]->correo_calificador;
			}
			
			$datosvaloradora=$this->DatosEmpresa($datosvaloracion->result()[0]->IDEmpresa_Valoradora);
			//ontengo el correo del que califico
			$correovaloradora=$this->CdatosUsuario($datosvaloracion->result()[0]->IDUsuario);
		
			
			//realizomos el cambio
			if($tipo=="cambio"){
			$array=array("Status_Val"=>'Pendiente');
			$array2=array("Status_valora"=>'Pendiente',"MotivoCambio"=>"Sin Relacion Comercial","Fecha_Resolu"=>date('Y-m-d'),"Res_Forzosa"=>90);
				//mando los correos electronicos
				ms_pendienteresolucionvalorada($correovaloradora,$datvalorada->Razon_Social,$datosvaloradora->Razon_Social,$datosvaloracion->result()[0]->Fecha_Valora);
				ms_pendienteresolucionvaloradora ($correovalora,$datvalorada->Razon_Social,$datosvaloradora->Razon_Social,$datosvaloracion->result()[0]->Fecha_Valora);
			
			}else{
				$array=array("Status_Val"=>'PendienteA');
				$array2=array("Status_valora"=>'PendienteA',"MotivoCambio"=>"Sin Relacion Comercial","Fecha_Resolu"=>date('Y-m-d'),"Res_Forzosa"=>90);
				//mando los correos
				ms_pendienteanulacionvaloradora($correovaloradora,$datvalorada->Razon_Social,$datosvaloradora->Razon_Social,$datosvaloracion->result()[0]->Fecha_Valora);
			    ms_pendienteanulacionvalorada($correovalora,$datvalorada->Razon_Social,$datosvaloradora->Razon_Social,$datosvaloracion->result()[0]->Fecha_Valora);
			}
		$this->db->where("IDValora='$num'");
		$this->db->update("calificaciones",$array);
		$this->db->where("IDValora='$num'");
		$this->db->update("valoraciones",$array2);
		
		$data["pass"]=1;
		$data["mensaje"]="ok";
		}
		return $data;
	}
	public function detallescalif($num){
		$sql=$this->db->select("Pregunta,Respuesta as calificacion")->from("tbdetallescalificaciones")->join('preguntas_val',"preguntas_val.IDPregunta=tbdetallescalificaciones.IDPregunta")->where("IDCalificacion='$num'")->get();
		return($sql->result());
	}
	public function detalletotalcuestionario($num){
		$sql="IDValora='$num' Limit 1";
		$this->db->select("*");
		$this->db->where($sql);
		$ret=$this->db->get("valoraciones");
		return($ret->result()[0]);
	}
	public function modificarval($num){
		$sql="IDValora='$num'";
		$this->db->select("IDValora,valoraciones.IDPregunta,Pregunta,valoraciones.Valoracion,preguntas_val.Forma");
		$this->db->join("preguntas_val","preguntas_val.IDPregunta=valoraciones.IDPregunta");
		$this->db->where($sql);
		$ret=$this->db->get("valoraciones");
		return($ret->result());
	}
	public function modificacal($IDValora,$respuestas,$IDEmpresa){
		//obtengo el tipo de cuestionario
		$valt=0;
		$sql="IDValora='$IDValora' and IDEmpresa='$IDEmpresa'";
		$preguntas="";
		$this->db->select("Tipo");
		$this->db->where($sql);
		$this->db->from("calificaciones");
		$tipo=$this->db->get();
		if($tipo->result()[0]->Tipo=="Cliente"){
			$Tipo="proveedor";
		}else{
			$Tipo="cliente";
		}
		$resp=$this->cuestionario($Tipo);
		$pt=(float)$resp["Total"];
		foreach ($respuestas as $key) {
			$num=explode("|",$key);
			if($num[1]=="NA" || $num[1]=="NS" || $num[1]=="NT" || !isset($num[1])){
				$dtp=$this->detpreguntas($num[0]);
				$pt=$pt-(float)$dtp->PorTotal;
			}
		}
		foreach ($respuestas as $keys) {
				$num=explode("|",$keys);
				$dtp=$this->detpreguntas($num[0]);
				$peso=($dtp->PorTotal/$pt)*100;
				if(is_numeric($num[1])){
					if($num[1]==0){
						$cal=10;
						$Val=$dtp->PorTotal;
					}else{
						$cal=$peso-(((int)$num[1]/30)*$peso);
						$Val = $peso-(((int)$num[1]/30)*$peso);
					}
				}else{
					if($num[1]==$dtp->Condicion){
						$cal=10;
						$Val=$peso;
					}else{
						$cal=0;
						$Val=0;
					}
				}
				$preguntas=$preguntas."|*|".$dtp->Pregunta."|".$cal;
				$valt = $valt + $Val;
			   $this->updatevaluedestalle($IDValora,$num[0],$num[1],$cal);
			
		}
		$this->updatecalificacion($IDValora,round($valt/10,2));
		$detallesval=$this->detalletotalcuestionario($IDValora);
		$datem1=$this->DatosEmpresa($IDEmpresa);
		$correovaloradora=$this->CdatosUsuario($detallesval->IDUsuario);
		$datem2=$this->DatosEmpresa($detallesval->IDEmpresa_Valorada);
		if($detallesval->correo_calificador=="-"){
				$correovalora=$this->devcorreoempresa($detallesval->IDEmpresa_Valorada);	
			}else{
				$correovalora=$detallesval->correo_calificador;
		}
		//quien realiza la calificacion
		ms_recalificacion1($datem1->Razon_Social,$datem2->Razon_Social,round($valt/10,2),$Tipo,$correovaloradora,$tipo->result()[0]->Tipo,$preguntas);
		//quien recibe la calificacion
	ms_recalificacion2($datem1->Razon_Social,$datem2->Razon_Social,round($valt/10,2),$Tipo,$correovaloradora,$tipo->result()[0]->Tipo,$preguntas);
		$data["valorada"]=$detallesval->IDEmpresa_Valorada;
		$data["Tipo"]=$tipo->result()[0]->Tipo;
		return $data;
	}
	public function updatevaluedestalle($IDvalora,$IDPregunta,$Valoracion,$Calificacion){
		$array=array("Valoracion"=>$Valoracion,"Calificacion"=>$Calificacion,"Status_valora"=>"ACTIVA","Fecha_Modif"=>date('Y-m-d'),"Res_Forzosa"=>0);
		$sql="IDValora='$IDvalora' and IDPregunta='$IDPregunta'";
		$this->db->where($sql);
		$this->db->update("valoraciones",$array);
	}
	public function updatecalificacion($IDValora,$calificacion){
		$data=array("Calificacion"=>$calificacion,"Status_Val"=>"Activa");
		$sql="IDValora='$IDValora'";
		$this->db->where($sql);
		$this->db->update("calificaciones",$data);
	}
	public function promporcuestion($empresa,$tipo){
		if($empresa==0){
			return false;
		}else {
			$sql="calificaciones.IDEmpresa='$empresa' and calificaciones.Forma='Recibida' and Tipo='$tipo' group by valoraciones.IDPregunta";
		$this->db->select("AVG(valoraciones.Calificacion) as calificacion,preguntas_val.Pregunta");
		$this->db->join("preguntas_val","preguntas_val.IDPregunta=valoraciones.IDPregunta");
		$this->db->join("calificaciones","valoraciones.IDValora=calificaciones.IDValora");
		$this->db->from('valoraciones');
		$this->db->where($sql);
		$resp=$this->db->get();
		if($resp->num_rows()==0){
			return false;
		}else{
			return $resp->result();
		}
		}
		

	}
}