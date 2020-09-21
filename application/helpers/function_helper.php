<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
if(!function_exists("generate_clave")){
	function generate_clave(){
	  $cadena_base =  'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
	  $cadena_base .= '0123456789' ;
	  $cadena_base .= '!@#%^&*()_,./<>?;:[]{}\|=+';
	 
	  $password = '';
	  $limite = strlen($cadena_base) - 1;
	 
	  for ($i=0; $i < 7; $i++)
	    $password .= $cadena_base[rand(0, $limite)];
	 
	  return $password;
	}
}
if(!function_exists("converter_cvs")){
	//convertir a cvs
	function converter_cvs($array,$titulo,$cabezeras){
		$output=fopen("php://output", 'W')or die("Can't open php://output");
		header('Content-Encoding: UTF-8');
    	header('Content-Type: text/csv; charset=utf-8' );
		header("Content-Disposition:attachment;filename=".$titulo.".csv"); 
		header('Content-Transfer-Encoding: binary');
	 	fputs( $output, "\xEF\xBB\xBF" ); 
		fputcsv($output, $cabezeras);
 
		foreach($array as $pregunta) {
		    fputcsv($output,$pregunta);
		}
		fclose($output) or die("Can't close php://output");
		}
}

if(!function_exists("_compracion"))
{
		function _comparacion(float $numero1,float $numero2)
		{
			
			if(strval($numero1)===strval($numero2)){
				return 1;
			}else if($numero1>$numero2){
				return 2;
			}elseif($numero1<$numero2){
				return 3;
			}
		}

}
if (!function_exists("promedio_array_riesgo")) {
	function promedio_array_riesgo($array)
	{
		$arriba = 0;
		$abajo = 0;
		$promedio=0;
		//vdebug($array);
		try{
			foreach ($array as $item) {
				
				$arriba = $arriba + ($item['porcentaje'] * $item['Peso']);
				$abajo = $abajo + $item['Peso'];
			}
			if ($abajo === 0) {
				return 0;
			}
			$promedio = $arriba / $abajo;
			return $promedio;
		}catch(Exception $e){
			
		}
		
	}
}
if (!function_exists("_fechas_array_List")) {
	function _fechas_array_List($_Tiempo)
	{
		$_fecha = [];

		if ($_Tiempo === 'A' || $_Tiempo === 'MA') {
			for ($i = 1; $i <= (int)date('m'); $i++) {
				if($i<10){
					$mes = '0'.$i;
				}else{
					$mes = $i;
				}
				array_push($_fecha,  date('Y')."-".$mes);
				
			}
		}

		if ($_Tiempo === 'M') {

			$fechaactual = date('Y-m-d');
			$fechaanterior = date('Y-m-d', strtotime($fechaactual . "- 31days"));

			for ($i = $fechaanterior; $i <= $fechaactual; $i = date("Y-m-d", strtotime($i . "+ 1 days"))) {

				array_push($_fecha, $i);
				
			}
		}
		return $_fecha;
	}
}
if (!function_exists("_fechas_array")){
	function _fechas_array($_Tiempo,$Fechas)
	{
		$_fecha=[];
		
		if($_Tiempo==='A' || $_Tiempo === 'MA'){
			for($i=5;$i<=count($Fechas)-1;$i++){
				
				$mes_anio=explode('-', $Fechas[$i]);
				array_push($_fecha, ['mes' => $mes_anio[1], "anio" => $mes_anio[0],"anio_anterior"=> $mes_anio[0]-1]);
			}
		}

		if($_Tiempo === 'M'){
		
			$fechaactual=date('Y-m-d');
			$fechaanterior=date('Y-m-d', strtotime($fechaactual."- 31days"));

			for ($i = $fechaanterior; $i <= $fechaactual; $i = date("Y-m-d", strtotime($i . "+ 1 days"))) {
			
				array_push($_fecha,$i);

			}

		}
		return $_fecha;
	}
}
if(!function_exists("validar_clave")){
	function validar_clave($clave){
   if(strlen($clave) < 6){
   	  $_data["pass"]=0;
      $_data["mensaje"] = "La clave debe tener al menos 6 caracteres";
   }else if(strlen($clave) > 16){
     $_data["pass"]=0;
      $_data["mensaje"] = "La clave no puede tener más de 16 caracteres";
    
   }else if (!preg_match('`[a-z]`',$clave)){
     $_data["pass"]=0;
      $_data["mensaje"] ="La clave debe tener al menos una letra minúscula";
     
   }else if (!preg_match('`[A-Z]`',$clave)){
     $_data["pass"]=0;
      $_data["mensaje"] ="La clave debe tener al menos una letra mayúscula";
    
   }else if (!preg_match('`[0-9]`',$clave)){
      $_data["pass"]=0;
      $_data["mensaje"] = "La clave debe tener al menos un caracter numérico";
     
   }else{
   	 $_data["pass"]=1;
   }
   return $_data;
   
}
}
if(!function_exists("limpiar_array")){
	function limpiar_array($array){
		foreach ($arry as $item) {
			
		}
	}	
}
if(!function_exists("_media_puntos"))
{
	function _media_puntos($_puntos_obtenidos,$_puntos_posibles,$_IDPRegunta=0){
		
		
		if(strval($_puntos_obtenidos)==="0" && strval($_puntos_posibles) === "0"){
			$num=0;
			
		}else{
			
			$num=(float)$_puntos_obtenidos/(float)$_puntos_posibles;
			$num=$num*10;
			$num=round($num,2);
			
		}
		
		if($num===0){
				$_data["class"]="neutro";
		}else if($num>0){
				$_data["class"]="green";
		}else if($num<0){
				$_data["class"]="red";
		}
		$_data["num"]=$num;
		return $_data;
	}
}
if(!function_exists('in_array_r')){
	function in_array_r($needle, $haystack, $strict = false) {
		foreach ($haystack as $item) {
			if (($strict ? $item === $needle : $item == $needle) || (is_array($item) && in_array_r($needle, $item, $strict))) {
				return true;
			}
		}
	
		return false;
	}
}
if(!function_exists('_increment'))
{
	function _increment($a,$b,$c)
	{
		($a===null)?$a=0:$a=$a;
		($b===null)?$b=0:$b=$b;
		
		$num=0;
		$_data=[];
		if(strval($a)==="0" && strval($b)==="0"){
			$num=0;
		}else if($a === null && $b===null){
			$num=0;
		}else if(strval($b)==="0"){
			$num=100;
		}else if(strval($a)==="0"){
			$num=-100;
			
		}else{
			$num=round((((float)$a-(float)$b)/(float)$b)*100,2);
		}
		
		if($c==="imagen"){
			if($num===0){
				$_data["class"]="neutro";
			}else if($num>0){
				$_data["class"]="green";
			}else if($num<0){
				$_data["class"]="red";
			}
			$_data["num"]=$num."%";
		}else{
			if($num===0){
				$_data["class"]="neutro";
			}else if($num>0){
				$_data["class"]="green";
			}else if($num<0){
				$_data["class"]="red";
			}
			$_data["num"]=$num."%";
		}
		
		return $_data;
	}
}
if(!function_exists('_build_joson'))
{
	function _build_json($_status=FALSE,$_data=FALSE,$_controller=FALSE)
	{
		$CI= &get_instance();
		if(!(boolean)$_status)
		{
			if(isset($_data['message_identifier']))
			{
				if((boolean)$_controller)
					$_data["message"]=$CI->lang->line($CI->data["controller"].$_data["message_identifier"]);
				else
					$_data["message"]=$CI->lang->line($_data["message_identifier"]);
			}
			else
			{
					$_data["message"]=$CI->lang->line("_cannot_complete");
			}
		}
		$_data["status"]=$_status;
		exit(json_encode($_data));
	}
}
if(!function_exists('_is_ajax_request'))
{
	function _is_ajax_request()
	{
		$CI= &get_instance();
		if(!$CI->input->is_ajax_request())
			_build_json();
	}
}
if(!function_exists('_is_post'))
{
	function _is_post()
	{
		if($_SERVER['REQUEST_METHOD']!=='POST')
			_build_json();
	}	
}
if(!function_exists("_media_puntos"))
{
	function _media_puntos($_puntos_obtenidos,$_puntos_posibles){
		
		if($_puntos_obtenidos===0 && $_puntos_posibles===0){
			$num=0;
		}else{
			$num=round(($_puntos_obtenidos/$_puntos_posibles)*10,2);
		}

		if($num===0){
				$_data["class"]="text-blue";
		}else if($num>0){
				$_data["class"]="text-success";
		}else if($num<0){
				$_data["class"]="text-red";
		}
		$_data["num"]=$num;
		return $_data;
	}
}
if(!function_exists("_comentario")){
	function comentario($fechainicio,$fechafin,$veces){
		//primero verifico cuantos dias han pasado de una fecha a otra fecha
		$meses=["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
		$_fecha_inicio=explode("/",$fechainicio);
		$_fecha_fin=explode("/",$fechafin);
		$_mes_inicio=$meses[(int)$_fecha_inicio[1]-1];
		$_mes_fin=$meses[(int)$_fecha_fin[1]-1];
		$fecha1 = strtotime($fechainicio);
        $fecha2 = strtotime($fechafin);
		$diferencia=$fecha2-$fecha1;
		$dias=(( ( $diferencia / 60 ) / 60 ) / 24);
		if((int)$dias===1){
			$dias="1 dia";
		}else{
			$dias="los ".$dias." dias";
		}
		if((int)$veces===1){
			$veces="una 1 vez";
		}else{
			$veces=$veces." veces";
		}
		$cadena="Los resultados estan basados en $veces que fue contestado este cuestionario, en $dias que van del $_fecha_inicio[2] de $_mes_inicio del $_fecha_inicio[0] a $_fecha_fin[2] de $_mes_fin del $_fecha_fin[0]";
		return $cadena;

	}
}
if(!function_exists("dame_mes"))
{
	function dame_mes($index){
		$meses=["Enero","Febrero","Marzo","Abril","Mayo","Junio","Julio","Agosto","Septiembre","Octubre","Noviembre","Diciembre"];
		return $meses[(int)$index-1];

	}
	

}




if (!function_exists("dame_rangos_fecha")) {
	function dame_rangos_fecha($tiempo)
	{
		$array=[];
		if($tiempo === 'MA'|| $tiempo==="AC"){
			$mes_I=(int)date('m');
			for($i=1;$i<= $mes_I;$i++){
				($i<10)? $mes='0'.$i:$mes=$i;
				array_push($array,"'".date('Y').'-'.$mes."-01' and '".date('Y').'-'.$mes."-31'");
			};
		}
		if($tiempo === 'M'){
			$mes=(int)date('m')-1;
			$anio=date('Y');
			$inicio = (int)date("d");
			$para = 31;
			($mes < 10) ? $_mes = '0' . $mes : $_mes = $mes;
			while($inicio<=$para){
				
				($inicio < 10) ? $dia = '0' . $inicio : $dia = $inicio;
				array_push($array, "'" . $anio . '-' . $_mes . "-" .  $dia . "' and '" . $anio . '-' . $_mes . "-" .  $dia . "'");

				if($inicio === 31){
					$para = date("d");
					$inicio = 1;
					$_mes = date('m');
					($mes < 10) ? $mes = '0' . $_mes : $_mes = $mes;

				}else{
					$inicio++;
				}
			}	
		}
		return $array;
	}
}
if(!function_exists("comentario_notificaciones")){
	function comentario_notificaciones($descripcion,$_ID_Empresa,$Razon_Social){
		$data=[];
		if($descripcion==="vista"){
			$data["link"]="/perfil/".$_ID_Empresa;
			$data["descrip"]="La empresa ".$Razon_Social." ha visitado su perfil";
		}
		if($descripcion==="Follow"){
			$data["link"]="/perfil/".$_ID_Empresa;
			$data["descrip"]="Esta siguiendo su perfil";
		}
		if($descripcion==="calificacionC"){
			$data["link"]="/imagencliente/".$_ID_Empresa;
			$data["descrip"]="La empresa  <strong>".$Razon_Social." </strong> lo ha calificado como Cliente.";
		}
		if($descripcion==="calificacionp"){
			$data["link"]="/imagenproveedor/".$_ID_Empresa;
			$data["descrip"]="La empresa  <strong>".$Razon_Social." </strong> lo ha calificado como Proveedor.";
		}
		if($descripcion==="calificacionrp"){
			$data["link"]="/imagenproveedor/".$_ID_Empresa;
			$data["descrip"]="La empresa  <strong>".$Razon_Social."</strong> lo ha recalificado como Proveedor";
		}
		if($descripcion==="calificacionrc"){
			$data["link"]="/imagencliente/".$_ID_Empresa;
			$data["descrip"]="La empresa <strong>".$Razon_Social."</strong> lo ha recalificado como Cliente";
		}
		return $data;
	}
}

if(!function_exists("_is_respcorrect"))
{
	function _is_respcorrect($respuesta_correcta,$respuesta,$calificacion,$tipopregunta){
		if($tipopregunta==="HORAS"){
			$dias=(int)$respuesta;
			$dias=1-(int)$dias/24;
			$peso=(float)$calificacion;
			$_calificacion=$peso*$dias;
			return $_calificacion;
		}
		if($tipopregunta==="SEGUNDOS"){
			$dias=(int)$respuesta;
			$dias=1-(int)$dias/60;
			$peso=(float)$calificacion;
			$_calificacion=$peso*$dias;
			return $_calificacion;
		}
		
		if($tipopregunta==="MINUTOS"){
			$dias=(int)$respuesta;
			$dias=1-(int)$dias/60;
			$peso=(float)$calificacion;
			$_calificacion=$peso*$dias;
			return $_calificacion;
		}

		if($tipopregunta==="DIAS"){
			$dias=(int)$respuesta;
			$dias=1-(int)$dias/34;
			$peso=(float)$calificacion;
			$_calificacion=$peso*$dias;
			return $_calificacion;
		}
		if($tipopregunta==="SI/NO" || $tipopregunta==="SI/NO/NA" || $tipopregunta==="SI/NO/NS"){
			if($respuesta==="NA" || $respuesta==="NS"){
				return $_calificacion=0;
			}else{
				if($respuesta_correcta!==$respuesta){
					return $_calificacion=0;
				}else{
					return $_calificacion=$calificacion;
				}
			}
		}
	}	
}

if(!function_exists("procentaje_riesgo")){
	function procentaje_riesgo($a,$b)
	{

		if((int)$b===0 || (int)$a === 0)
		{
			return 0;
		}
		return ($a/$b)*100;
	}
}


if(!function_exists("token1")){
	 function token1($pass){
		$fecha = date_create();
		$time = date_timestamp_get($fecha)/30;
		$key=$pass;
		//"GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ";
		$key=decode($key);
		//$time=44376117.366667;
		$x=dechex(floor($time)-1);
		
		while (strlen($x)<16){
			$x="0".$x;
		}
		$y=str_split($x,2);
		for ($a=0;$a<count($y);$a++){
			$y[$a]=hexdec($y[$a]);
		}
		
		$cad="";
		for ($a=0;$a<count($y);$a++){
			$cad.=chr($y[$a]);
		}
		$hm=hash_hmac("sha1",$cad,$key);
		$hmac=unpack("C*",pack("H*", $hm));
		$bin=substr($hm,-1);
		$offset=hexdec($bin); // mask 0xf
		$part1 = substr($hm,0, ($offset * 2));
		$part2 = substr($hm,($offset * 2),8);
		$part3 = substr($hm,($offset * 2)+8,strlen($hm)-$offset);
        $top=(hexdec(substr($hm,($offset * 2), 8)) & hexdec('7fffffff'));
		$otp = substr($top,strlen($top) - 6, 6);
		return $otp;
	}
}
if(!function_exists("token")){
	function token($pass){
		$fecha = date_create();

		$time = date_timestamp_get($fecha)/30;

		$key=$pass;
		//"GEZDGNBVGY3TQOJQGEZDGNBVGY3TQOJQ";
		$key=decode($key);

		//$time=44376117.366667;
		$x=dechex(floor($time));
		while (strlen($x)<16){
			$x="0".$x;
		}

		$y=str_split($x,2);

		for ($a=0;$a<count($y);$a++){
			$y[$a]=hexdec($y[$a]);
		}
		
		$cad="";
		for ($a=0;$a<count($y);$a++){
			$cad.=chr($y[$a]);
		}

		$hm=hash_hmac("sha1",$cad,$key);
		$hmac=unpack("C*",pack("H*", $hm));
		$bin=substr($hm,-1);
		$offset=hexdec($bin); // mask 0xf
		$part1 = substr($hm,0, ($offset * 2));
		$part2 = substr($hm,($offset * 2),8);
		$part3 = substr($hm,($offset * 2)+8,strlen($hm)-$offset);
        $top=(hexdec(substr($hm,($offset * 2), 8)) & hexdec('7fffffff'));
		$otp = substr($top,strlen($top) - 6, 6);
		return $otp;
	}
}
if(!function_exists("encode")){
	function encode($string)
	    {
	    	$alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567=';
	        if (strlen($string) == 0) {
	            // Gives an empty string
	            return '';
	        }
	        // Convert string to binary
	        $binaryString = '';
	        foreach (str_split($string) as $s) {
	            // Return each character as an 8-bit binary string
	            $binaryString .= sprintf('%08b', ord($s));
	        }
	        // Break into 5-bit chunks, then break that into an array
	        $binaryArray = chunk($binaryString, 5);
	        // Pad array to be divisible by 8
	        while (count($binaryArray) % 8 !== 0) {
	            $binaryArray[] = null;
	        }
	        $base32String = '';
	        // Encode in base32
	        foreach ($binaryArray as $bin) {
	            $char = 32;
	            if (!is_null($bin)) {
	                // Pad the binary strings
	                $bin = str_pad($bin, 5, 0, STR_PAD_RIGHT);
	                $char = bindec($bin);
	            }
	            // Base32 character
	            $base32String .= $alphabet[$char];
	        }
	        return $base32String;
	    }
}

 function decode($base32String)    {
        // Only work in upper cases
		$alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567=';
        $base32String = strtoupper($base32String);
        // Remove anything that is not base32 alphabet
        $pattern = '/[^A-Z2-7]/';

        $base32String = preg_replace($pattern, '', $base32String);

        if (strlen($base32String) == 0) {
            // Gives an empty string
            return '';
        }
        $base32Array = str_split($base32String);

        $string = '';
        foreach ($base32Array as $str) {
            $char = strpos($alphabet, $str);
           
            // Ignore the padding character
            if ($char !== 32) {
                $string .= sprintf('%05b', $char);
            }

        }
        while (strlen($string) %8 !== 0) {
            $string = substr($string, 0, strlen($string)-1);
        }

        $binaryArray = chunk($string, 8);

        $realString = '';

        foreach ($binaryArray as $bin) {
            // Pad each value to 8 bits
            $bin = str_pad($bin, 8, 0, STR_PAD_RIGHT);

            // Convert binary strings to ASCII
            $realString .= chr(bindec($bin));

        }

        return $realString;
    }
    function chunk($binaryString, $bits)    {
        $binaryString = chunk_split($binaryString, $bits, ' ');
        if (substr($binaryString, (strlen($binaryString)) - 1)  == ' ') {
            $binaryString = substr($binaryString, 0, strlen($binaryString)-1);
        }
        return explode(' ', $binaryString);
    }	

