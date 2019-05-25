<?
/**
 * Modelo para Email
 */
class Model_Email extends CI_Model
{
	
	public function __construct()
	{
		parent::__construct();
		$this->load->library('email');
		$this->config = Array(
			'protocol' => 'smtp',
			'smtp_host' => 'smtp.ionos.es',
			'smtp_port' => 587,
			'smtp_user' => 'infoadmyo@admyo.com',
			'smtp_pass' => 'Admyo246*',
			'mailtype'  => 'html', 
			'charset' => 'utf-8',
			'wordwrap' => TRUE,
			'smtp_crypto'=>'tls',
			'wrapchars'=>76,
			'charset'=>'utf-8',
			'validate'=>TRUE,
			'crlf'=>"\r\n",
			'newline'=>"\r\n",
			'bcc_batch_mode'=>FALSE,
			'bcc_batch_size'=>200,

		);
		$this->email->initialize($this->config);
		$this->email->from('infoadmyo@admyo.com', 'InfoAdmyo');
	}
	//funcion para enviar un correo a las empresas avisandoa que la imgen de un clinte o proveedor cambio
	public function aviso_cambio_imagen($_correo_envio,$_Razon_Social,$_Razon_social_cambio,$_tipo){
		$this->email->to($_Correo_envio);
		$this->email->subject($_Razon_Social.", cambio de imagen");
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
		<img class="img-responsive" src='.$_SERVER['HTTP_HOST'].'/assets/img/images-mail/header-admyo-bienvenida.jpg" />
		</div></center>
		<center><div class="col-sm-7">
		<div class="col-sm-12">
		<center><br><h3>¡Bienvenido a admyo!</h3></center>
		</div>
		<p>Al parecer tu '.$_tipo.' ha recibido una calificacion en la cual se ha modificado la imagen de '.$_Razon_social_cambio.' y por lo tanto ha cambiado tu riesgo entra en <a href="https://admyo.com">admyo</a> para conoser este cambio.
		<p>Admyo,es una plataforma enfocada en la reputación empresarial para que las empresas puedan crecer su negocio y gestionar su riesgo. Si no has visto nuestro video, te recomendamos que lo mires <a href="https://player.vimeo.com/video/48771589?autoplay=1" >aquí</a>.</p>
		<p><font color="#005288" style="font-weight: bold;">¿Quiere crecer su negocio diferenciándose de su competencia? </font> Descubra cuanto puede crecer su negocio requiriendo a sus clientes y proveedores que le califiquen. Promueva su perfil empresarial. </p>
		<p><font color="#005288" style="font-weight: bold;">¿Quieres aparecer en nuestra página de inicio?, ¿Que publiquemos sobre ti en redes sociales?,</font> entre más participes calificando a empresas más puntos de public static idad y descuentos obtendrás. </p>
		<p><font color="#005288" style="font-weight: bold;">¿Quieres saber el riesgo que corres con tus clientes o proveedores?</font> Exígeles que tengan y mantengan un perfil  empresarial en <a href="https://admyo.com/" >admyo.com </a></p>
		<p><font color="#005288" style="font-weight: bold;">¿Quiere saber si puede aplicar a un descuento?</font> Si es una empresa con menos de un año de antigüedad puedes obtener un descuento del <font style="font-weight: bold;"> 50% </font>, además tenemos acuerdos con algunas cámaras y asociaciones. Para más información mándenos un email a <a href="mailto:promociones@admyo.com" target="_top">promociones@admyo.com</a><br><br></p>
		
		<div class="col-sm-12"><center><a href="'.$_SERVER['HTTP_HOST'].'" ><button type="button" >IR A ADMYO</button></a><br><br></div>
		<p>Si no basta con hacer clic, copie y pegue el siguiente enlace en su navegador. <br><a href="'.$_SERVER['HTTP_HOST'].'" >"'.$_SERVER['HTTP_HOST'].'</a><br><br></p>
		<h4><font color="#005288" style="font-weight: bold;">¡Genere su perfil para que su negocio crezca!</font></h4>
		<p>Saludos,<br> 
		<font color="#005288" style="font-weight: bold;">Equipo admyo</font></p>     
		<div class="col-sm-12" style="border-width: 1px; border-style: dashed; border-color: #fcb034; "></div>
		<div class="col-sm-12"><br><p><font color="#cc9829" >““… A man I do not trust could not get money from me on all the bonds in Christendom. I think that is the fundamental basis of business.”…<font style="font-weight: bold;">J. P. Morgan</font></font></p></div>
		<div class="col-sm-12"><p><a href="https://www.admyo.com/terminos-condiciones/" style="color: #21334d;" target="_blank"> Politica de privacidad  |  Términos y condiciones </a></p></div>
		</div></center></div>
		</body>';
		$this->email->message($body);
		$this->email->send();
	}
	//funcion para enviar correo de registro
	public function Activar_Usuario($Token,$_Correo_envio,$_Nombre,$_Apellido,$_Razon_Social){
		$this->email->to($_Correo_envio);
		$this->email->subject("Bienvenido ".$_Nombre." ".$_Apellido.", active su cuenta");
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
		<img class="img-responsive" src='.$_SERVER['HTTP_HOST'].'/assets/img/images-mail/header-admyo-bienvenida.jpg" />
		</div></center>
		<center><div class="col-sm-7">
		<div class="col-sm-12">
		<center><br><h3>¡Bienvenido a admyo!</h3></center>
		</div>
		<div class="col-sm-12" style="margin-top:40px">
		<h5>'.$_Razon_Social.'</h5>
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
		</body>';
		
		$this->email->message($body);
		$this->email->send();
	}
	//funcion para enviar una valoracion
	public function enviar_valoracion($_Correo_envio,$_Tipo_valoracion,$_Razon_social_emisora,$_Promedio,$_Razon_social_receptora,$_Preguntas)
	{
		$this->email->to($_Correo_envio);
		$this->email->subject("¡Ha Realizado una Calificación en ADMYO!");
		$html="";
		foreach ($_Preguntas as $key ){ 
			$html.='<tr><td>'.$key["Pregunta"].'</td><td style="text-align:center;">'.$key["Respuesta"].'</td></tr>';
		}
		($_Tipo_valoracion==="cliente") ? $_Tipo_contrario="proveedor" : $_Tipo_contrario="cliente";
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
		<p>Hola '.$_Razon_social_emisora.'</p>
		<p>Ha realizado una calificación con un promedio de '.$_Promedio.', para '.$_Razon_social_receptora.' como '.$_Tipo_valoracion.' en <a href="'.$_SERVER['HTTP_HOST'].'" >admyo.com</a></p>
		<p>El detalle de la calificación realizada fue:</p>
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
		<p>•  Puede calificar a su '.$_Tipo_valoracion.' haciendo clic en el siguiente botón.</p>
		<div class="col-sm-12"><center><a href="'.$_SERVER['HTTP_HOST'].'/calificar" ><button type="button" >CALIFIQUE</button></a><br><br></div>
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
		$this->email->message($body);
		$this->email->send();

	}
	//funcion para recibir un valoracion
	public function recibir_valoracion($_Correo_envio,$_Razon_social_emisora,$_Razon_social_receptora,$_Tipo_valoracnon,$_Preguntas,$_Promedio)
	{
		$this->email->to($_Correo_envio);
		$this->email->subject("¡Ha Recibido una Calificación en ADMYO!");
		$html="";
		foreach ($_Preguntas as $key ){ 
			$html.='<tr><td>'.$key["Pregunta"].'</td><td style="text-align:center;">'.$key["Respuesta"].'</td></tr>';
		}
		($_Tipo_valoracnon==="cliente") ? $_Tipo_contrario="proveedor" : $_Tipo_contrario="cliente";
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
		<p>Hola '.$_Razon_social_receptora.'</p>
		<p>Ha recibido una calificación con un promedio de '.$_Promedio.' realizada por '.$_Razon_social_emisora.' como '.$_Tipo_valoracnon.' en <a href="'.$_SERVER['HTTP_HOST'].'" >admyo.com</a></p>
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
		<p>•  Puede calificar a su '.$_Tipo_contrario.' haciendo clic en el siguiente botón.</p>
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
		$this->email->message($body);
		$this->email->send();
	}
	//funcion para enviar el correo electronico de preregistro de un usuario
	public function invitar_usuario($Razon_Social,$Correo,$pass,$Token)
	{
		$this->email->to($Correo);
		$this->email->subject($Razon_Social." le invitamos a valorar en Admyo.");
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

		$this->email->message($body);
		$this->email->send();

	}
	public function visita($_Razon_Social,$_Correo){
		$this->email->to($_Correo);
		$this->email->subject("Visita de su Perfil en admyo.");
		$body=' <html>
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
		<center><br><h3>¡Han Visitado tu perfil!</h3></center>
		</div>
		<p>Hola '.$_Razon_Social.'</p>
		<p>Hace unos momentos una empresa visito tu perfil en <a href="https://admyo.com/" >admyo.com</a>.</p>
		<div class="col-sm-12"><center><a href="'.$_SERVER['HTTP_HOST'].'/" ><button type="button" >IR A SU PERFIL</button></a><br><br></div>        

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
		$this->email->message($body);
		$this->email->send();
	}

