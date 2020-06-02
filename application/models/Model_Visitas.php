<?php
class Model_Visitas extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
		$this->load->database();

		$this->load->helper('selec_Titulo');
	}
	//funcion para agregar una visita
	public function Addvisita($idempresa, $visitadora)
	{
		$array = array("IDEmpresa" => $idempresa, "IDEmpVisitadora" => $visitadora);
		$this->db->insert("visitas", $array);
	}
	//funcion para obtener los clientes
	public function ObtenerClientes($idempresa)
	{
		$clientes1 = [];
		//esta relacion es para obtener en la tabla tbrelacion las que esten como IDEmpresaPque es la principal
		$sql = $this->db->select('*')->where("IDEmpresaP='$idempresa' and Tipo='cliente'")->get("tbrelacion");
		if ($sql->num_rows() != 0) {
			foreach ($sql->result() as $provedor) {
				array_push($clientes1, array("num" => $provedor->IDEmpresaB));
			}
		}
		//ahora obtengo las que estan en la IDEmpresaB pero como cliente
		$sql = $this->db->select('*')->where("IDEmpresaB='$idempresa' and Tipo='proveedor'")->get("tbrelacion");
		$clientes2 = [];
		if ($sql->num_rows() != 0) {
			foreach ($sql->result() as $provedor) {
				array_push($clientes2, array("num" => $provedor->IDEmpresaP));
			}
		}
		$clientes = array_merge($clientes1, $clientes2);
		$clientes = array_map('unserialize', array_unique(array_map('serialize', $clientes)));
		return $clientes;
	}
	//funcion para obtener los proveedores
	public function ObtenerProveedores($idempresa)
	{
		$proveedores1 = [];
		//esta relacion es para obtener en la tabla tbrelacion las que esten como IDEmpresaPque es la principal
		$sql = $this->db->select('*')->where("IDEmpresaP='$idempresa' and Tipo='proveedor'")->get("tbrelacion");
		if ($sql->num_rows() != 0) {
			foreach ($sql->result() as $provedor) {
				array_push($proveedores1, array("num" => $provedor->IDEmpresaB));
			}
		}
		//ahora obtengo las que estan en la IDEmpresaB pero como cliente
		$sql = $this->db->select('*')->where("IDEmpresaB='$idempresa' and Tipo='cliente'")->get("tbrelacion");
		$proveedores2 = [];
		if ($sql->num_rows() != 0) {
			foreach ($sql->result() as $provedor) {
				array_push($proveedores2, array("num" => $provedor->IDEmpresaP));
			}
		}
		$proveedores = array_merge($proveedores1, $proveedores2);
		$proveedores = array_map('unserialize', array_unique(array_map('serialize', $proveedores)));
		return $proveedores;
	}
	//funcion para obtener los datos de una empresa
	public function DatosEmpresa($IDEmpresa)
	{
		$sql = $this->db->select('*')->where("IDEmpresa='$IDEmpresa'")->get('empresa');
		return $sql->result()[0];
	}
	//funcion para obtener todas las visitas de una empresa por año
	public function TVisitasA($IDEmpresa, $anio)
	{
		$sql = $this->db->select("*")->where("IDEmpresa='$IDEmpresa' and FechaVisita like '$anio-%'")->get("visitas");
		return $sql->result_array();
	}
	public function TVisitasM($IDEmpresa, $anio, $mes)
	{
		$sql = $this->db->select("*")->where("IDEmpresa='$IDEmpresa' and FechaVisita like '$anio-$mes%'")->get("visitas");
		if ($sql->num_rows() === 0) {
			return false;
		} else {
			return $sql->result();
		}
	}
	//funcion para vista general por año
	public function VisitasGeneral($IDEmpresa, $tip)
	{
		//primero obtengo el mes anterior al que me llega
		$TClientesM = [];
		$TProveedoresM = [];
		$TPotrasM = [];
		$TCPM = [];
		//ahora obtengo mis clienes8
		$Clientes = $this->ObtenerClientes($IDEmpresa);
	
		$Proveedores = $this->ObtenerProveedores($IDEmpresa);
		$TCPM = array_merge($Clientes, $Proveedores);
		$TCPM = array_map('unserialize', array_unique(array_map('serialize', $TCPM)));
		$TClientes = 0;
		$TProveedores = 0;
		$TAnonimas = 0;
		$TOtras = 0;


		$TClientesAN = 0;
		$TProveedoresAN = 0;
		$TAnonimasAN = 0;
		$TOtrasAN = 0;
		$Grafica1 = array("Clientes" => 0, "Proveedores" => 0, "Otras" => 0, "Anonimas" => 0);

		//ahora obtengo todas las visitas del mes actual
		if ($tip === "A") {
			$fechas = docemeces();
			$_Fecha_Fin_Actual = date('Y-m');
			$_Fecha_Inicio_Actual = date('Y') . "-01";
			$_Fecha_Fin_Pasado = (date('Y') - 1) . "-12";
			$_Fecha_Inicio_Pasado = (date('Y') - 1) . "-01";

			
			//obtengo el total de las visitas de los clientes
			foreach ($Clientes as $cliente) {
				$sql = $this->db->select('count(IDEmpresa) as total')->where("IDEmpresa='$IDEmpresa' and IDEmpVisitadora='" . $cliente["num"] . "' and DATE(FechaVisita) BETWEEN '".$_Fecha_Inicio_Actual. "-01' and '" . $_Fecha_Fin_Actual . "-31'")->get('visitas');

				if ($sql->num_rows() !== 0  || $sql->num_rows() !== null) {
					$TClientes = $TClientes + (int) $sql->result()[0]->total;
					$dat = $this->DatosEmpresa($cliente["num"]);
					array_push($TClientesM, array("Nombre_Comer" => $dat->Nombre_Comer, "Razon_Social" => $dat->Razon_Social, "num" => $cliente["num"]));
					
				} else {
					$TClientes = $TClientes + 0;
				}
				$sql2 = $this->db->select('count(IDEmpresa) as total')->where("IDEmpresa='$IDEmpresa' and IDEmpVisitadora='" . $cliente["num"] . "' and DATE(FechaVisita) BETWEEN '" . $_Fecha_Inicio_Pasado. "-01' and '" . $_Fecha_Fin_Pasado . "-31'")->get('visitas');
				if ($sql2->num_rows() !== 0  || $sql2->num_rows() !== null) {
					$TClientesAN = $TClientesAN + (int) $sql2->result()[0]->total;
				} else {
					$TClientesAN = $TClientesAN + 0;
				}
			}
			$Grafica1['Clientes'] = [$TClientesAN, $TClientes];

			//quito las empresas que ya estan en clientes 
			foreach ($Proveedores as $keys => $proveedor) {
				foreach ($Clientes as $key => $cliente) {
					if ($cliente === $proveedor) {
						unset($Proveedores[$keys]);
					}
				}
			}
				$Proveedores = array_map('unserialize', array_unique(array_map('serialize', $Proveedores)));
			
				//ahora si los busco
				foreach ($Proveedores as $cliente) {
					$sql = $this->db->select('count(IDEmpresa) as total')->where("IDEmpresa='$IDEmpresa' and IDEmpVisitadora='" . $cliente["num"] . "' and DATE(FechaVisita) BETWEEN '" . $_Fecha_Inicio_Actual . "-01' and '" . $_Fecha_Fin_Actual . "-31'")->get('visitas');
					if ($sql->num_rows() != 0) {
						$TProveedores = $TProveedores + (int) $sql->result()[0]->total;
						$dat = $this->DatosEmpresa($cliente["num"]);
						array_push($TProveedoresM, array("Nombre_Comer" => $dat->Nombre_Comer, "Razon_Social" => $dat->Razon_Social, "num" => $cliente["num"]));
					} else {
						$TProveedores = $TProveedores + 0;
					}
					$sql2 = $this->db->select('count(IDEmpresa) as total')->where("IDEmpresa='$IDEmpresa' and IDEmpVisitadora='" . $cliente["num"] . "' and DATE(FechaVisita) BETWEEN '" . $_Fecha_Inicio_Pasado . "-01' and '" . $_Fecha_Fin_Pasado . "-31'")->get('visitas');
					if ($sql->num_rows() != 0) {
						$TProveedoresAN = $TProveedoresAN + (int) $sql2->result()[0]->total;
					} else {
						$TProveedoresAN = $TProveedoresAN + 0;
					}
				}

				$Grafica1['Proveedores'] = [$TProveedoresAN, $TProveedores];


				$sql = $this->db->select('count(IDEmpresa) as total')->where("IDEmpresa='$IDEmpresa' and IDEmpVisitadora='0' and DATE(FechaVisita) BETWEEN  '" . $_Fecha_Inicio_Actual . "-01' and '" . $_Fecha_Fin_Actual . "-31'")->get('visitas');
				if ($sql->num_rows() !== 0  || $sql->num_rows() !== null) {
					$TAnonimas = (int) $sql->result()[0]->total;
				} else {
					$TAnonimas = 0;
				}
				
				$sql2 = $this->db->select('count(IDEmpresa) as total')->where("IDEmpresa='$IDEmpresa' and IDEmpVisitadora='0' and DATE(FechaVisita) BETWEEN  '" . $_Fecha_Inicio_Pasado . "-01' and '" . $_Fecha_Fin_Pasado . "-31'")->get('visitas');
				if ($sql2->num_rows() !== 0  || $sql2->num_rows() !== null) {
					$TAnonimasAN = (int) $sql2->result()[0]->total;
				} else {
					$TAnonimasAN = 0;
				}

				$Grafica1['Anonimas'] = [$TAnonimasAN, $TAnonimas];


				$vis = [];
				$anio2 = "";
				foreach ($fechas as $fecha) {

					$fecha_separada = explode("-", $fecha);
					$anio = $fecha_separada[0];
					if ($anio !== $anio2) {
						$VG = $this->TVisitasA($IDEmpresa, $anio);
						$anio2 = $anio;
						if (count($VG) !== 0) {
							array_push($vis, $VG);
						}
					}
				}

				foreach ($vis as $key => $empresa) {
					$empresavisitadora = $empresa[0]["IDEmpVisitadora"];
					foreach ($TCPM as $keys => $TDP) {
						if ($empresavisitadora === $TDP["num"]) {
							unset($vis[$key]);
						}
						if ($empresavisitadora === "0") {
							unset($vis[$key]);
						}
					}
				}


				$VG = array_map('unserialize', array_unique(array_map('serialize', $VG)));

			
				//ahora obtengo las visitas de los que no tiene  relacion
				foreach ($VG as $cliente) {
					$sql = $this->db->select('count(IDEmpresa) as total')->where("IDEmpresa='$IDEmpresa' and IDEmpVisitadora='" . $cliente["IDEmpVisitadora"] . "' and DATE(FechaVisita) BETWEEN '" . $_Fecha_Inicio_Actual . "-01' and '" . $_Fecha_Fin_Actual . "-31'")->get('visitas');
					if ($sql->num_rows() !== 0  || $sql->num_rows() !== null) {
						$TOtras = $TOtras + (int) $sql->result()[0]->total;
						$dat = $this->DatosEmpresa($cliente["IDEmpVisitadora"]);
						array_push($TPotrasM, array("Logo" => $dat->Logo, "Nombre_Comer" => $dat->Nombre_Comer, "Razon_Social" => $dat->Razon_Social, "num" => $dat->IDEmpresa));
					} else {
						$TOtras = $TOtras + 0;
					}
				
					$sql2 = $this->db->select('count(IDEmpresa) as total')->where("IDEmpresa='$IDEmpresa' and IDEmpVisitadora='" . $cliente["IDEmpVisitadora"] . "' and DATE(FechaVisita) BETWEEN '" . $_Fecha_Inicio_Pasado . "-01' and '" . $_Fecha_Fin_Pasado . "-31'")->get('visitas');

					if ($sql2->num_rows() !== 0  || $sql2->num_rows() !== null) {
						$TOtrasAN = $TOtrasAN + (int) $sql2->result()[0]->total;
					} else {
						$TOtrasAN = $TOtrasAN + 0;
					}
				}

				$Grafica1['Otras'] = [$TOtrasAN, $TOtras];
			
				//ahora obtnego las que que no son clientes ni proveedores

				$labels = [];
				$datats = [];
				$totales = (int) $TClientes + (int) $TProveedores + (int) $TAnonimas + (int) $TOtras;

				$data["Grafica1"] = $Grafica1;

				// ahora cuento cuantas visitas tube por mes asta el mes y año actual
				$actual = [];
				$visitast = 0;
				for ($i = 1; $i <= (int) date('m'); $i++) {
					if ($i > 10) {
						$mes = '0' + $i;
					} else {
						$mes = $i;
					}
					$visitas = $this->VisitasGeneralMesN(date('Y'), $mes, $IDEmpresa);
					$visitast = $visitast + $visitas;
					array_push($actual, $visitast);
				}
				$data["Total"] = $visitast;
				$pasado = [];
				$visitast = 0;
				for ($i = 1; $i <= 12; $i++) {
					if ($i > 10) {
						$mes = '0' + $i;
					} else {
						$mes = $i;
					}
					$visitas = $this->VisitasGeneralMesN((date('Y') - 1), $mes, $IDEmpresa);
					$visitast = $visitast + $visitas;
					array_push($pasado, $visitast);
				}

				$data['PeriodoG2'] = [(date('Y') - 1), date('Y')];
				$data["series1"] = array("dataActual" => $actual, "dataPasado" => $pasado);
				$data['text'] = date('Y');
				$data['Periodo'] = [(date('Y') - 1), date('Y')];
		}
			//otengo si es por mes
			if ($tip === "M") {
				$fechas = docemeces();
				$_mes_actual = $fechas[12] . "-" . date('d');
				$_mes_pasado = $fechas[11] . "-" . date('d');

				foreach ($Clientes as $cliente) {
					$sql = $this->db->select('count(IDEmpresa) as total')->where("IDEmpresa='$IDEmpresa' and IDEmpVisitadora='" . $cliente["num"] . "' and DATE(FechaVisita) BETWEEN '" . date('Y') . "-01' and '" . date('Y-m') . "' group by IDEmpVisitadora")->get('visitas');
					if ($sql->num_rows() !== null) {
						$TClientes = $TClientes + (int) $sql->result()[0]->total;
						$dat = $this->DatosEmpresa($cliente["num"]);
						array_push($TClientesM, array("Nombre_Comer" => $dat->Nombre_Comer, "Razon_Social" => $dat->Razon_Social, "num" => $cliente["num"]));
					} else {
						$TClientes = $TClientes + 0;
					}
					$sql2 = $this->db->select('count(IDEmpresa) as total')->where("IDEmpresa='$IDEmpresa' and IDEmpVisitadora='" . $cliente["num"] . "' and DATE(FechaVisita) BETWEEN '" . (date('Y') - 1) . "-01' and '" . (date('Y') - 1) . "-" . date('m') . "' group by IDEmpVisitadora")->get('visitas');
					if ($sql2->num_rows() !== null) {
						$TClientesAN = $TClientesAN + (int) $sql2->result()[0]->total;
					} else {
						$TClientesAN = $TClientesAN + 0;
					}
				}
				$Grafica1['Clientes'] = [$TClientesAN, $TClientes];
			
				//quito las empresas que ya estan en clientes 
				foreach ($Proveedores as $keys => $proveedor) {
					foreach ($Clientes as $key => $cliente) {
						if ($cliente === $proveedor) {
							unset($Proveedores[$keys]);
						}
					}
				}
				$Proveedores = array_map('unserialize', array_unique(array_map('serialize', $Proveedores)));
				//ahora si los busco
				foreach ($Proveedores as $cliente) {
					$sql = $this->db->select('count(IDEmpresa) as total')->where("IDEmpresa='$IDEmpresa' and IDEmpVisitadora='" . $cliente["num"] . "' and DATE(FechaVisita) BETWEEN '" . date('Y') . "-01' and '" . date('Y-m') . "' group by IDEmpVisitadora")->get('visitas');
					if ($sql->num_rows() !== null) {
						$TProveedores = $TProveedores + (int) $sql->result()[0]->total;
						$dat = $this->DatosEmpresa($cliente["num"]);
						array_push($TProveedoresM, array("Nombre_Comer" => $dat->Nombre_Comer, "Razon_Social" => $dat->Razon_Social, "num" => $cliente["num"]));
					} else {
						$TProveedores = $TProveedores + 0;
					}

					$sql2 = $this->db->select('count(IDEmpresa) as total')->where("IDEmpresa='$IDEmpresa' and IDEmpVisitadora='" . $cliente["num"] . "' and DATE(FechaVisita) BETWEEN '" . (date('Y') - 1) . "-01' and '" . (date('Y') - 1) . "-" . date('m') . "' group by IDEmpVisitadora")->get('visitas');
					if ($sql2->num_rows() != null) {
						$TProveedoresAN = $TProveedoresAN + (int) $sql2->result()[0]->total;
					} else {
						$TProveedoresAN = $TProveedoresAN + 0;
					}
				}

				$Grafica1['Proveedores'] = [$TProveedoresAN, $TProveedores];





				$sql = $this->db->select('count(IDEmpresa) as total')->where("IDEmpresa='$IDEmpresa' and IDEmpVisitadora='0' and DATE(FechaVisita) BETWEEN '" . date('Y') . "-01' and '" . date('Y-m') . "' group by IDEmpVisitadora")->get('visitas');
				if ($sql->num_rows() !== null) {
					$TAnonimas = (int) $sql->result()[0]->total;
				} else {
					$TAnonimas = 0;
				}

				$sql2 = $this->db->select('count(IDEmpresa) as total')->where("IDEmpresa='$IDEmpresa' and IDEmpVisitadora='0' and DATE(FechaVisita) BETWEEN '" . (date('Y') - 1) . "-01' and '" . (date('Y') - 1) . "-" . date('m') . "' group by IDEmpVisitadora")->get('visitas');
				if ($sql2->num_rows() !== null) {
					$TAnonimasAN = (int) $sql2->result()[0]->total;
				} else {
					$TAnonimasAN = 0;
				}

				$Grafica1['Anonimas'] = [$TAnonimasAN, $TAnonimas];

				//ahora obtnego las que que no son clientes ni proveedores

				$VG = $this->TVisitasA($IDEmpresa, date('Y'));

				if ($VG !== false) {
					foreach ($VG as $key => $empresa) {
						foreach ($TCPM as $keys => $TDP) {
							if ($empresa["IDEmpVisitadora"] === $TDP["num"]) {
								unset($VG[$key]);
							}
							if ($empresa["IDEmpVisitadora"] === "0") {
								unset($VG[$key]);
							}
						}
					}
					$VG = array_map('unserialize', array_unique(array_map('serialize', $VG)));
					//ahora obtengo las visitas de los que no tiene  relacion
					foreach ($VG as $cliente) {

						$sql = $this->db->select('count(IDEmpresa) as total')->where("IDEmpresa='$IDEmpresa' and IDEmpVisitadora='" . $cliente['IDEmpVisitadora'] . "' and DATE(FechaVisita) BETWEEN '" . date('Y') . "-01' and '" . date('Y-m') . "' group by IDEmpVisitadora")->get('visitas');
						if ($sql->num_rows() != null) {
							$TOtras = $TOtras + (int) $sql->result()[0]->total;
							$dat = $this->DatosEmpresa($cliente->IDEmpVisitadora);
							array_push($TPotrasM, array("Nombre_Comer" => $dat->Nombre_Comer, "Razon_Social" => $dat->Razon_Social, "num" => $cliente['IDEmpVisitadora']));
						} else {
							$TOtras = 0;
						}

						$sql2 = $this->db->select('count(IDEmpresa) as total')->where("IDEmpresa='$IDEmpresa' and IDEmpVisitadora='" . $cliente['IDEmpVisitadora'] . "' and DATE(FechaVisita) BETWEEN '" . (date('Y') - 1) . "-01' and '" . (date('Y') - 1) . "-" . date('m') . "' group by IDEmpVisitadora")->get('visitas');
						if ($sql2->num_rows() != null) {
							$TOtrasAN = $TOtrasAN + (int) $sql2->result()[0]->total;
						} else {
							$TOtrasAN = 0;
						}
					}
					$Grafica1['Otras'] = [$TOtrasAN, $TOtras];
				} else {
					$Grafica1['Otras'] = [0, 0];
				}

				$totales = (int) $TClientes + (int) $TProveedores + (int) $TAnonimas + (int) $TOtras;
				$data["Total"] = $totales;
				$data["Grafica1"] = $Grafica1;

				// ahora cuento cuantas visitas tube por mes asta el mes y año actual
				$actual = [];
				for ($i = 1; $i <= (int) date('m'); $i++) {
					if ($i > 10) {
						$mes = '0' + $i;
					} else {
						$mes = $i;
					}
					$visitas = $this->VisitasGeneralMesN(date('Y'), $mes, $IDEmpresa);
					array_push($actual, $visitas);
				}
				$pasado = [];
				for ($i = 1; $i <= 12; $i++) {
					if ($i > 10) {
						$mes = '0' + $i;
					} else {
						$mes = $i;
					}
					$visitas = $this->VisitasGeneralMesN((date('Y') - 1), $mes, $IDEmpresa);
					array_push($pasado, $visitas);
				}
				$data['PeriodoG2'] = [(date('Y') - 1), date('Y')];
				$data["series1"] = array("dataActual" => $actual, "dataPasado" => $pasado);
				$data['text'] = dame_mes(date('m')) . " " . date('Y');
				$data['Periodo'] = [dame_mes(date('m')) . " " . (date('Y') - 1), dame_mes(date('m')) . " " . date('Y')];
			}

			return $data;
	}
	//funcion para obtener el total de visitas por mes 
	public function VisitasGeneralMesN($anio, $mes, $empresa)
	{
		$sql = $this->db->select("count(IDEmpresa)  as total ,DATE_FORMAT(FechaVisita,'%m') as fecha ")->where("IDEmpresa='$empresa' and DATE(FechaVisita) between '$anio-$mes-01' and '$anio-$mes-31' group by DATE_FORMAT(FechaVisita,'%m')")->get("visitas");
		if ($sql->num_rows() === 0) {
			return 0;
		} else {
			return $sql->result()[0]->total;
		}
	}
	//funcion para obtener el total de visitas por mes 
	public function VisitasGeneraldiasN($anio, $mes, $d, $empresa)
	{
		$sql = $this->db->select("count(IDEmpresa)  as total ,DATE_FORMAT(FechaVisita,'%d') as fecha ")->where("IDEmpresa='$empresa' and DATE(FechaVisita) between '$anio-$mes-$d' and '$anio-$mes-$d' group by DATE_FORMAT(FechaVisita,'%d')")->get("visitas");
		if ($sql->num_rows() === 0) {
			return 0;
		} else {
			return $sql->result()[0]->total;
		}
	}


	//funcion para los detalles de las visitas recibidas
	public function detalles($IDEmpresa, $tip)
	{
		//obtengo los clientes y los proveedores
		$TClientesM = [];
		$TProveedoresM = [];
		$TPotrasM = [];
		$TCPM = [];
		//ahora obtengo mis clienes8
		$Clientes = $this->ObtenerClientes($IDEmpresa);
		$Proveedores = $this->ObtenerProveedores($IDEmpresa);
		$TCPM = array_merge($Clientes, $Proveedores);
		$TCPM = array_map('unserialize', array_unique(array_map('serialize', $TCPM)));
		$TClientes = 0;
		$TClientes2 = 0;
		$TProveedores = 0;
		$TProveedores2 = 0;
		$TAnonimas = 0;
		$TAnonimas2 = 0;
		$TOtras = 0;
		$TOtras2 = 0;
		if ($tip === "A") {
			$anioac = date("Y");
			$anioan = (int) date("Y") - 1;
			//primero obtengo las visitas de los clientes de este año
			foreach ($Clientes as $cliente) {
				$sql = $this->db->select('count(IDEmpresa) as total')->where("IDEmpresa='$IDEmpresa' and IDEmpVisitadora='" . $cliente["num"] . "' and DATE(FechaVisita) BETWEEN '" . date("Y") . "-01-01' and '" . date('Y-m-d') . "' group by IDEmpVisitadora")->get('visitas');
				if ($sql->num_rows() != 0) {
					$TClientes = $TClientes + (int) $sql->result()[0]->total;
					$dat = $this->DatosEmpresa($cliente["num"]);
					array_push($TClientesM, array("Nombre_Comer" => $dat->Nombre_Comer, "Razon_Social" => $dat->Razon_Social, "num" => $cliente["num"], "Logo" => $dat->Logo, "RFC" => $dat->RFC));
				}
			}
			//ahora obtento las visitas del año pasado
			foreach ($Clientes as $cliente) {
				$sql = $this->db->select('count(IDEmpresa) as total')->where("IDEmpresa='$IDEmpresa' and IDEmpVisitadora='" . $cliente["num"] . "' and DATE(FechaVisita) BETWEEN '" . $anioan . "-01-01' and '" . $anioan . "-" . date('m-d') . "' group by IDEmpVisitadora")->get('visitas');
				if ($sql->num_rows() != 0) {
					$TClientes2 = $TClientes2 + (int) $sql->result()[0]->total;
					$dat = $this->DatosEmpresa($cliente["num"]);
					array_push($TClientesM, array("Nombre_Comer" => $dat->Nombre_Comer, "Razon_Social" => $dat->Razon_Social, "num" => $cliente["num"], "Logo" => $dat->Logo, "RFC" => $dat->RFC));
				}
			}
			$TClientesM = array_map('unserialize', array_unique(array_map('serialize', $TClientesM)));
			//quito las empresas que ya estan en clientes 
			foreach ($Proveedores as $keys => $proveedor) {
				foreach ($Clientes as $key => $cliente) {
					if ($cliente === $proveedor) {
						unset($Proveedores[$keys]);
					}
				}
			}
			$Proveedores = array_map('unserialize', array_unique(array_map('serialize', $Proveedores)));
			//ahora si busco los proveedores de este año
			foreach ($Proveedores as $cliente) {
				$sql = $this->db->select('count(IDEmpresa) as total')->where("IDEmpresa='$IDEmpresa' and IDEmpVisitadora='" . $cliente["num"] . "' and DATE(FechaVisita) BETWEEN '" . date("Y") . "-01-01' and '" . date('Y-m-d') . "' group by IDEmpVisitadora")->get('visitas');
				if ($sql->num_rows() != 0) {
					$TProveedores = $TProveedores + (int) $sql->result()[0]->total;
					$dat = $this->DatosEmpresa($cliente["num"]);
					array_push($TProveedoresM, array("Nombre_Comer" => $dat->Nombre_Comer, "Razon_Social" => $dat->Razon_Social, "num" => $cliente["num"], "Logo" => $dat->Logo, "RFC" => $dat->RFC));
				}
			}
			//ahora los del año pasado
			foreach ($Proveedores as $cliente) {
				$sql = $this->db->select('count(IDEmpresa) as total')->where("IDEmpresa='$IDEmpresa' and IDEmpVisitadora='" . $cliente["num"] . "' and DATE(FechaVisita) BETWEEN '" . date("Y") . "-01-01' and '" . date('Y-m-d') . "' group by IDEmpVisitadora")->get('visitas');
				if ($sql->num_rows() != 0) {
					$TProveedores2 = $TProveedores2 + (int) $sql->result()[0]->total;
					$dat = $this->DatosEmpresa($cliente["num"]);
					array_push($TProveedoresM, array("Nombre_Comer" => $dat->Nombre_Comer, "Razon_Social" => $dat->Razon_Social, "num" => $cliente["num"], "Logo" => $dat->Logo, "RFC" => $dat->RFC));
				}
			}
			$TProveedoresM = array_map('unserialize', array_unique(array_map('serialize', $TProveedoresM)));
			//ahora obtengo las las otras
			$VG = $this->TVisitasA($IDEmpresa, date('Y'));
			if ($VG !== false) {
				foreach ($VG as $key => $empresa) {
					foreach ($TCPM as $keys => $TDP) {
						if ($empresa->IDEmpVisitadora === $TDP["num"]) {
							unset($VG[$key]);
						}
						if ($empresa->IDEmpVisitadora === "0") {
							unset($VG[$key]);
						}
					}
				}
				$VG = array_map('unserialize', array_unique(array_map('serialize', $VG)));
				//ahora obtengo las visitas de los que no tiene  relacion de este año
				foreach ($VG as $cliente) {
					$sql = $this->db->select('count(IDEmpresa) as total')->where("IDEmpresa='$IDEmpresa' and IDEmpVisitadora='" . $cliente->IDEmpVisitadora . "' and DATE(FechaVisita) BETWEEN '" . date("Y") . "-01-01' and '" . date('Y-m-d') . "' group by IDEmpVisitadora")->get('visitas');
					if ($sql->num_rows() != 0) {
						$TOtras = $TOtras + (int) $sql->result()[0]->total;
						$dat = $this->DatosEmpresa($cliente->IDEmpVisitadora);
						array_push($TPotrasM, array("Nombre_Comer" => $dat->Nombre_Comer, "Razon_Social" => $dat->Razon_Social, "num" => $cliente->IDEmpVisitadora, "Logo" => $dat->Logo, "RFC" => $dat->RFC));
					}
				}
				//ahora los que no tienen relacion del año pasado
				foreach ($VG as $cliente) {
					$sql = $this->db->select('count(IDEmpresa) as total')->where("IDEmpresa='$IDEmpresa' and IDEmpVisitadora='" . $cliente->IDEmpVisitadora . "' and DATE(FechaVisita) BETWEEN '" . $anioan . "-01-01' and '" . $anioan . date('m-d') . "' group by IDEmpVisitadora")->get('visitas');
					if ($sql->num_rows() != 0) {
						$TOtras = $TOtras + (int) $sql->result()[0]->total;
						$dat = $this->DatosEmpresa($cliente->IDEmpVisitadora);
						array_push($TPotrasM, array("Nombre_Comer" => $dat->Nombre_Comer, "Razon_Social" => $dat->Razon_Social, "num" => $cliente->IDEmpVisitadora, "Logo" => $dat->Logo, "RFC" => $dat->RFC));
					}
				}
				$TPotrasM = array_map('unserialize', array_unique(array_map('serialize', $TPotrasM)));
			}


			//ahora las anonimas
			$sql = $this->db->select('count(IDEmpresa) as total')->where("IDEmpresa='$IDEmpresa' and IDEmpVisitadora='0' and DATE(FechaVisita) BETWEEN '" . date("Y-m") . "-01' and '" . date('Y-m-d') . "' group by IDEmpVisitadora")->get('visitas');
			if ($sql->num_rows() != 0) {
				$TAnonimas = (int) $sql->result()[0]->total;
			} else {
				$TAnonimas = 0;
			}
			//ahora las anonimas del año pasado
			$sql = $this->db->select('count(IDEmpresa) as total')->where("IDEmpresa='$IDEmpresa' and IDEmpVisitadora='0' and DATE(FechaVisita) BETWEEN '" . $anioan . "-" . date("m") . "-01' and '" . $anioan . "-" . date('m-d') . "' group by IDEmpVisitadora")->get('visitas');
			if ($sql->num_rows() != 0) {
				$TAnonimas2 = (int) $sql->result()[0]->total;
			} else {
				$TAnonimas2 = 0;
			}
			$data["serieclietes"] = [['Años', "No de Visitas"], [date("Y"), $TClientes], [(string) $anioan, $TClientes2]];
			$data["serieproveedor"] = [['Años', "No de Visitas"], [date("Y"), $TProveedores], [(string) $anioan, $TProveedores2]];
			$data["serieotras"] = [['Años', "No de Visitas"], [date("Y"), $TOtras], [(string) $anioan, $TOtras2]];
			$data["serieproveedor"] = [['Años', "No de Visitas"], [date("Y"), $TAnonimas], [(string) $anioan, $TAnonimas2]];
		} //fin del if A
		$data["clientes"] = $TClientesM;
		$data["proveedores"] = $TProveedoresM;
		$data["otras"] = $TPotrasM;
		return $data;
	} //fin de la clase detalles

}
