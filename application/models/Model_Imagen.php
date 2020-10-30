<?
/**
 * funion para obter la parte de imagen
 */
class Model_Imagen extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('selec_Titulo');
		$this->load->model('Model_Configuraciongeneral');
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
	//funcion para obtenre las imagen de como cliente basado en las calificaciones de proveedores
	public function imgcliente($IDEmpresa,$tipo_fecha,$tipo_persona,$resumen=FALSE,$_Rangos_Fecha=[]){
		$fechas=docemeces();
		$fechas2=docemecespasados();
		$_media_calidad_actual=0;
		$_media_calidad_pasado=0;
		$_media_cumplimiento_actual=0;
		$_media_cumplimiento_pasado=0;
		$_media_oferta_actual=0;
		$_media_oferta_pasado=0;
		$_media_general_actual=0;
		$_media_general_pasada=0;
		$_Numero_de_calificaciones_actual=0;
		$_Numero_de_calificaciones_pasado=0;
	
		switch($tipo_fecha){
			case "MA":
			case "AC":
				$_fecha_inicio_actual =  date('Y-m').'-01';
				$_fecha_fin_actual = date('Y-m-d');
				$_fecha_inicio_pasada = (date('Y')-1)."-".date('m') . "-01";
				$_fecha_fin_pasada = (date('Y')-1)."-".date('m-d');
				$fecha_evolucion_inicio = explode("-", $_fecha_inicio_actual);
				$fecha_evolucion_fin = explode("-", $_fecha_fin_pasada);
				$fechas_rango = $fechas;
			break;
			case "A":
				$_fecha_inicio_actual = $fechas[0] . "-" . date("d");
				$_fecha_fin_actual = $fechas[12] . "-" . date("d");
				$_fecha_inicio_pasada = $fechas2[0] . "-" . date("d");
				$_fecha_fin_pasada = $fechas2[12] . "-" . date("d");
				$fecha_evolucion_inicio = explode("-", $fechas[0]);
				$fecha_evolucion_fin = explode("-", $fechas[12]);
				$fechas_rango = $fechas;
			break;
			case "M":
				$_fecha_inicio_actual = $fechas[11] . "-" . date("d");
				$_fecha_fin_actual = $fechas[12] . "-" . date("d");
				$_fecha_inicio_pasada = $fechas[9] . "-" . date("d");
				$_fecha_fin_pasada = $fechas[10] . "-" . date("d");
				$fecha_evolucion_inicio = explode("-", $fechas[11]);
				$fecha_evolucion_fin = explode("-", $fechas[12]);
				$inicio = date("d");
				$para = 31;
				$mes = $fecha_evolucion_inicio[1];
				$anio = $fecha_evolucion_inicio[0];

			break;
			case "R":
				//vdebug($_Rangos_Fecha);
				$_fecha_inicio_actual = date("Y-m-d", strtotime($_Rangos_Fecha["fecha_I"]));
				$_fecha_fin_actual = date("Y-m-d", strtotime($_Rangos_Fecha["fecha_F"]));				
				$fechaI=explode("-", $_fecha_inicio_actual);
				$fechaF = explode("-", $_fecha_fin_actual);
				$_fecha_inicio_pasada = ((int) $fechaI[0]-1) . "-" . $fechaI[1]."-". $fechaI[2];
				$_fecha_fin_pasada = ((int) $fechaF[0] - 1) . "-" . $fechaF[1] . "-" . $fechaF[2];

				$fecha_evolucion_inicio = $fechaI;
				$fecha_evolucion_fin = $fechaF;
				$f1 = $fechaI[0]."-". $fechaI[1];
				$f2 = $fechaF[0] . "-" . $fechaF[1];
				$calendario=[];
				$mes_ac= $fechaI[1];
				$anio_ac = $fechaI[0];
				while ($f1 != $f2) {
					
					array_push($calendario, $f1);
					if ((int)$mes_ac === 12) {
						$mes_ac = 1;
						$anio_ac++;
					} else {
						(int)$mes_ac++;
					}
					($mes_ac<10)? $mes_ac='0'.$mes_ac: $mes_ac= $mes_ac;
					$f1 = $anio_ac . "-" .  $mes_ac;
					
					
				}
				array_push($calendario, $f2);
				$fechas_rango = $calendario;		
			break;
		}
		
		/*
		///
		//primero necesito numero de calificaciones 
		//esto lo calculo con la suma de todas las calificaciones de la tabla de imagen ya sea de cliente o proveedor
		*/

		if($tipo_persona==="cliente"){
			$tb='tbimagen_cliente';
			$linoferta="";
		}else{
			$tb='tbimagen_proveedor';
			$linoferta=",round(sum(P_Obt_Oferta)/sum(P_Pos_Oferta)*10,2) as mediaoferta";
		}
			//traigo los registros de la tabla de imagen_cliente
			$promedios_actuales=$this->db->select("round(sum(P_Ob_Generales)/sum(P_Pos_Generales)*10,2) as mediageneral,round(sum(P_Obt_Calidad)/sum(P_Pos_Calidad)*10,2) mediacalidad,round(sum(P_Obt_Cumplimiento)/sum(P_Pos_Cumplimiento)*10,2) as mediacumplimiento,sum(N_Calificaciones)as numcalif".$linoferta)->where("IDEmpresa='$IDEmpresa' and date(Fecha) between '$_fecha_inicio_actual' and  '$_fecha_fin_actual'")->get($tb);
			
			$promedios_pasadas=$this->db->select("round(sum(P_Ob_Generales)/sum(P_Pos_Generales)*10,2) as mediageneral,round(sum(P_Obt_Calidad)/sum(P_Pos_Calidad)*10,2) mediacalidad,round(sum(P_Obt_Cumplimiento)/sum(P_Pos_Cumplimiento)*10,2) as mediacumplimiento,sum(N_Calificaciones)as numcalif".$linoferta)->where("IDEmpresa='$IDEmpresa' and date(Fecha) between '$_fecha_inicio_pasada' and  '$_fecha_fin_pasada'")->get($tb);
			
			//ahora obtenemos para calidad
		
	
		if($promedios_pasadas->result()[0]->mediageneral!==NULL)
			{
				$_media_general_pasada=$promedios_pasadas->result()[0]->mediageneral;
				$_media_calidad_pasado=$promedios_pasadas->result()[0]->mediacalidad;
				$_media_cumplimiento_pasado=$promedios_pasadas->result()[0]->mediacumplimiento;
				$_Numero_de_calificaciones_pasado=$promedios_pasadas->result()[0]->numcalif;
				if($tipo_persona==="proveedor"){
					$_media_oferta_pasado=$promedios_pasadas->result()[0]->mediaoferta;
				}
				
			}
			if($promedios_actuales->result()[0]->mediageneral!==NULL)
			{
				
				$_media_general_actual=$promedios_actuales->result()[0]->mediageneral;
				$_media_calidad_actual=$promedios_actuales->result()[0]->mediacalidad;	
				$_media_cumplimiento_actual=$promedios_actuales->result()[0]->mediacumplimiento;
				$_Numero_de_calificaciones_actual=$promedios_actuales->result()[0]->numcalif;
				if($tipo_persona==="proveedor"){
					$_media_oferta_actual=$promedios_actuales->result()[0]->mediaoferta;		
				}
				
			}
			$_data["aumentop"]=_increment($_media_general_actual,$_media_general_pasada,"imagen");
			$_data["Calidad"]=array("media"=>$_media_calidad_actual,"incremento"=> _increment($_media_calidad_actual,$_media_calidad_pasado,"imagen"));
			$_data["Cumplimiento"]=array("media"=>$_media_cumplimiento_actual,"incremento"=>_increment($_media_cumplimiento_actual,$_media_cumplimiento_pasado,"imagen"));
			if($tipo_persona==="proveedor"){
			$_data["Oferta"]=array("media"=>$_media_oferta_actual,"incremento"=>_increment($_media_oferta_actual,$_media_oferta_pasado,"imagen"));
			}
			$_data["totalCalif"]=$_Numero_de_calificaciones_actual;
			$_data["Media"]=$_media_general_actual;
			$_data["aumento"]=_increment($_Numero_de_calificaciones_actual,$_Numero_de_calificaciones_pasado,"imagen");
			
			if($resumen===FALSE){
					$evolucion=[];
					$evolucionlabel=[];
					$_evolucion_media=[];
					$_evolucion_media_calidad=[];
					$_evolucion_media_cumplimiento=[];
					$_evolucion_media_oferta=[];
					$_evolucion_media_sanidad=[];
					$_evolucion_media_socioambiental=[];
					$_evolucion_media_label=[];


					$evolucion_pasado=[];
					$evolucionlabel_pasado=[];
					$_evolucion_media_pasado=[];
					$_evolucion_media_calidad_pasado=[];
					$_evolucion_media_cumplimiento_pasado=[];
					$_evolucion_media_oferta_pasado=[];
					$_evolucion_media_label_pasado=[];

					$_evolucion_media_sanidad_pasado=[];
					$_evolucion_media_socioambiental_pasado=[];
					
					$cuantas_actual_gen=0;
					$cuantas_actual=0;
					$calidad=0;
					$sanidad=0;
					$socioambiental=0;
					$cumplimiento=0;
					$oferta=0;

					$cuantas_actual_gen_pasado=0;
					$cuantas_actual_pasado=0;
					$calidad_pasado=0;
					$cumplimiento_pasado=0;
					$sanidad_pasado=0;
					$socioambiental_pasado=0;
					$oferta_pasado=0;
				if($tipo_fecha==='AC'){
					// aqui tengo que mostrar la evolucion por mes desde enero asta el mes que se encuetra
					$mes_inicial=1;
					$mes_final=date('m');
					$anio_pasado=date('Y')-1;
					$anio_actual=date('Y');	

					for($i=1;$i<=(int)$mes_final;$i++){
						
						($i<10)?$mes='0'.$i:$mes=$i;
						array_push($evolucionlabel,da_mes($mes));

						$cuantas_actual_gen=$this->Total_calificaciones($anio_actual.'-'.$mes."-01",$anio_actual.'-'.$mes."-31",$IDEmpresa,$tipo_persona);
						if(count($evolucion)===0){
							array_push($evolucion,(int)$cuantas_actual_gen);
						}else{
							array_push($evolucion,(int)$evolucion[count($evolucion)-1]+(int)$cuantas_actual_gen);
						}
						

						$cuantas_actual_gen_pasado=$this->Total_calificaciones($anio_pasado.'-'.$mes."-01",$anio_pasado.'-'.$mes."-31",$IDEmpresa,$tipo_persona);
						if(count($evolucion_pasado)===0){
							array_push($evolucion_pasado,(int)$cuantas_actual_gen_pasado);
						}else{
							array_push($evolucion_pasado,(int)$evolucion_pasado[count($evolucion_pasado)-1]+(int)$cuantas_actual_gen_pasado);
						}
						
		
						
						$cuantas_actual=$this->Media_calificaciones($anio_actual.'-'.$mes."-01",$anio_actual.'-'.$mes."-31",$IDEmpresa,$tipo_persona);
						if(count($_evolucion_media)===0){
							array_push($_evolucion_media,(float)$cuantas_actual);
						}else{
							array_push($_evolucion_media,(float)$_evolucion_media[count($_evolucion_media)-1]+(float)$cuantas_actual);
						}
						
			

						$cuantas_actual_pasado=$this->Media_calificaciones($anio_pasado.'-'.$mes."-01",$anio_pasado.'-'.$mes."-31",$IDEmpresa,$tipo_persona);
						if(count($_evolucion_media_pasado)===0){
							array_push($_evolucion_media_pasado,(float)$cuantas_actual_pasado);
						}else{
							array_push($_evolucion_media_pasado,(float)$_evolucion_media_pasado[count($_evolucion_media_pasado)-1]+(float)$cuantas_actual_pasado);
						}
						
						
						

						$calidad=$this->Media_calificaciones_tipo($anio_actual.'-'.$mes."-01",$anio_actual.'-'.$mes."-31",$IDEmpresa,$tipo_persona,'Calidad');
						if(count($_evolucion_media_calidad)===0){
							array_push($_evolucion_media_calidad,(float)$calidad);
						}else{
							array_push($_evolucion_media_calidad,(float)$_evolucion_media_calidad[count($_evolucion_media_calidad)-1]+(float)$calidad);
						}
						

						$calidad_pasado=$this->Media_calificaciones_tipo($anio_pasado.'-'.$mes."-01",$anio_pasado.'-'.$mes."-31",$IDEmpresa,$tipo_persona,'Calidad');
						
						if(count($_evolucion_media_calidad)===0){
							array_push($_evolucion_media_calidad,(float)$calidad_pasado);
						}else{
							array_push($_evolucion_media_calidad,(float)$_evolucion_media_calidad[count($_evolucion_media_calidad)-1]+(float)$calidad_pasado);
						}
						
						

						$cumplimiento=$this->Media_calificaciones_tipo($anio_actual.'-'.$mes."-01",$anio_actual.'-'.$mes."-31",$IDEmpresa,$tipo_persona,'Cumplimiento');
						if(count($_evolucion_media_cumplimiento)===0){
							array_push($_evolucion_media_cumplimiento,(float)$cumplimiento);
						}else{
							array_push($_evolucion_media_cumplimiento,(float)$_evolucion_media_cumplimiento[count($_evolucion_media_cumplimiento)-1]+(float)$cumplimiento);
						}
						
						
						$cumplimiento_pasado=$this->Media_calificaciones_tipo($anio_pasado.'-'.$mes."-01",$anio_pasado.'-'.$mes."-31",$IDEmpresa,$tipo_persona,'Cumplimiento');
						if(count($_evolucion_media_cumplimiento_pasado)===0){
							array_push($_evolucion_media_cumplimiento_pasado,(float)$cumplimiento_pasado);
						}else{
							array_push($_evolucion_media_cumplimiento_pasado,(float)$_evolucion_media_cumplimiento_pasado[count($_evolucion_media_cumplimiento_pasado)-1]+(float)$cumplimiento_pasado);
						}
						
						// sanidad


						$sanidad=$this->Media_calificaciones_tipo($anio_actual.'-'.$mes."-01",$anio_actual.'-'.$mes."-31",$IDEmpresa,$tipo_persona,'Sanidad');
						if(count($_evolucion_media_sanidad)===0){
							array_push($_evolucion_media_sanidad,(float)$sanidad);
						}else{
							array_push($_evolucion_media_sanidad,(float)$_evolucion_media_sanidad[count($_evolucion_media_sanidad)-1]+(float)$sanidad);
						}
						
						
						$sanidad_pasado=$this->Media_calificaciones_tipo($anio_pasado.'-'.$mes."-01",$anio_pasado.'-'.$mes."-31",$IDEmpresa,$tipo_persona,'Sanidad');
						if(count($_evolucion_media_sanidad_pasado)===0){
							array_push($_evolucion_media_sanidad_pasado,(float)$sanidad_pasado);
						}else{
							array_push($_evolucion_media_sanidad_pasado,(float)$_evolucion_media_sanidad_pasado[count($_evolucion_media_sanidad_pasado)-1]+(float)$sanidad_pasado);
						}

						// socioambiental

						$socioambiental=$this->Media_calificaciones_tipo($anio_actual.'-'.$mes."-01",$anio_actual.'-'.$mes."-31",$IDEmpresa,$tipo_persona,'Socioambiental');
						if(count($_evolucion_media_socioambiental)===0){
							array_push($_evolucion_media_socioambiental,(float)$socioambiental);
						}else{
							array_push($_evolucion_media_socioambiental,(float)$_evolucion_media_socioambiental[count($_evolucion_media_socioambiental)-1]+(float)$socioambiental);
						}
						
						
						$socioambiental_pasado=$this->Media_calificaciones_tipo($anio_pasado.'-'.$mes."-01",$anio_pasado.'-'.$mes."-31",$IDEmpresa,$tipo_persona,'Socioambiental');
						if(count($_evolucion_media_socioambiental_pasado)===0){
							array_push($_evolucion_media_socioambiental_pasado,(float)$socioambiental_pasado);
						}else{
							array_push($_evolucion_media_socioambiental_pasado,(float)$_evolucion_media_socioambiental_pasado[count($_evolucion_media_socioambiental_pasado)-1]+(float)$socioambiental_pasado);
						}
						
						
						
						if($tipo_persona==="proveedor"):
							$oferta=$this->Media_calificaciones_tipo($anio_actual.'-'.$mes."-01",$anio_actual.'-'.$mes."-31",$IDEmpresa,$tipo_persona,'Oferta');
							
							if(count($_evolucion_media_oferta)===0){
								array_push($_evolucion_media_oferta,(float)$oferta);
							}else{
								array_push($_evolucion_media_oferta,(float)$_evolucion_media_oferta[count($_evolucion_media_oferta)-1]+(float)$oferta);
							}
							$oferta_pasado=$this->Media_calificaciones_tipo($anio_pasado.'-'.$mes."-01",$anio_pasado.'-'.$mes."-31",$IDEmpresa,$tipo_persona,'Oferta');
							if(count($_evolucion_media_oferta_pasado)===0){
								array_push($_evolucion_media_oferta_pasado,(float)$oferta_pasado);
							}else{
								array_push($_evolucion_media_oferta_pasado,(float)$_evolucion_media_oferta_pasado[count($_evolucion_media_oferta_pasado)-1]+(float)$oferta_pasado);
							}
							
						endif;
						
						
					}
					$_data['PeriodoG2'] = [(date('Y') - 1), date('Y')];
					$_data["series1"] = array("dataActual" => $anio_actual, "dataPasado" => $anio_pasado);
					$_data['text'] = dame_mes(date('m')) . " " . date('Y');
					$_data['Periodo'] = [dame_mes(date('m')) . " " . (date('Y') - 1), dame_mes(date('m')) . " " . date('Y')];
					$_data["serievolucion"]=array("data"=>array("data_actual"=>$evolucion,"data_pasado"=>$evolucion_pasado),"label"=>$evolucionlabel);
					$_data["evolucionmedia"]=array("data"=>array("data_actual"=>$_evolucion_media,"data_pasado"=>$_evolucion_media_pasado),"label"=>$evolucionlabel);	
					$_data["evolucion_calidad"]=array("data"=>array("data_actual"=>$_evolucion_media_calidad,"data_pasado"=>$_evolucion_media_calidad_pasado),"label"=>$evolucionlabel);
					$_data["evolucion_cumplimiento"]=array("data"=>array("data_actual"=>$_evolucion_media_cumplimiento,"data_pasado"=>$_evolucion_media_cumplimiento_pasado),"label"=>$evolucionlabel);
					$_data["evolucion_sanidad"]=array("data"=>array("data_actual"=>$_evolucion_media_sanidad,"data_pasado"=>$_evolucion_media_sanidad_pasado),"label"=>$evolucionlabel);	
					$_data["evolucion_socioambiental"]=array("data"=>array("data_actual"=>$_evolucion_media_socioambiental,"data_pasado"=>$_evolucion_media_socioambiental_pasado),"label"=>$evolucionlabel);
					if($tipo_persona==="proveedor"):
						$_data["evolucion_oferta"]=array("data"=>array("data_actual"=>$_evolucion_media_oferta,"data_pasado"=>$_evolucion_media_oferta_pasado),"label"=>$evolucionlabel);
					endif;

					return $_data;
				}
				if($tipo_fecha==='MA'){

					
					// aqui tengo que mostrar la evolucion por mes desde enero asta el mes que se encuetra
					$mes_inicial=1;
					$mes_final=date('m');
					$anio_pasado=date('Y')-1;
					$anio_actual=date('Y');
				
					for($i=1;$i<=(int)$mes_final;$i++){
						
						($i<10)?$mes='0'.$i:$mes=$i;
						array_push($evolucionlabel,da_mes($mes));

						$cuantas_actual_gen=$this->Total_calificaciones($anio_actual.'-'.$mes."-01",$anio_actual.'-'.$mes."-31",$IDEmpresa,$tipo_persona);
						array_push($evolucion,(int)$cuantas_actual_gen);

						$cuantas_actual_gen_pasado=$this->Total_calificaciones($anio_pasado.'-'.$mes."-01",$anio_pasado.'-'.$mes."-31",$IDEmpresa,$tipo_persona);
						array_push($evolucion_pasado,(int)$cuantas_actual_gen_pasado);
						
						$cuantas_actual=$this->Media_calificaciones($anio_actual.'-'.$mes."-01",$anio_actual.'-'.$mes."-31",$IDEmpresa,$tipo_persona);
						array_push($_evolucion_media,(float)$cuantas_actual);

						$cuantas_actual_pasado=$this->Media_calificaciones($anio_pasado.'-'.$mes."-01",$anio_pasado.'-'.$mes."-31",$IDEmpresa,$tipo_persona);
						array_push($_evolucion_media_pasado,(float)$cuantas_actual_pasado);

						$calidad=$this->Media_calificaciones_tipo($anio_actual.'-'.$mes."-01",$anio_actual.'-'.$mes."-31",$IDEmpresa,$tipo_persona,'Calidad');
						array_push($_evolucion_media_calidad,$calidad);

						$calidad_pasado=$this->Media_calificaciones_tipo($anio_pasado.'-'.$mes."-01",$anio_pasado.'-'.$mes."-31",$IDEmpresa,$tipo_persona,'Calidad');
						array_push($_evolucion_media_calidad_pasado,$calidad_pasado);

						$cumplimiento=$this->Media_calificaciones_tipo($anio_actual.'-'.$mes."-01",$anio_actual.'-'.$mes."-31",$IDEmpresa,$tipo_persona,'Cumplimiento');
						array_push($_evolucion_media_cumplimiento,$cumplimiento);
						
						$cumplimiento_pasado=$this->Media_calificaciones_tipo($anio_pasado.'-'.$mes."-01",$anio_pasado.'-'.$mes."-31",$IDEmpresa,$tipo_persona,'Cumplimiento');
						array_push($_evolucion_media_cumplimiento_pasado,$cumplimiento_pasado);
						
						// sanidad
						$sanidad=$this->Media_calificaciones_tipo($anio_actual.'-'.$mes."-01",$anio_actual.'-'.$mes."-31",$IDEmpresa,$tipo_persona,'Sanidad');
						array_push($_evolucion_media_sanidad,$sanidad);
						
						$sanidad_pasado=$this->Media_calificaciones_tipo($anio_pasado.'-'.$mes."-01",$anio_pasado.'-'.$mes."-31",$IDEmpresa,$tipo_persona,'Sanidad');
						array_push($_evolucion_media_sanidad_pasado,$sanidad_pasado);

						//socioambiental
						$socioambiental=$this->Media_calificaciones_tipo($anio_actual.'-'.$mes."-01",$anio_actual.'-'.$mes."-31",$IDEmpresa,$tipo_persona,'Socioambiental');
						array_push($_evolucion_media_sanidad,$socioambiental);
						
						$socioambiental_pasado=$this->Media_calificaciones_tipo($anio_pasado.'-'.$mes."-01",$anio_pasado.'-'.$mes."-31",$IDEmpresa,$tipo_persona,'Socioambiental');
						array_push($_evolucion_media_socioambiental_pasado,$socioambiental_pasado);



						if($tipo_persona==="proveedor"):
							$oferta=$this->Media_calificaciones_tipo($anio_actual.'-'.$mes."-01",$anio_actual.'-'.$mes."-31",$IDEmpresa,$tipo_persona,'Oferta');
							array_push($_evolucion_media_oferta,$oferta);

							$oferta_pasado=$this->Media_calificaciones_tipo($anio_pasado.'-'.$mes."-01",$anio_pasado.'-'.$mes."-31",$IDEmpresa,$tipo_persona,'Oferta');
							array_push($_evolucion_media_oferta_pasado,$oferta_pasado);
						endif;
						$cuantas_actual_gen=0;
						$cuantas_actual=0;
						$calidad=0;
						$cumplimiento=0;
						$sanidad=0;
						$socioambiental=0;
						$oferta=0;

						$cuantas_actual_gen_pasado=0;
						$cuantas_actual_pasado=0;
						$socioambiental_pasado=0;
						$sanidad_pasado=0;
						$calidad_pasado=0;
						$cumplimiento_pasado=0;
						$oferta_pasado=0;
						
					}
					$_data['PeriodoG2'] = [(date('Y') - 1), date('Y')];
					$_data["series1"] = array("dataActual" => $anio_actual, "dataPasado" => $anio_pasado);
					$_data['text'] = dame_mes(date('m')) . " " . date('Y');
					$_data['Periodo'] = [dame_mes(date('m')) . " " . (date('Y') - 1), dame_mes(date('m')) . " " . date('Y')];
					$_data["serievolucion"]=array("data"=>array("data_actual"=>$evolucion,"data_pasado"=>$evolucion_pasado),"label"=>$evolucionlabel);
					$_data["evolucionmedia"]=array("data"=>array("data_actual"=>$_evolucion_media,"data_pasado"=>$_evolucion_media_pasado),"label"=>$evolucionlabel);	
					$_data["evolucion_calidad"]=array("data"=>array("data_actual"=>$_evolucion_media_calidad,"data_pasado"=>$_evolucion_media_calidad_pasado),"label"=>$evolucionlabel);
					$_data["evolucion_cumplimiento"]=array("data"=>array("data_actual"=>$_evolucion_media_cumplimiento,"data_pasado"=>$_evolucion_media_cumplimiento_pasado),"label"=>$evolucionlabel);
					$_data["evolucion_sanidad"]=array("data"=>array("data_actual"=>$_evolucion_media_sanidad,"data_pasado"=>$_evolucion_media_sanidad_pasado),"label"=>$evolucionlabel);
					$_data["evolucion_socioambiental"]=array("data"=>array("data_actual"=>$_evolucion_media_socioambiental,"data_pasado"=>$_evolucion_media_socioambiental_pasado),"label"=>$evolucionlabel);
					
					if($tipo_persona==="proveedor"):
						$_data["evolucion_oferta"]=array("data"=>array("data_actual"=>$_evolucion_media_oferta,"data_pasado"=>$_evolucion_media_oferta_pasado),"label"=>$evolucionlabel);
					endif;

					return $_data;

				}
				if($tipo_fecha==="A" || $tipo_fecha === "R"){
					
					foreach ($fechas_rango as $fechacom) {
						$datos=explode("-", $fechacom);
						$cuantas=$this->Total_calificaciones($fechacom."-01",$fechacom."-31",$IDEmpresa,$tipo_persona);
						
						array_push($evolucionlabel,da_mes($datos[1])."-".$datos[0]);
						array_push($evolucion,(int)$cuantas);
						$cuantas=$this->Media_calificaciones($fechacom."-01",$fechacom."-31",$IDEmpresa,$tipo_persona);
						array_push($_evolucion_media_label,da_mes($datos[1])."-".$datos[0]);
						array_push($_evolucion_media,(float)$cuantas);
						//ahora vamos a llenar los arrays para las evoluciones de las graficas
						$calidad=$this->Media_calificaciones_tipo($fechacom."-01",$fechacom."-31",$IDEmpresa,$tipo_persona,'Calidad');
						array_push($_evolucion_media_calidad,$calidad);
						$cumplimiento=$this->Media_calificaciones_tipo($fechacom."-01",$fechacom."-31",$IDEmpresa,$tipo_persona,'Cumplimiento');
						array_push($_evolucion_media_cumplimiento,$cumplimiento);
						if($tipo_persona==="proveedor"):
							$oferta=$this->Media_calificaciones_tipo($fechacom."-01",$fechacom."-31",$IDEmpresa,$tipo_persona,'Oferta');
							
							array_push($_evolucion_media_oferta,$oferta);
						endif;
						
					}

				}
				if($tipo_fecha==="M"){
					
					while($inicio<=$para){
						if($inicio===31){
							$para=date("d");
							$inicio=1;
							$mes=$fecha_evolucion_fin[1];
							$anio=$fecha_evolucion_fin[0];
							$fechacom=$anio."-".$mes;
							$cuantas=$this->Total_calificaciones($fechacom."-".$inicio,$fechacom."-".$inicio,$IDEmpresa,$tipo_persona);
							array_push($evolucionlabel,$inicio."-".da_mes($mes));
							array_push($evolucion,(int)$cuantas);
							$cuantas=$this->Media_calificaciones($fechacom."-".$inicio,$fechacom."-".$inicio,$IDEmpresa,$tipo_persona);
							array_push($_evolucion_media_label,$inicio."-".da_mes($mes));
							array_push($_evolucion_media,(float)$cuantas);
							//ahora vamos a llenar los arrays para las evoluciones de las graficas
							$calidad=$this->Media_calificaciones_tipo($fechacom."-".$inicio,$fechacom."-".$inicio,$IDEmpresa,$tipo_persona,'Calidad');
							array_push($_evolucion_media_calidad,$calidad);
							$cumplimiento=$this->Media_calificaciones_tipo($fechacom."-".$inicio,$fechacom."-".$inicio,$IDEmpresa,$tipo_persona,'Cumplimiento');
							array_push($_evolucion_media_cumplimiento,$cumplimiento);
							
							if($tipo_persona==="proveedor"):
								$oferta=$this->Media_calificaciones_tipo($fechacom."-".$inicio,$fechacom."-".$inicio,$IDEmpresa,$tipo_persona,'Oferta');
								array_push($_evolucion_media_oferta,$oferta);
							endif;
						}else{
							$fechacom=$anio."-".$mes;
							$cuantas=$this->Total_calificaciones($fechacom."-".$inicio,$fechacom."-".$inicio,$IDEmpresa,$tipo_persona);
							
							array_push($evolucionlabel,$inicio."-".da_mes($mes));
							array_push($evolucion,(int)$cuantas);

							$cuantas=$this->Media_calificaciones($fechacom."-".$inicio,$fechacom."-".$inicio,$IDEmpresa,$tipo_persona);
							array_push($_evolucion_media_label,$inicio."-".da_mes($mes));
							array_push($_evolucion_media,(float)$cuantas);
							//ahora vamos a llenar los arrays para las evoluciones de las graficas
							$calidad=$this->Media_calificaciones_tipo($fechacom."-".$inicio,$fechacom."-".$inicio,$IDEmpresa,$tipo_persona,'Calidad');
							array_push($_evolucion_media_calidad,$calidad);
							$cumplimiento=$this->Media_calificaciones_tipo($fechacom."-".$inicio,$fechacom."-".$inicio,$IDEmpresa,$tipo_persona,'Cumplimiento');
							array_push($_evolucion_media_cumplimiento,$cumplimiento);
							
							if($tipo_persona==="proveedor"):
								$oferta=$this->Media_calificaciones_tipo($fechacom."-".$inicio,$fechacom."-".$inicio,$IDEmpresa,$tipo_persona,'Oferta');
								
								array_push($_evolucion_media_oferta,$oferta);
							endif;
							$inicio++;
						}
					}

					$_data["serievolucion"]=array("data"=>array("data_pasado"=>$evolucion),"label"=>$evolucionlabel);
					$_data["evolucionmedia"]=array("data"=>array("data_pasado"=>$_evolucion_media),"label"=>$evolucionlabel);	
					$_data["evolucion_calidad"]=array("data"=>array("data_pasado"=>$_evolucion_media_calidad),"label"=>$evolucionlabel);
					$_data["evolucion_cumplimiento"]=array("data"=>array("data_pasado"=>$_evolucion_media_cumplimiento),"label"=>$evolucionlabel);
					if($tipo_persona==="proveedor"):
						$_data["evolucion_oferta"]=array("data"=>array("data_pasado"=>$_evolucion_media_oferta),"label"=>$evolucionlabel);
					endif;
					$_data['PeriodoG2'] = [$_fecha_inicio_actual , $_fecha_fin_actual ];
					$_data["series1"] = array("dataActual" => $_fecha_fin_actual, "dataPasado" => $_fecha_inicio_actual);
					$_data['text'] = $_fecha_inicio_actual."-".$_fecha_fin_actual;
					$_data['Periodo'] = [$_fecha_inicio_actual, $_fecha_fin_actual];
					return $_data;
				}
				
				
					
				
				
				

			}
				
			return $_data;
	}
	public function Total_calificaciones($_fecha_inicio,$_fecha_fin,$IDEmpresa,$forma){
		$forma=strtolower($forma);
		if($forma==="cliente"){
			$sql=$this->db->select('sum(N_Calificaciones) as Numcalificaciones')->where("IDEmpresa='$IDEmpresa' and date(Fecha) between '$_fecha_inicio' and  '$_fecha_fin'")->get('tbimagen_cliente');
		}else{
			$sql=$this->db->select('sum(N_Calificaciones) as Numcalificaciones')->where("IDEmpresa='$IDEmpresa' and date(Fecha) between '$_fecha_inicio' and  '$_fecha_fin'")->get('tbimagen_proveedor');
		}
		
		if($sql->num_rows()===0){
			return 0;
		}else{
			return $sql->result()[0]->Numcalificaciones;
		}
	}
	public function Media_calificaciones($_fecha_inicio,$_fecha_fin,$IDEmpresa,$forma){
		$forma=strtolower($forma);
		if($forma==="cliente"){
			$sql=$this->db->select('round(sum(P_Ob_Generales)/sum(P_Pos_Generales)*10,2) as mediageneral')->where("IDEmpresa='$IDEmpresa' and date(Fecha) between '$_fecha_inicio' and  '$_fecha_fin'")->get('tbimagen_cliente');
		}else{
			$sql=$this->db->select('round(sum(P_Ob_Generales)/sum(P_Pos_Generales)*10,2) as mediageneral')->where("IDEmpresa='$IDEmpresa' and date(Fecha) between '$_fecha_inicio' and  '$_fecha_fin'")->get('tbimagen_proveedor');
		}
		
		if($sql->num_rows()===0){
			return 0;
		}else{
			return $sql->result()[0]->mediageneral;
		}
	}
	public function Media_calificaciones_tipo($_fecha_inicio,$_fecha_fin,$IDEmpresa,$forma,$_Tipo){
		$forma=strtolower($forma);
		switch ($_Tipo) {
			case 'Calidad':
				$sql='round(sum(P_Obt_Calidad)/sum(P_Pos_Calidad)*10,2) as media';
				break;
			case 'Cumplimiento':
				$sql='round(sum(P_Obt_Cumplimiento)/sum(P_Pos_Cumplimiento)*10,2) as media';
				break;
			case 'Oferta':
				$sql='round(sum(P_Obt_Oferta)/sum(P_Pos_Oferta)*10,2) as media';
				break;
			case 'Sanidad':
				$sql='round(sum(P_Obt_Sanidad)/sum(P_Pos_Sanidad)*10,2) as media';
				break;
			case 'Socioambiental':
				$sql='round(sum(P_Obt_Socioambiental)/sum(P_Obt_Socioambiental)*10,2) as media';
				break;
			
		}
		if($forma==="cliente"){
			$sql=$this->db->select($sql)->where("IDEmpresa='$IDEmpresa' and date(Fecha) between '$_fecha_inicio' and  '$_fecha_fin'")->get('tbimagen_cliente');
		}else{
			$sql=$this->db->select($sql)->where("IDEmpresa='$IDEmpresa' and date(Fecha) between '$_fecha_inicio' and  '$_fecha_fin'")->get('tbimagen_proveedor');
		}
		//vdebug($sql->result()[0]);
		if($sql->num_rows()===0 || $sql->result()[0]->media=== null){
			return 0;
		}else{
			return  $sql->result()[0]->media;
			
		}
	}
	public function resumenImagen($IDEmpresa,$_tipo_usuario,$forma)
	{
		$fechas=docemeces();
		$fechas2=docemecespasados();
		$_media_calidad_actual=0;
		$_media_calidad_pasado=0;
		$_media_cumplimiento_actual=0;
		$_media_cumplimiento_pasado=0;
		$_media_oferta_actual=0;
		$_media_oferta_pasado=0;
		$_media_general_actual=0;
		$_media_general_pasada=0;
		$_Numero_de_calificaciones_actual=0;
		$_Numero_de_calificaciones_pasado=0;
		$_tipo_personas=["Cliente","Proveedor"];
			$_fecha_inicio_actual=$fechas[0]."-".date("d");
			$_fecha_fin_actual=$fechas[12]."-".date("d");
			$_fecha_inicio_pasada=$fechas2[0]."-".date("d");
			$_fecha_fin_pasada=$fechas2[12]."-".date("d");
			$fecha_evolucion_inicio=explode("-",$fechas[0]);
			$fecha_evolucion_fin=explode("-",$fechas[12]);
			$fechas_rango=$fechas;
		foreach ($_tipo_personas as $tipo_persona) {
			
		
		if($tipo_persona==="Cliente"){
			$tb='tbimagen_cliente';
			$linoferta="";
		}else{

			$tb='tbimagen_proveedor';
			$linoferta=",round(sum(P_Obt_Oferta)/sum(P_Pos_Oferta)*10,2) as mediaoferta";
			
		}

		//traigo los registros de la tabla de imagen_cliente
			$promedios_actuales=$this->db->select("round(sum(P_Ob_Generales)/sum(P_Pos_Generales)*10,2) as mediageneral,round(sum(P_Obt_Calidad)/sum(P_Pos_Calidad)*10,2) mediacalidad,round(sum(P_Obt_Cumplimiento)/sum(P_Pos_Cumplimiento)*10,2) as mediacumplimiento,sum(N_Calificaciones)as numcalif".$linoferta)->where("IDEmpresa='$IDEmpresa' and date(Fecha) between '$_fecha_inicio_actual' and  '$_fecha_fin_actual'")->get($tb);
			$promedios_pasadas=$this->db->select("round(sum(P_Ob_Generales)/sum(P_Pos_Generales)*10,2) as mediageneral,round(sum(P_Obt_Calidad)/sum(P_Pos_Calidad)*10,2) mediacalidad,round(sum(P_Obt_Cumplimiento)/sum(P_Pos_Cumplimiento)*10,2) as mediacumplimiento,sum(N_Calificaciones)as numcalif".$linoferta)->where("IDEmpresa='$IDEmpresa' and date(Fecha) between '$_fecha_inicio_pasada' and  '$_fecha_fin_pasada'")->get($tb);
		
		if($promedios_pasadas->result()[0]->mediageneral!==NULL)
			{
				$_media_general_pasada=$promedios_pasadas->result()[0]->mediageneral;
				$_media_calidad_pasado=$promedios_pasadas->result()[0]->mediacalidad;
				$_media_cumplimiento_pasado=$promedios_pasadas->result()[0]->mediacumplimiento;
				$_Numero_de_calificaciones_pasado=$promedios_pasadas->result()[0]->numcalif;
				if($tipo_persona==="Proveedor"){
					$_media_oferta_pasado=$promedios_pasadas->result()[0]->mediaoferta;
				}
				
			}
			if($promedios_actuales->result()[0]->mediageneral!==NULL)
			{
				$_media_general_actual=$promedios_actuales->result()[0]->mediageneral;
				$_media_calidad_actual=$promedios_actuales->result()[0]->mediacalidad;	
				$_media_cumplimiento_actual=$promedios_actuales->result()[0]->mediacumplimiento;
				$_Numero_de_calificaciones_actual=$promedios_actuales->result()[0]->numcalif;
				if($tipo_persona==="Proveedor"){
					$_media_oferta_actual=$promedios_actuales->result()[0]->mediaoferta;		
				}
				
			}
			$_data["aumentop_".$tipo_persona]=_increment($_media_general_actual,$_media_general_pasada,"imagen");
			$_data["Calidad_".$tipo_persona]=array("media"=>$_media_calidad_actual,"incremento"=> _increment($_media_calidad_actual,$_media_calidad_pasado,"imagen"));
			$_data["Cumplimiento_".$tipo_persona]=array("media"=>$_media_cumplimiento_actual,"incremento"=>_increment($_media_cumplimiento_actual,$_media_cumplimiento_pasado,"imagen"));
			if($tipo_persona==="Proveedor"){
			$_data["Oferta_".$tipo_persona]=array("media"=>$_media_cumplimiento_actual,"incremento"=>_increment($_media_oferta_actual,$_media_oferta_pasado,"imagen"));
			}
			$_data["totalCalif_".$tipo_persona]=$_Numero_de_calificaciones_actual;
			$_data["Media_".$tipo_persona]=$_media_general_actual;
			$_data["aumento_".$tipo_persona]=_increment($_Numero_de_calificaciones_actual,$_Numero_de_calificaciones_pasado,"imagen");

		}

		return $_data;
		
	}
	public function promediorang($IDEmpresa,$date1,$date2,$categoria,$tipo,$status){
		$listasid=[];
		//obtengo los ide las calificaciones segun los criterios
		$sql=$this->db->select('IDCalificacion')->where("IDEmpresaReceptor='$IDEmpresa' and Status='$status' and Emitidopara='$tipo' and DATE(FechaRealizada) between '$date1'  and '$date2'")->get('tbcalificaciones');
		$listnomencla=$this->db->select($categoria)->where("Tipo='$tipo'")->get("tbconfigcuestionarios");
		$numenclaturas=explode(",",$listnomencla->result()[0]->$categoria);

		foreach ($numenclaturas as $nomenclatura) {
			if($nomenclatura!=""){
				$datos=$this->DatsPreguntas($nomenclatura);
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
	//fucion que obtiene el numero de calificaiones totales en un rango de fechas
	public function totcalifd($anio,$mes,$dia,$IDEmpresa,$forma){
		$sql=$this->db->select('count(*) as total')->where("IDEmpresaReceptor='$IDEmpresa' and Emitidopara='$forma' and DATE(FechaRealizada) between '$anio-$mes-$dia' and '$anio-$mes-$dia' group by IDEmpresaReceptor")->get('tbcalificaciones');
		if($sql->num_rows()===0){
			$calificaciones=0;
		}else{
			$calificaciones=$sql->result()[0]->total;
		}
		return $calificaciones;
	}
	//fucion que obtiene el numero de calificaiones totales en un rango de fechas
	public function mediacalificacionesd($anio,$mes,$dia,$IDEmpresa,$forma){
		$sql=$this->db->select('sum(PuntosObtenidos) as puntosobtenido,sum(PuntosPosibles)as puntosposibles')->from('tbcalificaciones')->join('tbdetallescalificaciones','tbdetallescalificaciones.IDCalificacion=tbcalificaciones.IDCalificacion')->where("IDEmpresaReceptor='$IDEmpresa' and Emitidopara='$forma' and DATE(FechaRealizada) between '$anio-$mes-$dia' and '$anio-$mes-$dia'")->get();
		if($sql->num_rows()===0){
			$calificaciones=0;
		}else if($sql->result()[0]->puntosobtenido===NULL && $sql->result()[0]->puntosposibles===NULL){
			$calificaciones=0;
		}else{
			$calificaciones=_media_puntos($sql->result()[0]->puntosobtenido,$sql->result()[0]->puntosposibles);
		}
		return $calificaciones;
	}
	//fucion que obtiene el numero de calificaiones totales en un rango de fechas
	public function totcalif($anio,$mes,$IDEmpresa,$forma){
		$sql=$this->db->select('count(*) as total')->where("IDEmpresaReceptor='$IDEmpresa' and date(FechaRealizada) between '".$anio."-".$mes."-01' and '".$anio."-".$mes."-31' and Status='Activa' and Emitidopara='$forma' group by IDEmpresaReceptor")->get('tbcalificaciones');
		if($sql->num_rows()===0){
			$calificaciones=0;
		}else{
			$calificaciones=$sql->result()[0]->total;
		}
		return $calificaciones;
	}
	public function mediacalif($anio,$mes,$IDEmpresa,$forma){
		$sql=$this->db->select('sum(PuntosObtenidos) as puntosobtenido,sum(PuntosPosibles)as puntosposibles')->from('tbcalificaciones')->join('tbdetallescalificaciones','tbdetallescalificaciones.IDCalificacion=tbcalificaciones.IDCalificacion')->where("IDEmpresaReceptor='$IDEmpresa' and date(FechaRealizada) between '".$anio."-".$mes."-01' and '".$anio."-".$mes."-31' and Status='Activa' and Emitidopara='$forma'")->get();
		if($sql->num_rows()===0){
			$calificaciones=0;
		}else if($sql->result()[0]->puntosobtenido===NULL && $sql->result()[0]->puntosposibles===NULL){
			$calificaciones=0;
		}else{
			$calificaciones=_media_puntos($sql->result()[0]->puntosobtenido,$sql->result()[0]->puntosposibles);
		}
		return $calificaciones;
	}
	//funcion para loa datoa de pregunta por ID
	public function datos_preguntaID($IDPregunta)
	{
		$sql=$this->db->select("*")->where("IDPregunta='$IDPregunta'")->get("preguntas_val");
		return $sql->row_array();
	}
	//funcion para obtener el listado de ID de preguntas segun sea el tipo
	public function listpreguntas($categoria,$tipo,$giro){
		$tipo=ucwords($tipo);   
		if($categoria!=""){
			$listasid=[];
			$listnomencla=$this->db->select($categoria)->where("Tipo='".$tipo."' and IDNivel2='$giro'")->get("tbconfigcuestionarios");			
			
			return $listnomencla->row();
		}
		
	}
	//funcion para obtener los detalles de las preguntas
	public function DatsPreguntas($Nomclatura){
		$sql=$this->db->select('*')->where("Nomenclatura='$Nomclatura'")->get("preguntas_val");
		return $sql->result()[0];

	}
	//obtener el promedio y el aumento en una categoria sea cumplimento calidad oferta o recomendaciones
	public function Promedioauemntocategoria($categoria,$IDEmpresa,$Tipo,$Forma){
		$matrispreguntas=[];
		$puntosposibles=0;
		$puntosobtenidos=0;
		$puntosposibles2=0;
		$puntosobtenidos2=0;
		$promedio1=0;
		$promedio2=0;
			//obtengo las nomenclaturas de las preguntas dependiendo la categoria
		$sql=$this->db->select($categoria)->where("Tipo='$Tipo'")->get('tbconfigcuestionarios');
		$nomenclaturas=explode(",",$sql->result()[0]->$categoria);
			//convierto las nomenclaturas en id
		foreach ($nomenclaturas as $pregunta) {
			$preg=$this->DatsPreguntas($pregunta);
			array_push($matrispreguntas,$preg->IDPregunta);
		}
			//ahora busco esa pregunta en la base de datos dependiendo si es por año o por mes
			

		$fechas=docemeces();
		$fechas2=docemecespasados();
		if($Forma==="A"){
			
				//ahora tengo ya que tengo los ids de las preguntas solo de detealles de preguntas obtengo los puntos posibles y los puntos obtendios los sumo y los divido entre 10 para obtener la media de una categoria;
			//obtengo los id de las valoraciones que he tendio por empresa
			$sql=$this->db->select('IDCalificacion')->where("Status='Activa' and Emitidopara='$Tipo' and IDEmpresaReceptor='$IDEmpresa' and DATE(FechaRealizada) between '".$fechas[0]."-01' and '".$fechas[12]."-31'")->get('tbcalificaciones');

			if($sql->num_rows()!=0){
				foreach ($sql->result() as $valoracion) {
					foreach ($matrispreguntas as $pregunta) {
						//12 meses actuales
						$sql=$this->db->select("sum(PuntosPosibles) as puntosposibles,sum(PuntosObtenidos) as puntosobtenidos")->where("DATE(FechaRealiza) between '".$fechas[0]."-01' and '".$fechas[12]."-31' and IDPregunta='$pregunta' and IDCalificacion='$valoracion->IDCalificacion'")->get('tbdetallescalificaciones');

						if($sql->num_rows()!=0){
							$puntosposibles=$puntosposibles+(int)$sql->result()[0]->puntosposibles;
							$puntosobtenidos=$puntosobtenidos+(int)$sql->result()[0]->puntosobtenidos;
						}
					}
				}
			}
			
			$sql=$this->db->select('IDCalificacion')->where("Status='Activa' and Emitidopara='$Tipo' and IDEmpresaReceptor='$IDEmpresa' and DATE(FechaRealizada) between '".$fechas2[0]."-01' and '".$fechas2[12]."-31'")->get('tbcalificaciones');
			if($sql->num_rows()!=0){
				foreach ($sql->result() as $valoracion) {
					foreach ($matrispreguntas as $pregunta) {
						//doce meses pasados
						$sql=$this->db->select("sum(PuntosPosibles) as puntosposibles,sum(PuntosObtenidos) as puntosobtenidos")->where("DATE(FechaRealiza) between '".$fechas2[0]."-01' and '".$fechas2[12]."-31' and IDPregunta='$pregunta' and IDCalificacion='$valoracion->IDCalificacion'")->get('tbdetallescalificaciones');
						if($sql->num_rows()!=0){
							$puntosposibles2=$puntosposibles2+(int)$sql->result()[0]->puntosposibles;
							$puntosobtenidos2=$puntosobtenidos2+(int)$sql->result()[0]->puntosobtenidos;
						}
					}
				}
			}
			
			$_promedio_actual=_media_puntos($puntosobtenidos,$puntosposibles);
			$_promedio_pasado=_media_puntos($puntosobtenidos2,$puntosposibles2);
			
			 	
		}else if($Forma==="M"){
			//mes actual
			$sql=$this->db->select('IDCalificacion')->where("Status='Activa' and Emitidopara='$Tipo' and IDEmpresaReceptor='$IDEmpresa' and DATE(FechaRealizada) between '".date("Y")."-".(date("m")-1)."-".date("d")."' and '".date("Y-m-d")."'")->get('tbcalificaciones');
			
			if($sql->num_rows()!=0){
				foreach ($sql->row() as $valoracion) {
					foreach ($matrispreguntas as $pregunta) {
						$sql=$this->db->select("sum(PuntosPosibles) as puntosposibles,sum(PuntosObtenidos) as puntosobtenidos")->where("DATE(FechaRealiza) between '".date("Y")."-".(date("m")-1)."-".date("d")."' and '".date("Y-m-d")."' and IDPregunta='$pregunta' and IDCalificacion='$valoracion'")->get('tbdetallescalificaciones');
						

						if($sql->num_rows()!=0){
							$puntosposibles=$puntosposibles+(int)$sql->result()[0]->puntosposibles;
							$puntosobtenidos=$puntosobtenidos+(int)$sql->result()[0]->puntosobtenidos;
						}

					}
				}
			}
			$_promedio_actual=_media_puntos($puntosobtenidos,$puntosposibles);
			$puntosposibles=0;
			$puntosobtenidos=0;
			//mes anterior
			$sql=$this->db->select('IDCalificacion')->where("Status='Activa' and Emitidopara='$Tipo' and IDEmpresaReceptor='$IDEmpresa' and DATE(FechaRealizada) between '".$fechas[11]."-01' and '".$fechas[11]."-".date("d")."'")->get('tbcalificaciones');
			if($sql->num_rows()!=0){
				foreach ($sql->result() as $valoracion) {
					foreach ($matrispreguntas as $pregunta) {
						$sql=$this->db->select("sum(PuntosPosibles) as puntosposibles,sum(PuntosObtenidos) as puntosobtenidos")->where("DATE(FechaRealiza) between '".$fechas[11]."-01' and '".$fechas[11]."-".date("d")."' and IDPregunta='$pregunta' and IDCalificacion='$valoracion->IDCalificacion'")->get('tbdetallescalificaciones');
						if($sql->num_rows()!=0){
							$puntosposibles=$puntosposibles+(int)$sql->result()[0]->puntosposibles;
							$puntosobtenidos=$puntosobtenidos+(int)$sql->result()[0]->puntosobtenidos;
						}
					}
				}
			}
			$_promedio_pasado=_media_puntos($puntosobtenidos,$puntosposibles,"imagen");	
			}

			$data["incremento"]=_increment($_promedio_actual["num"],$_promedio_pasado["num"],"imagen");			
			$data["media"]=$_promedio_actual;
			return $data;
	}
	public function detalleImagen($forma,$IDEmpresa,$tipo)
	{
		
		$tipo_fecha=$tipo;
		$tipo_persona=strtolower($forma);
		$fechas=docemeces();
		$fechas2=docemecespasados();
		$_media_calidad_actual=0;
		$_media_calidad_pasado=0;
		$_media_cumplimiento_actual=0;
		$_media_cumplimiento_pasado=0;
		$_media_oferta_actual=0;
		$_media_oferta_pasado=0;
		$_media_general_actual=0;
		$_media_general_pasada=0;
		$_Numero_de_calificaciones_actual=0;
		$_Numero_de_calificaciones_pasado=0;
		
		//A = acumulado se va obtienido la suma de los menses desde enero asta el mes que se encuetra
		//M = ultimos 30 dias sin acumular
		// MA = Mes actual desde enero al mes actual sin acumular

		
		switch ($tipo_fecha){
			case 'AC':
			case 'MA':
				$_fecha_Inicio_Actual=date('Y')."-01-01";
				$_fecha_Fin_Actual=date('Y-m-d');
				$fecha_evolucion_inicio=explode("-",$_fecha_Inicio_Actual);
				$fecha_evolucion_fin=explode("-",$_fecha_Fin_Actual);
				$fechas_rango=$fechas;
				$PeriodoInicio= $fecha_evolucion_inicio[0]." ".da_mes($fecha_evolucion_inicio[1]);
				$PeriodoFinal=$fecha_evolucion_fin[0]." ".da_mes($fecha_evolucion_fin[1]);
				$_data['Periodo']=$PeriodoInicio."-".$PeriodoFinal;
			break;
			case 'M':
				$_fecha_Inicio_Actual=$fechas[11]."-".date("d");
				$_fecha_Fin_Actual=$fechas[12]."-".date("d");
				$fecha_evolucion_inicio=explode("-",$_fecha_Inicio_Actual);
				$fecha_evolucion_fin=explode("-",$_fecha_Fin_Actual);
				$fechas_rango=$fechas;
				$PeriodoInicio= $fecha_evolucion_inicio[2]." ".da_mes($fecha_evolucion_inicio[1])." ".$fecha_evolucion_inicio[0];
				$PeriodoFinal=$fecha_evolucion_fin[2]." ".da_mes($fecha_evolucion_fin[1])." ".$fecha_evolucion_fin[0];
				$_data['Periodo']=$PeriodoInicio."-".$PeriodoFinal;
		}
		
		
		/* ahora obtengo los giros que tiene asignados y muestro el principal*/
		$sql=$this->db->select('*')->where("IDEmpresa='$IDEmpresa' and Principal='1'")->get("giroempresa");
		$_Giro_Principal=$sql->row()->IDGiro2;
		
		/*
		///
		//primero necesito numero de calificaciones 
		//esto lo calculo con la suma de todas las calificaciones de la tabla de imagen ya sea de cliente o proveedor
		*/
		$tipo_persona = strtolower($tipo_persona);
		
		
		if($tipo_persona==="cliente"){
			
			
			
			$listacp=$this->ObtenerClientes($IDEmpresa);
			//traigo los registros de la tabla de imagen_cliente
			$promedios_actuales=$this->db->select("round(sum(P_Ob_Generales)/sum(P_Pos_Generales)*10,2) as mediageneral,round(sum(P_Obt_Calidad)/sum(P_Pos_Calidad)*10,2) mediacalidad,round(sum(P_Obt_Cumplimiento)/sum(P_Pos_Cumplimiento)*10,2) as mediacumplimiento,sum(N_Calificaciones)as numcalif")->where("IDEmpresa='$IDEmpresa' and date(Fecha) between '$_fecha_Inicio_Actual' and  '$_fecha_Fin_Actual' ")->get('tbimagen_cliente');
			
			
			
		}else{
			$listacp=$this->ObtenerProveedores($IDEmpresa);
			$listapreguntasoferta=$this->listpreguntas("Oferta",$forma,$_Giro_Principal);
			$promedios_actuales=$this->db->select("round(sum(P_Ob_Generales)/sum(P_Pos_Generales)*10,2) as mediageneral,round(sum(P_Obt_Calidad)/sum(P_Pos_Calidad)*10,2) mediacalidad,round(sum(P_Obt_Cumplimiento)/sum(P_Pos_Cumplimiento)*10,2) as mediacumplimiento,round(sum(P_Obt_Oferta)/sum(P_Pos_Oferta)*10,2) as mediaoferta,sum(N_Calificaciones)as numcalif")->where("IDEmpresa='$IDEmpresa' and date(Fecha) between '$_fecha_Inicio_Actual' and  '$_fecha_Fin_Actual' ")->get('tbimagen_proveedor');
		}

	
		

		
		$listapreguntascalidad=$this->listpreguntas("Calidad",$forma,$_Giro_Principal);
		$listapreguntascumplimento=$this->listpreguntas("Cumplimiento",$forma,$_Giro_Principal);
		$listapreguntassanidad=$this->listpreguntas("Sanidad",$forma,$_Giro_Principal);
		$listapreguntassocioambiental=$this->listpreguntas("Socioambiental",$forma,$_Giro_Principal);
		
			if($promedios_actuales->result()[0]->mediageneral!==NULL)
			{
				$_media_general_actual=$promedios_actuales->result()[0]->mediageneral;
				$_media_calidad_actual=$promedios_actuales->result()[0]->mediacalidad;	
				$_media_cumplimiento_actual=$promedios_actuales->result()[0]->mediacumplimiento;
				$_Numero_de_calificaciones_actual=$promedios_actuales->result()[0]->numcalif;
				if($tipo_persona==="proveedor"){
					$_media_oferta_actual=$promedios_actuales->result()[0]->mediaoferta;		
				}
				
			}
			

		

		$listapreguntascalidad=explode(",",$listapreguntascalidad->Calidad);
		$_data["listCalidad"]=$this->Grafics_($listapreguntascalidad,$tipo_fecha,$_fecha_Inicio_Actual,$_fecha_Fin_Actual,$IDEmpresa,$tipo_persona);
		
		$listapreguntascumplimento=explode(",",$listapreguntascumplimento->Cumplimiento);
		$_data["listCumplimiento"]=$this->Grafics_($listapreguntascumplimento,$tipo_fecha,$_fecha_Inicio_Actual,$_fecha_Fin_Actual,$IDEmpresa,$tipo_persona);
		
		$listapreguntassanidad=explode(",",$listapreguntassanidad->Sanidad);
		$_data["listSanidad"]=$this->Grafics_($listapreguntassanidad,$tipo_fecha,$_fecha_Inicio_Actual,$_fecha_Fin_Actual,$IDEmpresa,$tipo_persona);
		
		$listapreguntassocioambiental=explode(",",$listapreguntassocioambiental->Socioambiental);
		$_data["listSociambiental"]=$this->Grafics_($listapreguntassocioambiental,$tipo_fecha,$_fecha_Inicio_Actual,$_fecha_Fin_Actual,$IDEmpresa,$tipo_persona);
		
		
		if($tipo_persona==="proveedor")
		{
			$listapreguntasoferta=explode(",",$listapreguntasoferta->Oferta);
			$_data["listOferta"]=$this->Grafics_($listapreguntasoferta,$tipo_fecha,$_fecha_Inicio_Actual,$_fecha_Fin_Actual,$IDEmpresa,$tipo_persona);
		}
		$_data['MediaGenral']=$_media_general_actual;
		
		return $_data;

	}
	// funcion para obtener los datos de las graficas
	public function Grafics_($listadoPreguntas,$tipo_fecha,$_fecha_Inicio_Actual,$_fecha_Fin_Actual,$IDEmpresa,$tipo_persona){
		$listadatosp=[];
		$rangos=dame_rangos_fecha($tipo_fecha);
		$Rango_Promedio="'".$_fecha_Inicio_Actual."' and '".$_fecha_Fin_Actual."'";
		$acum_=false;
		$acum=true;
		//vdebug($listapreguntascalidad);
		foreach ($listadoPreguntas as $preguntacalidad) { 
			//primero vamos con calidad
			$totalprimero=0;
			$totalsegundo=0;
			$numeroclientesevaluados=0;
			$respuesta="";
			$datospregunta=$this->datos_preguntaID($preguntacalidad);
			
			if($datospregunta["Forma"]!="AB" || $datospregunta["Forma"]!="OP"){
				// primero obtengo el total de calificaciones y la media
				$respuesta=$this->promedioPregunta($IDEmpresa,$Rango_Promedio,$tipo_persona,$datospregunta['IDPregunta']);
				$evolucion=[];
				$evolucionlabel=[];

				$barrdataNum = [];
				$barrdata = [[],[],[],[],[]];
				$barlabel=[];
				$acum=0;
				foreach($rangos as $fecha){
					$_fechas_explode=explode('and',$fecha);
					$fecha_I=str_replace("'","",$_fechas_explode[1]);
					$date=explode("-",$fecha_I);
					//obtengo los datos de la grafica de evolucion
					$_datosP=$this->promedioPregunta($IDEmpresa,$fecha,$tipo_persona,$datospregunta['IDPregunta']);
					array_push($evolucionlabel,$date[0]."-".da_mes($date[1])."-".$date[2]);
					if($tipo_fecha==="M"){
						array_push($evolucion,(int)$respuesta['Total']);
					}else{
						if(count($evolucion)===0){
							array_push($evolucion,(int)$respuesta['Total']);
						}else{
							array_push($evolucion,($evolucion[count($evolucion)-1])+(int)$respuesta['Total']);
						}
					}
					

					// obtengo los datos de la grafica de barras
					$_dat=$this->cuantaspreguntascorrectas($IDEmpresa,$fecha,$tipo_persona,$datospregunta['IDPregunta'],$datospregunta['Forma']);
					
					if($datospregunta['Forma']==="DIAS" || $datospregunta['Forma']==="HORAS" || $datospregunta['Forma']==="NUM" ){
						array_push($barlabel,$date[0]."-".da_mes($date[1])."-".$date[2]);
						if($_dat['Total']===null){
							array_push($barrdataNum,0);
						}else{
							array_push($barrdata,(int)$_dat['Total']);
						}
						
					}
					
					if(trim($datospregunta['Forma'])==="Si/No/NA" || 
					trim($datospregunta['Forma'])==="Si/No" || 
					trim($datospregunta['Forma'])==="Si/No/NA/NS" || 
					trim($datospregunta['Forma'])==="Si/No/No Aplica" || 
					trim($datospregunta['Forma'])==="No tiene/NA/NS/Si/No"){
						if(isset($_dat['Total_Na'])){								
								if($tipo_fecha==='AC'){
								
									if( count($barrdata[0])=== 0){
										
										array_push($barrdata[0],(int)$_dat['Total_Si']);
										array_push($barrdata[1],(int)$_dat['Total_No']);
										array_push($barrdata[2],(int)$_dat['Total_Na']);
									
									}else{
										
										array_push($barrdata[0],$barrdata[0][count($barrdata[0])-1]+(int)$_dat['Total_Si']);
										array_push($barrdata[1],$barrdata[1][count($barrdata[1])-1]+(int)$_dat['Total_No']);
										array_push($barrdata[2],$barrdata[2][count($barrdata[2])-1]+(int)$_dat['Total_Na']);
										
									}
								}else{
									array_push($barrdata[0],(int)$_dat['Total_Si']);
									array_push($barrdata[1],(int)$_dat['Total_No']);
									array_push($barrdata[2],(int)$_dat['Total_Na']);
								}
								array_push($barlabel,array('Si','No','NA'));
						}else if(isset($_dat['Total_Ns']) && isset($_dat['Total_Na'])){
							
								if( $tipo_fecha==='AC'){
									if(count($barrdata[0])=== 0){
										array_push($barrdata[0],(int)$_dat['Total_Si']);
										array_push($barrdata[1],(int)$_dat['Total_No']);
										array_push($barrdata[2],(int)$_dat['Total_Na']);
										array_push($barrdata[3],(int)$_dat['Total_Ns']);
										
										
									}else{
										array_push($barrdata[0],$barrdata[0][count($barrdata[0])-1]+$_dat['Total_Si']);
										array_push($barrdata[1],$barrdata[1][count($barrdata[1])-1]+$_dat['Total_No']);
										array_push($barrdata[2],$barrdata[2][count($barrdata[2])-1]+$_dat['Total_Na']);
										array_push($barrdata[3],$barrdata[3][count($barrdata[3])-1]+$_dat['Total_Ns']);
									}
								}else{
										array_push($barrdata[0],(int)$_dat['Total_Si']);
										array_push($barrdata[1],(int)$_dat['Total_No']);
										array_push($barrdata[2],(int)$_dat['Total_Na']);
										array_push($barrdata[3],(int)$_dat['Total_Ns']);
								}
									
							
							array_push($barlabel,array('Si','No','NS','NA'));
						}else if(isset($_dat['Total_Nt']) && isset($_dat['Total_Na']) && isset($_dat['Total_Ns'])){
								if($tipo_fecha==='AC'){
									if(count($barrdata[0])=== 0){
										array_push($barrdata[0],(int)$_dat['Total_Si']);
										array_push($barrdata[1],(int)$_dat['Total_No']);
										array_push($barrdata[2],(int)$_dat['Total_Na']);
										array_push($barrdata[3],(int)$_dat['Total_Ns']);
										array_push($barrdata[4],(int)$_dat['Total_Nt']);
										
									}else{
										array_push($barrdata[0],$barrdata[0][count($barrdata[0])-1]+$_dat['Total_Si']);
										array_push($barrdata[1],$barrdata[1][count($barrdata[1])-1]+$_dat['Total_No']);
										array_push($barrdata[2],$barrdata[2][count($barrdata[2])-1]+$_dat['Total_Na']);
										array_push($barrdata[3],$barrdata[3][count($barrdata[3])-1]+$_dat['Total_Ns']);
										array_push($barrdata[4],$barrdata[4][count($barrdata[4])-1]+$_dat['Total_Nt']);
										
									}
								}else{
										array_push($barrdata[0],(int)$_dat['Total_Si']);
										array_push($barrdata[1],(int)$_dat['Total_No']);
										array_push($barrdata[2],(int)$_dat['Total_Na']);
										array_push($barrdata[3],(int)$_dat['Total_Ns']);
										array_push($barrdata[4],(int)$_dat['Total_Nt']);
										
								}
							
							array_push($barlabel,array('Si','No','NS','NA','NT'));
						}else if(isset($_dat['Total_No']) && isset($_dat['Total_Si'])){
						
									if( $tipo_fecha==='AC'){
									if(count($barrdata[0])=== 0){
										array_push($barrdata[0],(int)$_dat['Total_Si']);
										array_push($barrdata[1],(int)$_dat['Total_No']);
									}else{
										array_push($barrdata[0],$barrdata[0][count($barrdata[0])-1]+$_dat['Total_Si']);
										array_push($barrdata[1],$barrdata[1][count($barrdata[1])-1]+$_dat['Total_No']);
									}
								}else{
										array_push($barrdata[0],(int)$_dat['Total_Si']);
										array_push($barrdata[1],(int)$_dat['Total_No']);
								}
							array_push($barlabel,array('Si','No'));
					
					
					}
					
					
					
				}
				
				
				
				
			}	
			$grap = new stdClass();
			$grap->label="# de Calificaciones Recibidas";
			$grap->data=$evolucion;
			if($datospregunta['Forma']==="DIAS" || $datospregunta['Forma']==="NUM"){
				array_push($listadatosp,array("GraphisRs"=>array("data"=>$barrdataNum,"label"=>$barlabel),"evolucion"=>array('DataGrap'=>$grap,"label"=>$evolucionlabel),"IDPregunta"=>$datospregunta['IDPregunta'],"Forma"=>$datospregunta['Forma'],"Pregunta"=>$datospregunta['Pregunta'],'TotalCalificaciones'=>$respuesta['Total'],"Media"=>$respuesta['Media']));				
			}else{
				$explode=explode('/',$datospregunta['Forma']);
				$array_Color=['#10E0D0','#F2143F','#f7ac43','#F012BE','#605ca8'];
				
				$array_Tem=[];
				foreach($explode as $resp=>$item){
					$data_temp = new stdClass();
					$data_temp->label=$item;
					$data_temp->data=$barrdata[$resp];
					$data_temp->backgroundColor=$array_Color[$resp];
					
					array_push($array_Tem,$data_temp);
					
				}
				
				
				array_push($listadatosp,array("GraphisRs"=>$array_Tem,"evolucion"=>array('DataGrap'=>$grap,"label"=>$evolucionlabel),"IDPregunta"=>$datospregunta['IDPregunta'],"Forma"=>$datospregunta['Forma'],"Pregunta"=>$datospregunta['Pregunta'],'TotalCalificaciones'=>$respuesta['Total'],"Media"=>$respuesta['Media']));
				
				
			}
			
			
		}
	}
		return $listadatosp;
	}
	//funcion para saber cuantos contestaron esa pregunta la respuesta correcta
	public function cuantaspreguntascorrectas($IDEmpresa,$rangofecha,$para,$IDPregunta,$tipopregunta){
		$tipopregunta=trim($tipopregunta);
		
		if($tipopregunta==="DIAS" || $tipopregunta==="HORAS" || $tipopregunta==="NUM" ){
			$sql=$this->db->select("sum(Respuesta) as total")->from("tbcalificaciones")->join("tbdetallescalificaciones","tbdetallescalificaciones.IDCalificacion=tbcalificaciones.IDCalificacion")->where("tbcalificaciones.IDEmpresaReceptor=$IDEmpresa and date(FechaRealizada) between ".$rangofecha." and Emitidopara='$para' and IDPregunta='$IDPregunta'")->get();
				$_data['Total']=$sql->row()->total;
			
		}else if($tipopregunta==="Si/No/NA" || $tipopregunta==="Si/No" || $tipopregunta==="Si/No/NA/NS" || $tipopregunta="Si/No/No Aplica" || $tipopregunta==="No tiene/NA/NS/Si/No"){
			$Si_sql=$this->db->select("count(*) as totalSI")->from("tbcalificaciones")->join("tbdetallescalificaciones","tbdetallescalificaciones.IDCalificacion=tbcalificaciones.IDCalificacion")->where("tbcalificaciones.IDEmpresaReceptor=$IDEmpresa and date(FechaRealizada) between ".$rangofecha." and Emitidopara='$para' and IDPregunta='$IDPregunta' and Respuesta='SI'")->get();
			$No_sql=$this->db->select("count(*) as totalNO")->from("tbcalificaciones")->join("tbdetallescalificaciones","tbdetallescalificaciones.IDCalificacion=tbcalificaciones.IDCalificacion")->where("tbcalificaciones.IDEmpresaReceptor=$IDEmpresa and date(FechaRealizada) between ".$rangofecha." and Emitidopara='$para' and IDPregunta='$IDPregunta' and Respuesta='NO'")->get();
			$_data['Total_Si']=$Si_sql->row()->totalSI;
			$_data['Total_No']=$No_sql->row()->totalNO;
			switch($tipopregunta){
				case'Si/No/NA':
				case'Si/No/No Aplica':
					$NA_sql=$this->db->select("count(*) as totalNA")->from("tbcalificaciones")->join("tbdetallescalificaciones","tbdetallescalificaciones.IDCalificacion=tbcalificaciones.IDCalificacion")->where("tbcalificaciones.IDEmpresaReceptor=$IDEmpresa and date(FechaRealizada) between ".$rangofecha." and Emitidopara='$para' and IDPregunta='$IDPregunta' and Respuesta='NA'")->get();
					$_data['Total_Na']=$NA_sql->row()->totalNA;
				break;
				case'Si/No/NA/NS':
					$NA_sql=$this->db->select("count(*) as totalNA")->from("tbcalificaciones")->join("tbdetallescalificaciones","tbdetallescalificaciones.IDCalificacion=tbcalificaciones.IDCalificacion")->where("tbcalificaciones.IDEmpresaReceptor=$IDEmpresa and date(FechaRealizada) between ".$rangofecha." and Emitidopara='$para' and IDPregunta='$IDPregunta' and Respuesta='NA'")->get();
					$_data['Total_Na']=$NA_sql->row()->totalNA;
					$NS_sql=$this->db->select("count(*) as totalNS")->from("tbcalificaciones")->join("tbdetallescalificaciones","tbdetallescalificaciones.IDCalificacion=tbcalificaciones.IDCalificacion")->where("tbcalificaciones.IDEmpresaReceptor=$IDEmpresa and date(FechaRealizada) between ".$rangofecha." and Emitidopara='$para' and IDPregunta='$IDPregunta' and Respuesta='NS'")->get();
					$_data['Total_Ns']=$NA_sql->row()->totalNS;
				break;
				case"No tiene/NA/NS/Si/No":
					$NA_sql=$this->db->select("count(*) as totalNA")->from("tbcalificaciones")->join("tbdetallescalificaciones","tbdetallescalificaciones.IDCalificacion=tbcalificaciones.IDCalificacion")->where("tbcalificaciones.IDEmpresaReceptor=$IDEmpresa and date(FechaRealizada) between ".$rangofecha." and Emitidopara='$para' and IDPregunta='$IDPregunta' and Respuesta='NA'")->get();
					$_data['Total_Na']=$NA_sql->row()->totalNA;
					$NS_sql=$this->db->select("count(*) as totalNS")->from("tbcalificaciones")->join("tbdetallescalificaciones","tbdetallescalificaciones.IDCalificacion=tbcalificaciones.IDCalificacion")->where("tbcalificaciones.IDEmpresaReceptor=$IDEmpresa and date(FechaRealizada) between ".$rangofecha." and Emitidopara='$para' and IDPregunta='$IDPregunta' and Respuesta='NS'")->get();
					$_data['Total_Ns']=$NA_sql->row()->totalNS;
					$NT_sql=$this->db->select("count(*) as totalNT")->from("tbcalificaciones")->join("tbdetallescalificaciones","tbdetallescalificaciones.IDCalificacion=tbcalificaciones.IDCalificacion")->where("tbcalificaciones.IDEmpresaReceptor=$IDEmpresa and date(FechaRealizada) between ".$rangofecha." and Emitidopara='$para' and IDPregunta='$IDPregunta' and Respuesta='NT'")->get();
					$_data['Total_Nt']=$NT_sql->row()->totalNT;
				break;
			}
			
		}
		
		return $_data;
	}
	//funcion para obtener la calificacion de de una pregunta en un determinado tiempo
	public function promedioPregunta($IDEmpresa,$rangofecha,$para,$IDPregunta){
			$Total=$this->db->select("count(*) as total")->from("tbcalificaciones")->join("tbdetallescalificaciones","tbdetallescalificaciones.IDCalificacion=tbcalificaciones.IDCalificacion")->where("tbcalificaciones.IDEmpresaReceptor=$IDEmpresa and date(FechaRealizada) between ".$rangofecha." and Emitidopara='$para' and IDPregunta='$IDPregunta'")->get();
			$Media=$this->db->select("round(sum(PuntosObtenidos)/sum(PuntosPosibles)*10,2) as media")->from("tbcalificaciones")->join("tbdetallescalificaciones","tbdetallescalificaciones.IDCalificacion=tbcalificaciones.IDCalificacion")->where("tbcalificaciones.IDEmpresaReceptor=$IDEmpresa and date(FechaRealizada) between ".$rangofecha." and Emitidopara='$para' and IDPregunta='$IDPregunta'")->get();
			
			$data['Total']=$Total->row()->total;
			if($Media->row()->media === null){
				$data['Media']=0;
			}else{
				$data['Media']=$Media->row()->media;
			}
			
			
			return $data;
	}
	//function para modificar la imagen en esta parte agrego o modifico la tabla de imgen ya sea cliente o proveedor
	public function updateimagen(
		$_IDEmpresa,
		$_puntos_obs_general,
		$_puntos_pos_general,
		$_puntos_ob_calidad,
		$_puntos_pos_calidad,
		$_puntos_ob_cumplimiento,
		$_puntos_pos_cumplimiento,
		$_puntos_ob_oferta,
		$_puntos_pos_oferta,
		$_tipo_imagen,
		$_sub_giro
		){
			
			//ahora busco si es que hay algun registro de imagen en la fecha en la que se esta registrando la calificacion
			if($_tipo_imagen==="cliente"){
				$_datos_imagen=$this->db->select('*')->where("IDEmpresa='$_IDEmpresa' and Fecha='".date('Y-m-d')."'")->get('tbimagen_cliente');
			}else{
				$_datos_imagen=$this->db->select('*')->where("IDEmpresa='$_IDEmpresa' and Fecha='".date('Y-m-d')."'")->get('tbimagen_proveedor');
			}
			
			
				if($_puntos_obs_general===0 && $_puntos_pos_general===0){
					$num=0;
				}else{
					$num=round(($_puntos_obs_general/$_puntos_pos_general)*10,2);
				}
				
				//si no hay registro entonces agrego uno nuevo 
				$array=array(
					"IDEmpresa"=>$_IDEmpresa,
					"P_Ob_Generales"=>$_puntos_obs_general,
					"P_Pos_Generales"=>$_puntos_pos_general,
					"P_Obt_Calidad"=>$_puntos_ob_calidad,
					"P_Pos_Calidad"=>$_puntos_pos_calidad,
					"P_Obt_Cumplimiento"=>$_puntos_ob_cumplimiento,
					"P_Pos_Cumplimiento"=>$_puntos_pos_cumplimiento,
					"Ultima_Media"=>$num,
					"Fecha"=>date('Y-m-d'),
					"IDGiro"=>$_sub_giro
					);
					if($_tipo_imagen!=="Cliente"){
						$array["P_Obt_Oferta"]=$_puntos_ob_oferta;
						$array["P_Pos_Oferta"]=$_puntos_pos_oferta;
						$this->db->insert("tbimagen_proveedor",$array);
					}else{
						$this->db->insert("tbimagen_cliente",$array);
					}		
		
	}

	//funcion para obtener la media de la imagen de una empresa
	public function ImagenGen($IDEmpresa,$tipo_persona){
		
		if($tipo_persona==="cliente"){
			$tb='tbimagen_cliente';
			$linoferta="";
		}else{
			$tb='tbimagen_proveedor';
			$linoferta=",round(sum(P_Obt_Oferta)/sum(P_Pos_Oferta)*10,2) as mediaoferta";
		}
			//traigo los registros de la tabla de imagen_cliente
			$promedios_actuales=$this->db->select("round(sum(P_Ob_Generales)/sum(P_Pos_Generales)*10,2) as mediageneral,round(sum(P_Obt_Calidad)/sum(P_Pos_Calidad)*10,2) mediacalidad,round(sum(P_Obt_Cumplimiento)/sum(P_Pos_Cumplimiento)*10,2) as mediacumplimiento,sum(N_Calificaciones)as numcalif".$linoferta)->where("IDEmpresa='$IDEmpresa' ")->get($tb);
			
			if($promedios_actuales->result()[0]->mediageneral === NULL){
				return 0;
			}else{
				return $promedios_actuales->result()[0]->mediageneral;
			}
			
		
	}

	// funcion para obtener la imagen de una empresa con fecha general
	public function ImagenGenFecha($IDEmpresa,$tipo_persona,$periodo){
		$_categorias  = $this->Model_Configuraciongeneral->getCategorias();
		$rangos=_fechas_array_List($periodo);
		//vdebug($rangos);
		if( $periodo === 'M'){
			$fecha_Inicio=$rangos[0];
			$fecha_Fin=$rangos[count($rangos)-1];
		}else{
			$fecha_Inicio=$rangos[0]."-01";
			$fecha_Fin=$rangos[count($rangos)-1].date('-d');
		}

		$data['periodo'] = $fecha_Inicio.' / '.$fecha_Fin;
		
		if($tipo_persona==="cliente"){
			$tb='tbimagen_cliente';
			$linoferta="";
		}else{
			$tb='tbimagen_proveedor';
			$linoferta=",round(sum(P_Obt_Oferta)/sum(P_Pos_Oferta)*10,2) as mediaoferta";
			array_push($_categorias,'Oferta');
		}
			//traigo los registros de la tabla de imagen_cliente
			$strin_cadena = "count(*) AS numcalif,round(sum(P_Ob_Generales)/sum(P_Pos_Generales)*10,2) as mediageneral,";
			
			for($i=0; $i<=count($_categorias)-1;$i++){
				$item = $_categorias[$i];
				$strin_cadena = $strin_cadena."round(sum(P_Obt_$item)/sum(P_Pos_$item)*10,2) as media$item";
				if($i!==count($_categorias)-1){
					$strin_cadena = $strin_cadena. ",";
				}
			}
			$strin_cadena = $strin_cadena.$linoferta;
			$promedios_actuales=$this->db->select($strin_cadena)->where("IDEmpresa='$IDEmpresa' and date(Fecha) BETWEEN '$fecha_Inicio' and '$fecha_Fin'")->get($tb);
			$respuesta = $promedios_actuales->result_array();
			if($respuesta[0]['mediageneral'] === NULL){
				$data['mediageneral'] = 0;
			}else{
				$data['mediageneral']= $respuesta[0]['mediageneral'];
			}
			foreach ($_categorias as $value) {
				
				$cadena = "media$value";
				$miniscula = strtolower($cadena);
				if($respuesta[0][$cadena] === NULL){
					$data[$miniscula ] = 0;
				}else{
					$data[$miniscula]= $respuesta[0][$cadena ];
				}
			}
			if($respuesta[0]['numcalif'] === NULL){
				$data['numcalif'] = 0;
			}else{
				$data['numcalif']= $respuesta[0]['numcalif'];
			}
			$data['categorias'] = $_categorias;
			return $data;
	}

	
}