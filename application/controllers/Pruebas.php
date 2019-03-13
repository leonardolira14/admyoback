<?
/**
 * 
 */
class Pruebas extends CI_Controller
{
	
	function __construct()
	{
		parent::__construct();
	}
	public function p1(){
		$key=encode("Emerson411_");
		vdebug(token($key));
	}
}