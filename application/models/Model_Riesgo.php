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
		$this->load->model('Model_Empresa');
		$this->load->model('Model_Empresa');
		$this->load->model('Model_Calificaciones');
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
	public function obtenerrisgos($IDEmpresa,$_tipo_persona,$_tipo_fecha,$resumen=FALSE,$Tipo_Persona){
		
		
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
		if($_tipo_fecha==="A")
		{
			$_fecha_actual=$fechas[12];
			$_fecha_pasada=$fechas[0];
		}
		else
		{
			$_fecha_actual=$fechas[12];
			$_fecha_pasada=$fechas[11];
				
		}
		
		//empiezo para la grafica de evolucion
		if($_tipo_persona==="cliente"){
			$clientes=$this->ObtenerClientes($IDEmpresa);
			$tb="tbriesgo_clientes";
			
		}else{
			$clientes=$this->ObtenerProveedores($IDEmpresa);
			$tb="tbriesgo_proveedores";
			
		}
		switch($Tipo_Persona){
			case 'Cliente':
				$tbimagen="tbimagen_cliente";
				break;
			case 'Proveedor':
				$tbimagen="tbimagen_proveedor";
				break;
		}
		foreach($clientes as $cliente){
			$general_actual=$this->MeediaCategoria("P_Ob_Generales","P_Pos_Generales",$cliente["num"],$_fecha_actual,$_fecha_actual,$tbimagen);
			$general_pasada=$this->MeediaCategoria("P_Ob_Generales","P_Pos_Generales",$cliente["num"],$_fecha_pasada,$_fecha_pasada,$tbimagen);
			switch(_comparacion($general_actual,$general_pasada)){
				case 1:
					$mantenidos++;
					break;
				case 2:
					$mejorados++;
					break;
				case 3:
					$empeorados++;
					break;
			}
			
			//general
			$calidad_actual=$this->MeediaCategoria("P_Obt_Calidad","P_Pos_Calidad",$cliente["num"],$_fecha_actual,$_fecha_actual,$tbimagen);
			$calidad_pasada=$this->MeediaCategoria("P_Obt_Calidad","P_Pos_Calidad",$cliente["num"],$_fecha_pasada,$_fecha_pasada,$tbimagen);
			switch(_comparacion($calidad_actual,$calidad_pasada)){
				case 1:
					$mantenidoscalidad++;
					break;
				case 2:
					$mejoradoscalidad++;
					break;
				case 3:
					$empeoradoscalidad++;
					break;
			}
			
			//cumplimento
			$cumplimiento_actual=$this->MeediaCategoria("P_Obt_Cumplimiento","P_Pos_Cumplimiento",$cliente["num"],$_fecha_actual,$_fecha_actual,$tbimagen);
			$cumplimiento_pasada=$this->MeediaCategoria("P_Obt_Cumplimiento","P_Pos_Cumplimiento",$cliente["num"],$_fecha_pasada,$_fecha_pasada,$tbimagen);
			
			switch(_comparacion($cumplimiento_actual,$cumplimiento_pasada)){
				case 1:
					$mantenidoscumplimiento++;
					break;
				case 2:
					$mejoradoscumplimiento++;
					break;
				case 3:
					$empeoradoscumplimiento++;
					break;
			}
			if($Tipo_Persona!=="Cliente"){
				//oferta
				$oferta_actual=$this->MeediaCategoria("P_Obt_Oferta","P_Pos_Oferta",$cliente["num"],$_fecha_actual,$_fecha_actual,$tbimagen);
				
				$oferta_pasada=$this->MeediaCategoria("P_Obt_Oferta","P_Pos_Oferta",$cliente["num"],$_fecha_pasada,$_fecha_pasada,$tbimagen);
				switch(_comparacion($oferta_actual,$oferta_pasada)){
					case 1:
						$mantenidosoferta++;
						break;
					case 2:
						$mejoradosoferta++;
						break;
					case 3:
						$empeoradosoferta++;
						break;
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
		if($Tipo_Persona!=="Cliente"){
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
		
		$sql=$this->db->select("round(sum($categoria)/sum($categoria2)*10,2) as media")->where("IDEmpresa='$IDEmpresa' and date(Fecha) between '".$_fecha_inicio."-01' and '".$_fecha_fin."-".date("d")."'")->get($_tb);
		if($sql->row()->media==="" || $sql->row()->media===NULL || $sql->row()->media===0){
			return 0;
		}else{
			return (float)$sql->row()->media;
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
	public function listpreguntas_nivel2($categoria,$tipo,$giro){
		if($categoria!=""){
			$listasid=[];
			$listnomencla=$this->db->select($categoria)->where("Tipo='".$tipo."' and IDNivel2='$giro'")->get("tbconfigcuestionarios");
			$numenclaturas=explode(",",$listnomencla->result()[0]->$categoria);			
			foreach ($numenclaturas as $nomenclatura) {
				if($nomenclatura!=""){
					$datos=$this->Model_Calificaciones->detpreguntas($nomenclatura);
					array_push($listasid,$datos);
				}
				
			}
			return $listasid;
		}
		
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

	//funcion para obtener los detalles del riego
	public function detalles_riesgo($_IDEmpresa,$_Tipo,$_Fecha,$_rama){
		$fechas=docemeces();
		$fechas2=docemecespasados();

		$lista_preguntas_calidad=[];
		$lista_preguntas_cumplimiento=[];
		$lista_preguntas_oferta=[];
		
		//ahora obtengo los datos de mi empresa
		$datos_empresa=$this->Model_Empresa->getempresa($_IDEmpresa);
		//ahora obtengo el giro pricipal
		

		if($_rama===''){
			$giro_principal=$this->Model_Empresa->Get_Giro_Principal($_IDEmpresa);
			$giro_principal=$giro_principal["IDGiro2"];
		}else{
			$giro_principal=$_rama;
		}

		//primero obtengo a quein evaluar
		if($_Tipo==="cliente"){
			$lista_de_personas = $this->ObtenerClientes($_IDEmpresa);
		}else{
			$lista_de_personas = $this->ObtenerProveedores($_IDEmpresa);
		}

		

		
		$lista_preguntas_calidad=$this->listpreguntas_nivel2("calidad",$_Tipo,$giro_principal);
		$lista_preguntas_cumplimiento=$this->listpreguntas_nivel2("cumplimiento",$_Tipo,$giro_principal);
		if($_Tipo==="proveedor"){
			$lista_preguntas_oferta=$this->listpreguntas_nivel2("oferta",$_Tipo,$giro_principal);
		}

		if($_Fecha==="A"){
			$fech1="'".$fechas[0]."-".date('d')."' and '".$fechas[12]."-".date('d')."'";
			$fech2="'".$fechas2[0]."-".date('d')."' and '".$fechas2[12]."-".date('d')."'";
			
		}else{
			$fech1="'".$fechas[11]."-".date('d')."' and '".$fechas[12]."-".date('d')."'";
			$fech2="'".$fechas[9]."-".date('d')."' and '".$fechas[10]."-".date('d')."'";
			
		}
		//vdebug($fech1);
		//vdebug($lista_de_personas);
		
		


		$cuantos_clientes_evaluados_cumplimiento=0;
		$cuantos_clientes_evaluados_oferta=0;

		
		$subtotal_calificaciones_cumplimiento=0;
		$subtotal_calificaciones_oferta=0;

		
		$subtotal_calificaciones_cumplimiento_pasado=0;
		$subtotal_calificaciones_oferta_pasado=0;

		
		$total_calificaciones_cumplimiento=0;
		$total_calificaciones_oferta=0;

		
		$total_calificaciones_cumplimiento_pasado=0;
		$total_calificaciones_oferta_pasado=0;


		$listadatos_calidad=[];

		//ahora empiexo a contar de calidad
		foreach ($lista_preguntas_calidad as $pregunta) {
			$puntos_actuales_obtenidos_calidad=0;
			$puntos_actuales_posibles_calidad=0;
			$cuantos_clientes_empeorados_calidad=0;
			$cuantos_clientes_mantenidos_calidad=0;
			$cuantos_clientes_mejorados_calidad=0;
			$puntos_pasados_obtenidos_calidad=0;
			$puntos_pasados_posibles_calidad=0;
			$numero_total_calificaciones=0;
			if($pregunta->Forma!="AB" || $pregunta->Forma!="OP"){
				foreach ($lista_de_personas as $persona) {

					$puntos_actuales=$this->getPuntos($pregunta->IDPregunta,$_IDEmpresa,$giro_principal,$fech1);
					$puntos_pasados=$this->getPuntos($pregunta->IDPregunta,$_IDEmpresa,$giro_principal,$fech2);
					$numero_total_calificaciones=$numero_total_calificaciones+($puntos_actuales["num"]+(float)$puntos_pasados["num"]);
					
					
					$puntos_pasados_obtenidos_calidad=$puntos_pasados_obtenidos_calidad+(float)$puntos_pasados["obtenidos"];
					$puntos_pasados_posibles_calidad=$puntos_pasados_posibles_calidad+(float)$puntos_pasados["posibles"];

					$puntos_actuales_obtenidos_calidad=$puntos_actuales_obtenidos_calidad+(float)$puntos_actuales["obtenidos"];
					$puntos_actuales_posibles_calidad=$puntos_actuales_posibles_calidad+(float)$puntos_actuales["posibles"];
					
					$_promedio_pasado=_media_puntos((float)$puntos_pasados["obtenidos"],(float)$puntos_pasados["posibles"]);
					$_promedio_actual=_media_puntos((float)$puntos_actuales["obtenidos"],(float)$puntos_actuales["posibles"]);
					switch($_promedio_pasado){
						case $_promedio_actual:
							$cuantos_clientes_mantenidos_calidad++;
							break;
						case $_promedio_pasado>$_promedio_actual:
							$cuantos_clientes_empeorados_calidad++;
							break;
						case $_promedio_pasado<$_promedio_actual:
							$cuantos_clientes_mejorados_calidad++;
							break;
					}
					
				}
			}
			
			$_promedio_pasado=_media_puntos((float)$puntos_pasados_obtenidos_calidad,(float)$puntos_pasados_posibles_calidad);
			$_promedio_actual=_media_puntos((float)$puntos_actuales_obtenidos_calidad,(float)$puntos_pasados_posibles_calidad);
			array_push($listadatos_calidad,array("empeorados"=>$cuantos_clientes_empeorados_calidad,"mantenidos"=>$cuantos_clientes_mantenidos_calidad,"mejorados"=>$cuantos_clientes_mejorados_calidad,"promedio_actual"=>$_promedio_actual,"promedio_pasado"=>$_promedio_pasado,"Pregunta"=>$pregunta->Pregunta,"Respuesta"=>$pregunta->Condicion,"total_calificaciones"=>$numero_total_calificaciones));
		}
		$data["listado_calidad"]=$listadatos_calidad;
		$listadatos_cumplimiento=[];
		//ahora empiexo a contar de calidad
		foreach ($lista_preguntas_cumplimiento as $pregunta) {
			$puntos_actuales_obtenidos_cumplimiento=0;
			$puntos_actuales_posibles_cumplimiento=0;
			$cuantos_clientes_empeorados_cumplimiento=0;
			$cuantos_clientes_mantenidos_cumplimiento=0;
			$cuantos_clientes_mejorados_cumplimiento=0;
			$puntos_pasados_obtenidos_cumplimiento=0;
			$puntos_pasados_posibles_cumplimiento=0;
			$numero_total_calificaciones=0;
			if($pregunta->Forma!="AB" || $pregunta->Forma!="OP"){
				foreach ($lista_de_personas as $persona) {

					$puntos_actuales=$this->getPuntos($pregunta->IDPregunta,$_IDEmpresa,$giro_principal,$fech1);
					$puntos_pasados=$this->getPuntos($pregunta->IDPregunta,$_IDEmpresa,$giro_principal,$fech2);
					$numero_total_calificaciones=$numero_total_calificaciones+($puntos_actuales["num"]+(float)$puntos_pasados["num"]);
					
					
					$puntos_pasados_obtenidos_cumplimiento=$puntos_pasados_obtenidos_cumplimiento+(float)$puntos_pasados["obtenidos"];
					$puntos_pasados_posibles_cumplimiento=$puntos_pasados_posibles_cumplimiento+(float)$puntos_pasados["posibles"];

					$puntos_actuales_obtenidos_cumplimiento=$puntos_actuales_obtenidos_cumplimiento+(float)$puntos_actuales["obtenidos"];
					$puntos_actuales_posibles_cumplimiento=$puntos_actuales_posibles_cumplimiento+(float)$puntos_actuales["posibles"];
					
					$_promedio_pasado=_media_puntos((float)$puntos_pasados["obtenidos"],(float)$puntos_pasados["posibles"]);
					$_promedio_actual=_media_puntos((float)$puntos_actuales["obtenidos"],(float)$puntos_actuales["posibles"]);
					switch($_promedio_pasado){
						case $_promedio_actual:
							$cuantos_clientes_mantenidos_cumplimiento++;
							break;
						case $_promedio_pasado>$_promedio_actual:
							$cuantos_clientes_empeorados_cumplimiento++;
							break;
						case $_promedio_pasado<$_promedio_actual:
							$cuantos_clientes_mejorados_cumplimiento++;
							break;
					}
					
				}
			}
			$cuantos_clientes_evaluados_cumplimiento=$cuantos_clientes_mantenidos_cumplimiento+$cuantos_clientes_empeorados_cumplimiento+$cuantos_clientes_mejorados_cumplimiento;
			$_promedio_pasado=_media_puntos((float)$puntos_pasados_obtenidos_cumplimiento,(float)$puntos_pasados_posibles_cumplimiento);
			$_promedio_actual=_media_puntos((float)$puntos_actuales_obtenidos_cumplimiento,(float)$puntos_pasados_posibles_cumplimiento);
			array_push($listadatos_cumplimiento,array("empeorados"=>$cuantos_clientes_empeorados_cumplimiento,"mantenidos"=>$cuantos_clientes_mantenidos_cumplimiento,"mejorados"=>$cuantos_clientes_mejorados_cumplimiento,"promedio_actual"=>$_promedio_actual,"promedio_pasado"=>$_promedio_pasado,"Pregunta"=>$pregunta->Pregunta,"Respuesta"=>$pregunta->Condicion,"total_calificaciones"=>$numero_total_calificaciones));
		}
		$data["listado_cumplimiento"]=$listadatos_cumplimiento;
			
		//vdebug($listadatos_calidad);
		return $data;
		
	}

	//nuevas funciones para los detalles de riesgo
	//funcion para obtener los puntos obtenidos y posibles  y el total de calificaciones para 
	public function getPuntos($_IDPregunta,$_IDEmpresa,$_Giro,$Fecha){
		$respuesta=$this->db->select('count(*) as num, SUM(PuntosObtenidos) as obtenidos,SUM(PuntosPosibles) AS posibles')
		->join('tbdetallescalificaciones','tbdetallescalificaciones.IDCalificacion=tbcalificaciones.IDCalificacion')
		->where("IDPregunta='$_IDPregunta' and IDEmpresaReceptor='$_IDEmpresa' AND IDGiroReceptor='$_Giro' AND  date(FechaRealiza) BETWEEN  $Fecha")
		->from('tbcalificaciones')->get();
		return $respuesta->row_array();
	}
	
	//fin del bloque de detalles de riegos








	public function total_preguntas_porcentaje($_ID_Pregunta,$_ID_Receptor,$_Respuesta_correcta,$_Fecha,$_Tipo_pregunta){
		$sql_actual_total=$this->db->select('count(*) as total')->join('tbdetallescalificaciones',"tbcalificaciones.IDCalificacion=tbdetallescalificaciones.IDCalificacion")->from('tbcalificaciones')->where("IDEmpresaReceptor='$_ID_Receptor' and IDPregunta='$_ID_Pregunta' and date(FechaRealizada) between $_Fecha")->get();
		if($_Tipo_pregunta==="DIAS" || $_Tipo_pregunta==="HORAS" || $_Tipo_pregunta==="NUM" ){
			$sql_erroneas_actual=$this->db->select('sum(Respuesta) as erroneas')->join('tbdetallescalificaciones',"tbcalificaciones.IDCalificacion=tbdetallescalificaciones.IDCalificacion")->from('tbcalificaciones')->where("IDEmpresaReceptor='$_ID_Receptor' and IDPregunta='$_ID_Pregunta' and tbdetallescalificaciones.Respuesta != '$_Respuesta_correcta' and date(FechaRealizada) between $_Fecha")->get();
			
			
		}else{
			$sql_erroneas_actual=$this->db->select('count(*) as erroneas')->join('tbdetallescalificaciones',"tbcalificaciones.IDCalificacion=tbdetallescalificaciones.IDCalificacion")->from('tbcalificaciones')->where("IDEmpresaReceptor='$_ID_Receptor' and IDPregunta='$_ID_Pregunta' and tbdetallescalificaciones.Respuesta != '$_Respuesta_correcta' and date(FechaRealizada) between $_Fecha")->get();
			
		}
		

		
		$data["total"]=$sql_actual_total->row_array()["total"];
		$data["erroneas"]=$sql_erroneas_actual->row_array()["erroneas"];
		return $data;
	}
}