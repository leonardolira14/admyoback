<?
/**
 *  modelo de preguntas todo lo relacionado con ellas
 * 
 */
class Model_Preguntas extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
    }

    public function Datos_Pregunta($IDPregunta){
        $listado = $this->db->select('*')->where("IDPregunta='$IDPregunta'")->get('preguntas_val');
        return $listado->row_array();
    }
    // funcion para obtener el listado de las preguntas por categoria
    public function configuracion_cuestionario($IDNival2,$tipo){
        $tipo = ucfirst($tipo);
        $listado = $this->db->select('*')->where("IDNivel2='$IDNival2' and Tipo='$tipo'")->get('tbconfigcuestionarios');
        return $listado->result_array();
    }
}