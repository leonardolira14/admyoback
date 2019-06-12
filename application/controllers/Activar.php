<?php
header('Access-Control-Allow-Origin: *');
date_default_timezone_set ("America/Mexico_City");
if (!defined('BASEPATH'))
   exit('No direct script access allowed');

class Activar extends CI_Controller {

	public function __construct(){
		parent::__construct();
		$this->load->model('Model_Notificaciones');
		$this->load->model('Model_Conecta_admyo');
		$this->load->model('Model_Empresa');
		$this->load->model('Model_Usuarios');
		
	}
	
	public function activarpago(){
		$result = @file_get_contents('php://input');
		$fp = fopen('acceso.txt', 'w+');
		fwrite($fp, $result);
		$data = json_decode($result);
		http_response_code(200);
		if(isset($data)){
			if($data->type=='charge.paid'){
				$numreferencia=$data->data->object->order_id;
				$this->Model_Conecta_admyo->activarpago($numreferencia);
			}
		}
		
	}
}