	public function cambio_de_valoracion_emisora($RazonValorada,$_Correo,$FechaVal){
		$this->email->to($_Correo);
		$this->email->subject("Visita de su Perfil en admyo.");
		$body = '<html>
                        <head>
                          <meta charset="utf-8" />
                        </head>
                        <body>
                          <div style="overflow: hidden; margin: 0 auto; width: 1104px">
                            
                            <div style="float: left; margin: 10px 0 0 0; width: 69%; text-align:center;"><br/>
                                                          

                              <p>&nbsp;</p><p>&nbsp;</p><p>&nbsp;</p>
                              <h2> '.$RazonValorada.' solicita un cambio en la calificación que le dio el día '.$FechaVal.' </h2><br/><p>&nbsp;</p><p>&nbsp;</p>
                              <p align="left">Esta calificación permanecerá un máximo de, 3 meses en calificaciones pendientes de resolución, para que tome una acción.
                              <a href="https://admyo.com/" style="color: #fff; text-decoration: none;" target="_blank">
                                <div style="float: left; margin: 10px 10px 10px 10px; background-color: #e46c0a; color: #fff; width: 40%; -moz-border-radius: 15px 15px 15px 15px; -webkit-border-radius: 15px 15px 15px 15px; border-radius: 15px 15px 15px 15px; height: 135px">
                                  <br/><h1>CAMBIE CALIFICACIÓN</h1>
                                </div>
                              </a>
                              <a href="https://admyo.com" style="color: #fff; text-decoration: none;" target="_blank">
                                <div style="float: left; margin: 10px 10px 10px 10px; background-color: #21334d; color: #fff; width: 40%; -moz-border-radius: 15px 15px 15px 15px; -webkit-border-radius: 15px 15px 15px 15px; border-radius: 15px 15px 15px 15px; height: 135px;">
                                  <h1>MANTENER CALIFICACIÓN</h1>
                                </div>
                              <p>&nbsp;</p><p>&nbsp;</p>
                              </a><div style="clear: both"></div>
                              <p>&nbsp;</p><p align="left">Detalle de la calificación realizada el '.$FechaVal.'</p>
                            </div>
                          </div>
                          <div style="overflow: hidden; margin: 0 auto; width: 1104px">
                           <div style="float: left; margin: 0px 0px 0px 13px; width: 23%; background-color: #ffca69; text-align: center;" ><br/><br/>
                            <a href="https://admyo.com/planes-precios/" style="color: #fff; text-decoration: none;" target="_blank">
                              <div style="background-color: #e46c0a; color: #fff; width: 70%; margin: 0 auto;-moz-border-radius: 15px 15px 15px 15px; -webkit-border-radius: 15px 15px 15px 15px; border-radius: 15px 15px 15px 15px; height: 70px; padding: 2px;">
                                <h2>Promoción</h2>
                              </div><br/>
                            </a>
                          </div>
                          </div>
                          <div style="overflow: hidden; margin: 0 auto; width: 1104px">
                            <div style="float: left; margin: 0px 0px 0px 12px; width: 23%; background-color: #a6a6a6; text-align: center;" >
                              Si usted no se ha dado de alta en ADMYO, póngase en contacto con nosotros en: support@admyo.com
                              &nbsp;
                            </div>
                            <div style="float: left; width: 70%; text-align: center; margin: 10px 0 0 0; padding: 5px; color: #21334d;">
                              <p align="left">Si no esta de acuerdo con la calificación o no tiene relaciones comerciales con esta empresa, puede solicitar un cambio de calificación en su perfil de empresa en admyo.com</p>
                              <p>&nbsp;</p><span style="font-weight: bold; font-style: italic; font-size: 20px">"El marketing boca a boca ha sido siempre importante. Hoy es más importante que nunca debido al poder de internet"</span><br/> Joe Pulizzi y Newt Barrett, autores de Get Content Get Customers.
                            </div>
                            <div style="float: left; margin: 0px 0px 0px 12px; width: 23%; text-align: center;" >
                              <a href="https://admyo.com" style="color: #21334d;" target="_blank">www.admyo.com</a>
                              &nbsp;
                            </div>
                          </div>    
                          <div style="overflow: hidden; margin: 0 auto; width: 1104px"><a href="https://www.admyo.com/terminos-condiciones/" style="color: #21334d;" target="_blank"> Politica de privacidad  |  Términos y condiciones </a></div>
                        </body>
                      </html> ';
		$this->email->message($body);
		$this->email->send();
	}
}