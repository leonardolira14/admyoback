<?php 
if ( ! defined('BASEPATH')) exit('No direct script access allowed');
date_default_timezone_set('America/Mexico_City');
require_once("assets/php/conekta-php/lib/Conekta.php");
class Model_Conecta_qval extends CI_Model{
	public function __construct(){
		parent::__construct();
		$this->load->database();
		$this->constant="vkq4suQesgv6FVvfcWgc2TRQCmAc80iE";
		$this->apikey="key_A5ahydDgxgjiYsnKrDbisQ";
		$this->description="Plan Qval";
		$this->currency="MXN";
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
	public function CdatosUsuario($usuario){
		$sql="IDUsuario='$usuario'";
		$this->db->select('Correo');
		$this->db->where($sql);
		$this->db->from('usuarios');
		$resp=$this->db->get();
		return $resp->result()[0]->Correo;
	}
	public function Tarjeta($nombre,$correo,$token,$plan,$precio,$tel,$_tiempo){
		$precio=floatval($precio)*100;
		$idplan=tr_replace(" ","_",$plan).str_replace(' ', '', $nombre);
		if($_tiempo===1){
			$intervalo="month";
		}else if($precio==="1"){
			$intervalo="year";
		}
		try {
		 
		\Conekta\Conekta::setApiKey($this->apikey);
		\Conekta\Conekta::setApiVersion("2.0.0");
					$data=array(
				"name" => $nombre,
				"email" => $correo,
				"phone"=>$tel,
				"payment_sources" => array(
				  array(
					  "type" => "card",
					  "token_id" => $token
				  )
				)//payment_sources
			  );
		 $customer = \Conekta\Customer::create($data);
		
		 $plan = \Conekta\Plan::create(
			array(
					"id" => $idplan,
					"name" => $plan,
					"amount" => $precio,
					"currency" => "MXN",
					"interval" => $intervalo
			)//plan
		);
	
		 $order=$customer->createSubscription(
		 	array(
		 			'plan'=>$idplan
		 		)
			 );
			 if($order["status"]==="past_due"){
				//elimino el plan y el cliente y le mando la alerta de declinado
				$customer = \Conekta\Customer::find( $customer["id"]);
				$customer->delete();
				
				$plan = \Conekta\plan::find($idplan);
				$plan->delete();
				$data["status"]="fail";
				$data["error"]="tarjeta no valida";
			 }else{
				$data["status"]="active";
				$data["customer_id"]=$order["customer_id"];
				$data["plan_id"]=$order["plan_id"];
			 }
		  return $data;
		} 
		catch (Exception $e) {
			// Catch all exceptions including validation errors.
		 return $e->getMessage(); 
		}
	} 
	public function oxxo($amount,$nombre,$correo,$tel,$nombreplan){
		if(count($amount)<5)
				{
					$amount=floatval($amount)*100;
				}
		\Conekta\Conekta::setApiKey($this->apikey);
		$request=array(
			"line_items" => array(
			  array(
				"name" => $this->description." ".$nombreplan,
				"unit_price" =>$amount,
				"quantity" => 1
			  )//first line_item
			),
			 	"currency" => $this->currency,
				"customer_info" => array(
					  "name" => $nombre,
					  "email" => $correo,
					  "phone" => "+52".$tel
					),
					"charges" => array(
				array(
					"payment_method" => array(
							"type" => "oxxo_cash"
					)//payment_method
				) //first charge
			) //charges
		);
		try{
				$response = \Conekta\Order::create($request);  	
				return $response;
		}catch (Exception $e) {
		  	// Catch all exceptions including validation errors.
		  	return $e->getMessage(); 
		}
	}
	public function tranfer($amount, $nombre, $correo, $tel, $nombreplan){
		if(count($amount)<5)
				{
					$amount=floatval($amount)*100;
				}
		\Conekta\Conekta::setApiKey($this->apikey);
		$request=array(
				"line_items"=>array(
						array(
							"name" => $this->description." ".$nombreplan,
							"unit_price" =>$amount,
							"quantity" => 1
							)//unico item s pagar
					),
				"currency" => "MXN",
				"customer_info"=>array(
					"name" => $nombre,
			        "email" => $correo,
			        "phone" => $tel
					),
				"charges"=>array(
					array(
						"payment_method"=>array(
							"type"=> "spei"
							)
						)
					)
			);
		try {
			$response = \Conekta\Order::create($request);  	
				return $response;
		} catch (Exception $e) {
				return $e->getMessage(); 
		
		}
	}
	public function activarpago($ref){
		$sql="IDOrden='$ref'";
		$this->db->select('*');
		$this->db->where($sql);
		$resp=$this->db->get("reg_pago");
		//obtenfo los datos de la empresa
		$datosempresa=$this->DatosEmpresa($resp->resul()[0]->IDEmpresa);
		//obtenfo los datos del usuario que pago
		$datosusuario=$this->CdatosUsuario($resp->resul()[0]->IDUsurio);
		//ahora actualizo los dias de pago de la empresa
		$array=array("DiasPago"=>date('d'),"Esta"=>1);
		$this->db->where("IDEmpresa='".$resp->resul()[0]->IDEmpresa."'");
		$this->db->update("empresa",$array);
		ms_confirmpago($datosusuario,$datosempresa->Razon_Social,date('d'),date("m"),date("y"),$resp->resul()[0]->monto);
		
	}
	//funcion para obtener el resumen de una subsripcion
	public function obtner_info($num){
		$customer = \Conekta\Customer::find($num);
		vdebug($customer);
		//$subscription = $customer->subscription->resume();
		//return $subscription;
	}
}