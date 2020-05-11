<?php

function seleccolor($num){
	if($num==0){
		$class="bgazul";
	}
	if(($num>=1) && ($num<=3.9)){
		$class="bgrojo";
	}
	if(($num>=4) && ($num<=7.9)){
		$class="bgamarillo";
	}
	if(($num>=8) && ($num<=10)){
		$class="bgverde";
	}
	return $class;

}
function docemeces(){
  $fechas=[];
    for($i=12;$i>=0;$i--){ 
      array_push($fechas,date("Y-m",mktime(0,0,0,date("m")-$i,date("d"),date("Y"))));
    } 
    return $fechas;
}
function docemecespasados(){
  $fechas=[];
    for($i=12;$i>=0;$i--){ 
      array_push($fechas,date("Y-m",mktime(0,0,0,date("m")-$i,date("d"),date("Y")-2)));
    } 
    return $fechas;
}
function seleccolorimg($num){
	if($num==0){
		$class="sello-azul.png";
	}
	if(($num>=1) && ($num<=3.9)){
		$class="sello-rojo.png";
	}
	if(($num>=4) && ($num<=7.9)){
		$class="sello-amarillo.png";
	}
	if(($num>=8) && ($num<=10)){
		$class="sello-verde.png";
	}
	return $class;

}
function tamleta($num){
	$clas="12px";
	if(strlen($num)>=50){
		$clas="3.5vh";
	}
	if((strlen($num)>=40) && (strlen($num)<=49)){
		$clas="5vh";
	}
	if((strlen($num)>=30) && (strlen($num)<=39)){
		$clas="5.8vh";
	}
	if((strlen($num)>=20) && (strlen($num)<=29)){
		$clas="6.5vh";
	}
	if((strlen($num)>=1) && (strlen($num)<=19)){
		$clas="7.5vh";
	}
	return $clas;
}
function dametextocalifi($total,$total1){
			$total=$total+$total1;
			$p="";
			switch($total) {
				case $total<=0:
				$p="Sin Calificaciones";
				break;
				case $total>=1 && $total<=50:
				$p="Menos de 50 Calificaciones";
				break;
				case $total>50 && $total<=150:
				$p="Menos de 150 Calificaciones";
				break;
				case $total>150 && $total<=250:
				$p="Menos de 250 Calificaciones";
				break;
				case $total>250 && $total<=500:
				$p="Menos de 500 Calificaciones";
				break;
				case $total>500 && $total<=1000:
				$p="Menos de 1000 Calificaciones";
				break;
				case $total>1000:
				$p="Mas de 1000 Calificaciones";
				break;
			}
			return $p;
}
function porcentaje($total,$numero){
	if($numero!=0){
		$p=$numero/$total*100;
		return round($p,2);
	}else{
		return round(0,2);
	}
		
	
	
}
function restar_fecha($inicio,$fin){
$fechainicial = new DateTime($inicio);
$fechafinal = new DateTime($fin);
$diferencia = $fechainicial->diff($fechafinal);
return $diferencia->days;
}
function da_mes($mes){
  $mes=(int)$mes;
	$meses=["Ene","Feb","Mar","Abr","May","Jun","Jul","Ago","Sep","Oct","Nov","Dic"];
  ;
  return $meses[$mes-1];
}
function ms_confirmpago($email,$Razon_Social,$dia,$mes,$anio,$total){
	$mail = new PHPMailer();
	//$mail->Host = "localhost";
	$mail->IsSendmail();
	$mail->SMTPAuth = true;
	$mail->Host       = "smtp.1and1.es";
	$mail->SMTPSecure = "tls";
    $mail->Port       = 587;
    $mail->Username   = "leonardo@admyo.com";
    $mail->Password   = "1017854Leo/";
    $mail->CharSet = "UTF-8";
        $mail->ContentType = "text/html";
        $mail->From = "no-reply@admyo.com";
        $mail->FromName = "Admyo";
    $mail->Subject = "¡Confirmación de Pago Admyo!";
	$mail->AddAddress($email);
 $body ='<html>
                      <head>
                         <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                              <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
                          <style type="text/css">
                           body{font-family: "arial";}p{text-align: justify;font-size: 11pt;color: #878788;}
                           .container {margin-right: auto;margin-left: auto; width: 100%;}.col-sm-7 {width: 90%;}.img-responsive{display: block;max-width: 100%;height: auto;}
                           h3{font-size: 18pt;color: #005288;font-style: italic;font-weight: bold;}button{border-radius: 10px;border: 2px solid #e96610;padding: 15px 75px;cursor:pointer;background-color:#e96610;color: #ffffff;}h4{text-align: justify;}h5{text-align: justify;}
                         </style>
                      </head>
                      <body>
                      <div class="container">
                         <center><div class="col-sm-7">
                          <img class="img-responsive" src="https://admyo.com/images/images-mail/header-admyo-notificacion-cambio-valoracion-empresacalificada.jpg"/>
                        </div></center>
                        <center><div class="col-sm-7">
                          <div class="col-sm-12">
                          <center><br><h3>Confirmacion de Pago</h3></center>
                        </div>
                          <p>Hola '.$Razon_Social.'</p>
                          <p>Se ha confirmado tu pago el dia '.$dia.' de '.$mes.' de '.$ano.', por un total de $'.$total.'.</p>
                          <p>Gracias por suscribirte Has completado tu suscripción ahora ya puedes comenzar a calificar a tus clientes y provedores.</p>                       
                          <p>Saludos.<br> 
                      <font color="#005288" style="font-weight: bold;">Equipo admyo</font></p>     
                      <div class="col-sm-12" style="border-width: 1px; border-style: dashed; border-color: #fcb034; "></div>
                      <div class="col-sm-12"><br><p><font color="#cc9829" >“The most important thing for a young man is to establish credit - a reputation and character”.<br><font style="font-weight: bold;">John D. Rockefeller</font></font></p></div>
<div class="col-sm-12"><p>IMPORTANTE: El presente correo electrónico es confidencial y/o puede contener información privilegiada. 
Su contenido no pretende ni debe considerarse como constitutivo de ninguna relación legal, contractual 
o de otra índole similar.</p></div>
                      </div></center></div>
</body>
</html>';
      $mail->Body = $body;
      $mail->Send();
      
      return $mail;
}
function ms_recalificacion1($Razon_Valoradora,$Razon_valorada,$calificacion,$Tipo,$email,$tipo_contrario,$preguntas){
	$mail = new PHPMailer();
	//$mail->Host = "localhost";
	$mail->IsSendmail();
	$mail->SMTPAuth = true;
	$mail->Host       = "smtp.1and1.es";
	$mail->SMTPSecure = "tls";
    $mail->Port       = 587;
    $mail->Username   = "leonardo@admyo.com";
    $mail->Password   = "1017854Leo/";
    $mail->CharSet = "UTF-8";
        $mail->ContentType = "text/html";
        $mail->From = "no-reply@admyo.com";
        $mail->FromName = "Admyo";
    $mail->Subject = "¡Ha Recibido una Calificación en ADMYO!";
	$mail->AddAddress($email);
	$html="";
						$linea=explode("|*|",$preguntas);
            for ($i=1; $i < count($linea); $i++) { 
              $datos=explode("|",$linea[$i]);
              $html.='<tr><td>'.$datos[0].'</td><td style="text-align:center;">'.$datos[1].'</td></tr>';
            }
						$body = 
                                '<html>
                      <head>
                           <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                         <style type="text/css">
                         @import url(http://fonts.googleapis.com/css?family=Patua+One|Open+Sans);
                           body{font-family: "arial";}p{text-align: justify;font-size: 11pt;color: #878788;}
                           .container {margin-right: auto;margin-left: auto; width: 100%;}.col-sm-7 {width: 90%;}.img-responsive{display: block;max-width: 100%;height: auto;}
                           h3{font-size: 18pt;color: #005288;font-style: italic;font-weight: bold;}button{border-radius: 10px;border: 2px solid #e96610;padding: 15px 75px;cursor:pointer;background-color:#e96610;color: #ffffff;}h4{text-align: justify;}h5{text-align: justify;}
                           table {
  border-collapse: separate;
  border: 4px solid #fff;  
  background: #fff;
  -moz-border-radius: 5px;
  -webkit-border-radius: 5px;
  border-radius: 5px;
  margin: 20px auto;
  -moz-box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
  -webkit-box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
  box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
}

thead {
  -moz-border-radius: 8px;
  -webkit-border-radius: 8px;
  border-radius: 8px;
}

thead td {
  font-family: "Open Sans", sans-serif;
  font-size: 23px;
  font-weight: 400;
  color: #fff;
  text-shadow: 1px 1px 0px rgba(0, 0, 0, 0.5);
  text-align: left;
  padding: 20px;
  background-image: url("data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4gPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PGxpbmVhckdyYWRpZW50IGlkPSJncmFkIiBncmFkaWVudFVuaXRzPSJvYmplY3RCb3VuZGluZ0JveCIgeDE9IjAuNSIgeTE9IjAuMCIgeDI9IjAuNSIgeTI9IjEuMCI+PHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iIzY0NmY3ZiIvPjxzdG9wIG9mZnNldD0iMTAwJSIgc3RvcC1jb2xvcj0iIzRhNTU2NCIvPjwvbGluZWFyR3JhZGllbnQ+PC9kZWZzPjxyZWN0IHg9IjAiIHk9IjAiIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JhZCkiIC8+PC9zdmc+IA==");
  background-size: 100%;
  background-image: -webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(0%, #005d8f), color-stop(100%, #4a5564));
  background-image: -moz-linear-gradient(#005d8f, #004266);
  background-image: -webkit-linear-gradient(#005d8f, #004266);
  background-image: linear-gradient(#005d8f, #004266);
  border-top: 1px solid #005d8f;
}
thead th:first-child {
  -moz-border-radius-topleft: 8px;
  -webkit-border-top-left-radius: 8px;
  border-top-left-radius: 8px;
}
thead th:last-child {
  -moz-border-radius-topright: 8px;
  -webkit-border-top-right-radius: 8px;
  border-top-right-radius: 8px;
}

tbody tr td {
  font-family: "Open Sans", sans-serif;
  font-weight: 400;
  color: #5f6062;
  font-size: 16px;
  padding: 20px 20px 20px 20px;
  border-bottom: 1px solid #e0e0e0;
}

tbody tr:nth-child(2n) {
  background: #e6f2f5;
}

tbody tr:last-child td {
  border-bottom: none;
}
tbody tr:last-child td:first-child {
  -moz-border-radius-bottomleft: 8px;
  -webkit-border-bottom-left-radius: 8px;
  border-bottom-left-radius: 8px;
}
tbody tr:last-child td:last-child {
  -moz-border-radius-bottomright: 8px;
  -webkit-border-bottom-right-radius: 8px;
  border-bottom-right-radius: 8px;
}

tbody:hover > tr td {
  filter: progid:DXImageTransform.Microsoft.Alpha(Opacity=50);
  opacity: 0.5;
  /* uncomment for blur effect */
  /* color:transparent;
  @include text-shadow(0px 0px 2px rgba(0,0,0,0.8));*/
}

tbody:hover > tr:hover td {
  text-shadow: none;
  color: #2d2d2d;
  filter: progid:DXImageTransform.Microsoft.Alpha(enabled=false);
  opacity: 1;
}

                         </style>
                      </head>
                      <body>
                      <div class="container">
                         <center><div class="col-sm-7">
                          <img class="img-responsive" src="'.$_SERVER['HTTP_HOST'].'/assets/img/images-mail/header-admyo-recibiste-calificacion.jpg" />
                        </div></center>
                        <center><div class="col-sm-7">
                          <div class="col-sm-12">
                          <center><br><h3>¡Ha Recibido una Calificación!</h3></center>
                        </div>
                          <p>Hola '.$Razon_Valoradora.'</p>
                          <p>Ha realizado el cambio de una calificación con un promedio de '.$calificacion.' realizada a '.$Razon_valorada.' como '.$Tipo.' en <a href="https://admyo.com/" >admyo.com</a></p>
                          <p>El detalle de la calificación recibida fue:</p>
                          <div class="col-sm-12">
                          <table>
                                <thead>
                                    <tr>
                                        <td style=" border-radius: 8px 0px 0px 0px; -moz-border-radius: 8px 0px 0px 0px; -webkit-border-radius: 8px 0px 0px 0px;">Pregunta</td>
                                        <td style=" border-radius: 0px 8px 0px 0px; -moz-border-radius: 0px 8px 0px 0px; -webkit-border-radius: 0px 8px 0px 0px;" >Calificación</td>
                                    </tr>
                                </thead>
                                <tbody id="tbody">
                                '.$html.'
                                </tbody>
                            </table>
                            
                          </div>
<p>•  Si no está conforme con esta calificación puede solicitar un cambio dentro de su perfil en admyo.com</p>

<div class="col-sm-12"><center><a href="'.$_SERVER['HTTP_HOST'].'/" ><button type="button" >CALIFIQUE</button></a><br><br></div>
<p><font color="#005288" style="font-weight: bold;">¿Quiere crecer su negocio diferenciándose de su competencia? </font>Descubra cuanto puede crecer su negocio requiriendo a sus clientes y proveedores que le califiquen. Promueva su perfil empresarial. <br><br></p>
<h4><font color="#005288" style="font-weight: bold;">¡Genere su perfil para que su negocio crezca!</font></h4>
<p>Saludos,<br> 
<font color="#005288" style="font-weight: bold;">Equipo admyo</font></p>     
                      <div class="col-sm-12" style="border-width: 1px; border-style: dashed; border-color: #fcb034; "></div>
                      <div class="col-sm-12"><br><p><font color="#cc9829" >The most important thing for a young man is to establish credit - a reputation and character”... <br><font style="font-weight: bold;">John D. Rockefeller</font></font></p></div>
<div class="col-sm-12"><p>IMPORTANTE: El presente correo electrónico es confidencial y/o puede contener información privilegiada. 
Su contenido no pretende ni debe considerarse como constitutivo de ninguna relación legal, contractual 
o de otra índole similar.</p></div>
                      </div></center></div>
</body>
</html>';

                    		$mail->Body = $body;
                    		$mail->Send();
		
				return $mail;
				
}
function ms_recalificacion2($Razon_Valoradora,$Razon_valorada,$calificacion,$Tipo,$email,$tipo_contrario,$preguntas){
	$mail = new PHPMailer();
	//$mail->Host = "localhost";
	$mail->IsSendmail();
	$mail->SMTPAuth = true;
	$mail->Host       = "smtp.1and1.es";
	$mail->SMTPSecure = "tls";
    $mail->Port       = 587;
    $mail->Username   = "leonardo@admyo.com";
    $mail->Password   = "1017854Leo/";
    $mail->CharSet = "UTF-8";
        $mail->ContentType = "text/html";
        $mail->From = "no-reply@admyo.com";
        $mail->FromName = "Admyo";
    $mail->Subject = "¡Ha Recibido una Calificación en ADMYO!";
	$mail->AddAddress($email);
	$html="";
						$linea=explode("|*|",$preguntas);
            for ($i=1; $i < count($linea); $i++) { 
              $datos=explode("|",$linea[$i]);
              $html.='<tr><td>'.$datos[0].'</td><td style="text-align:center;">'.$datos[1].'</td></tr>';
            }
						$body = 
                                '<html>
                      <head>
                           <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                         <style type="text/css">
                         @import url(http://fonts.googleapis.com/css?family=Patua+One|Open+Sans);
                           body{font-family: "arial";}p{text-align: justify;font-size: 11pt;color: #878788;}
                           .container {margin-right: auto;margin-left: auto; width: 100%;}.col-sm-7 {width: 90%;}.img-responsive{display: block;max-width: 100%;height: auto;}
                           h3{font-size: 18pt;color: #005288;font-style: italic;font-weight: bold;}button{border-radius: 10px;border: 2px solid #e96610;padding: 15px 75px;cursor:pointer;background-color:#e96610;color: #ffffff;}h4{text-align: justify;}h5{text-align: justify;}
                           table {
  border-collapse: separate;
  border: 4px solid #fff;  
  background: #fff;
  -moz-border-radius: 5px;
  -webkit-border-radius: 5px;
  border-radius: 5px;
  margin: 20px auto;
  -moz-box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
  -webkit-box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
  box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
}

thead {
  -moz-border-radius: 8px;
  -webkit-border-radius: 8px;
  border-radius: 8px;
}

thead td {
  font-family: "Open Sans", sans-serif;
  font-size: 23px;
  font-weight: 400;
  color: #fff;
  text-shadow: 1px 1px 0px rgba(0, 0, 0, 0.5);
  text-align: left;
  padding: 20px;
  background-image: url("data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4gPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PGxpbmVhckdyYWRpZW50IGlkPSJncmFkIiBncmFkaWVudFVuaXRzPSJvYmplY3RCb3VuZGluZ0JveCIgeDE9IjAuNSIgeTE9IjAuMCIgeDI9IjAuNSIgeTI9IjEuMCI+PHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iIzY0NmY3ZiIvPjxzdG9wIG9mZnNldD0iMTAwJSIgc3RvcC1jb2xvcj0iIzRhNTU2NCIvPjwvbGluZWFyR3JhZGllbnQ+PC9kZWZzPjxyZWN0IHg9IjAiIHk9IjAiIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JhZCkiIC8+PC9zdmc+IA==");
  background-size: 100%;
  background-image: -webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(0%, #005d8f), color-stop(100%, #4a5564));
  background-image: -moz-linear-gradient(#005d8f, #004266);
  background-image: -webkit-linear-gradient(#005d8f, #004266);
  background-image: linear-gradient(#005d8f, #004266);
  border-top: 1px solid #005d8f;
}
thead th:first-child {
  -moz-border-radius-topleft: 8px;
  -webkit-border-top-left-radius: 8px;
  border-top-left-radius: 8px;
}
thead th:last-child {
  -moz-border-radius-topright: 8px;
  -webkit-border-top-right-radius: 8px;
  border-top-right-radius: 8px;
}

tbody tr td {
  font-family: "Open Sans", sans-serif;
  font-weight: 400;
  color: #5f6062;
  font-size: 16px;
  padding: 20px 20px 20px 20px;
  border-bottom: 1px solid #e0e0e0;
}

tbody tr:nth-child(2n) {
  background: #e6f2f5;
}

tbody tr:last-child td {
  border-bottom: none;
}
tbody tr:last-child td:first-child {
  -moz-border-radius-bottomleft: 8px;
  -webkit-border-bottom-left-radius: 8px;
  border-bottom-left-radius: 8px;
}
tbody tr:last-child td:last-child {
  -moz-border-radius-bottomright: 8px;
  -webkit-border-bottom-right-radius: 8px;
  border-bottom-right-radius: 8px;
}

tbody:hover > tr td {
  filter: progid:DXImageTransform.Microsoft.Alpha(Opacity=50);
  opacity: 0.5;
  /* uncomment for blur effect */
  /* color:transparent;
  @include text-shadow(0px 0px 2px rgba(0,0,0,0.8));*/
}

tbody:hover > tr:hover td {
  text-shadow: none;
  color: #2d2d2d;
  filter: progid:DXImageTransform.Microsoft.Alpha(enabled=false);
  opacity: 1;
}

                         </style>
                      </head>
                      <body>
                      <div class="container">
                         <center><div class="col-sm-7">
                          <img class="img-responsive" src="'.$_SERVER['HTTP_HOST'].'/assets/img/images-mail/header-admyo-recibiste-calificacion.jpg" />
                        </div></center>
                        <center><div class="col-sm-7">
                          <div class="col-sm-12">
                          <center><br><h3>¡Ha Recibido una Calificación!</h3></center>
                        </div>
                          <p>Hola '.$Razon_valorada.'</p>
                          <p>Ha recibido una cambio de una calificación con un promedio de '.$calificacion.' realizada por '.$Razon_Valoradora.' como '.$Tipo.' en <a href="https://admyo.com/" >admyo.com</a></p>
                          <p>El detalle de la calificación recibida fue:</p>
                          <div class="col-sm-12">
                          <table>
                                <thead>
                                    <tr>
                                        <td style=" border-radius: 8px 0px 0px 0px; -moz-border-radius: 8px 0px 0px 0px; -webkit-border-radius: 8px 0px 0px 0px;">Pregunta</td>
                                        <td style=" border-radius: 0px 8px 0px 0px; -moz-border-radius: 0px 8px 0px 0px; -webkit-border-radius: 0px 8px 0px 0px;" >Calificación</td>
                                    </tr>
                                </thead>
                                <tbody id="tbody">
                                '.$html.'
                                </tbody>
                            </table>
                            
                          </div>
<p>•  Si no está conforme con esta calificación puede solicitar un cambio dentro de su perfil en admyo.com</p>
<p>•  Si este no es su cliente o proveedor puede darlo de baja en su perfil de empresa en admyo.com</p>
<p>•  Puede calificar a su '.$tipo_contrario.' haciendo clic en el siguiente botón.</p>
<div class="col-sm-12"><center><a href="'.$_SERVER['HTTP_HOST'].'/" ><button type="button" >CALIFIQUE</button></a><br><br></div>
<p><font color="#005288" style="font-weight: bold;">¿Quiere crecer su negocio diferenciándose de su competencia? </font>Descubra cuanto puede crecer su negocio requiriendo a sus clientes y proveedores que le califiquen. Promueva su perfil empresarial. <br><br></p>
<h4><font color="#005288" style="font-weight: bold;">¡Genere su perfil para que su negocio crezca!</font></h4>
<p>Saludos,<br> 
<font color="#005288" style="font-weight: bold;">Equipo admyo</font></p>     
                      <div class="col-sm-12" style="border-width: 1px; border-style: dashed; border-color: #fcb034; "></div>
                      <div class="col-sm-12"><br><p><font color="#cc9829" >The most important thing for a young man is to establish credit - a reputation and character”... <br><font style="font-weight: bold;">John D. Rockefeller</font></font></p></div>
<div class="col-sm-12"><p>IMPORTANTE: El presente correo electrónico es confidencial y/o puede contener información privilegiada. 
Su contenido no pretende ni debe considerarse como constitutivo de ninguna relación legal, contractual 
o de otra índole similar.</p></div>
                      </div></center></div>
</body>
</html>';

                    		$mail->Body = $body;
                    		$mail->Send();
		
				return $mail;
				
}
function  ms_pendienteresolucionvalorada($correo,$Razon_Social,$Razon_Social_calificadora,$FechaVal){
	$mail = new PHPMailer();
	//$mail->Host = "localhost";
	$mail->IsSendmail();
	$mail->SMTPAuth = true;
	$mail->Host       = "smtp.1and1.es";
	$mail->SMTPSecure = "tls";
    $mail->Port       = 587;
    $mail->Username   = "leonardo@admyo.com";
    $mail->Password   = "1017854Leo/";
    $mail->CharSet = "UTF-8";
        $mail->ContentType = "text/html";
        $mail->From = "no-reply@admyo.com";
        $mail->FromName = "Admyo";
	        $mail->Subject = 'Solicitud de cambio de calificación';
        $mail->AddAddress($correo);
        
        $body ='<html>
                      <head>
                         <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                              <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
                          <style type="text/css">
                           body{font-family: "arial";}p{text-align: justify;font-size: 11pt;color: #878788;}
                           .container {margin-right: auto;margin-left: auto; width: 100%;}.col-sm-7 {width: 90%;}.img-responsive{display: block;max-width: 100%;height: auto;}
                           h3{font-size: 18pt;color: #005288;font-style: italic;font-weight: bold;}button{border-radius: 10px;border: 2px solid #e96610;padding: 15px 75px;cursor:pointer;background-color:#e96610;color: #ffffff;}h4{text-align: justify;}h5{text-align: justify;}
                         </style>
                      </head>
                      <body>
                      <div class="container">
                         <center><div class="col-sm-7">
                          <img class="img-responsive" src="'.$_SERVER['HTTP_HOST'].'/assets/img/images-mail/header-admyo-solicitud-cambio-calificacion-empresa calificada.jpg" />
                        </div></center>
                        <center><div class="col-sm-7">
                          <div class="col-sm-12">
                          <center><br><h3>Solicitud de anulación de calificación</h3></center>
                        </div>
                          <p>Hola '.$Razon_Social.'</p>
                          <p>La calificación que efectuó  '.$Razon_Social_calificadora.',el dia '.$FechaVal.' se ha puesto en pendiente de resolución y no está sumando en su media. Así mismo, se ha enviado un email a la empresa '.$Razon_Social_calificadora.' solicitando cambie dicha calificación en 90 días, de caso contrario el sistema cambiara el estatus de esta valoración para ser tomada en cuenta. </p>
                          <p>Usted podrá visualizar en su perfil, cual es el estado de esta calificación. Así mismo también le enviaremos un email cuando ocurra un cambio.<br><br></p>
<p>Saludos,<br> 
<font color="#005288" style="font-weight: bold;">Equipo admyo</font></p>     
                      <div class="col-sm-12" style="border-width: 1px; border-style: dashed; border-color: #fcb034; "></div>
                      <div class="col-sm-12"><br><p><font color="#cc9829" >“The most important thing for a young man is to establish credit - a reputation and character”.<br><font style="font-weight: bold;">John D. Rockefeller</font></font></p></div>
<div class="col-sm-12"><p>IMPORTANTE: El presente correo electrónico es confidencial y/o puede contener información privilegiada. 
Su contenido no pretende ni debe considerarse como constitutivo de ninguna relación legal, contractual 
o de otra índole similar.</p></div>
                      </div></center></div>
</body>
</html>';
$mail->Body = $body;
      $mail->Send();
      
      return $mail;
	
}
function ms_pendienteresolucionvaloradora ($correo,$Razon_Social,$Razon_Social_calificadora,$FechaVal){
	$mail = new PHPMailer();
	//$mail->Host = "localhost";
	$mail->IsSendmail();
	$mail->SMTPAuth = true;
	$mail->Host       = "smtp.1and1.es";
	$mail->SMTPSecure = "tls";
    $mail->Port       = 587;
    $mail->Username   = "leonardo@admyo.com";
    $mail->Password   = "1017854Leo/";
    $mail->CharSet = "UTF-8";
        $mail->ContentType = "text/html";
        $mail->From = "no-reply@admyo.com";
        $mail->FromName = "Admyo";
	       $mail->Subject = 'Solicitud de cambio de calificación';
        $mail->AddAddress($correo);
        
        $body ='<html>
                      <head>
                         <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                              <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
                          <style type="text/css">
                           body{font-family: "arial";}p{text-align: justify;font-size: 11pt;color: #878788;}
                           .container {margin-right: auto;margin-left: auto; width: 100%;}.col-sm-7 {width: 90%;}.img-responsive{display: block;max-width: 100%;height: auto;}
                           h3{font-size: 18pt;color: #005288;font-style: italic;font-weight: bold;}button{border-radius: 10px;border: 2px solid #e96610;padding: 15px 75px;cursor:pointer;background-color:#e96610;color: #ffffff;}h4{text-align: justify;}h5{text-align: justify;}
                         </style>
                      </head>
                      <body>
                      <div class="container">
                         <center><div class="col-sm-7">
                          <img class="img-responsive" src="'.$_SERVER['HTTP_HOST'].'/assets/img/images-mail/header-admyo-notificacion-cambio-valoracion-empresacalificada.jpg"/>
                        </div></center>
                        <center><div class="col-sm-7">
                          <div class="col-sm-12">
                          <center><br><h3>Solicitud de cambio de calificación </h3></center>
                        </div>
                          <p>Hola '.$Razon_Social_calificadora.'</p>
                          <p>La empresa '.$Razon_Social.' ha solicitado un <font color="#005288" style="font-weight: bold;">cambio </font> en la calificación que le efectuó el día '.$FechaVal.'.</p> 
                          <p>Esta calificación ya ha sido puesta en pendiente de resolución y no está siendo considerada en la media. Para que esta calificación sea considerada, es necesario que cambie dicha calificación en los siguientes 90 días, de caso contrario esta sera considerada con el valor actual. </p>
                          
                          <p><font color="#005288" style="font-weight: bold;">¡Siga reduciendo su riesgo empresarial, califique y exija a sus clientes y proveedores que mantengan un perfil de reputación!</font></p>
<p>Saludos,<br> 
<font color="#005288" style="font-weight: bold;">Equipo admyo</font></p>     
                      <div class="col-sm-12" style="border-width: 1px; border-style: dashed; border-color: #fcb034; "></div>
                      <div class="col-sm-12"><br><p><font color="#cc9829" >“The most important thing for a young man is to establish credit - a reputation and character”.<br><font style="font-weight: bold;">John D. Rockefeller</font></font></p></div>
<div class="col-sm-12"><p>IMPORTANTE: El presente correo electrónico es confidencial y/o puede contener información privilegiada. 
Su contenido no pretende ni debe considerarse como constitutivo de ninguna relación legal, contractual 
o de otra índole similar.</p></div>
                      </div></center></div>
</body>
</html>';
$mail->Body = $body;
      $mail->Send();
      
      return $mail;
}
function ms_pendienteanulacionvalorada ($correo,$Razon_Social,$Razon_Social_calificadora,$FechaVal){
	$mail = new PHPMailer();
	//$mail->Host = "localhost";
	$mail->IsSendmail();
	$mail->SMTPAuth = true;
	$mail->Host       = "smtp.1and1.es";
	$mail->SMTPSecure = "tls";
    $mail->Port       = 587;
    $mail->Username   = "leonardo@admyo.com";
    $mail->Password   = "1017854Leo/";
    $mail->CharSet = "UTF-8";
        $mail->ContentType = "text/html";
        $mail->From = "no-reply@admyo.com";
        $mail->FromName = "Admyo";
	        $mail->Subject = 'Solicitud de cambio de calificación';
        $mail->AddAddress($correo);
        
        $body ='<html>
                      <head>
                         <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                              <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
                          <style type="text/css">
                           body{font-family: "arial";}p{text-align: justify;font-size: 11pt;color: #878788;}
                           .container {margin-right: auto;margin-left: auto; width: 100%;}.col-sm-7 {width: 90%;}.img-responsive{display: block;max-width: 100%;height: auto;}
                           h3{font-size: 18pt;color: #005288;font-style: italic;font-weight: bold;}button{border-radius: 10px;border: 2px solid #e96610;padding: 15px 75px;cursor:pointer;background-color:#e96610;color: #ffffff;}h4{text-align: justify;}h5{text-align: justify;}
                         </style>
                      </head>
                      <body>
                      <div class="container">
                         <center><div class="col-sm-7">
                          <img class="img-responsive" src="'.$_SERVER['HTTP_HOST'].'/assets/img/images-mail/header-admyo-solicitud-cambio-calificacion-empresa calificada.jpg" />
                        </div></center>
                        <center><div class="col-sm-7">
                          <div class="col-sm-12">
                          <center><br><h3>Solicitud de anulación de calificación</h3></center>
                        </div>
                          <p>Hola '.$Razon_Social.'</p>
                          <p>La calificación que efectuó la empresa '.$Razon_Social_calificadora.',el dia '.$FechaVal.' se ha puesto en pendiente de resolución y no está sumando en su media. Así mismo, se ha enviado un email a la empresa '.$Razon_Social_calificadora.' solicitando la documentación necesaria para justificar la relación comercial que mantienen. En caso de no recibir esta documentación la calificación será anulada automáticamente en 90 días. </p>
                          <p>Usted podrá visualizar en su perfil, cual es el estado de esta calificación. Así mismo también le enviaremos un email cuando ocurra un cambio.<br><br></p>
<p>Saludos,<br> 
<font color="#005288" style="font-weight: bold;">Equipo admyo</font></p>     
                      <div class="col-sm-12" style="border-width: 1px; border-style: dashed; border-color: #fcb034; "></div>
                      <div class="col-sm-12"><br><p><font color="#cc9829" >“The most important thing for a young man is to establish credit - a reputation and character”.<br><font style="font-weight: bold;">John D. Rockefeller</font></font></p></div>
<div class="col-sm-12"><p>IMPORTANTE: El presente correo electrónico es confidencial y/o puede contener información privilegiada. 
Su contenido no pretende ni debe considerarse como constitutivo de ninguna relación legal, contractual 
o de otra índole similar.</p></div>
                      </div></center></div>
</body>
</html>';
$mail->Body = $body;
      $mail->Send();
      
      return $mail;
	
}
function ms_pendienteanulacionvaloradora  ($correo,$Razon_Social,$Razon_Social_calificadora,$FechaVal){
	$mail = new PHPMailer();
	//$mail->Host = "localhost";
	$mail->IsSendmail();
	$mail->SMTPAuth = true;
	$mail->Host       = "smtp.1and1.es";
	$mail->SMTPSecure = "tls";
    $mail->Port       = 587;
    $mail->Username   = "leonardo@admyo.com";
    $mail->Password   = "1017854Leo/";
    $mail->CharSet = "UTF-8";
        $mail->ContentType = "text/html";
        $mail->From = "no-reply@admyo.com";
        $mail->FromName = "Admyo";
	       $mail->Subject = 'Solicitud de cambio de calificación';
        $mail->AddAddress($correo);
        
        $body ='<html>
                      <head>
                         <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                              <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
                          <style type="text/css">
                           body{font-family: "arial";}p{text-align: justify;font-size: 11pt;color: #878788;}
                           .container {margin-right: auto;margin-left: auto; width: 100%;}.col-sm-7 {width: 90%;}.img-responsive{display: block;max-width: 100%;height: auto;}
                           h3{font-size: 18pt;color: #005288;font-style: italic;font-weight: bold;}button{border-radius: 10px;border: 2px solid #e96610;padding: 15px 75px;cursor:pointer;background-color:#e96610;color: #ffffff;}h4{text-align: justify;}h5{text-align: justify;}
                         </style>
                      </head>
                      <body>
                      <div class="container">
                         <center><div class="col-sm-7">
                          <img class="img-responsive" src="'.$_SERVER['HTTP_HOST'].'/assets/img/images-mail/header-admyo-notificacion-cambio-valoracion-empresacalificada.jpg"/>
                        </div></center>
                        <center><div class="col-sm-7">
                          <div class="col-sm-12">
                          <center><br><h3>Solicitud de anulación de calificación </h3></center>
                        </div>
                          <p>Hola '.$Razon_Social_calificadora.'</p>
                          <p>La empresa '.$Razon_Social.' ha solicitado una <font color="#005288" style="font-weight: bold;">anulación</font> en la calificación que le efectuó el día '.$FechaVal.'. por motivo de no relación comercial.</p> 
                          <p>Esta calificación ya ha sido puesta en pendiente de resolución y no está siendo considerada en la media. Para que esta calificación sea considerada, es necesario que envíe en los próximos 90 días a info@admyo.com un email con una factura demostrando la relación comercial que tiene. </p>
                          <p>En caso contrario esta calificación será anulada del sistema.</p>
                          <p><font color="#005288" style="font-weight: bold;">¡Siga reduciendo su riesgo empresarial, califique y exija a sus clientes y proveedores que mantengan un perfil de reputación!</font></p>
<p>Saludos,<br> 
<font color="#005288" style="font-weight: bold;">Equipo admyo</font></p>     
                      <div class="col-sm-12" style="border-width: 1px; border-style: dashed; border-color: #fcb034; "></div>
                      <div class="col-sm-12"><br><p><font color="#cc9829" >“The most important thing for a young man is to establish credit - a reputation and character”.<br><font style="font-weight: bold;">John D. Rockefeller</font></font></p></div>
<div class="col-sm-12"><p>IMPORTANTE: El presente correo electrónico es confidencial y/o puede contener información privilegiada. 
Su contenido no pretende ni debe considerarse como constitutivo de ninguna relación legal, contractual 
o de otra índole similar.</p></div>
                      </div></center></div>
</body>
</html>';
$mail->Body = $body;
      $mail->Send();
      
      return $mail;
}
function enviarcotizacion($correo,$nombre,$telefono,$plan){
	$mail = new PHPMailer();
	//$mail->Host = "localhost";
	$mail->IsSendmail();
	$mail->SMTPAuth = true;
	$mail->Host       = "smtp.1and1.es";
$mail->SMTPSecure = "tls";
$mail->Port       = 587;
$mail->Username   = "leonardo@admyo.com";
$mail->Password   = "1017854Leo/";
        $mail->CharSet = "UTF-8";
        $mail->ContentType = "text/html";
        $mail->From = "no-reply@admyo.com";
        $mail->FromName = "Admyo";
        $mail->Subject = 'solicitan Cotizacion';
        $mail->AddAddress('lira@ztark.mx','Leonardo Lira Cazares');
        $mail->AddAddress('bernardodetomas@admyo.com','Bernardo de Tomas');
        $mail->AddAddress('pablo@ztark.mx','PABLO PARROQUIN ORTIZ');
        $body ='<html>
                      <head>
                         <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                              <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
                          <style type="text/css">
                           body{font-family: "arial";}p{text-align: justify;font-size: 11pt;color: #878788;}
                           .container {margin-right: auto;margin-left: auto; width: 100%;}.col-sm-7 {width: 90%;}.img-responsive{display: block;max-width: 100%;height: auto;}
                           h3{font-size: 18pt;color: #005288;font-style: italic;font-weight: bold;}button{border-radius: 10px;border: 2px solid #e96610;padding: 15px 75px;cursor:pointer;background-color:#e96610;color: #ffffff;}h4{text-align: justify;}h5{text-align: justify;}
                         </style>
                      </head>
                      <body>
                      <div class="container">
                         <center><div class="col-sm-7">
                          <img class="img-responsive" src="'.$_SERVER['HTTP_HOST'].'/assets/img/images-mail/header-admyo-notificacion-cambio-valoracion-empresacalificada.jpg"/>
                        </div></center>
                        <center><div class="col-sm-7">
                          <div class="col-sm-12">
                          <center><br><h3>Solicitud de cotizacion</h3></center>
                        </div>
                          <p>Hola </p>
                          <p>Se ha solicitado una cotizacion el dia '.date("d").' de '.date("m").' de '.date("Y").'</p>
                          <p>
                          	Datos de Contacto
                          	<p>
                          	Nombre: '.$nombre.'.
                          	<p>
                          	Correo electrónico: '.$correo.'.
                          	<p>
                          	Telefono: '.$telefono.'.
                          	<p>
                          	Plan: '.$plan.'.
                                               
                          <p>Saludos.<br> 
                      <font color="#005288" style="font-weight: bold;">Equipo admyo</font></p>     
                      <div class="col-sm-12" style="border-width: 1px; border-style: dashed; border-color: #fcb034; "></div>
                      <div class="col-sm-12"><br><p><font color="#cc9829" >“The most important thing for a young man is to establish credit - a reputation and character”.<br><font style="font-weight: bold;">John D. Rockefeller</font></font></p></div>
<div class="col-sm-12"><p>IMPORTANTE: El presente correo electrónico es confidencial y/o puede contener información privilegiada. 
Su contenido no pretende ni debe considerarse como constitutivo de ninguna relación legal, contractual 
o de otra índole similar.</p></div>
                      </div></center></div>
</body>
</html>';
      $mail->Body = $body;
      $mail->Send();
      
      return $mail;

}
function mail_activarus($Correo,$Razon_Social,$Token,$pssw){
	$mail = new PHPMailer();
	//$mail->Host = "localhost";
	$mail->IsSendmail();
	$mail->SMTPAuth = true;
	$mail->Host       = "smtp.1and1.es";
	$mail->SMTPSecure = "tls";
    $mail->Port       = 587;
    $mail->Username   = "leonardo@admyo.com";
    $mail->Password   = "1017854Leo/";
    $mail->CharSet = "UTF-8";
        $mail->ContentType = "text/html";
        $mail->From = "no-reply@admyo.com";
        $mail->FromName = "Admyo";
    $mail->Subject = "Bienvenido "." ".$Razon_Social.", active su cuenta";
	$mail->AddAddress($Correo);

				$body  =
                  '<html>
                      <head>
                         <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                         <style type="text/css">
                           body{font-family: "arial";}p{text-align: justify;font-size: 11pt;color: #878788;}
                           .container {margin-right: auto;margin-left: auto; width: 100%;}.col-sm-7 {width: 90%;}.img-responsive{display: block;max-width: 100%;height: auto;}
                           h3{font-size: 18pt;color: #005288;font-style: italic;font-weight: bold;}button{border-radius: 10px;border: 2px solid #e96610;padding: 15px 75px;cursor:pointer;background-color:#e96610;color: #ffffff;}h4{text-align: justify;}h5{text-align: justify;}
                         </style>
                      </head>
                      <body>
                      <div class="container">
                         <center><div class="col-sm-7">
                          <img class="img-responsive" src=""'.$_SERVER['HTTP_HOST'].'/assets/img/images-mail/header-admyo-bienvenida.jpg" />
                        </div></center>
                        <center><div class="col-sm-7">
                          <div class="col-sm-12">
                          <center><br><h3>¡Bienvenido a admyo!</h3></center>
                        </div>
                          <p>En nombre del equipo de admyo, le damos la bienvenida. admyo.com es una plataforma enfocada en la reputación empresarial para que las empresas puedan crecer su negocio y gestionar su riesgo. Si no has visto nuestro video, te recomendamos que lo mires <a href="https://player.vimeo.com/video/48771589?autoplay=1" >aquí</a>.</p>
                          <p><font color="#005288" style="font-weight: bold;">¿Quiere crecer su negocio diferenciándose de su competencia? </font> Descubra cuanto puede crecer su negocio requiriendo a sus clientes y proveedores que le califiquen. Promueva su perfil empresarial. </p>
                          <p><font color="#005288" style="font-weight: bold;">¿Quieres aparecer en nuestra página de inicio?, ¿Que publiquemos sobre ti en redes sociales?,</font> entre más participes calificando a empresas más puntos de public static idad y descuentos obtendrás. </p>
                          <p><font color="#005288" style="font-weight: bold;">¿Quieres saber el riesgo que corres con tus clientes o proveedores?</font> Exígeles que tengan y mantengan un perfil  empresarial en <a href="https://admyo.com/" >admyo.com </a></p>
<p><font color="#005288" style="font-weight: bold;">¿Quiere saber si puede aplicar a un descuento?</font> Si es una empresa con menos de un año de antigüedad puedes obtener un descuento del <font style="font-weight: bold;"> 50% </font>, además tenemos acuerdos con algunas cámaras y asociaciones. Para más información mándenos un email a <a href="mailto:promociones@admyo.com" target="_top">promociones@admyo.com</a><br><br></p>
<h5><font style="font-weight: bold;">Es necesario que active su cuenta. Haga clic en el siguiente botón</font></h5>
<div class="col-sm-12"><center><a href="'.$_SERVER['HTTP_HOST'].'/activar/acttoken/'.$Token.'" ><button type="button" >ACTIVE SU CUENTA</button></a><br><br></div>
<p>Si no basta con hacer clic, copie y pegue el siguiente enlace en su navegador. <br><a href="'.$_SERVER['HTTP_HOST'].'/activar/acttoken/'.$Token.'" >"'.$_SERVER['HTTP_HOST'].'/activar/acttoken/'.$Token.'</a><br><br></p>
<h4><font color="#005288" style="font-weight: bold;">¡Genere su perfil para que su negocio crezca!</font></h4>
<p>Saludos,<br> 
<font color="#005288" style="font-weight: bold;">Equipo admyo</font></p>     
                      <div class="col-sm-12" style="border-width: 1px; border-style: dashed; border-color: #fcb034; "></div>
                      <div class="col-sm-12"><br><p><font color="#cc9829" >““… A man I do not trust could not get money from me on all the bonds in Christendom. I think that is the fundamental basis of business.”…<font style="font-weight: bold;">J. P. Morgan</font></font></p></div>
<div class="col-sm-12"><p><a href="https://www.admyo.com/terminos-condiciones/" style="color: #21334d;" target="_blank"> Politica de privacidad  |  Términos y condiciones </a></p></div>
                      </div></center></div>
</body>
</html>';

			$mail->Body = $body;
			$mail->Send();
			
			return $mail;
}
function mail_invitarUsu($Correo,$Razon_Social,$Token,$pass){
	$mail = new PHPMailer();
	//$mail->Host = "localhost";
	$mail->IsSendmail();
	$mail->SMTPAuth = true;
	$mail->Host       = "smtp.1and1.es";
	$mail->SMTPSecure = "tls";
    $mail->Port       = 587;
    $mail->Username   = "leonardo@admyo.com";
    $mail->Password   = "1017854Leo/";
    $mail->CharSet = "UTF-8";
        $mail->ContentType = "text/html";
        $mail->From = "no-reply@admyo.com";
        $mail->FromName = "Admyo";
    $mail->Subject = $Razon_Social." Le invitamos a valorar.";
	$mail->AddAddress($Correo);

				$body  = '<html>
			     <head>
			         <link href="style/datafields.css" rel="stylesheet" type="text/css" />
			         <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
			         <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                         <style type="text/css">
                           body{font-family: "arial";}p{text-align: justify;font-size: 11pt;color: #878788;}
                           .container {margin-right: auto;margin-left: auto; width: 100%;}.col-sm-7 {width: 90%;}.img-responsive{display: block;max-width: 100%;height: auto;}
                           h3{font-size: 18pt;color: #005288;font-style: italic;font-weight: bold;}button{border-radius: 10px;border: 2px solid #e96610;padding: 15px 75px;cursor:pointer;background-color:#e96610;color: #ffffff;}h4{text-align: justify;}h5{text-align: justify;}
                         </style>
			     </head>
				 <body>
					  <center><div class="col-sm-7">
                          <img class="img-responsive" src="'.$_SERVER['HTTP_HOST'].'/assets/img/images-mail/header-admyo-bienvenida.jpg" />
                        </div></center>
					 <br />
					 <div>
					 <span style="font-family: Tahoma;">
					 </span>
					 <table border="0" cellspacing="2" cellpadding="2">
						 <tbody>
							 <tr>
								 <td width="1100">
								 <div><span style="font-family: Tahoma; font-size: 10pt;"></span></div>
								 <div><strong style="font-size: 14pt; font-family: Tahoma;">Estimado <empresa valorada>,'.$Razon_Social.'</strong></div>
								 <div>&nbsp;</div>
								 <div><span style="font-family: Tahoma; font-size: 10pt;">Hace tiempo recibiste una o varias valoraciones como cliente o proveedor en Admyo. Estas empresas, tienen interés en crearse una reputación empresarial para poder vender más y piden que le valores de vuelta. Te tardas 1 minuto en valorar a 1 empresa.</span></div>
								 <div>&nbsp;</div>
								 <div><span style="font-family: Tahoma; font-size: 10pt;">Recientemente, hemos cambiado el funcionamiento de registro en Admyo. En el caso de que no te hayas dado de alta, el sistema antiguo te registró sin clave. Te pedimos que restablezcas tu contraseña en login, o haciendo click aquí, para poder acceder.</span></div>								 
								 <div>&nbsp;</div>
								<p><font color="#005288" style="font-weight: bold;">¿Quiere saber si puede aplicar a un descuento?</font> Si es una empresa con menos de un año de antigüedad puedes obtener un descuento del <font style="font-weight: bold;"> 50% </font>, además tenemos acuerdos con algunas cámaras y asociaciones. Para más información mándenos un email a <a href="mailto:promociones@admyo.com" target="_top">promociones@admyo.com</a><br><br></p>
<h5><font style="font-weight: bold;">Es necesario que active su cuenta. Haga clic en el siguiente botón</font></h5>
<p><h5><font style="font-weight: bold;">Para poder ingresar en Admyo utiliza los siguientes datos:</font></h5>
<p> Correo electrónico: '.$Correo.'
<p>Contraseña: '.$pass.'
<div class="col-sm-12"><center><a href="'.$_SERVER['HTTP_HOST'].'/activar/acttoken/'.$Token.'" ><button type="button" >ACTIVE SU CUENTA</button></a><br><br></div>
<p>Si no basta con hacer clic, copie y pegue el siguiente enlace en su navegador. <br><a href="'.$_SERVER['HTTP_HOST'].'/activar/acttoken/'.$Token.'" >"'.$_SERVER['HTTP_HOST'].'/activar/acttoken/'.$Token.'</a><br><br></p>
<h4><font color="#005288" style="font-weight: bold;">¡Genere su perfil para que su negocio crezca!</font></h4>
<p>Saludos,<br> 
<font color="#005288" style="font-weight: bold;">Equipo admyo</font></p>     
                      <div class="col-sm-12" style="border-width: 1px; border-style: dashed; border-color: #fcb034; "></div>
                      <div class="col-sm-12"><br><p><font color="#cc9829" >““… A man I do not trust could not get money from me on all the bonds in Christendom. I think that is the fundamental basis of business.”…<font style="font-weight: bold;">J. P. Morgan</font></font></p></div>
<div class="col-sm-12"><p><a href="https://www.admyo.com/terminos-condiciones/" style="color: #21334d;" target="_blank"> Politica de privacidad  |  Términos y condiciones </a></p></div>
                      </div></center></div>
</body>
</html>';


			$mail->Body = $body;
			$mail->Send();
			
			return $mail;		
}
 function ResetPassword($Correo,$Password){
$mail = new PHPMailer();
	//$mail->Host = "localhost";
	$mail->IsSendmail();
	$mail->SMTPAuth = true;
	$mail->Host       = "smtp.1and1.es";
	$mail->SMTPSecure = "tls";
    $mail->Port       = 587;
    $mail->Username   = "leonardo@admyo.com";
    $mail->Password   = "1017854Leo/";
    $mail->CharSet = "UTF-8";
        $mail->ContentType = "text/html";
        $mail->From = "no-reply@admyo.com";
        $mail->FromName = "Admyo";
    $mail->Subject = "Restabecer Contraseña en Admyo";
	$mail->AddAddress($Correo);
  $body = '<html>
                      <head>
                         <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                         <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
                         <style type="text/css">
                           body{font-family: "arial";}p{text-align: justify;font-size: 11pt;color: #4d4d4f;}
                           h3{font-size: 18pt;color: #005288;font-style: italic;font-weight: bold;}button{border-radius: 10px;border: 2px solid #e96610;padding: 15px 75px;background-color:#e96610;color: #ffffff;}
                         </style>
                      </head>
                      <body>
                      <div class="container">
                         <center><div class="col-sm-7">
                          <img class="img-responsive" src="'.$_SERVER['HTTP_HOST'].'/assets/img/images-mail/header-admyo-solicitud-cambio-password.jpg" />
                        </div></center>
                        <center><div class="col-sm-7">
                          <div class="col-sm-12">
                          <center><br><h3>Hola</h3></center>
                        </div>
                          <p>Hemos recibido una solicitud de cambio de contraseña asociada a este email en admyo.com, si usted no lo ha solicitado puede ignorar tranquilamente este email.</p>
                          <p>Datos de nuevo acceso:</p>
                          	Correo Electronico: '.$Correo.'<br/>
                          <br/>Contraseña: '.$Password.'<br/>
                       
<div class="col-sm-12"><br><a href="'.$_SERVER['HTTP_HOST'].'" ><button type="button" >ACCESA CON TUS NUEVOS DATOS</button></a><br><br></div>
<p>Gracias por usar admyo.<br>Saludos,<br> 
<font color="#005288" style="font-weight: bold;">Equipo admyo</font></p>     
                      <div class="col-sm-12" style="border-width: 1px; border-style: dashed; border-color: #fcb034; "></div>
                      <div class="col-sm-12"><br><p><font color="#cc9829" >“When trust is lost, a nations ability to transact business is palpably undermined”... <font style="font-weight: bold;">Alan Greenspan</font></font></p></div></div></div>
</body>
</html>';
$mail->Body = $body;
			$mail->Send();
			
			return $mail;	
}

function ms_EnvioValoracion($Razon_Valoradora,$Razon_valorada,$calificacion,$Tipo,$email,$tipo_contrario,$preguntas){
	$mail = new PHPMailer();
	//$mail->Host = "localhost";
	$mail->IsSendmail();
	$mail->SMTPAuth = true;
	$mail->Host       = "smtp.1and1.es";
	$mail->SMTPSecure = "tls";
    $mail->Port       = 587;
    $mail->Username   = "leonardo@admyo.com";
    $mail->Password   = "1017854Leo/";
    $mail->CharSet = "UTF-8";
        $mail->ContentType = "text/html";
        $mail->From = "no-reply@admyo.com";
        $mail->FromName = "Admyo";
    $mail->Subject = "¡Ha Recibido una Calificación en ADMYO!";
	$mail->AddAddress($email);
	$html="";
						$linea=explode("|*|",$preguntas);
            for ($i=1; $i < count($linea); $i++) { 
              $datos=explode("|",$linea[$i]);
              $html.='<tr><td>'.$datos[0].'</td><td style="text-align:center;">'.$datos[1].'</td></tr>';
            }
						$body = 
                                '<html>
                      <head>
                           <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
                         <style type="text/css">
                         @import url(http://fonts.googleapis.com/css?family=Patua+One|Open+Sans);
                           body{font-family: "arial";}p{text-align: justify;font-size: 11pt;color: #878788;}
                           .container {margin-right: auto;margin-left: auto; width: 100%;}.col-sm-7 {width: 90%;}.img-responsive{display: block;max-width: 100%;height: auto;}
                           h3{font-size: 18pt;color: #005288;font-style: italic;font-weight: bold;}button{border-radius: 10px;border: 2px solid #e96610;padding: 15px 75px;cursor:pointer;background-color:#e96610;color: #ffffff;}h4{text-align: justify;}h5{text-align: justify;}
                           table {
  border-collapse: separate;
  border: 4px solid #fff;  
  background: #fff;
  -moz-border-radius: 5px;
  -webkit-border-radius: 5px;
  border-radius: 5px;
  margin: 20px auto;
  -moz-box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
  -webkit-box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
  box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.3);
}

thead {
  -moz-border-radius: 8px;
  -webkit-border-radius: 8px;
  border-radius: 8px;
}

thead td {
  font-family: "Open Sans", sans-serif;
  font-size: 23px;
  font-weight: 400;
  color: #fff;
  text-shadow: 1px 1px 0px rgba(0, 0, 0, 0.5);
  text-align: left;
  padding: 20px;
  background-image: url("data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4gPHN2ZyB2ZXJzaW9uPSIxLjEiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PGRlZnM+PGxpbmVhckdyYWRpZW50IGlkPSJncmFkIiBncmFkaWVudFVuaXRzPSJvYmplY3RCb3VuZGluZ0JveCIgeDE9IjAuNSIgeTE9IjAuMCIgeDI9IjAuNSIgeTI9IjEuMCI+PHN0b3Agb2Zmc2V0PSIwJSIgc3RvcC1jb2xvcj0iIzY0NmY3ZiIvPjxzdG9wIG9mZnNldD0iMTAwJSIgc3RvcC1jb2xvcj0iIzRhNTU2NCIvPjwvbGluZWFyR3JhZGllbnQ+PC9kZWZzPjxyZWN0IHg9IjAiIHk9IjAiIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjZ3JhZCkiIC8+PC9zdmc+IA==");
  background-size: 100%;
  background-image: -webkit-gradient(linear, 50% 0%, 50% 100%, color-stop(0%, #005d8f), color-stop(100%, #4a5564));
  background-image: -moz-linear-gradient(#005d8f, #004266);
  background-image: -webkit-linear-gradient(#005d8f, #004266);
  background-image: linear-gradient(#005d8f, #004266);
  border-top: 1px solid #005d8f;
}
thead th:first-child {
  -moz-border-radius-topleft: 8px;
  -webkit-border-top-left-radius: 8px;
  border-top-left-radius: 8px;
}
thead th:last-child {
  -moz-border-radius-topright: 8px;
  -webkit-border-top-right-radius: 8px;
  border-top-right-radius: 8px;
}

tbody tr td {
  font-family: "Open Sans", sans-serif;
  font-weight: 400;
  color: #5f6062;
  font-size: 16px;
  padding: 20px 20px 20px 20px;
  border-bottom: 1px solid #e0e0e0;
}

tbody tr:nth-child(2n) {
  background: #e6f2f5;
}

tbody tr:last-child td {
  border-bottom: none;
}
tbody tr:last-child td:first-child {
  -moz-border-radius-bottomleft: 8px;
  -webkit-border-bottom-left-radius: 8px;
  border-bottom-left-radius: 8px;
}
tbody tr:last-child td:last-child {
  -moz-border-radius-bottomright: 8px;
  -webkit-border-bottom-right-radius: 8px;
  border-bottom-right-radius: 8px;
}

tbody:hover > tr td {
  filter: progid:DXImageTransform.Microsoft.Alpha(Opacity=50);
  opacity: 0.5;
  /* uncomment for blur effect */
  /* color:transparent;
  @include text-shadow(0px 0px 2px rgba(0,0,0,0.8));*/
}

tbody:hover > tr:hover td {
  text-shadow: none;
  color: #2d2d2d;
  filter: progid:DXImageTransform.Microsoft.Alpha(enabled=false);
  opacity: 1;
}

                         </style>
                      </head>
                      <body>
                      <div class="container">
                         <center><div class="col-sm-7">
                          <img class="img-responsive" src="https://admyo.com/images/images-mail/header-admyo-recibiste-calificacion.jpg" />
                        </div></center>
                        <center><div class="col-sm-7">
                          <div class="col-sm-12">
                          <center><br><h3>¡Ha Recibido una Calificación!</h3></center>
                        </div>
                          <p>Hola '.$Razon_valorada.'</p>
                          <p>Ha recibido una calificación con un promedio de '.$calificacion.' realizada por '.$Razon_Valoradora.' como '.$Tipo.' en <a href="https://admyo.com/" >admyo.com</a></p>
                          <p>El detalle de la calificación recibida fue:</p>
                          <div class="col-sm-12">
                          <table>
                                <thead>
                                    <tr>
                                        <td style=" border-radius: 8px 0px 0px 0px; -moz-border-radius: 8px 0px 0px 0px; -webkit-border-radius: 8px 0px 0px 0px;">Pregunta</td>
                                        <td style=" border-radius: 0px 8px 0px 0px; -moz-border-radius: 0px 8px 0px 0px; -webkit-border-radius: 0px 8px 0px 0px;" >Calificación</td>
                                    </tr>
                                </thead>
                                <tbody id="tbody">
                                '.$html.'
                                </tbody>
                            </table>
                            
                          </div>
<p>•  Si no está conforme con esta calificación puede solicitar un cambio dentro de su perfil en admyo.com</p>
<p>•  Si este no es su cliente o proveedor puede darlo de baja en su perfil de empresa en admyo.com</p>
<p>•  Puede calificar a su '.$tipo_contrario.' haciendo clic en el siguiente botón.</p>
<div class="col-sm-12"><center><a href="'.$_SERVER['HTTP_HOST'].'/" ><button type="button" >CALIFIQUE</button></a><br><br></div>
<p><font color="#005288" style="font-weight: bold;">¿Quiere crecer su negocio diferenciándose de su competencia? </font>Descubra cuanto puede crecer su negocio requiriendo a sus clientes y proveedores que le califiquen. Promueva su perfil empresarial. <br><br></p>
<h4><font color="#005288" style="font-weight: bold;">¡Genere su perfil para que su negocio crezca!</font></h4>
<p>Saludos,<br> 
<font color="#005288" style="font-weight: bold;">Equipo admyo</font></p>     
                      <div class="col-sm-12" style="border-width: 1px; border-style: dashed; border-color: #fcb034; "></div>
                      <div class="col-sm-12"><br><p><font color="#cc9829" >The most important thing for a young man is to establish credit - a reputation and character”... <br><font style="font-weight: bold;">John D. Rockefeller</font></font></p></div>
<div class="col-sm-12"><p>IMPORTANTE: El presente correo electrónico es confidencial y/o puede contener información privilegiada. 
Su contenido no pretende ni debe considerarse como constitutivo de ninguna relación legal, contractual 
o de otra índole similar.</p></div>
                      </div></center></div>
</body>
</html>';

                    		$mail->Body = $body;
                    		$mail->Send();
		
				return $mail;
				
}
