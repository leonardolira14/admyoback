<?

/**
 * 
 * vdebug($_Calificaciones);
 */
class Model_Configuraciongeneral extends CI_Model
{
    function __construct()
	{
		$this->load->database();
		
    }
    
    // funcion para la obtener las configuraciones de las categorias de preguntas
    public function getCategorias(){
        $_Resultadosr=$this->db->query("SELECT Configuracion FROM  tbconfiguracionesgen WHERE Categoria='categorias'");
        $respuesta = json_decode( $_Resultadosr->row_array()['Configuracion']);
        return  $respuesta;
    }
}