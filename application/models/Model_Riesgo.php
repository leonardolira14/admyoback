<?
/**
 * 
 */
class Model_Riesgo extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('selec_Titulo');
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
	//funcion para obtener los proveedores
	public function ObtenerProveedores($idempresa){
		$proveedores1=[];
		//esta relacion es para obtener en la tabla tbrelacion las que esten como IDEmpresaPque es la principal
		$sql=$this->db->select('*')->where("IDEmpresaP='$idempresa' and Tipo='proveedor'")->get("tbrelacion");
		if($sql->num_rows()!=0){	
			foreach ($sql->result() as $provedor) {
				array_push($proveedores1,array("num"=>$provedor->IDEmpresaB));
			}
		}
		//ahora obtengo las que estan en la IDEmpresaB pero como cliente
		$sql=$this->db->select('*')->where("IDEmpresaB='$idempresa' and Tipo='cliente'")->get("tbrelacion");
		$proveedores2=[];
		if($sql->num_rows()!=0){
			foreach ($sql->result() as $provedor) {
				array_push($proveedores2,array("num"=>$provedor->IDEmpresaP));
			}
		}
		$proveedores=array_merge($proveedores1,$proveedores2);
		$proveedores = array_map('unserialize', array_unique(array_map('serialize', $proveedores)));
		return $proveedores;
	}
	//funcion para obtner que media y cuantas calificaciones hay en un rango de fechas
	public function mediaemrpesa($IDEmpresa,$anio1,$anio2,$mes1,$mes2,$dia1,$dia2,$forma,$tipo,$status){
		if($mes1<10){
			$mes1="0".(int)$mes1;
		}
		if($mes2<10){
			$mes2="0".(int)$mes2;
		}
		$promedio=0;
		$puntosposibles=0;
		$puntosobtenidos=0;
		$sql=$this->db->select("IDCalificacion")->where("IDEmpresaReceptor='$IDEmpresa' and Status='$status' and Emitidopara='$tipo' and DATE(FechaRealizada) between '$anio1-$mes1-$dia1'and '$anio2-$mes2-$dia2'")->get("tbcalificaciones");
		if($sql->num_rows()===0){
			return false;
		}else{
			foreach ($sql->result() as $valoracion) {
				$sqll=$this->db->select("sum(PuntosObtenidos) as puntosobtenidos, sum(PuntosPosibles)as puntosposibles")->where("IDCalificacion='$valoracion->IDCalificacion'")->get("tbdetallescalificaciones");
				$puntosposibles=$puntosposibles+(float)$sqll->result()[0]->puntosposibles;
				$puntosobtenidos=$puntosobtenidos+(float)$sqll->result()[0]->puntosobtenidos;
			}
			$promedio=round(($puntosobtenidos/$puntosposibles)*10,2);
			return $data["promedio"]=$promedio;
		}
		
	}
	//funcion obtener el riesgo general
	public function obtenerrisgos($IDEmpresa,$_tipo_persona,$_tipo_fecha,$resumen=FALSE){
		
		$mejorados=0;
		$empeorados=0;
		$mantenidos=0;
		$empeoradosg=0;
		//para calidad
		$mantenidoscalidad=0;
		$empeoradoscalidad=0;
		$mejoradoscalidad=0;
		$totalcalidad=0;
		//para cumplimento
		$mantenidoscumplimiento=0;
		$empeoradoscumplimiento=0;
		$mejoradoscumplimiento=0;
		$totalcumplimento=0;
		//para oferta
		$mantenidosoferta=0;
		$empeoradosoferta=0;
		$mejoradosoferta=0;
		$totaloferta=0;
		$fechas=docemeces();
		$fechas2=docemecespasados();
		$evolucion=[];
		$evolucionlabel=[];	
		
		
		//empiezo para la grafica de evolucion
		if($_tipo_persona==="cliente"){
			$clientes=$this->ObtenerClientes($IDEmpresa);
			$tb="tbriesgo_clientes";
			$tbimagen="tbimagen_cliente";
		}else{
			$clientes=$this->ObtenerProveedores($IDEmpresa);
			$tb="tbriesgo_proveedores";
			$tbimagen="tbimagen_proveedor";
		}
		
			if($_tipo_fecha==="A")
			{
				$_fecha_actual=$fechas[12]."-".date("d");
				$_fecha_pasada=$fechas2[12]."-".date("d");
				if($resumen===FALSE){
					foreach ($fechas as $fecha) {
						$datos=explode("-",$fecha);
						$num=$this->NumCantidad("Empeorados",$IDEmpresa,$fecha."-01",$fecha."-31",$tb);
						array_push($evolucionlabel,da_mes($datos[1])."-".$datos[0]);
						array_push($evolucion,(int)$num);
					}
				}
				$data["evolucion"]=array("Labels"=>$evolucionlabel,"data"=>[array("data"=>$evolucion,"label"=>"No de Empeorados")]);
			}
			else
			{
				$_fecha_actual=$fechas[12]."-".date("d");
				$_fecha_pasada=$fechas[11]."-".date("d");
				if($resumen===FALSE){
					$inicio=date("d");
					$para=31;
					$datos=$datos=explode("-",$fechas[11]);
					$mes=$datos[1];
					$anio=$datos[0];
					while($inicio<=$para){
						$fecha=$anio."-".$mes;
						if($inicio===31){
							$para=date("d");
							$inicio=1;
							$datos=$datos=explode("-",$fechas[12]);
							$mes=$datos[1];
							$anio=$datos[0];
							$fecha=$anio."-".$mes;
							$num=$this->NumCantidad("Empeorados",$IDEmpresa,$fecha."-".$inicio,$fecha."-".$inicio,$tb);
							array_push($evolucionlabel,$inicio."-".da_mes($mes));
							array_push($evolucion,(int)$num);
						}else{
							$num=$this->NumCantidad("Empeorados",$IDEmpresa,$fecha."-".$inicio,$fecha."-".$inicio,$tb);
							array_push($evolucionlabel,$inicio."-".da_mes($mes));
							array_push($evolucion,(int)$num);
							$inicio++;
						}
					}
					$data["evolucion"]=array("Labels"=>$evolucionlabel,"data"=>[array("data"=>$evolucion,"label"=>"No de Empeorados")]);
				}
			}
		
		
		//ahora empiezo compara mis clientes o proveedores para ver como se han comportado con respecto mes/anio
		foreach ($clientes as $cliente) {
			$mediaactual=$this->db->select("Ultima_Media")->where("IDEmpresa='".$cliente["num"]."' and Fecha='$_fecha_actual'")->get($tbimagen);
			$mediapasada=$this->db->select("Ultima_Media")->where("IDEmpresa='".$cliente["num"]."' and Fecha='$_fecha_pasada'")->get($tbimagen);
			if($mediaactual->num_rows()===0 && $mediapasada->num_rows()===0)
			{
				$mantenidos++;
			}else if($mediaactual->num_rows()===0 && $mediapasada->num_rows()!==0)
			{
				$empeorados++;
			}else if($mediaactual->num_rows()!==0 && $mediapasada->num_rows()===0)
			{
				$mejorados++;	
			}else if($mediaactual->result()[0]->Ultima_Media===$mediapasada->result()[0]->Ultima_Media){
				$mantenidos++;
			}else if($mediaactual->result()[0]->Ultima_Media<$mediapasada->result()[0]->Ultima_MediaLL){
				$empeorados++;
			}else if($mediaactual->result()[0]->Ultima_Media>$mediapasada->result()[0]->Ultima_Media){
				$mejorados++;		
			}
			//calidad
			$calidad_actual=$this->MeediaCategoria("P_Obt_Calidad","P_Pos_Calidad",$cliente["num"],$_fecha_actual,$_fecha_actual,$tbimagen);
			$calidad_pasada=$this->MeediaCategoria("P_Obt_Calidad","P_Pos_Calidad",$cliente["num"],$_fecha_pasada,$_fecha_pasada,$tbimagen);
			if(_comparacion($calidad_actual,$calidad_pasada)===1){
				$mantenidoscalidad++;
			}else if(_comparacion($calidad_actual,$calidad_pasada)===2){
				$mejoradoscalidad++;
			}else if(_comparacion($calidad_actual,$calidad_pasada)===3){
				$empeoradoscalidad++;
			}
			//calidad
			$cumplimiento_actual=$this->MeediaCategoria("P_Obt_Cumplimiento","P_Pos_Cumplimiento",$cliente["num"],$_fecha_actual,$_fecha_actual,$tbimagen);
			$cumplimiento_pasada=$this->MeediaCategoria("P_Obt_Cumplimiento","P_Pos_Cumplimiento",$cliente["num"],$_fecha_pasada,$_fecha_pasada,$tbimagen);
			if(_comparacion($calidad_actual,$calidad_pasada)===1){
				$mantenidoscumplimiento++;
			}else if(_comparacion($calidad_actual,$calidad_pasada)===2){
				$mejoradoscumplimiento++;
			}else if(_comparacion($calidad_actual,$calidad_pasada)===3){
				$empeoradoscumplimiento++;
			}
			if($_tipo_persona!=="cliente"){
				//calidad
			$oferta_actual=$this->MeediaCategoria("P_Obt_Oferta","P_Pos_Oferta",$cliente["num"],$_fecha_actual,$_fecha_actual,$tbimagen);
			$oferta_pasada=$this->MeediaCategoria("P_Obt_Oferta","P_Pos_Oferta",$cliente["num"],$_fecha_pasada,$_fecha_pasada,$tbimagen);
			if(_comparacion($oferta_actual,$oferta_pasada)===1){
				$mantenidosoferta++;
			}else if(_comparacion($oferta_actual,$oferta_pasada)===2){
				$mejoradosoferta++;
			}else if(_comparacion($oferta_actual,$oferta_pasada)===3){
				$empeoradosoferta++;
			}
			}
			
		}
		$total=$mejorados+$empeorados+$mantenidos;
		$data["mejorados"]=array("numero"=>$mejorados,"porcentaje"=>porcentaje($total,$mejorados));
		$data["empeorados"]=array("numero"=>$empeorados,"porcentaje"=>porcentaje($total,$empeorados));
		$data["mantenidos"]=array("numero"=>$mantenidos,"porcentaje"=>porcentaje($total,$mantenidos));
		 $data["seriecir"]=array("label"=>["Mejorados","Empeorados","Mantenidos"],"data"=>[$mejorados,$empeorados,$mantenidos]);
		$totalcalidad=$mejoradoscalidad+$empeoradoscalidad+$mantenidoscalidad;
		$data["mejoradoscalidad"]=array("num"=>$mejoradoscalidad,"porcentaje"=>porcentaje($totalcalidad,$mejorados));
		$data["empeoradoscalidad"]=array("num"=>$empeoradoscalidad,"porcentaje"=>porcentaje($totalcalidad,$empeorados));
		$data["mantenidoscalidad"]=array("num"=>$mantenidoscalidad,"porcentaje"=>porcentaje($totalcalidad,$mantenidos));

		$totalcumplimento=$mejoradoscumplimiento+$empeoradoscumplimiento+$mantenidoscumplimiento;
		$data["mejoradoscumplimiento"]=array("num"=>$mejoradoscumplimiento,"porcentaje"=>porcentaje($totalcumplimento,$mejorados));
		$data["empeoradoscumplimiento"]=array("num"=>$empeoradoscumplimiento,"porcentaje"=>porcentaje($totalcumplimento,$empeorados));
		$data["mantenidoscumplimiento"]=array("num"=>$mantenidoscumplimiento,"porcentaje"=>porcentaje($totalcumplimento,$mantenidos));
		if($_tipo_persona!=="cliente"){
			$totaloferta=$mejoradosoferta+$empeoradosoferta+$mantenidosoferta;
			$data["mejoradosoferta"]=array("num"=>$mejoradosoferta,"porcentaje"=>porcentaje($totaloferta,$mejoradosoferta));
			$data["empeoradosoferta"]=array("num"=>$empeoradosoferta,"porcentaje"=>porcentaje($totaloferta,$empeoradosoferta));
			$data["mantenidosoferta"]=array("num"=>$mantenidosoferta,"porcentaje"=>porcentaje($totaloferta,$mantenidosoferta));
			
		}
		return $data;
	}
	
	//funcion para obtener el promedio de una categoria en una fecha
	public function MeediaCategoria($categoria,$categoria2,$IDEmpresa,$_fecha_inicio,$_fecha_fin,$_tb)
	{
		$sql=$this->db->select("round(sum($categoria)/sum($categoria2)*10,2) as media")->where("IDEmpresa='$IDEmpresa' and date(Fecha) between '".$_fecha_inicio."' and '".$_fecha_fin."'")->get($_tb);
		if($sql->row()->media==="" || $sql->row()->media===NULL || $sql->row()->media===0){
			return 0;
		}else{
			return $sql->row()->media;
		}
	}
	//funcion para obtener la cantidad de una categoria
	public function NumCantidad($categoria,$IDEmpresa,$_fecha_inicio,$_fecha_fin,$_tb){
		$sql=$this->db->select("sum($categoria) as $categoria")->where("IDEmpresa='$IDEmpresa' and date(Fecha) between '".$_fecha_inicio."' and '".$_fecha_fin."'")->get($_tb);
		if($sql->result()[0]->$categoria===NULL){
			return 0;
		}else{
			return $sql->result()[0]->$categoria;
		}
		
	}
	

	//funcion para definir el incremento


	//fucnion para obtener el promedio de un rango de fechas por categoria
	public function promediorang($IDEmpresa,$date1,$date2,$categoria,$tipo,$status){
		$listasid=[];
		//obtengo los ide las calificaciones segun los criterios
		$sql=$this->db->select('IDCalificacion')->where("IDEmpresaReceptor='$IDEmpresa' and Status='$status' and Emitidopara='$tipo' and DATE(FechaRealizada) between '$date1'  and '$date2'")->get('tbcalificaciones');
		$listnomencla=$this->db->select($categoria)->where("Tipo='$tipo'")->get("tbconfigcuestionarios");
		$numenclaturas=explode(",",$listnomencla->result()[0]->$categoria);
		foreach ($numenclaturas as $nomenclatura) {
			if($nomenclatura!=""){
				$datos=$this->datos_pregunta($nomenclatura);
				array_push($listasid,$datos->IDPregunta);
			}
		}
		if($sql->num_rows===0){
			return false;
		}else{
			$puntosposibles=0;
			$puntosobtenidos=0;
				//ahora obtengo las nomenclaturas del listado dependiendo que es

			foreach ($sql->result() as $calificacion)
			{
				foreach ($listasid as $idpregunta) 
				{
					$sqll=$this->db->select('PuntosObtenidos,PuntosPosibles')->where("IDPregunta='$idpregunta' and IDCalificacion='".$calificacion->IDCalificacion."'")->get("tbdetallescalificaciones");
					if($sqll->num_rows()==0){
						$puntosposibles=$puntosposibles+0;
						$puntosobtenidos=$puntosobtenidos+0;
					}else{
						$puntosposibles=$puntosposibles+$sqll->result()[0]->PuntosPosibles;
						$puntosobtenidos=$puntosobtenidos+$sqll->result()[0]->PuntosObtenidos;
					}

				}
			}
			if($puntosobtenidos===0 || $puntosposibles===0){
				$promedio=0;
			}else{
				$promedio=round(($puntosobtenidos/$puntosposibles)*10,2);
			}
			
		}
		return $promedio;
		
	}
	//funcion para loa datoa de pregunta por ID
	public function datos_preguntaID($IDPregunta)
	{
		$sql=$this->db->select("*")->where("IDPregunta='$IDPregunta'")->get("preguntas_val");
		return $sql->result()[0];
	}
	//funcion para los dtos de pregunta por nomenclarura
	public function datos_pregunta($nomenclatura)
	{
		
		$sql=$this->db->select("*")->where("Nomenclatura='$nomenclatura'")->get("preguntas_val");
		if($sql->num_rows()===0){
			return false;
		}else{
			return $sql->result()[0];
		}
			
	}
	//funcion para los detalles para el riesgo
	public function detalles($tipo,$tipo2,$IDEmpresa){
		
		$_tipo_persona=$tipo;
		$_tipo_fecha=$tipo2;
		$mejorados=0;
		$empeorados=0;
		$mantenidos=0;
		$empeoradosg=0;
		//para calidad
		$mantenidoscalidad=0;
		$empeoradoscalidad=0;
		$mejoradoscalidad=0;
		$totalcalidad=0;
		//para cumplimento
		$mantenidoscumplimiento=0;
		$empeoradoscumplimiento=0;
		$mejoradoscumplimiento=0;
		$totalcumplimento=0;
		//para oferta
		$mantenidosoferta=0;
		$empeoradosoferta=0;
		$mejoradosoferta=0;
		$totaloferta=0;
		$fechas=docemeces();
		$fechas2=docemecespasados();
		
		
		//empiezo para la grafica de evolucion
		if($_tipo_persona==="cliente"){
			$clientes=$this->ObtenerClientes($IDEmpresa);
			$listacp=$clientes;
			$tb="tbriesgo_clientes";
			$tbimagen="tbimagen_cliente";
			$listcalidad=$this->listpreguntas("Calidad",$tipo);
			$listcumplimento=$this->listpreguntas("Cumplimiento",$tipo);
		}else{
			$clientes=$this->ObtenerProveedores($IDEmpresa);
			$listacp=$clientes;
			$tb="tbriesgo_proveedores";
			$tbimagen="tbimagen_proveedor";
			$listcalidad=$this->listpreguntas("Calidad",$tipo);
			$listcumplimento=$this->listpreguntas("Cumplimiento",$tipo);
			$listoferta=$this->listpreguntas("Oferta",$tipo);
		}
		
		//tengo que saber que es si clientes o de proveedores
		$data["tipo"]=$tipo;
		$data["tipo2"]=$tipo2;
		

		if($tipo2==="A"){
			$fech1="'".$fechas[0]."-01' and '".$fechas[12]."-31'";
			$fech2="'".$fechas2[0]."-01' and '".$fechas2[12]."-31'";
			$fech_1=$fechas[0]."-01";
			$fech_2=$fechas[12]."-31";
			$_fecha_actual=$fechas[12]."-".date("d");
			$_fecha_pasada=$fechas2[12]."-".date("d");
		}else{
			$fech1="'".$fechas[12]."-01' and '".$fechas[12]."-31'";
			$fech2="'".$fechas[11]."-01' and '".$fechas2[11]."-31'";
			$fech_1=$fechas[11]."-".date("d");
			$fech_2=$fechas[12]."-".date("d");
			$_fecha_actual=$fechas[12]."-".date("d");
			$_fecha_pasada=$fechas[11]."-".date("d");
		}
		$listadatosp=[];
		
		foreach ($listcalidad as $preguntacalidad) { 
			//primero vamos con calidad
			$totalprimero=0;
			$totalsegundo=0;
			$numeroclientesevaluados=0;
			$datospregunta=$this->datos_preguntaID($preguntacalidad);
			
			if($datospregunta->Forma!="AB" || $datospregunta->Forma!="OP"){
			foreach ($listacp as $cp){
				$total=$this->cuantaspreguntascorrectas($cp["num"],$fech1,$tipo,$preguntacalidad,$datospregunta->Condicion,$datospregunta->Forma);
				$totalprimero=$totalprimero+$total;

				if($total>0){
					$numeroclientesevaluados++;
				}

				$totalsegundo=$totalsegundo+$this->cuantaspreguntascorrectas($cp["num"],$fech2,$tipo,$preguntacalidad,$datospregunta->Condicion,$datospregunta->Forma);
				
			}
			array_push($listadatosp,array("Pregunta"=>$datospregunta->Pregunta,"Totalcalificaciones"=>$totalprimero,"respuesta"=>$datospregunta->Condicion,"TotalClientes"=>$numeroclientesevaluados,"serie"=>[[" ","Actual","Pasado"],[" ",$totalprimero,$totalsegundo]]));
			}		
		}

		$data["calidad"]=$listadatosp;
		$listadatosp=[];
		foreach ($listcumplimento as $preguntacalidad) { 
			// cumplimiento
			$totalprimero=0;
			$totalsegundo=0;
			$numeroclientesevaluados=0;
			$datospregunta=$this->datos_preguntaID($preguntacalidad);
			if($datospregunta->Forma!="AB" || $datospregunta->Forma!="OP"){
				foreach ($listacp as $cp){
				$total=$this->cuantaspreguntascorrectas($cp["num"],$fech1,$tipo,$preguntacalidad,$datospregunta->Condicion,$datospregunta->Forma);
				$totalprimero=$totalprimero+$total;
				if($total>0){
					$numeroclientesevaluados++;
				}
				$totalsegundo=$totalsegundo+$this->cuantaspreguntascorrectas($cp["num"],$fech2,$tipo,$preguntacalidad,$datospregunta->Condicion,$datospregunta->Forma);
				
			}
			array_push($listadatosp,array("Pregunta"=>$datospregunta->Pregunta,"Totalcalificaciones"=>$totalprimero,"respuesta"=>$datospregunta->Condicion,"TotalClientes"=>$numeroclientesevaluados,"serie"=>[[" ","Actual","Pasado"],[" ",$totalprimero,$totalsegundo]]));
			}
					
		}
		$data["cumplimiento"]=$listadatosp;
		//vemos si esta la de oferta si no esta nos pasamos derecho
		if(isset($listoferta)){
			$listadatosp=[];
			
			foreach ($listoferta as $preguntacalidad) { 
			// cumplimiento
				$totalprimero=0;
				$totalsegundo=0;
				$numeroclientesevaluados=0;

				$datospregunta=$this->datos_preguntaID($preguntacalidad);
				if($datospregunta->Forma!="AB" || $datospregunta->Forma!="OP"){
				foreach ($listacp as $cp){
					$total=$this->cuantaspreguntascorrectas($cp["num"],$fech1,$tipo,$preguntacalidad,$datospregunta->Condicion,$datospregunta->Forma);
					$totalprimero=$totalprimero+$total;
					if($total>0){
						$numeroclientesevaluados++;
					}
					$totalsegundo=$totalsegundo+$this->cuantaspreguntascorrectas($cp["num"],$fech2,$tipo,$preguntacalidad,$datospregunta->Condicion,$datospregunta->Forma);

				}
				array_push($listadatosp,array("Pregunta"=>$datospregunta->Pregunta,"Totalcalificaciones"=>$totalprimero,"respuesta"=>$datospregunta->Condicion,"TotalClientes"=>$numeroclientesevaluados,"serie"=>[[" ","Actual","Pasado"],[" ",$totalprimero,$totalsegundo]]));
				}		
			}
			$data["oferta"]=$listadatosp;
		}
		foreach ($listacp as $cliente) {
			//calidad
			$calidad_actual=$this->MeediaCategoria("P_Obt_Calidad","P_Pos_Calidad",$cliente["num"],$_fecha_actual,$_fecha_actual,$tbimagen);
			$calidad_pasada=$this->MeediaCategoria("P_Obt_Calidad","P_Pos_Calidad",$cliente["num"],$_fecha_pasada,$_fecha_pasada,$tbimagen);
			if(_comparacion($calidad_actual,$calidad_pasada)===1){
				$mantenidoscalidad++;
			}else if(_comparacion($calidad_actual,$calidad_pasada)===2){
				$mejoradoscalidad++;
			}else if(_comparacion($calidad_actual,$calidad_pasada)===3){
				$empeoradoscalidad++;
			}
			//calidad
			$cumplimiento_actual=$this->MeediaCategoria("P_Obt_Cumplimiento","P_Pos_Cumplimiento",$cliente["num"],$_fecha_actual,$_fecha_actual,$tbimagen);
			$cumplimiento_pasada=$this->MeediaCategoria("P_Obt_Cumplimiento","P_Pos_Cumplimiento",$cliente["num"],$_fecha_pasada,$_fecha_pasada,$tbimagen);
			if(_comparacion($calidad_actual,$calidad_pasada)===1){
				$mantenidoscumplimiento++;
			}else if(_comparacion($calidad_actual,$calidad_pasada)===2){
				$mejoradoscumplimiento++;
			}else if(_comparacion($calidad_actual,$calidad_pasada)===3){
				$empeoradoscumplimiento++;
			}
			if(isset($listoferta)){
				//calidad
			$oferta_actual=$this->MeediaCategoria("P_Obt_Oferta","P_Pos_Oferta",$cliente["num"],$_fecha_actual,$_fecha_actual,$tbimagen);
			$oferta_pasada=$this->MeediaCategoria("P_Obt_Oferta","P_Pos_Oferta",$cliente["num"],$_fecha_pasada,$_fecha_pasada,$tbimagen);
			if(_comparacion($oferta_actual,$oferta_pasada)===1){
				$mantenidosoferta++;
			}else if(_comparacion($oferta_actual,$oferta_pasada)===2){
				$mejoradosoferta++;
			}else if(_comparacion($oferta_actual,$oferta_pasada)===3){
				$empeoradosoferta++;
			}
			}
		}
		$totalcalidad=(int)$mejoradoscalidad+(int)$empeoradoscalidad+(int)$mantenidoscalidad;
		$data["mejoradoscalidad"]=array("num"=>$mejoradoscalidad,"porcentaje"=>porcentaje($totalcalidad,$mejoradoscalidad));
		
		$data["empeoradoscalidad"]=array("num"=>$empeoradoscalidad,"porcentaje"=>porcentaje($totalcalidad,$empeoradoscalidad));
		$data["mantenidoscalidad"]=array("num"=>$mantenidoscalidad,"porcentaje"=>porcentaje($totalcalidad,$mantenidoscalidad));

		$totalcumplimento=(int)$mejoradoscumplimiento+(int)$empeoradoscumplimiento+(int)$mantenidoscumplimiento;
		$data["mejoradoscumplimiento"]=array("num"=>$mejoradoscumplimiento,"porcentaje"=>porcentaje($totalcumplimento,$mejoradoscumplimiento));
		$data["empeoradoscumplimiento"]=array("num"=>$empeoradoscumplimiento,"porcentaje"=>porcentaje($totalcumplimento,$empeoradoscumplimiento));
		$data["mantenidoscumplimiento"]=array("num"=>$mantenidoscumplimiento,"porcentaje"=>porcentaje($totalcumplimento,$mantenidoscumplimiento));
		if($_tipo_persona!=="clientes"){
			$totaloferta=$mejoradosoferta+$empeoradosoferta+$mantenidosoferta;
			$data["mejoradosoferta"]=array("num"=>$mejoradosoferta,"porcentaje"=>porcentaje($totaloferta,$mejoradosoferta));
			$data["empeoradosoferta"]=array("num"=>$empeoradosoferta,"porcentaje"=>porcentaje($totaloferta,$empeoradosoferta));
			$data["mantenidosoferta"]=array("num"=>$mantenidosoferta,"porcentaje"=>porcentaje($totaloferta,$mantenidosoferta));
				
		}
		return $data;
	}
	//funcion para saber cuantos contestaron esa pregunta la respuesta correcta
	public function cuantaspreguntascorrectas($IDEmpresa,$rangofecha,$para,$IDPregunta,$respuesta,$tipopregunta){
		$tipopregunta=trim($tipopregunta);
		if($tipopregunta==="Dias" || $tipopregunta==="Horas" || $tipopregunta==="Num" ){
			$sql=$this->db->select("avg(Respuesta) as total")->from("tbcalificaciones")->join("tbdetallescalificaciones","tbdetallescalificaciones.IDCalificacion=tbcalificaciones.IDCalificacion")->where("tbcalificaciones.IDEmpresaReceptor=$IDEmpresa and date(FechaRealizada) between ".$rangofecha." and Emitidopara='$para' and IDPregunta='$IDPregunta' and Respuesta='$respuesta'")->get();

		}else if($tipopregunta==="Si/No/NA" || $tipopregunta==="Si/No" || $tipopregunta==="Si/No/NA/NS" || $tipopregunta==="No tiene/NA/NS/Si/No"){
			$sql=$this->db->select("count(*) as total")->from("tbcalificaciones")->join("tbdetallescalificaciones","tbdetallescalificaciones.IDCalificacion=tbcalificaciones.IDCalificacion")->where("tbcalificaciones.IDEmpresaReceptor=$IDEmpresa and date(FechaRealizada) between ".$rangofecha." and Emitidopara='$para' and IDPregunta='$IDPregunta' and Respuesta='$respuesta'")->get();
		}
		return $sql->result()[0]->total;
	}
	//funcion para obtener el listado de ID de preguntas segun sea el tipo
	public function listpreguntas($categoria,$tipo){
		if($categoria!=""){
			$listasid=[];
			$listnomencla=$this->db->select($categoria)->where("Tipo='".$tipo."'")->get("tbconfigcuestionarios");
			$numenclaturas=explode(",",$listnomencla->result()[0]->$categoria);
			foreach ($numenclaturas as $nomenclatura) {
				if($nomenclatura!=""){
					$datos=$this->datos_pregunta($nomenclatura);
					array_push($listasid,$datos->IDPregunta);
				}
				
			}
			return $listasid;
		}
		
	}

}