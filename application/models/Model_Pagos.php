<?php

class Model_Pagos extends CI_Model{
    function __construct()
    {
        parent::__construct();
        $this->load->database();
    }

    // funcion para ver si un cliente o una empresa no tiene algun registo de pago
    public function checkcliente($IDEmpresa){
        $sql = $this->db->select('*')->where("IDEmpresa='$IDEmpresa'")->get('tb_datos_pago_clientes');
        if($sql->num_rows()===0){
            return false;
        }else{
            return $sql->row_array();
        }
        
    }

    // funcion para agregar el registro de pagos
    public function addcliente($IDEmpresa,$IDCliente_pasarela){
        $array = array(
            "IDEmpresa"=> $IDEmpresa,
            "IDCliente_pasarela"=> $IDCliente_pasarela
        );
        return $this->db->insert("tb_datos_pago_clientes",$array);
    }

    // actualizar plan ID
    public function updatePlan($planID, $IDEmpresa){
        $array= array(
            "TipoCuenta"=> $planID
        );
        $respuesta = $this->db->where("IDEmpresa='$IDEmpresa'")->update('empresa',$array);
        return $respuesta;
    }
}