<?
/**
 * 
 */
class Model_RiesgoN extends CI_Model
{
	
	function __construct()
	{
		parent::__construct();
		$this->load->database();
		$this->load->helper('selec_Titulo');
		$this->load->model('Model_Empresa');
        $this->load->model('Model_Calificaciones');
        $this->load->model('Model_Clieprop');
        $this->load->model('Model_Preguntas');
    }

      /* funcion para obtener los detalles del riesgo de una empresa
     @ IDSector Nivel 
     @ quienes
     @ comoque
     @ periodo
     @ $IDEmpresa 
    */
        public function detalleRiesgo($IDEmpresa,$IDGiro,$Quienes,$ComoQue,$Periodo){
            //obtengo el periodo
            $listas_fechas=_fechas_array_List($Periodo);
            // vdebug($listas_fechas);
            $lista_clientes = [];
            
            // obtengo el giro principal de la empresa si no traigo filtros
            if($IDGiro === ''){
                $IDGiro = '1';
            }

            // tengo que obtener la lista de preguntas dependiendo el Giro
             // obtener las preguntas de ese giro 
            $listado_preguntas = $this->Model_Preguntas->configuracion_cuestionario($IDGiro,$ComoQue);
            //vdebug($listado_preguntas);
            $preguntas_cumplimiento=explode(',',$listado_preguntas[0]['Cumplimiento']);
            $preguntas_calidad=explode(',',$listado_preguntas[0]['Calidad']);
            $preguntas_sanidad=explode(',',$listado_preguntas[0]['Sanidad']);
            $preguntas_socioambiental=explode(',',$listado_preguntas[0]['Socioambiental']);
            $preguntas_oferta=explode(',',$listado_preguntas[0]['Oferta']);

            //vdebug($preguntas_cumplimiento);
            
            //empiezo con calidad, tengo que recorrer cada pregunta y ver cuantas veces fue respondida 
            // a la lista de clientes o proveedores que estubieron en en cada fecha solo las preguntas de SI y NO
            /*
                variables para guardas los datos de cada seccion
            */
            


            $ListaCumplimineto=[];
            $ListaSocioambiental=[];
            $ListaSanidad=[];
            $ListaOferta=[];
            // calidad
           $ListaCalidad['datospregunta']=[];
            foreach($preguntas_calidad as $pregunta){
                
                //vdebug($pregunta);
                $ListaCalidad_data['totalContestadas']=[];
               $ListaCalidad_data['Si']=[];
                $ListaCalidad_data['No']=[];
                $ListaCalidad_data['Na']=[];
                $ListaCalidad_data['Ns']=[];
                $ListaCalidad_data['labels']=[];
                
                $datos_pregunta = $this->Model_Preguntas->Datos_Pregunta($pregunta);
                 $total_contestadas =0;
                // vdebug($datos_pregunta['Forma']);
                if($datos_pregunta['Forma']==='SI/NO' || $datos_pregunta['Forma']==='SI/NO/NA' || $datos_pregunta['Forma']==='SI/NO/NS'){
                    //  con las fechas enlistadas 
                   
                    foreach($listas_fechas as $fecha){
                          array_push($ListaCalidad_data['labels'],$fecha);
                        //obtengo los clientes o proveedores que son de esta fecha hacia atras
                        $listaClientes= $this->getClientesProveedores($IDEmpresa,$fecha,$Periodo,$Quienes);
                       // vdebug($listaClientes);
                        
                        //ahora tengo que ver por cada cliente cuetas veces hicieron esa pregunta
                        $total_si=0;
                        $total_no=0;
                        $total_na=0;
                        $total_ns=0;
                        foreach($listaClientes as $cliente){
                            $Respuesta =$this-> PreguntaRespondidaFecha($ComoQue,$pregunta,$fecha,$cliente['IDEmpresaB'],$datos_pregunta['Forma'],$Periodo);
                            $total_contestadas = $total_contestadas +$Respuesta['NumeroTotal'];
                            if($datos_pregunta['Forma']==='SI/NO'){
                                $total_si= $total_si + $Respuesta['SI'] ;
                                $total_no= $total_no + $Respuesta['NO'];
                            }
                            if($datos_pregunta['Forma']==='SI/NO/NA'){
                                $total_si= $total_si + $Respuesta['SI'] ;
                                $total_no= $total_no + $Respuesta['NO'];
                                 $total_na=$total_na + $Respuesta['NA'];
                            }
                            if($datos_pregunta['Forma']==='SI/NO/NS'){
                                $total_si= $total_si + $Respuesta['SI'] ;
                                $total_no= $total_no + $Respuesta['NO'];
                                $total_ns=$total_ns + $Respuesta['NS'];
                            }
                        }
                        array_push($ListaCalidad_data['totalContestadas'],$total_contestadas);
                        if($datos_pregunta['Forma']==='SI/NO'){
                            array_push($ListaCalidad_data['Si'],$total_si);
                            array_push($ListaCalidad_data['No'],$total_no);
                        }
                        if($datos_pregunta['Forma']==='SI/NO/NA' || $datos_pregunta['Forma']==='SI/NO/NS'){
                            array_push($ListaCalidad_data['Si'],$total_si);
                            array_push($ListaCalidad_data['No'],$total_no);
                            array_push($ListaCalidad_data['Na'],$total_na);
                        }
                        if($datos_pregunta['Forma']==='SI/NO/NS'){
                            array_push($ListaCalidad_data['Si'],$total_si);
                            array_push($ListaCalidad_data['No'],$total_no);
                            array_push($ListaCalidad_data['Ns'],$total_ns);
                        }
                       
                        
                    }
                   
                    array_push($ListaCalidad['datospregunta'],array(
                        "Pregunta"=>$datos_pregunta['Pregunta'],
                        "labels"=>$ListaCalidad_data['labels'],
                        "data"=>[
                            (object)["data"=>$ListaCalidad_data['Si'],"label"=> 'Si',"backgroundColor"=> '#10E0D0'],
                            (object)["data"=>$ListaCalidad_data['No'],"label"=>'No',"backgroundColor"=> '#F2143F'],
                             (object)["data"=>$ListaCalidad_data['Na'],"label"=>'Na',"backgroundColor"=> '#8F8F8F'],
                            (object)["data"=>$ListaCalidad_data['Ns'],"label"=>'NS',"backgroundColor"=> '#8F8F8F'],
                        ],
                        "dataTotal"=>[
                            (object)["data"=>$ListaCalidad_data['totalContestadas'],"label"=> 'Calificaciones Recibidas'],
                            
                        ]
                    ));
                    
                }
                          
            }
            
            // cumplimiento
            $ListaCumplimineto['datospregunta']=[];
            foreach($preguntas_cumplimiento as $pregunta){
                //vdebug($pregunta);
                $ListaCalidad_data['totalContestadas']=[];
               $ListaCalidad_data['Si']=[];
                $ListaCalidad_data['No']=[];
                $ListaCalidad_data['Na']=[];
                $ListaCalidad_data['Ns']=[];
                $ListaCalidad_data['labels']=[];
                
                $datos_pregunta = $this->Model_Preguntas->Datos_Pregunta($pregunta);
                 $total_contestadas =0;
               
                if($datos_pregunta['Forma']==='SI/NO' || $datos_pregunta['Forma']==='SI/NO/NA' || $datos_pregunta['Forma']==='SI/NO/NS'){
                    //  con las fechas enlistadas 
                   
                    foreach($listas_fechas as $fecha){
                          array_push($ListaCalidad_data['labels'],$fecha);
                        //obtengo los clientes o proveedores que son de esta fecha hacia atras
                        $listaClientes= $this->getClientesProveedores($IDEmpresa,$fecha,$Periodo,$Quienes);
                       // vdebug($listaClientes);
                        
                        //ahora tengo que ver por cada cliente cuetas veces hicieron esa pregunta
                        $total_si=0;
                        $total_no=0;
                        $total_na=0;
                        $total_ns=0;
                        foreach($listaClientes as $cliente){
                            $Respuesta =$this-> PreguntaRespondidaFecha($ComoQue,$pregunta,$fecha,$cliente['IDEmpresaB'],$datos_pregunta['Forma'],$Periodo);
                            $total_contestadas = $total_contestadas +$Respuesta['NumeroTotal'];
                            if($datos_pregunta['Forma']==='SI/NO'){
                                $total_si= $total_si + $Respuesta['SI'] ;
                                $total_no= $total_no + $Respuesta['NO'];
                            }
                            if($datos_pregunta['Forma']==='SI/NO/NA'){
                                $total_si= $total_si + $Respuesta['SI'] ;
                                $total_no= $total_no + $Respuesta['NO'];
                                 $total_na=$total_na + $Respuesta['NA'];
                            }
                            if($datos_pregunta['Forma']==='SI/NO/NS'){
                                $total_si= $total_si + $Respuesta['SI'] ;
                                $total_no= $total_no + $Respuesta['NO'];
                                $total_ns=$total_ns + $Respuesta['NS'];
                            }
                        }
                        array_push($ListaCalidad_data['totalContestadas'],$total_contestadas);
                        if($datos_pregunta['Forma']==='SI/NO'){
                            array_push($ListaCalidad_data['Si'],$total_si);
                            array_push($ListaCalidad_data['No'],$total_no);
                        }
                        if($datos_pregunta['Forma']==='SI/NO/NA' || $datos_pregunta['Forma']==='SI/NO/NS'){
                            array_push($ListaCalidad_data['Si'],$total_si);
                            array_push($ListaCalidad_data['No'],$total_no);
                            array_push($ListaCalidad_data['Na'],$total_na);
                        }
                        if($datos_pregunta['Forma']==='SI/NO/NS'){
                            array_push($ListaCalidad_data['Si'],$total_si);
                            array_push($ListaCalidad_data['No'],$total_no);
                            array_push($ListaCalidad_data['Ns'],$total_ns);
                        }
                       
                        
                    }
                   
                    array_push($ListaCumplimineto['datospregunta'],array(
                        "Pregunta"=>$datos_pregunta['Pregunta'],
                        "labels"=>$ListaCalidad_data['labels'],
                        "data"=>[
                            (object)["data"=>$ListaCalidad_data['Si'],"label"=> 'Si',"backgroundColor"=> '#10E0D0'],
                            (object)["data"=>$ListaCalidad_data['No'],"label"=>'No',"backgroundColor"=> '#F2143F'],
                             (object)["data"=>$ListaCalidad_data['Na'],"label"=>'Na',"backgroundColor"=> '#8F8F8F'],
                            (object)["data"=>$ListaCalidad_data['Ns'],"label"=>'NS',"backgroundColor"=> '#8F8F8F'],
                        ],
                        "dataTotal"=>[
                            (object)["data"=>$ListaCalidad_data['totalContestadas'],"label"=> 'Calificaciones Recibidas'],
                            
                        ]
                    ));
                    
                }
                          
            }

            // Socioambiental
            $ListaSocioambiental['datospregunta']=[];
            foreach($preguntas_socioambiental as $pregunta){
                //vdebug($pregunta);
                $ListaCalidad_data['totalContestadas']=[];
               $ListaCalidad_data['Si']=[];
                $ListaCalidad_data['No']=[];
                $ListaCalidad_data['Na']=[];
                $ListaCalidad_data['Ns']=[];
                $ListaCalidad_data['labels']=[];
                
                $datos_pregunta = $this->Model_Preguntas->Datos_Pregunta($pregunta);
                 $total_contestadas =0;
               
                if($datos_pregunta['Forma']==='SI/NO' || $datos_pregunta['Forma']==='SI/NO/NA' || $datos_pregunta['Forma']==='SI/NO/NS'){
                    //  con las fechas enlistadas 
                   
                    foreach($listas_fechas as $fecha){
                          array_push($ListaCalidad_data['labels'],$fecha);
                        //obtengo los clientes o proveedores que son de esta fecha hacia atras
                        $listaClientes= $this->getClientesProveedores($IDEmpresa,$fecha,$Periodo,$Quienes);
                       // vdebug($listaClientes);
                        
                        //ahora tengo que ver por cada cliente cuetas veces hicieron esa pregunta
                        $total_si=0;
                        $total_no=0;
                        $total_na=0;
                        $total_ns=0;
                        foreach($listaClientes as $cliente){
                            $Respuesta =$this-> PreguntaRespondidaFecha($ComoQue,$pregunta,$fecha,$cliente['IDEmpresaB'],$datos_pregunta['Forma'],$Periodo);
                            $total_contestadas = $total_contestadas +$Respuesta['NumeroTotal'];
                            if($datos_pregunta['Forma']==='SI/NO'){
                                $total_si= $total_si + $Respuesta['SI'] ;
                                $total_no= $total_no + $Respuesta['NO'];
                            }
                            if($datos_pregunta['Forma']==='SI/NO/NA'){
                                $total_si= $total_si + $Respuesta['SI'] ;
                                $total_no= $total_no + $Respuesta['NO'];
                                 $total_na=$total_na + $Respuesta['NA'];
                            }
                            if($datos_pregunta['Forma']==='SI/NO/NS'){
                                $total_si= $total_si + $Respuesta['SI'] ;
                                $total_no= $total_no + $Respuesta['NO'];
                                $total_ns=$total_ns + $Respuesta['NS'];
                            }
                        }
                        array_push($ListaCalidad_data['totalContestadas'],$total_contestadas);
                        if($datos_pregunta['Forma']==='SI/NO'){
                            array_push($ListaCalidad_data['Si'],$total_si);
                            array_push($ListaCalidad_data['No'],$total_no);
                        }
                        if($datos_pregunta['Forma']==='SI/NO/NA' || $datos_pregunta['Forma']==='SI/NO/NS'){
                            array_push($ListaCalidad_data['Si'],$total_si);
                            array_push($ListaCalidad_data['No'],$total_no);
                            array_push($ListaCalidad_data['Na'],$total_na);
                        }
                        if($datos_pregunta['Forma']==='SI/NO/NS'){
                            array_push($ListaCalidad_data['Si'],$total_si);
                            array_push($ListaCalidad_data['No'],$total_no);
                            array_push($ListaCalidad_data['Ns'],$total_ns);
                        }
                       
                        
                    }
                   
                    array_push($ListaSocioambiental['datospregunta'],array(
                        "Pregunta"=>$datos_pregunta['Pregunta'],
                        "labels"=>$ListaCalidad_data['labels'],
                        "data"=>[
                            (object)["data"=>$ListaCalidad_data['Si'],"label"=> 'Si',"backgroundColor"=> '#10E0D0'],
                            (object)["data"=>$ListaCalidad_data['No'],"label"=>'No',"backgroundColor"=> '#F2143F'],
                             (object)["data"=>$ListaCalidad_data['Na'],"label"=>'Na',"backgroundColor"=> '#8F8F8F'],
                            (object)["data"=>$ListaCalidad_data['Ns'],"label"=>'NS',"backgroundColor"=> '#8F8F8F'],
                        ],
                        "dataTotal"=>[
                            (object)["data"=>$ListaCalidad_data['totalContestadas'],"label"=> 'Calificaciones Recibidas'],
                            
                        ]
                    ));
                    
                }
                          
            }
            // fin de socioambiental

            // Sanidad
            $ListaSanidad['datospregunta']=[];
            foreach($preguntas_sanidad as $pregunta){
                //vdebug($pregunta);
                $ListaCalidad_data['totalContestadas']=[];
               $ListaCalidad_data['Si']=[];
                $ListaCalidad_data['No']=[];
                $ListaCalidad_data['Na']=[];
                $ListaCalidad_data['Ns']=[];
                $ListaCalidad_data['labels']=[];
                
                $datos_pregunta = $this->Model_Preguntas->Datos_Pregunta($pregunta);
                 $total_contestadas =0;
               
                if($datos_pregunta['Forma']==='SI/NO' || $datos_pregunta['Forma']==='SI/NO/NA' || $datos_pregunta['Forma']==='SI/NO/NS'){
                    //  con las fechas enlistadas 
                   
                    foreach($listas_fechas as $fecha){
                          array_push($ListaCalidad_data['labels'],$fecha);
                        //obtengo los clientes o proveedores que son de esta fecha hacia atras
                        $listaClientes= $this->getClientesProveedores($IDEmpresa,$fecha,$Periodo,$Quienes);
                       // vdebug($listaClientes);
                        
                        //ahora tengo que ver por cada cliente cuetas veces hicieron esa pregunta
                        $total_si=0;
                        $total_no=0;
                        $total_na=0;
                        $total_ns=0;
                        foreach($listaClientes as $cliente){
                            $Respuesta =$this-> PreguntaRespondidaFecha($ComoQue,$pregunta,$fecha,$cliente['IDEmpresaB'],$datos_pregunta['Forma'],$Periodo);
                            $total_contestadas = $total_contestadas +$Respuesta['NumeroTotal'];
                            if($datos_pregunta['Forma']==='SI/NO'){
                                $total_si= $total_si + $Respuesta['SI'] ;
                                $total_no= $total_no + $Respuesta['NO'];
                            }
                            if($datos_pregunta['Forma']==='SI/NO/NA'){
                                $total_si= $total_si + $Respuesta['SI'] ;
                                $total_no= $total_no + $Respuesta['NO'];
                                 $total_na=$total_na + $Respuesta['NA'];
                            }
                            if($datos_pregunta['Forma']==='SI/NO/NS'){
                                $total_si= $total_si + $Respuesta['SI'] ;
                                $total_no= $total_no + $Respuesta['NO'];
                                $total_ns=$total_ns + $Respuesta['NS'];
                            }
                        }
                        array_push($ListaCalidad_data['totalContestadas'],$total_contestadas);
                        if($datos_pregunta['Forma']==='SI/NO'){
                            array_push($ListaCalidad_data['Si'],$total_si);
                            array_push($ListaCalidad_data['No'],$total_no);
                        }
                        if($datos_pregunta['Forma']==='SI/NO/NA' || $datos_pregunta['Forma']==='SI/NO/NS'){
                            array_push($ListaCalidad_data['Si'],$total_si);
                            array_push($ListaCalidad_data['No'],$total_no);
                            array_push($ListaCalidad_data['Na'],$total_na);
                        }
                        if($datos_pregunta['Forma']==='SI/NO/NS'){
                            array_push($ListaCalidad_data['Si'],$total_si);
                            array_push($ListaCalidad_data['No'],$total_no);
                            array_push($ListaCalidad_data['Ns'],$total_ns);
                        }
                       
                        
                    }
                   
                    array_push($ListaSanidad['datospregunta'],array(
                        "Pregunta"=>$datos_pregunta['Pregunta'],
                        "labels"=>$ListaCalidad_data['labels'],
                        "data"=>[
                            (object)["data"=>$ListaCalidad_data['Si'],"label"=> 'Si',"backgroundColor"=> '#10E0D0'],
                            (object)["data"=>$ListaCalidad_data['No'],"label"=>'No',"backgroundColor"=> '#F2143F'],
                             (object)["data"=>$ListaCalidad_data['Na'],"label"=>'Na',"backgroundColor"=> '#8F8F8F'],
                            (object)["data"=>$ListaCalidad_data['Ns'],"label"=>'NS',"backgroundColor"=> '#8F8F8F'],
                        ],
                        "dataTotal"=>[
                            (object)["data"=>$ListaCalidad_data['totalContestadas'],"label"=> 'Calificaciones Recibidas'],
                            
                        ]
                    ));
                    
                }
                          
            }
            // fin de sanidad




            // oferta
            $ListaOferta['datospregunta']=[];
            foreach($preguntas_oferta as $pregunta){
                //vdebug($pregunta);
                $ListaCalidad_data['totalContestadas']=[];
               $ListaCalidad_data['Si']=[];
                $ListaCalidad_data['No']=[];
                $ListaCalidad_data['Na']=[];
                $ListaCalidad_data['Ns']=[];
                $ListaCalidad_data['labels']=[];
                
                $datos_pregunta = $this->Model_Preguntas->Datos_Pregunta($pregunta);
                 $total_contestadas =0;
               
                if($datos_pregunta['Forma']==='SI/NO' || $datos_pregunta['Forma']==='SI/NO/NA' || $datos_pregunta['Forma']==='SI/NO/NS'){
                    //  con las fechas enlistadas 
                   
                    foreach($listas_fechas as $fecha){
                          array_push($ListaCalidad_data['labels'],$fecha);
                        //obtengo los clientes o proveedores que son de esta fecha hacia atras
                        $listaClientes= $this->getClientesProveedores($IDEmpresa,$fecha,$Periodo,$Quienes);
                       // vdebug($listaClientes);
                        
                        //ahora tengo que ver por cada cliente cuetas veces hicieron esa pregunta
                        $total_si=0;
                        $total_no=0;
                        $total_na=0;
                        $total_ns=0;
                        foreach($listaClientes as $cliente){
                            $Respuesta =$this-> PreguntaRespondidaFecha($ComoQue,$pregunta,$fecha,$cliente['IDEmpresaB'],$datos_pregunta['Forma'],$Periodo);
                            $total_contestadas = $total_contestadas +$Respuesta['NumeroTotal'];
                            if($datos_pregunta['Forma']==='SI/NO'){
                                $total_si= $total_si + $Respuesta['SI'] ;
                                $total_no= $total_no + $Respuesta['NO'];
                            }
                            if($datos_pregunta['Forma']==='SI/NO/NA'){
                                $total_si= $total_si + $Respuesta['SI'] ;
                                $total_no= $total_no + $Respuesta['NO'];
                                 $total_na=$total_na + $Respuesta['NA'];
                            }
                            if($datos_pregunta['Forma']==='SI/NO/NS'){
                                $total_si= $total_si + $Respuesta['SI'] ;
                                $total_no= $total_no + $Respuesta['NO'];
                                $total_ns=$total_ns + $Respuesta['NS'];
                            }
                        }
                        array_push($ListaCalidad_data['totalContestadas'],$total_contestadas);
                        if($datos_pregunta['Forma']==='SI/NO'){
                            array_push($ListaCalidad_data['Si'],$total_si);
                            array_push($ListaCalidad_data['No'],$total_no);
                        }
                        if($datos_pregunta['Forma']==='SI/NO/NA' || $datos_pregunta['Forma']==='SI/NO/NS'){
                            array_push($ListaCalidad_data['Si'],$total_si);
                            array_push($ListaCalidad_data['No'],$total_no);
                            array_push($ListaCalidad_data['Na'],$total_na);
                        }
                        if($datos_pregunta['Forma']==='SI/NO/NS'){
                            array_push($ListaCalidad_data['Si'],$total_si);
                            array_push($ListaCalidad_data['No'],$total_no);
                            array_push($ListaCalidad_data['Ns'],$total_ns);
                        }
                       
                        
                    }
                   
                    array_push($ListaCumplimineto['datospregunta'],array(
                        "Pregunta"=>$datos_pregunta['Pregunta'],
                        "labels"=>$ListaCalidad_data['labels'],
                        "data"=>[
                            (object)["data"=>$ListaCalidad_data['Si'],"label"=> 'Si',"backgroundColor"=> '#10E0D0'],
                            (object)["data"=>$ListaCalidad_data['No'],"label"=>'No',"backgroundColor"=> '#F2143F'],
                             (object)["data"=>$ListaCalidad_data['Na'],"label"=>'Na',"backgroundColor"=> '#8F8F8F'],
                            (object)["data"=>$ListaCalidad_data['Ns'],"label"=>'NS',"backgroundColor"=> '#8F8F8F'],
                        ],
                       "dataTotal"=>[
                            (object)["data"=>$ListaCalidad_data['totalContestadas'],"label"=> 'Calificaciones Recibidas'],
                            
                        ]
                ));
                    
                }
                          
            }
            $data["calidad"] = $ListaCalidad;
            $data["cumplimiento"] = $ListaCumplimineto;
            $data["sanidad"] = $ListaSanidad;
            $data['socioambiental'] =  $ListaSocioambiental;
            $data["oferta"] = $ListaOferta;

           return $data;
                
            /// aqui termina las preguntas de cumplimiento


        }










    /* funcion para obtener el riego general de una empresa
     @ IDEmpresa
     @ IDGiro
     @ Periodo
     @ Quienes (Clientes o proveedores)
     @ comoQue (Como Clientes o Como Proveedores)
    */
    public function RiesgoGen($IDEmpresa,$IDGiro,$Periodo,$Quienes,$comoQue){
        
       //obtengo el periodo
         $listas_fechas=dame_rangos_fecha($Periodo);
         //vdebug($listas_fechas);
        $lista_clientes = [];
        
        // obtengo el giro principal de la empresa si no traigo filtros
        if($IDGiro === ''){
            $IDGiro = '1';
        }
         // obtener las preguntas de ese giro 
        $listado_preguntas = $this->Model_Preguntas->configuracion_cuestionario($IDGiro,$comoQue);
        //vdebug($listado_preguntas);
        $preguntas_cumplimiento=explode(',',$listado_preguntas[0]['Cumplimiento']);
        $preguntas_calidad=explode(',',$listado_preguntas[0]['Calidad']);
        $preguntas_socioambiental=explode(',',$listado_preguntas[0]['Socioambiental']);
        $preguntas_sanidad=explode(',',$listado_preguntas[0]['Sanidad']);
        $preguntas_oferta=explode(',',$listado_preguntas[0]['Oferta']);

      
              
        $cantidad_por_categoria=[];
        $porcentaje_categoria_cumplimiento=[];
        $porcentaje_categoria_calidad = [];
        $porcentaje_categoria_sanidad = [];
        $porcentaje_categoria_socioambiental = [];
         $porcentaje_categoria_oferta = [];
        $listas_fechas_ = _fechas_array_List($Periodo);
        //vdebug($listas_fechas_);
            $label_Grafica=[];
            $data_grafica_General_SD = [];
            $data_grafica_General_Mejorados =[];
            $data_grafica_General_Empeorados = [];

            $data_grafica_Calidad_SD = [];
            $data_grafica_Calidad_Mejorados =[];
            $data_grafica_Calidad_Empeorados = [];

            $data_grafica_Cumplimiento_SD = [];
            $data_grafica_Cumplimiento_Mejorados =[];
            $data_grafica_Cumplimiento_Empeorados = [];

            $data_grafica_Socioambiental_SD = [];
            $data_grafica_Socioambiental_Mejorados =[];
            $data_grafica_Socioambiental_Empeorados = [];

            $data_grafica_Sanidad_SD = [];
            $data_grafica_Sanidad_Mejorados =[];
            $data_grafica_Sanidad_Empeorados = [];

            $data_grafica_Oferta_SD = [];
            $data_grafica_Oferta_Mejorados =[];
            $data_grafica_Oferta_Empeorados = [];
            
            $porcentaje_por_clientes["Cumplimiento"]= [];
            $porcentaje_por_clientes["Calidad"] = [];
            $porcentaje_por_clientes["Sanidad"] = [];
            $porcentaje_por_clientes["Socioambiental"] = [];
            $porcentaje_por_clientes["Oferta"]= [];

             $datos_anterior_general=[];
            
             $Porcentaje_Calidad_General= 0;
             $Porcentaje_Cumplimiento_General= 0;
             $Porcentaje_Socioambiental_General= 0;
             $Porcentaje_Sanidad_General= 0;
             $Porcentaje_Oferta_General= 0;
            //$listas_fechas_ = ['2016-12-30','2016-12-31'];
            // ahora obtengo las graficas
            $total_Empeorados=0;
            $total_Mejorados=0;
            foreach($listas_fechas_ as $fecha){
                //vdebug($fecha);
                $listaClientes= $this->getClientesProveedores($IDEmpresa,$fecha,$Periodo,$Quienes);
                
                $Total_Cumplimiento_SD = 0;
                $Total_Cumplimiento_Mejorado = 0;
                $Total_Cumplimiento_Empeorado = 0;

                $Total_Calidad_SD = 0;
                $Total_Calidad_Mejorado = 0;
                $Total_Calidad_Empeorado = 0;

                $Total_Oferta_SD = 0;
                $Total_Oferta_Mejorado = 0;
                $Total_Oferta_Empeorado = 0;

                $Total_Sanidad_SD = 0;
                $Total_Sanidad_Mejorado = 0;
                $Total_Sanidad_Empeorado = 0;

                $Total_Socioambiental_SD = 0;
                $Total_Socioambiental_Mejorado = 0;
                $Total_Socioambiental_Empeorado = 0;

                $Total_SD_gen=0;
                $Total_Empeorado_gen=0;
                $Total_Mejorado_gen=0;

                foreach ($listaClientes as $cliente) {
                   
                    //vdebug($cliente);
                    $porcentaje_array_pasado = [];
                    $porcentaje_array_actual = [];
                    
                    $dato_anterior_cumplimiento= [];
                    $dato_anterior_calidad= [];
                    $dato_anterior_sanidad= [];
                    $dato_anterior_socioambiental= [];
                    $dato_anterior_oferta= [];
                    
                    $Suma_porcentaje_general_pasado = 0;
                    $Suma_porcentaje_general_actual = 0;


                    
                    // cumplimiento 
                     foreach($preguntas_cumplimiento as $pregunta){
                        $datos_pregunta = $this->Model_Preguntas->Datos_Pregunta($pregunta);
                        if($datos_pregunta['Forma']==='SI/NO' || $datos_pregunta['Forma']==='SI/NO/NA' || $datos_pregunta['Forma']==='SI/NO/NS'){
                           
                            // para obtener el porcentaje anterior le resto un dia ala fecha actual
                            if($Periodo === "MA" || $Periodo==='A'){
                                 $fechaanterior = date('Y-m', strtotime($fecha . "- 30days"));
                            }else{
                                 $fechaanterior = date('Y-m-d', strtotime($fecha . "- 1days"));
                            }
                          
                            
                            // porcentaje pasado
                            $porcentaje_pasado=  $this->RiesgoPorcentajeFechaPregunta($cliente['IDEmpresaB'],$fechaanterior,$pregunta,$Periodo);
                           
                            if($porcentaje_pasado['Porcentaje'] === null){
                                $porcentaje_pas = 0;
                            }else{
                                $porcentaje_pas = $porcentaje_pasado['Porcentaje'];
                            }
                            
                             array_push($porcentaje_array_pasado,array("porcentaje"=>$porcentaje_pas,"Peso"=>$datos_pregunta['PorTotal']));
                            
                            // porcentaje actual
                            $porcentaje_actual = $this->RiesgoPorcentajeFechaPregunta($cliente['IDEmpresaB'],$fecha,$pregunta,$Periodo);
                            if($porcentaje_actual['Porcentaje'] === null){
                                $porcentaje_actu = 0;
                            }else{
                                $porcentaje_actu = $porcentaje_actual['Porcentaje'];
                            }
                            array_push($porcentaje_array_actual,array("porcentaje"=>$porcentaje_actu,"Peso"=>$datos_pregunta['PorTotal']));
                            
                           
                        }
                    }
                    // ahora obtengo el procentaje pasado y actual para poder comparar
                    $_Porcentaje_pasado= promedio_array_riesgo($porcentaje_array_pasado);
                    $_Porcentaje_actual= promedio_array_riesgo($porcentaje_array_actual);
                    
                    $Porcentaje_Cumplimiento_General= $Porcentaje_Cumplimiento_General + $_Porcentaje_actual;
            
                    $Suma_porcentaje_general_pasado=$Suma_porcentaje_general_pasado+$_Porcentaje_pasado;
                    $Suma_porcentaje_general_actual = $Suma_porcentaje_general_actual +$_Porcentaje_actual;
                    // guardo el procentaje actual del cliente 
                    array_push($porcentaje_por_clientes["Cumplimiento"],$_Porcentaje_actual);


                    if($_Porcentaje_pasado === 0  && $_Porcentaje_actual === 0 ){
                        $Total_Cumplimiento_SD ++;
                        array_push($dato_anterior_cumplimiento,'SD');
                    }else if($_Porcentaje_pasado   > $_Porcentaje_actual  ){
                        $Total_Cumplimiento_Empeorado ++;
                        array_push($dato_anterior_cumplimiento,'E');
                    }else if($_Porcentaje_pasado < $_Porcentaje_actual  ){
                        $Total_Cumplimiento_Mejorado ++;
                        array_push($dato_anterior_cumplimiento,'M');
                    }else if($_Porcentaje_pasado === $_Porcentaje_actual ){
                        if($dato_anterior_cumplimiento[count($dato_anterior_cumplimiento)-1]==='E'){
                            $Total_Cumplimiento_Empeorado ++;
                        }
                        if($dato_anterior_cumplimiento[count($dato_anterior_cumplimiento)-1]==='SD'){
                             $Total_Cumplimiento_SD++;   
                        }
                        if($dato_anterior_cumplimiento[count($dato_anterior_cumplimiento)-1]==='M'){
                            $Total_Cumplimiento_Mejorado ++;    
                        }
                    }



                    // socioambiental

                    $porcentaje_array_pasado = [];
                    $porcentaje_array_actual = [];


                     foreach($preguntas_socioambiental as $pregunta){
                        $datos_pregunta = $this->Model_Preguntas->Datos_Pregunta($pregunta);
                        if($datos_pregunta['Forma']==='SI/NO' || $datos_pregunta['Forma']==='SI/NO/NA' || $datos_pregunta['Forma']==='SI/NO/NS'){
                           
                            // para obtener el porcentaje anterior le resto un dia ala fecha actual
                            if($Periodo === "MA" || $Periodo==='A'){
                                 $fechaanterior = date('Y-m', strtotime($fecha . "- 30days"));
                            }else{
                                 $fechaanterior = date('Y-m-d', strtotime($fecha . "- 1days"));
                            }
                          
                            
                            // porcentaje pasado
                            $porcentaje_pasado=  $this->RiesgoPorcentajeFechaPregunta($cliente['IDEmpresaB'],$fechaanterior,$pregunta,$Periodo);
                           
                            if($porcentaje_pasado['Porcentaje'] === null){
                                $porcentaje_pas = 0;
                            }else{
                                $porcentaje_pas = $porcentaje_pasado['Porcentaje'];
                            }
                            
                             array_push($porcentaje_array_pasado,array("porcentaje"=>$porcentaje_pas,"Peso"=>$datos_pregunta['PorTotal']));
                            
                            // porcentaje actual
                            $porcentaje_actual = $this->RiesgoPorcentajeFechaPregunta($cliente['IDEmpresaB'],$fecha,$pregunta,$Periodo);
                            if($porcentaje_actual['Porcentaje'] === null){
                                $porcentaje_actu = 0;
                            }else{
                                $porcentaje_actu = $porcentaje_actual['Porcentaje'];
                            }
                            array_push($porcentaje_array_actual,array("porcentaje"=>$porcentaje_actu,"Peso"=>$datos_pregunta['PorTotal']));
                            
                           
                        }
                    }
                    // ahora obtengo el procentaje pasado y actual para poder comparar
                    $_Porcentaje_pasado= promedio_array_riesgo($porcentaje_array_pasado);
                    $_Porcentaje_actual= promedio_array_riesgo($porcentaje_array_actual);
                    
                    $Porcentaje_Socioambiental_General= $Porcentaje_Socioambiental_General + $_Porcentaje_actual;
            
                    $Suma_porcentaje_general_pasado=$Suma_porcentaje_general_pasado+$_Porcentaje_pasado;
                    $Suma_porcentaje_general_actual = $Suma_porcentaje_general_actual +$_Porcentaje_actual;
                    // guardo el procentaje actual del cliente 
                    array_push($porcentaje_por_clientes["Socioambiental"],$_Porcentaje_actual);


                    if($_Porcentaje_pasado === 0  && $_Porcentaje_actual === 0 ){
                        $Total_Socioambiental_SD ++;
                        array_push($dato_anterior_socioambiental,'SD');
                    }else if($_Porcentaje_pasado   > $_Porcentaje_actual  ){
                        $Total_Socioambiental_Empeorado ++;
                        array_push($dato_anterior_socioambiental,'E');
                    }else if($_Porcentaje_pasado < $_Porcentaje_actual  ){
                        $Total_Socioambiental_Mejorado ++;
                        array_push($dato_anterior_socioambiental,'M');
                    }else if($_Porcentaje_pasado === $_Porcentaje_actual ){
                        if($dato_anterior_socioambiental[count($dato_anterior_socioambiental)-1]==='E'){
                            $Total_Socioambiental_Empeorado ++;
                        }
                        if($dato_anterior_socioambiental[count($dato_anterior_socioambiental)-1]==='SD'){
                             $Total_Socioambiental_SD++;   
                        }
                        if($dato_anterior_socioambiental[count($dato_anterior_socioambiental)-1]==='M'){
                            $Total_Socioambiental_Mejorado ++;    
                        }
                    }

                    // finaliza socioambiental

                   // sanidad

                        $porcentaje_array_pasado = [];
                        $porcentaje_array_actual = [];


                     foreach($preguntas_sanidad as $pregunta){
                        $datos_pregunta = $this->Model_Preguntas->Datos_Pregunta($pregunta);
                        if($datos_pregunta['Forma']==='SI/NO' || $datos_pregunta['Forma']==='SI/NO/NA' || $datos_pregunta['Forma']==='SI/NO/NS'){
                           
                            // para obtener el porcentaje anterior le resto un dia ala fecha actual
                            if($Periodo === "MA" || $Periodo==='A'){
                                 $fechaanterior = date('Y-m', strtotime($fecha . "- 30days"));
                            }else{
                                 $fechaanterior = date('Y-m-d', strtotime($fecha . "- 1days"));
                            }
                          
                            
                            // porcentaje pasado
                            $porcentaje_pasado=  $this->RiesgoPorcentajeFechaPregunta($cliente['IDEmpresaB'],$fechaanterior,$pregunta,$Periodo);
                           
                            if($porcentaje_pasado['Porcentaje'] === null){
                                $porcentaje_pas = 0;
                            }else{
                                $porcentaje_pas = $porcentaje_pasado['Porcentaje'];
                            }
                            
                             array_push($porcentaje_array_pasado,array("porcentaje"=>$porcentaje_pas,"Peso"=>$datos_pregunta['PorTotal']));
                            
                            // porcentaje actual
                            $porcentaje_actual = $this->RiesgoPorcentajeFechaPregunta($cliente['IDEmpresaB'],$fecha,$pregunta,$Periodo);
                            if($porcentaje_actual['Porcentaje'] === null){
                                $porcentaje_actu = 0;
                            }else{
                                $porcentaje_actu = $porcentaje_actual['Porcentaje'];
                            }
                            array_push($porcentaje_array_actual,array("porcentaje"=>$porcentaje_actu,"Peso"=>$datos_pregunta['PorTotal']));
                            
                           
                        }
                    }
                    // ahora obtengo el procentaje pasado y actual para poder comparar
                    $_Porcentaje_pasado= promedio_array_riesgo($porcentaje_array_pasado);
                    $_Porcentaje_actual= promedio_array_riesgo($porcentaje_array_actual);
                    
                    $Porcentaje_Sanidad_General= $Porcentaje_Sanidad_General + $_Porcentaje_actual;
            
                    $Suma_porcentaje_general_pasado=$Suma_porcentaje_general_pasado+$_Porcentaje_pasado;
                    $Suma_porcentaje_general_actual = $Suma_porcentaje_general_actual +$_Porcentaje_actual;
                    // guardo el procentaje actual del cliente 
                    array_push($porcentaje_por_clientes["Sanidad"],$_Porcentaje_actual);


                    if($_Porcentaje_pasado === 0  && $_Porcentaje_actual === 0 ){
                        $Total_Sanidad_SD ++;
                        array_push($dato_anterior_sanidad,'SD');
                    }else if($_Porcentaje_pasado   > $_Porcentaje_actual  ){
                        $Total_Sanidad_Empeorado ++;
                        array_push($dato_anterior_sanidad,'E');
                    }else if($_Porcentaje_pasado < $_Porcentaje_actual  ){
                        $Total_Sanidad_Mejorado ++;
                        array_push($dato_anterior_sanidad,'M');
                    }else if($_Porcentaje_pasado === $_Porcentaje_actual ){
                        if($dato_anterior_sanidad[count($dato_anterior_sanidad)-1]==='E'){
                            $Total_Sanidad_Empeorado ++;
                        }
                        if($dato_anterior_sanidad[count($dato_anterior_sanidad)-1]==='SD'){
                             $Total_Sanidad_SD++;   
                        }
                        if($dato_anterior_sanidad[count($dato_anterior_sanidad)-1]==='M'){
                            $Total_Sanidad_Mejorado ++;    
                        }
                    }

                   // finaliza sanidad
                    
                    
                    $porcentaje_array_pasado = [];
                    $porcentaje_array_actual = [];
                    // calidad
                         foreach($preguntas_calidad as $pregunta){
                            $datos_pregunta = $this->Model_Preguntas->Datos_Pregunta($pregunta);
                            if($datos_pregunta['Forma']==='SI/NO' || $datos_pregunta['Forma']==='SI/NO/NA' || $datos_pregunta['Forma']==='SI/NO/NS'){
                            
                                // para obtener el porcentaje anterior le resto un dia ala fecha actual
                                if($Periodo === "MA" || $Periodo==='A'){
                                 $fechaanterior = date('Y-m', strtotime($fecha . "- 30days"));
                            }else{
                                 $fechaanterior = date('Y-m-d', strtotime($fecha . "- 1days"));
                            }
                            
                                // porcentaje pasado
                                $porcentaje_pasado=  $this->RiesgoPorcentajeFechaPregunta($cliente['IDEmpresaB'],$fechaanterior,$pregunta,$Periodo);
                            
                                if($porcentaje_pasado['Porcentaje'] === null){
                                    $porcentaje_pas = 0;
                                }else{
                                    $porcentaje_pas = $porcentaje_pasado['Porcentaje'];
                                }
                                
                                 array_push($porcentaje_array_pasado,array("porcentaje"=>$porcentaje_pas,"Peso"=>$datos_pregunta['PorTotal']));
                            
                                // porcentaje actual
                                $porcentaje_actual = $this->RiesgoPorcentajeFechaPregunta($cliente['IDEmpresaB'],$fecha,$pregunta,$Periodo);
                                if($porcentaje_actual['Porcentaje'] === null){
                                    $porcentaje_actu = 0;
                                }else{
                                    $porcentaje_actu = $porcentaje_actual['Porcentaje'];
                                }
                                
                                 array_push($porcentaje_array_actual,array("porcentaje"=>$porcentaje_actu,"Peso"=>$datos_pregunta['PorTotal']));
                           
                            
                            }
                            
                        }
                        // ahora obtengo el procentaje pasado y actual para poder comparar
                        $_Porcentaje_pasado= promedio_array_riesgo($porcentaje_array_pasado);
                        $_Porcentaje_actual= promedio_array_riesgo($porcentaje_array_actual);
                         $Porcentaje_Calidad_General= $Porcentaje_Calidad_General + $_Porcentaje_actual;
            
                        $Suma_porcentaje_general_pasado=$Suma_porcentaje_general_pasado+$_Porcentaje_pasado;
                        $Suma_porcentaje_general_actual = $Suma_porcentaje_general_actual +$_Porcentaje_actual;
                        
                        array_push($porcentaje_por_clientes["Calidad"],$_Porcentaje_actual);

                        if($_Porcentaje_pasado === 0  && $_Porcentaje_actual === 0 ){
                            $Total_Calidad_SD ++;
                            array_push($dato_anterior_calidad,'SD');
                        }else if($_Porcentaje_pasado  > $_Porcentaje_actual ){
                            $Total_Calidad_Empeorado ++;
                            array_push($dato_anterior_calidad,'E');
                        }else if($_Porcentaje_pasado  < $_Porcentaje_actual  ){
                            $Total_Calidad_Mejorado ++;
                            array_push($dato_anterior_calidad,'M');
                        }else if($_Porcentaje_pasado === $_Porcentaje_actual ){
                            if($dato_anterior_calidad[count($dato_anterior_calidad)-1]==='E'){
                                $Total_Calidad_Empeorado ++;
                            }
                            if($dato_anterior_calidad[count($dato_anterior_calidad)-1]==='SD'){
                                $Total_Calidad_SD++;   
                            }
                            if($dato_anterior_calidad[count($dato_anterior_calidad)-1]==='M'){
                                $Total_Calidad_Mejorado ++;    
                            }
                        }

                   if($comoQue === 'proveedor'){

                    
                     $porcentaje_array_pasado = [];
                     $porcentaje_array_actual = [];
                    // oferta
                        foreach($preguntas_oferta as $pregunta){
                            $datos_pregunta = $this->Model_Preguntas->Datos_Pregunta($pregunta);
                            if($datos_pregunta['Forma']==='SI/NO' || $datos_pregunta['Forma']==='SI/NO/NA' || $datos_pregunta['Forma']==='SI/NO/NS'){
                            
                                // para obtener el porcentaje anterior le resto un dia ala fecha actual
                                if($Periodo === "MA" || $Periodo==='A'){
                                 $fechaanterior = date('Y-m', strtotime($fecha . "- 30days"));
                                }else{
                                    $fechaanterior = date('Y-m-d', strtotime($fecha . "- 1days"));
                                }
                            
                                // porcentaje pasado
                                $porcentaje_pasado=  $this->RiesgoPorcentajeFechaPregunta($cliente['IDEmpresaB'],$fechaanterior,$pregunta,$Periodo);
                            
                                if($porcentaje_pasado['Porcentaje'] === null){
                                    $porcentaje_pas = 0;
                                }else{
                                    $porcentaje_pas = $porcentaje_pasado['Porcentaje'];
                                }
                                 array_push($porcentaje_array_pasado,array("porcentaje"=>$porcentaje_pas,"Peso"=>$datos_pregunta['PorTotal']));
                            
                                
                                // porcentaje actual
                                $porcentaje_actual = $this->RiesgoPorcentajeFechaPregunta($cliente['IDEmpresaB'],$fecha,$pregunta,$Periodo);
                                if($porcentaje_actual['Porcentaje'] === null){
                                    $porcentaje_actu = 0;
                                }else{
                                    $porcentaje_actu = $porcentaje_actual['Porcentaje'];
                                }
                               
                                array_push($porcentaje_array_actual,array("porcentaje"=>$porcentaje_actu,"Peso"=>$datos_pregunta['PorTotal']));
                           
                            
                            }
                            
                        }
                        // ahora obtengo el procentaje pasado y actual para poder comparar
                        $_Porcentaje_pasado= promedio_array_riesgo($porcentaje_array_pasado);
                        $_Porcentaje_actual= promedio_array_riesgo($porcentaje_array_actual);
                         $Porcentaje_Oferta_General=  $Porcentaje_Oferta_General +$_Porcentaje_actual;
                        $Suma_porcentaje_general_pasado=$Suma_porcentaje_general_pasado+$_Porcentaje_pasado;
                        $Suma_porcentaje_general_actual = $Suma_porcentaje_general_actual +$_Porcentaje_actual;
                        
                        array_push($porcentaje_por_clientes["Oferta"],$_Porcentaje_actual);

                        if($_Porcentaje_pasado === 0  && $_Porcentaje_actual === 0 ){
                            $Total_Oferta_SD ++;
                            array_push($dato_anterior_oferta,'SD');
                        }else if($_Porcentaje_pasado  > $_Porcentaje_actual ){
                            $Total_Oferta_Empeorado ++;
                            array_push($dato_anterior_oferta,'E');
                        }else if($_Porcentaje_pasado  < $_Porcentaje_actual  ){
                            $Total_Oferta_Mejorado ++;
                            array_push($dato_anterior_oferta,'M');
                        }else if($_Porcentaje_pasado === $_Porcentaje_actual ){
                            if($dato_anterior_oferta[count($dato_anterior_oferta)-1]==='E'){
                                $Total_Oferta_Empeorado ++;
                            }
                            if($dato_anterior_oferta[count($dato_anterior_oferta)-1]==='SD'){
                                $Total_Oferta_SD++;   
                            }
                            if($dato_anterior_oferta[count($dato_anterior_oferta)-1]==='M'){
                                $Total_Oferta_Mejorado ++;    
                            }
                        }
                     }
                     if($Suma_porcentaje_general_pasado === 0  && $Suma_porcentaje_general_actual === 0 ){
                            $Total_SD_gen ++;
                            array_push($datos_anterior_general,array("Status"=>'SD',"IDCliente"=>$cliente['IDEmpresaB']));
                        }else if($Suma_porcentaje_general_pasado  > $Suma_porcentaje_general_actual ){
                            $Total_Empeorado_gen ++;
                            array_push($datos_anterior_general,array("Status"=>'E',"IDCliente"=>$cliente['IDEmpresaB']));
                        }else if($Suma_porcentaje_general_pasado  < $Suma_porcentaje_general_actual  ){
                            $Total_Mejorado_gen ++;
                             array_push($datos_anterior_general,array("Status"=>'M',"IDCliente"=>$cliente['IDEmpresaB']));
                        }else if($Suma_porcentaje_general_pasado === $Suma_porcentaje_general_actual ){
                            for($i=count($datos_anterior_general);$i>=0; $i--){
                                if($dato["IDCliente"]===$cliente['IDEmpresaB']){
                                    if($dato["Status"]==='E'){
                                    $Total_Empeorado_gen ++;
                                    }
                                    if($dato["Status"]==='SD'){
                                        $Total_SD_gen++;   
                                    }
                                    if($dato["Status"]==='M'){
                                        $Total_Mejorado_gen ++;    
                                    }
                                }
                            }
                            
                        }
                    }
                    


               array_push($data_grafica_General_SD,$Total_SD_gen);
                array_push($data_grafica_General_Mejorados,$Total_Mejorado_gen);
                array_push($data_grafica_General_Empeorados,$Total_Empeorado_gen);
                    

                array_push($data_grafica_Calidad_SD,$Total_Calidad_SD);
                array_push($data_grafica_Calidad_Mejorados,$Total_Calidad_Mejorado);
                array_push($data_grafica_Calidad_Empeorados,$Total_Calidad_Empeorado);

                array_push($data_grafica_Cumplimiento_SD,$Total_Cumplimiento_SD);
                array_push($data_grafica_Cumplimiento_Mejorados,$Total_Cumplimiento_Mejorado);
                array_push($data_grafica_Cumplimiento_Empeorados,$Total_Cumplimiento_Empeorado);
                
                array_push($data_grafica_Socioambiental_SD,$Total_Socioambiental_SD);
                array_push($data_grafica_Socioambiental_Mejorados,$Total_Socioambiental_Mejorado);
                array_push($data_grafica_Socioambiental_Empeorados,$Total_Socioambiental_Empeorado);

                array_push($data_grafica_Sanidad_SD,$Total_Sanidad_SD);
                array_push($data_grafica_Sanidad_Mejorados,$Total_Sanidad_Mejorado);
                array_push($data_grafica_Sanidad_Empeorados,$Total_Sanidad_Empeorado);


                array_push($data_grafica_Oferta_SD,$Total_Oferta_SD);
                array_push($data_grafica_Oferta_Mejorados,$Total_Oferta_Mejorado);
                array_push($data_grafica_Oferta_Empeorados,$Total_Oferta_Empeorado);
            
                
            }
            $data['graficas']=array(
                "Calidad"=>array(
                    "SD"=>$data_grafica_Calidad_SD,
                    "Mejorados"=>$data_grafica_Calidad_Mejorados,
                    "Empeorado"=>$data_grafica_Calidad_Empeorados
                ),
                "Cumplimiento"=>array(
                    "SD"=>$data_grafica_Cumplimiento_SD,
                    "Mejorados"=>$data_grafica_Cumplimiento_Mejorados,
                    "Empeorado"=>$data_grafica_Cumplimiento_Empeorados
                ),
                
                "Sanidad"=>array(
                    "SD"=>$data_grafica_Sanidad_SD,
                    "Mejorados"=>$data_grafica_Sanidad_Mejorados,
                    "Empeorado"=>$data_grafica_Sanidad_Empeorados
                ),
                "Socioambiental"=>array(
                    "SD"=>$data_grafica_Socioambiental_SD,
                    "Mejorados"=>$data_grafica_Socioambiental_Mejorados,
                    "Empeorado"=>$data_grafica_Socioambiental_Empeorados
                ),
                "Oferta"=>array(
                    "SD"=>$data_grafica_Oferta_SD,
                    "Mejorados"=>$data_grafica_Oferta_Mejorados,
                    "Empeorado"=>$data_grafica_Oferta_Empeorados
                ),

                 "General"=>array(
                    "SD"=>$data_grafica_General_SD,
                    "Mejorados"=>$data_grafica_General_Mejorados,
                    "Empeorado"=>$data_grafica_General_Empeorados
                ),
                

            );

            $data["labels"]=$listas_fechas_;
            $data["NumMejorados"]= $Total_Mejorado_gen;
            $data["NumEmpeorados"]= $Total_Empeorado_gen;
            $data["NumSD"]= $Total_SD_gen;
            $data["porcentajes"]= array(
                "General"=>$Porcentaje_Oferta_General+$Porcentaje_Calidad_General+$Porcentaje_Cumplimiento_General+$Porcentaje_Sanidad_General+$Porcentaje_Socioambiental_General,
                "Oferta"=>$Porcentaje_Oferta_General,
                "Calidad"=>$Porcentaje_Calidad_General,
                "Socioambiental"=>$Porcentaje_Socioambiental_General,
                "Sanidad"=>$Porcentaje_Sanidad_General,
                "Cumplimiento"=>$Porcentaje_Cumplimiento_General

            );
            
            $data["periodo"]=$listas_fechas_[0]."-".$listas_fechas_[count($listas_fechas_)-1];
            return $data;
            

    }        

    public function RiesgoGenPerfil($IDEmpresa,$IDGiro,$Periodo,$Quienes,$comoQue){
        
       //obtengo el periodo
         $listas_fechas=dame_rangos_fecha($Periodo);
         //vdebug($listas_fechas);
        $lista_clientes = [];
        
        // obtengo el giro principal de la empresa si no traigo filtros
        if($IDGiro === ''){
            $IDGiro = '1';
        }
         // obtener las preguntas de ese giro 
        $listado_preguntas = $this->Model_Preguntas->configuracion_cuestionario($IDGiro,$comoQue);
        //vdebug($listado_preguntas);
        $preguntas_cumplimiento=explode(',',$listado_preguntas[0]['Cumplimiento']);
        $preguntas_calidad=explode(',',$listado_preguntas[0]['Calidad']);
        $preguntas_oferta=explode(',',$listado_preguntas[0]['Oferta']);

      
       //vdebug($listas_fechas);






        
        $cantidad_por_categoria=[];
        $porcentaje_categoria_cumplimiento=[];
        $porcentaje_categoria_calidad = [];
         $porcentaje_categoria_oferta = [];
        $listas_fechas_ = _fechas_array_List($Periodo);
        //vdebug($listas_fechas_);
            $label_Grafica=[];
            $data_grafica_General_SD = [];
            $data_grafica_General_Mejorados =[];
            $data_grafica_General_Empeorados = [];

            $data_grafica_Calidad_SD = [];
            $data_grafica_Calidad_Mejorados =[];
            $data_grafica_Calidad_Empeorados = [];

            $data_grafica_Cumplimiento_SD = [];
            $data_grafica_Cumplimiento_Mejorados =[];
            $data_grafica_Cumplimiento_Empeorados = [];

            $data_grafica_Oferta_SD = [];
            $data_grafica_Oferta_Mejorados =[];
            $data_grafica_Oferta_Empeorados = [];
            
            $porcentaje_por_clientes["Cumplimiento"]= [];
            $porcentaje_por_clientes["Calidad"] = [];
            $porcentaje_por_clientes["Oferta"]= [];

             $datos_anterior_general=[];
            
             $Porcentaje_Calidad_General= 0;
             $Porcentaje_Cumplimiento_General= 0;
             $Porcentaje_Oferta_General= 0;
            //$listas_fechas_ = ['2016-12-30','2016-12-31'];
            // ahora obtengo las graficas
            $total_Empeorados=0;
            $total_Mejorados=0;
            foreach($listas_fechas_ as $fecha){
                //vdebug($fecha);
                $listaClientes= $this->getClientesProveedores($IDEmpresa,$fecha,$Periodo,$Quienes);
                
                $Total_Cumplimiento_SD = 0;
                $Total_Cumplimiento_Mejorado = 0;
                $Total_Cumplimiento_Empeorado = 0;
                $Total_Calidad_SD = 0;
                $Total_Calidad_Mejorado = 0;
                $Total_Calidad_Empeorado = 0;
                $Total_Oferta_SD = 0;
                $Total_Oferta_Mejorado = 0;
                $Total_Oferta_Empeorado = 0;
                $Total_SD_gen=0;
                $Total_Empeorado_gen=0;
                $Total_Mejorado_gen=0;

                foreach ($listaClientes as $cliente) {
                   
                    //vdebug($cliente);
                    $porcentaje_array_pasado = [];
                    $porcentaje_array_actual = [];
                    $dato_anterior_cumplimiento= [];
                    $dato_anterior_calidad= [];
                    $dato_anterior_oferta= [];
                    
                    $Suma_porcentaje_general_pasado = 0;
                    $Suma_porcentaje_general_actual = 0;


                    
                    // cumplimiento 
                     foreach($preguntas_cumplimiento as $pregunta){
                        $datos_pregunta = $this->Model_Preguntas->Datos_Pregunta($pregunta);
                        if($datos_pregunta['Forma']==='SI/NO' || $datos_pregunta['Forma']==='SI/NO/NA' || $datos_pregunta['Forma']==='SI/NO/NS'){
                           
                            // para obtener el porcentaje anterior le resto un dia ala fecha actual
                            if($Periodo === "MA" || $Periodo==='A'){
                                 $fechaanterior = date('Y-m', strtotime($fecha . "- 30days"));
                            }else{
                                 $fechaanterior = date('Y-m-d', strtotime($fecha . "- 1days"));
                            }
                          
                            
                            // porcentaje pasado
                            $porcentaje_pasado=  $this->RiesgoPorcentajeFechaPregunta($cliente['IDEmpresaB'],$fechaanterior,$pregunta,$Periodo);
                           
                            if($porcentaje_pasado['Porcentaje'] === null){
                                $porcentaje_pas = 0;
                            }else{
                                $porcentaje_pas = $porcentaje_pasado['Porcentaje'];
                            }
                            
                             array_push($porcentaje_array_pasado,array("porcentaje"=>$porcentaje_pas,"Peso"=>$datos_pregunta['PorTotal']));
                            
                            // porcentaje actual
                            $porcentaje_actual = $this->RiesgoPorcentajeFechaPregunta($cliente['IDEmpresaB'],$fecha,$pregunta,$Periodo);
                            if($porcentaje_actual['Porcentaje'] === null){
                                $porcentaje_actu = 0;
                            }else{
                                $porcentaje_actu = $porcentaje_actual['Porcentaje'];
                            }
                            array_push($porcentaje_array_actual,array("porcentaje"=>$porcentaje_actu,"Peso"=>$datos_pregunta['PorTotal']));
                            
                           
                        }
                    }
                    // ahora obtengo el procentaje pasado y actual para poder comparar
                    $_Porcentaje_pasado= promedio_array_riesgo($porcentaje_array_pasado);
                    $_Porcentaje_actual= promedio_array_riesgo($porcentaje_array_actual);
                    
                    $Porcentaje_Cumplimiento_General= $Porcentaje_Cumplimiento_General + $_Porcentaje_actual;
            
                    $Suma_porcentaje_general_pasado=$Suma_porcentaje_general_pasado+$_Porcentaje_pasado;
                    $Suma_porcentaje_general_actual = $Suma_porcentaje_general_actual +$_Porcentaje_actual;
                    // guardo el procentaje actual del cliente 
                    array_push($porcentaje_por_clientes["Cumplimiento"],$_Porcentaje_actual);


                    if($_Porcentaje_pasado === 0  && $_Porcentaje_actual === 0 ){
                        $Total_Cumplimiento_SD ++;
                        array_push($dato_anterior_cumplimiento,'SD');
                    }else if($_Porcentaje_pasado   > $_Porcentaje_actual  ){
                        $Total_Cumplimiento_Empeorado ++;
                        array_push($dato_anterior_cumplimiento,'E');
                    }else if($_Porcentaje_pasado < $_Porcentaje_actual  ){
                        $Total_Cumplimiento_Mejorado ++;
                        array_push($dato_anterior_cumplimiento,'M');
                    }else if($_Porcentaje_pasado === $_Porcentaje_actual ){
                        if($dato_anterior_cumplimiento[count($dato_anterior_cumplimiento)-1]==='E'){
                            $Total_Cumplimiento_Empeorado ++;
                        }
                        if($dato_anterior_cumplimiento[count($dato_anterior_cumplimiento)-1]==='SD'){
                             $Total_Cumplimiento_SD++;   
                        }
                        if($dato_anterior_cumplimiento[count($dato_anterior_cumplimiento)-1]==='M'){
                            $Total_Cumplimiento_Mejorado ++;    
                        }
                    }


                   
                    
                    
                    $porcentaje_array_pasado = [];
                    $porcentaje_array_actual = [];
                    // calidad
                         foreach($preguntas_calidad as $pregunta){
                            $datos_pregunta = $this->Model_Preguntas->Datos_Pregunta($pregunta);
                            if($datos_pregunta['Forma']==='SI/NO' || $datos_pregunta['Forma']==='SI/NO/NA' || $datos_pregunta['Forma']==='SI/NO/NS'){
                            
                                // para obtener el porcentaje anterior le resto un dia ala fecha actual
                                if($Periodo === "MA" || $Periodo==='A'){
                                 $fechaanterior = date('Y-m', strtotime($fecha . "- 30days"));
                            }else{
                                 $fechaanterior = date('Y-m-d', strtotime($fecha . "- 1days"));
                            }
                            
                                // porcentaje pasado
                                $porcentaje_pasado=  $this->RiesgoPorcentajeFechaPregunta($cliente['IDEmpresaB'],$fechaanterior,$pregunta,$Periodo);
                            
                                if($porcentaje_pasado['Porcentaje'] === null){
                                    $porcentaje_pas = 0;
                                }else{
                                    $porcentaje_pas = $porcentaje_pasado['Porcentaje'];
                                }
                                
                                 array_push($porcentaje_array_pasado,array("porcentaje"=>$porcentaje_pas,"Peso"=>$datos_pregunta['PorTotal']));
                            
                                // porcentaje actual
                                $porcentaje_actual = $this->RiesgoPorcentajeFechaPregunta($cliente['IDEmpresaB'],$fecha,$pregunta,$Periodo);
                                if($porcentaje_actual['Porcentaje'] === null){
                                    $porcentaje_actu = 0;
                                }else{
                                    $porcentaje_actu = $porcentaje_actual['Porcentaje'];
                                }
                                
                                 array_push($porcentaje_array_actual,array("porcentaje"=>$porcentaje_actu,"Peso"=>$datos_pregunta['PorTotal']));
                           
                            
                            }
                            
                        }
                        // ahora obtengo el procentaje pasado y actual para poder comparar
                        $_Porcentaje_pasado= promedio_array_riesgo($porcentaje_array_pasado);
                        $_Porcentaje_actual= promedio_array_riesgo($porcentaje_array_actual);
                         $Porcentaje_Calidad_General= $Porcentaje_Calidad_General + $_Porcentaje_actual;
            
                        $Suma_porcentaje_general_pasado=$Suma_porcentaje_general_pasado+$_Porcentaje_pasado;
                        $Suma_porcentaje_general_actual = $Suma_porcentaje_general_actual +$_Porcentaje_actual;
                        
                        array_push($porcentaje_por_clientes["Calidad"],$_Porcentaje_actual);

                        if($_Porcentaje_pasado === 0  && $_Porcentaje_actual === 0 ){
                            $Total_Calidad_SD ++;
                            array_push($dato_anterior_calidad,'SD');
                        }else if($_Porcentaje_pasado  > $_Porcentaje_actual ){
                            $Total_Calidad_Empeorado ++;
                            array_push($dato_anterior_calidad,'E');
                        }else if($_Porcentaje_pasado  < $_Porcentaje_actual  ){
                            $Total_Calidad_Mejorado ++;
                            array_push($dato_anterior_calidad,'M');
                        }else if($_Porcentaje_pasado === $_Porcentaje_actual ){
                            if($dato_anterior_calidad[count($dato_anterior_calidad)-1]==='E'){
                                $Total_Calidad_Empeorado ++;
                            }
                            if($dato_anterior_calidad[count($dato_anterior_calidad)-1]==='SD'){
                                $Total_Calidad_SD++;   
                            }
                            if($dato_anterior_calidad[count($dato_anterior_calidad)-1]==='M'){
                                $Total_Calidad_Mejorado ++;    
                            }
                        }

                   if($comoQue === 'proveedor'){

                    
                     $porcentaje_array_pasado = [];
                     $porcentaje_array_actual = [];
                    // oferta
                        foreach($preguntas_oferta as $pregunta){
                            $datos_pregunta = $this->Model_Preguntas->Datos_Pregunta($pregunta);
                            if($datos_pregunta['Forma']==='SI/NO' || $datos_pregunta['Forma']==='SI/NO/NA' || $datos_pregunta['Forma']==='SI/NO/NS'){
                            
                                // para obtener el porcentaje anterior le resto un dia ala fecha actual
                                if($Periodo === "MA" || $Periodo==='A'){
                                 $fechaanterior = date('Y-m', strtotime($fecha . "- 30days"));
                                }else{
                                    $fechaanterior = date('Y-m-d', strtotime($fecha . "- 1days"));
                                }
                            
                                // porcentaje pasado
                                $porcentaje_pasado=  $this->RiesgoPorcentajeFechaPregunta($cliente['IDEmpresaB'],$fechaanterior,$pregunta,$Periodo);
                            
                                if($porcentaje_pasado['Porcentaje'] === null){
                                    $porcentaje_pas = 0;
                                }else{
                                    $porcentaje_pas = $porcentaje_pasado['Porcentaje'];
                                }
                                 array_push($porcentaje_array_pasado,array("porcentaje"=>$porcentaje_pas,"Peso"=>$datos_pregunta['PorTotal']));
                            
                                
                                // porcentaje actual
                                $porcentaje_actual = $this->RiesgoPorcentajeFechaPregunta($cliente['IDEmpresaB'],$fecha,$pregunta,$Periodo);
                                if($porcentaje_actual['Porcentaje'] === null){
                                    $porcentaje_actu = 0;
                                }else{
                                    $porcentaje_actu = $porcentaje_actual['Porcentaje'];
                                }
                               
                                array_push($porcentaje_array_actual,array("porcentaje"=>$porcentaje_actu,"Peso"=>$datos_pregunta['PorTotal']));
                           
                            
                            }
                            
                        }
                        // ahora obtengo el procentaje pasado y actual para poder comparar
                        $_Porcentaje_pasado= promedio_array_riesgo($porcentaje_array_pasado);
                        $_Porcentaje_actual= promedio_array_riesgo($porcentaje_array_actual);
                         $Porcentaje_Oferta_General=  $Porcentaje_Oferta_General +$_Porcentaje_actual;
                        $Suma_porcentaje_general_pasado=$Suma_porcentaje_general_pasado+$_Porcentaje_pasado;
                        $Suma_porcentaje_general_actual = $Suma_porcentaje_general_actual +$_Porcentaje_actual;
                        
                        array_push($porcentaje_por_clientes["Oferta"],$_Porcentaje_actual);

                        if($_Porcentaje_pasado === 0  && $_Porcentaje_actual === 0 ){
                            $Total_Oferta_SD ++;
                            array_push($dato_anterior_oferta,'SD');
                        }else if($_Porcentaje_pasado  > $_Porcentaje_actual ){
                            $Total_Oferta_Empeorado ++;
                            array_push($dato_anterior_oferta,'E');
                        }else if($_Porcentaje_pasado  < $_Porcentaje_actual  ){
                            $Total_Oferta_Mejorado ++;
                            array_push($dato_anterior_oferta,'M');
                        }else if($_Porcentaje_pasado === $_Porcentaje_actual ){
                            if($dato_anterior_oferta[count($dato_anterior_oferta)-1]==='E'){
                                $Total_Oferta_Empeorado ++;
                            }
                            if($dato_anterior_oferta[count($dato_anterior_oferta)-1]==='SD'){
                                $Total_Oferta_SD++;   
                            }
                            if($dato_anterior_oferta[count($dato_anterior_oferta)-1]==='M'){
                                $Total_Oferta_Mejorado ++;    
                            }
                        }
                     }
                     if($Suma_porcentaje_general_pasado === 0  && $Suma_porcentaje_general_actual === 0 ){
                            $Total_SD_gen ++;
                            array_push($datos_anterior_general,array("Status"=>'SD',"IDCliente"=>$cliente['IDEmpresaB']));
                        }else if($Suma_porcentaje_general_pasado  > $Suma_porcentaje_general_actual ){
                            $Total_Empeorado_gen ++;
                            array_push($datos_anterior_general,array("Status"=>'E',"IDCliente"=>$cliente['IDEmpresaB']));
                        }else if($Suma_porcentaje_general_pasado  < $Suma_porcentaje_general_actual  ){
                            $Total_Mejorado_gen ++;
                             array_push($datos_anterior_general,array("Status"=>'M',"IDCliente"=>$cliente['IDEmpresaB']));
                        }else if($Suma_porcentaje_general_pasado === $Suma_porcentaje_general_actual ){
                            for($i=count($datos_anterior_general);$i>=0; $i--){
                                if($dato["IDCliente"]===$cliente['IDEmpresaB']){
                                    if($dato["Status"]==='E'){
                                    $Total_Empeorado_gen ++;
                                    }
                                    if($dato["Status"]==='SD'){
                                        $Total_SD_gen++;   
                                    }
                                    if($dato["Status"]==='M'){
                                        $Total_Mejorado_gen ++;    
                                    }
                                }
                            }
                            
                        }
                    }
                    


               array_push($data_grafica_General_SD,$Total_SD_gen);
                array_push($data_grafica_General_Mejorados,$Total_Mejorado_gen);
                array_push($data_grafica_General_Empeorados,$Total_Empeorado_gen);
                    

                array_push($data_grafica_Calidad_SD,$Total_Calidad_SD);
                array_push($data_grafica_Calidad_Mejorados,$Total_Calidad_Mejorado);
                array_push($data_grafica_Calidad_Empeorados,$Total_Calidad_Empeorado);

                array_push($data_grafica_Cumplimiento_SD,$Total_Cumplimiento_SD);
                array_push($data_grafica_Cumplimiento_Mejorados,$Total_Cumplimiento_Mejorado);
                array_push($data_grafica_Cumplimiento_Empeorados,$Total_Cumplimiento_Empeorado);

                array_push($data_grafica_Oferta_SD,$Total_Oferta_SD);
                array_push($data_grafica_Oferta_Mejorados,$Total_Oferta_Mejorado);
                array_push($data_grafica_Oferta_Empeorados,$Total_Oferta_Empeorado);
            
                
            }
            $data['graficas']=array(
                "Calidad"=>array(
                    "SD"=>$data_grafica_Calidad_SD,
                    "Mejorados"=>$data_grafica_Calidad_Mejorados,
                    "Empeorado"=>$data_grafica_Calidad_Empeorados
                ),
                "Cumplimiento"=>array(
                    "SD"=>$data_grafica_Cumplimiento_SD,
                    "Mejorados"=>$data_grafica_Cumplimiento_Mejorados,
                    "Empeorado"=>$data_grafica_Cumplimiento_Empeorados
                ),
                "Oferta"=>array(
                    "SD"=>$data_grafica_Oferta_SD,
                    "Mejorados"=>$data_grafica_Oferta_Mejorados,
                    "Empeorado"=>$data_grafica_Oferta_Empeorados
                ),

                 "General"=>array(
                    "SD"=>$data_grafica_General_SD,
                    "Mejorados"=>$data_grafica_General_Mejorados,
                    "Empeorado"=>$data_grafica_General_Empeorados
                ),
                

            );

            $data["labels"]=$listas_fechas_;
            $data["NumMejorados"]= $Total_Mejorado_gen;
            $data["NumEmpeorados"]= $Total_Empeorado_gen;
            $data["NumSD"]= $Total_SD_gen;
            $data["porcentajes"]= array(
                "General"=>$Porcentaje_Oferta_General+$Porcentaje_Calidad_General+$Porcentaje_Cumplimiento_General,
                "Oferta"=>$Porcentaje_Oferta_General,
                "Calidad"=>$Porcentaje_Calidad_General,
                "Cumplimiento"=>$Porcentaje_Cumplimiento_General

            );
            
            $data["periodo"]=$listas_fechas_[0]."-".$listas_fechas_[count($listas_fechas_)-1];
            return $data;
            

    }
    public function RiesgoGenListadoClientes($IDEmpresa,$IDGiro,$Periodo,$Quienes,$comoQue){
        
       //obtengo el periodo
         $listas_fechas=dame_rangos_fecha($Periodo);
         //vdebug($listas_fechas);
        $lista_clientes = [];
        
        // obtengo el giro principal de la empresa si no traigo filtros
        if($IDGiro === ''){
            $IDGiro = '1';
        }
         // obtener las preguntas de ese giro 
        $listado_preguntas = $this->Model_Preguntas->configuracion_cuestionario($IDGiro,$comoQue);
        //vdebug($listado_preguntas);
        $preguntas_cumplimiento=explode(',',$listado_preguntas[0]['Cumplimiento']);
        $preguntas_calidad=explode(',',$listado_preguntas[0]['Calidad']);
        $preguntas_oferta=explode(',',$listado_preguntas[0]['Oferta']);

      
       //vdebug($listas_fechas);






        
        $cantidad_por_categoria=[];
        $porcentaje_categoria_cumplimiento=[];
        $porcentaje_categoria_calidad = [];
         $porcentaje_categoria_oferta = [];
        $listas_fechas_ = _fechas_array_List($Periodo);
        //vdebug($listas_fechas_);
            $label_Grafica=[];
            $data_grafica_General_SD = [];
            $data_grafica_General_Mejorados =[];
            $data_grafica_General_Empeorados = [];

            $data_grafica_Calidad_SD = [];
            $data_grafica_Calidad_Mejorados =[];
            $data_grafica_Calidad_Empeorados = [];

            $data_grafica_Cumplimiento_SD = [];
            $data_grafica_Cumplimiento_Mejorados =[];
            $data_grafica_Cumplimiento_Empeorados = [];

            $data_grafica_Oferta_SD = [];
            $data_grafica_Oferta_Mejorados =[];
            $data_grafica_Oferta_Empeorados = [];
            
            $porcentaje_por_clientes["Cumplimiento"]= [];
            $porcentaje_por_clientes["Calidad"] = [];
            $porcentaje_por_clientes["Oferta"]= [];

             $datos_anterior_general=[];
            
             $Porcentaje_Calidad_General= 0;
             $Porcentaje_Cumplimiento_General= 0;
             $Porcentaje_Oferta_General= 0;
            //$listas_fechas_ = ['2016-12-30','2016-12-31'];
            // ahora obtengo las graficas
            $total_Empeorados=0;
            $total_Mejorados=0;
            foreach($listas_fechas_ as $fecha){
                //vdebug($fecha);
                $listaClientes= $this->getClientesProveedores($IDEmpresa,$fecha,$Periodo,$Quienes);
                
                $Total_Cumplimiento_SD = 0;
                $Total_Cumplimiento_Mejorado = 0;
                $Total_Cumplimiento_Empeorado = 0;
                $Total_Calidad_SD = 0;
                $Total_Calidad_Mejorado = 0;
                $Total_Calidad_Empeorado = 0;
                $Total_Oferta_SD = 0;
                $Total_Oferta_Mejorado = 0;
                $Total_Oferta_Empeorado = 0;
                $Total_SD_gen=0;
                $Total_Empeorado_gen=0;
                $Total_Mejorado_gen=0;

                foreach ($listaClientes as $cliente) {
                   
                    //vdebug($cliente);
                    $porcentaje_array_pasado = [];
                    $porcentaje_array_actual = [];
                    $dato_anterior_cumplimiento= [];
                    $dato_anterior_calidad= [];
                    $dato_anterior_oferta= [];
                    
                    $Suma_porcentaje_general_pasado = 0;
                    $Suma_porcentaje_general_actual = 0;


                    
                    // cumplimiento 
                     foreach($preguntas_cumplimiento as $pregunta){
                        $datos_pregunta = $this->Model_Preguntas->Datos_Pregunta($pregunta);
                        if($datos_pregunta['Forma']==='SI/NO' || $datos_pregunta['Forma']==='SI/NO/NA' || $datos_pregunta['Forma']==='SI/NO/NS'){
                           
                            // para obtener el porcentaje anterior le resto un dia ala fecha actual
                            if($Periodo === "MA" || $Periodo==='A'){
                                 $fechaanterior = date('Y-m', strtotime($fecha . "- 30days"));
                            }else{
                                 $fechaanterior = date('Y-m-d', strtotime($fecha . "- 1days"));
                            }
                          
                            
                            // porcentaje pasado
                            $porcentaje_pasado=  $this->RiesgoPorcentajeFechaPregunta($cliente['IDEmpresaB'],$fechaanterior,$pregunta,$Periodo);
                           
                            if($porcentaje_pasado['Porcentaje'] === null){
                                $porcentaje_pas = 0;
                            }else{
                                $porcentaje_pas = $porcentaje_pasado['Porcentaje'];
                            }
                            
                             array_push($porcentaje_array_pasado,array("porcentaje"=>$porcentaje_pas,"Peso"=>$datos_pregunta['PorTotal']));
                            
                            // porcentaje actual
                            $porcentaje_actual = $this->RiesgoPorcentajeFechaPregunta($cliente['IDEmpresaB'],$fecha,$pregunta,$Periodo);
                            if($porcentaje_actual['Porcentaje'] === null){
                                $porcentaje_actu = 0;
                            }else{
                                $porcentaje_actu = $porcentaje_actual['Porcentaje'];
                            }
                            array_push($porcentaje_array_actual,array("porcentaje"=>$porcentaje_actu,"Peso"=>$datos_pregunta['PorTotal']));
                            
                           
                        }
                    }
                    // ahora obtengo el procentaje pasado y actual para poder comparar
                    $_Porcentaje_pasado= promedio_array_riesgo($porcentaje_array_pasado);
                    $_Porcentaje_actual= promedio_array_riesgo($porcentaje_array_actual);
                    
                    $Porcentaje_Cumplimiento_General= $Porcentaje_Cumplimiento_General + $_Porcentaje_actual;
            
                    $Suma_porcentaje_general_pasado=$Suma_porcentaje_general_pasado+$_Porcentaje_pasado;
                    $Suma_porcentaje_general_actual = $Suma_porcentaje_general_actual +$_Porcentaje_actual;
                    // guardo el procentaje actual del cliente 
                    array_push($porcentaje_por_clientes["Cumplimiento"],$_Porcentaje_actual);


                    if($_Porcentaje_pasado === 0  && $_Porcentaje_actual === 0 ){
                        $Total_Cumplimiento_SD ++;
                        array_push($dato_anterior_cumplimiento,'SD');
                    }else if($_Porcentaje_pasado   > $_Porcentaje_actual  ){
                        $Total_Cumplimiento_Empeorado ++;
                        array_push($dato_anterior_cumplimiento,'E');
                    }else if($_Porcentaje_pasado < $_Porcentaje_actual  ){
                        $Total_Cumplimiento_Mejorado ++;
                        array_push($dato_anterior_cumplimiento,'M');
                    }else if($_Porcentaje_pasado === $_Porcentaje_actual ){
                        if($dato_anterior_cumplimiento[count($dato_anterior_cumplimiento)-1]==='E'){
                            $Total_Cumplimiento_Empeorado ++;
                        }
                        if($dato_anterior_cumplimiento[count($dato_anterior_cumplimiento)-1]==='SD'){
                             $Total_Cumplimiento_SD++;   
                        }
                        if($dato_anterior_cumplimiento[count($dato_anterior_cumplimiento)-1]==='M'){
                            $Total_Cumplimiento_Mejorado ++;    
                        }
                    }


                   
                    
                    
                    $porcentaje_array_pasado = [];
                    $porcentaje_array_actual = [];
                    // calidad
                         foreach($preguntas_calidad as $pregunta){
                            $datos_pregunta = $this->Model_Preguntas->Datos_Pregunta($pregunta);
                            if($datos_pregunta['Forma']==='SI/NO' || $datos_pregunta['Forma']==='SI/NO/NA' || $datos_pregunta['Forma']==='SI/NO/NS'){
                            
                                // para obtener el porcentaje anterior le resto un dia ala fecha actual
                                if($Periodo === "MA" || $Periodo==='A'){
                                 $fechaanterior = date('Y-m', strtotime($fecha . "- 30days"));
                            }else{
                                 $fechaanterior = date('Y-m-d', strtotime($fecha . "- 1days"));
                            }
                            
                                // porcentaje pasado
                                $porcentaje_pasado=  $this->RiesgoPorcentajeFechaPregunta($cliente['IDEmpresaB'],$fechaanterior,$pregunta,$Periodo);
                            
                                if($porcentaje_pasado['Porcentaje'] === null){
                                    $porcentaje_pas = 0;
                                }else{
                                    $porcentaje_pas = $porcentaje_pasado['Porcentaje'];
                                }
                                
                                 array_push($porcentaje_array_pasado,array("porcentaje"=>$porcentaje_pas,"Peso"=>$datos_pregunta['PorTotal']));
                            
                                // porcentaje actual
                                $porcentaje_actual = $this->RiesgoPorcentajeFechaPregunta($cliente['IDEmpresaB'],$fecha,$pregunta,$Periodo);
                                if($porcentaje_actual['Porcentaje'] === null){
                                    $porcentaje_actu = 0;
                                }else{
                                    $porcentaje_actu = $porcentaje_actual['Porcentaje'];
                                }
                                
                                 array_push($porcentaje_array_actual,array("porcentaje"=>$porcentaje_actu,"Peso"=>$datos_pregunta['PorTotal']));
                           
                            
                            }
                            
                        }
                        // ahora obtengo el procentaje pasado y actual para poder comparar
                        $_Porcentaje_pasado= promedio_array_riesgo($porcentaje_array_pasado);
                        $_Porcentaje_actual= promedio_array_riesgo($porcentaje_array_actual);
                         $Porcentaje_Calidad_General= $Porcentaje_Calidad_General + $_Porcentaje_actual;
            
                        $Suma_porcentaje_general_pasado=$Suma_porcentaje_general_pasado+$_Porcentaje_pasado;
                        $Suma_porcentaje_general_actual = $Suma_porcentaje_general_actual +$_Porcentaje_actual;
                        
                        array_push($porcentaje_por_clientes["Calidad"],$_Porcentaje_actual);

                        if($_Porcentaje_pasado === 0  && $_Porcentaje_actual === 0 ){
                            $Total_Calidad_SD ++;
                            array_push($dato_anterior_calidad,'SD');
                        }else if($_Porcentaje_pasado  > $_Porcentaje_actual ){
                            $Total_Calidad_Empeorado ++;
                            array_push($dato_anterior_calidad,'E');
                        }else if($_Porcentaje_pasado  < $_Porcentaje_actual  ){
                            $Total_Calidad_Mejorado ++;
                            array_push($dato_anterior_calidad,'M');
                        }else if($_Porcentaje_pasado === $_Porcentaje_actual ){
                            if($dato_anterior_calidad[count($dato_anterior_calidad)-1]==='E'){
                                $Total_Calidad_Empeorado ++;
                            }
                            if($dato_anterior_calidad[count($dato_anterior_calidad)-1]==='SD'){
                                $Total_Calidad_SD++;   
                            }
                            if($dato_anterior_calidad[count($dato_anterior_calidad)-1]==='M'){
                                $Total_Calidad_Mejorado ++;    
                            }
                        }

                   if($comoQue === 'proveedor'){

                    
                     $porcentaje_array_pasado = [];
                     $porcentaje_array_actual = [];
                    // oferta
                        foreach($preguntas_oferta as $pregunta){
                            $datos_pregunta = $this->Model_Preguntas->Datos_Pregunta($pregunta);
                            if($datos_pregunta['Forma']==='SI/NO' || $datos_pregunta['Forma']==='SI/NO/NA' || $datos_pregunta['Forma']==='SI/NO/NS'){
                            
                                // para obtener el porcentaje anterior le resto un dia ala fecha actual
                                if($Periodo === "MA" || $Periodo==='A'){
                                 $fechaanterior = date('Y-m', strtotime($fecha . "- 30days"));
                                }else{
                                    $fechaanterior = date('Y-m-d', strtotime($fecha . "- 1days"));
                                }
                            
                                // porcentaje pasado
                                $porcentaje_pasado=  $this->RiesgoPorcentajeFechaPregunta($cliente['IDEmpresaB'],$fechaanterior,$pregunta,$Periodo);
                            
                                if($porcentaje_pasado['Porcentaje'] === null){
                                    $porcentaje_pas = 0;
                                }else{
                                    $porcentaje_pas = $porcentaje_pasado['Porcentaje'];
                                }
                                 array_push($porcentaje_array_pasado,array("porcentaje"=>$porcentaje_pas,"Peso"=>$datos_pregunta['PorTotal']));
                            
                                
                                // porcentaje actual
                                $porcentaje_actual = $this->RiesgoPorcentajeFechaPregunta($cliente['IDEmpresaB'],$fecha,$pregunta,$Periodo);
                                if($porcentaje_actual['Porcentaje'] === null){
                                    $porcentaje_actu = 0;
                                }else{
                                    $porcentaje_actu = $porcentaje_actual['Porcentaje'];
                                }
                               
                                array_push($porcentaje_array_actual,array("porcentaje"=>$porcentaje_actu,"Peso"=>$datos_pregunta['PorTotal']));
                           
                            
                            }
                            
                        }
                        // ahora obtengo el procentaje pasado y actual para poder comparar
                        $_Porcentaje_pasado= promedio_array_riesgo($porcentaje_array_pasado);
                        $_Porcentaje_actual= promedio_array_riesgo($porcentaje_array_actual);
                         $Porcentaje_Oferta_General=  $Porcentaje_Oferta_General +$_Porcentaje_actual;
                        $Suma_porcentaje_general_pasado=$Suma_porcentaje_general_pasado+$_Porcentaje_pasado;
                        $Suma_porcentaje_general_actual = $Suma_porcentaje_general_actual +$_Porcentaje_actual;
                        
                        array_push($porcentaje_por_clientes["Oferta"],$_Porcentaje_actual);

                        if($_Porcentaje_pasado === 0  && $_Porcentaje_actual === 0 ){
                            $Total_Oferta_SD ++;
                            array_push($dato_anterior_oferta,'SD');
                        }else if($_Porcentaje_pasado  > $_Porcentaje_actual ){
                            $Total_Oferta_Empeorado ++;
                            array_push($dato_anterior_oferta,'E');
                        }else if($_Porcentaje_pasado  < $_Porcentaje_actual  ){
                            $Total_Oferta_Mejorado ++;
                            array_push($dato_anterior_oferta,'M');
                        }else if($_Porcentaje_pasado === $_Porcentaje_actual ){
                            if($dato_anterior_oferta[count($dato_anterior_oferta)-1]==='E'){
                                $Total_Oferta_Empeorado ++;
                            }
                            if($dato_anterior_oferta[count($dato_anterior_oferta)-1]==='SD'){
                                $Total_Oferta_SD++;   
                            }
                            if($dato_anterior_oferta[count($dato_anterior_oferta)-1]==='M'){
                                $Total_Oferta_Mejorado ++;    
                            }
                        }
                     }
                     if($Suma_porcentaje_general_pasado === 0  && $Suma_porcentaje_general_actual === 0 ){
                            $Total_SD_gen ++;
                            array_push($datos_anterior_general,array("Status"=>'SD',"IDCliente"=>$cliente['IDEmpresaB']));
                        }else if($Suma_porcentaje_general_pasado  > $Suma_porcentaje_general_actual ){
                            $Total_Empeorado_gen ++;
                            array_push($datos_anterior_general,array("Status"=>'E',"IDCliente"=>$cliente['IDEmpresaB']));
                        }else if($Suma_porcentaje_general_pasado  < $Suma_porcentaje_general_actual  ){
                            $Total_Mejorado_gen ++;
                             array_push($datos_anterior_general,array("Status"=>'M',"IDCliente"=>$cliente['IDEmpresaB']));
                        }else if($Suma_porcentaje_general_pasado === $Suma_porcentaje_general_actual ){
                            for($i=count($datos_anterior_general);$i>=0; $i--){
                                if($dato["IDCliente"]===$cliente['IDEmpresaB']){
                                    if($dato["Status"]==='E'){
                                    $Total_Empeorado_gen ++;
                                    }
                                    if($dato["Status"]==='SD'){
                                        $Total_SD_gen++;   
                                    }
                                    if($dato["Status"]==='M'){
                                        $Total_Mejorado_gen ++;    
                                    }
                                }
                            }
                            
                        }
                    }
                    


               array_push($data_grafica_General_SD,$Total_SD_gen);
                array_push($data_grafica_General_Mejorados,$Total_Mejorado_gen);
                array_push($data_grafica_General_Empeorados,$Total_Empeorado_gen);
                    

                array_push($data_grafica_Calidad_SD,$Total_Calidad_SD);
                array_push($data_grafica_Calidad_Mejorados,$Total_Calidad_Mejorado);
                array_push($data_grafica_Calidad_Empeorados,$Total_Calidad_Empeorado);

                array_push($data_grafica_Cumplimiento_SD,$Total_Cumplimiento_SD);
                array_push($data_grafica_Cumplimiento_Mejorados,$Total_Cumplimiento_Mejorado);
                array_push($data_grafica_Cumplimiento_Empeorados,$Total_Cumplimiento_Empeorado);

                array_push($data_grafica_Oferta_SD,$Total_Oferta_SD);
                array_push($data_grafica_Oferta_Mejorados,$Total_Oferta_Mejorado);
                array_push($data_grafica_Oferta_Empeorados,$Total_Oferta_Empeorado);
            
                
            }
            $data['graficas']=array(
                "Calidad"=>array(
                    "SD"=>$data_grafica_Calidad_SD,
                    "Mejorados"=>$data_grafica_Calidad_Mejorados,
                    "Empeorado"=>$data_grafica_Calidad_Empeorados
                ),
                "Cumplimiento"=>array(
                    "SD"=>$data_grafica_Cumplimiento_SD,
                    "Mejorados"=>$data_grafica_Cumplimiento_Mejorados,
                    "Empeorado"=>$data_grafica_Cumplimiento_Empeorados
                ),
                "Oferta"=>array(
                    "SD"=>$data_grafica_Oferta_SD,
                    "Mejorados"=>$data_grafica_Oferta_Mejorados,
                    "Empeorado"=>$data_grafica_Oferta_Empeorados
                ),

                 "General"=>array(
                    "SD"=>$data_grafica_General_SD,
                    "Mejorados"=>$data_grafica_General_Mejorados,
                    "Empeorado"=>$data_grafica_General_Empeorados
                ),
                

            );

            $data["labels"]=$listas_fechas_;
            $data["NumMejorados"]= $Total_Mejorado_gen;
            $data["NumEmpeorados"]= $Total_Empeorado_gen;
            $data["NumSD"]= $Total_SD_gen;
            $data["porcentajes"]= array(
                "General"=>$Porcentaje_Oferta_General+$Porcentaje_Calidad_General+$Porcentaje_Cumplimiento_General,
                "Oferta"=>$Porcentaje_Oferta_General,
                "Calidad"=>$Porcentaje_Calidad_General,
                "Cumplimiento"=>$Porcentaje_Cumplimiento_General

            );
            
            $data["periodo"]=$listas_fechas_[0]."-".$listas_fechas_[count($listas_fechas_)-1];
            return $data;
            

    } 






    // funcion para saber como fue contstada una preguna en relacion con lavaloracion y el cliente que la recibio
    public function PreguntaRespondidaFecha($Para,$IDPregunta,$Fecha,$IDReceptor,$Forma,$periodo){
        $Para = strtoupper($Para);
        //vdebug($Forma);
        $Formas = explode('/',$Forma);
       // vdebug($Formas);

       if($periodo!=='M'){
            $Fecha =$Fecha."-31";
       }
        // calculo cuantas veces se realizo esa pregunta en esa fecha
        $respuesta_count = $this->db->select('count(tbcalificaciones.IDCalificacion) AS numcalificaciones')
                              ->join('tbdetallescalificaciones','tbdetallescalificaciones.IDCalificacion = tbcalificaciones.IDCalificacion')
                              ->where("IDEmpresaReceptor ='$IDReceptor' AND date(FechaRealizada)='$Fecha' AND Emitidopara = '$Para' AND STATUS='ACTIVA' AND IDPregunta='$IDPregunta'")
                              ->from('tbcalificaciones')
                              ->get();
         $data['NumeroTotal']=$respuesta_count->row_array()["numcalificaciones"];
            foreach($Formas as $respuesta_){
                $respuesta_ = strtoupper($respuesta_);
                $respuesta = $this->db->select('count(*) as num')
                              ->join('tbdetallescalificaciones','tbdetallescalificaciones.IDCalificacion = tbcalificaciones.IDCalificacion')
                              ->where( " IDEmpresaReceptor ='$IDReceptor'  AND Respuesta ='$respuesta_' AND date(FechaRealizada)='$Fecha' AND Emitidopara = '$Para' AND STATUS='ACTIVA' AND IDPregunta='$IDPregunta'")
                              ->from('tbcalificaciones')
                              ->get();
            $data[$respuesta_] = $respuesta->row_array()["num"];
            }
       
        $data['NumeroTotal']=$respuesta_count->row_array()["numcalificaciones"];
       
       return $data ;
    }

    public function PreguntaRespondidaFechaM($Para,$IDPregunta,$mes,$anio,$IDReceptor){
        $Para = strtoupper($Para);

        // calculo cuantas veces se realizo esa pregunta en esa fecha
        $respuesta_count = $this->db->select('count(tbcalificaciones.IDCalificacion) AS numcalificaciones')
                              ->join('tbdetallescalificaciones','tbdetallescalificaciones.IDCalificacion = tbcalificaciones.IDCalificacion')
                              ->where("IDEmpresaReceptor ='$IDReceptor' AND month(FechaRealizada)='$mes' and YEAR(FechaRealizada)='$anio' AND Emitidopara = '$Para' AND STATUS='ACTIVA' AND IDPregunta='$IDPregunta'")
                              ->from('tbcalificaciones')
                              ->get();
        $respuesta = $this->db->select('count(*) as error')
                              ->join('tbdetallescalificaciones','tbdetallescalificaciones.IDCalificacion = tbcalificaciones.IDCalificacion')
                              ->where( " IDEmpresaReceptor ='$IDReceptor'  AND Respuesta <> (SELECT condicion FROM preguntas_val WHERE IDPregunta = '$IDPregunta') AND month(FechaRealizada)='$mes' and YEAR(FechaRealizada)='$anio' AND Emitidopara = '$Para' AND STATUS='ACTIVA' AND IDPregunta='$IDPregunta'")
                              ->from('tbcalificaciones')
                              ->get();
        

        $data['NumeroTotal']=$respuesta_count->row_array()["numcalificaciones"];
        $data['erroneas']=$respuesta->row_array()["error"];
        $data['correctas']=$respuesta_correcta->row_array()["correcta"];
       
       return $data ;
    }

    // funcion para obtener los ID de los clientes o de los proveedores
    public function getClientesProveedores($IDEmprsa,$Fecha,$Tiempo,$Tipo){

        if( $Tiempo === 'MA'|| $Tiempo === 'AC' || $Tiempo === 'A'){
            $fecha =$Fecha."-31";
            $respuesta = $this->db->query("SELECT IDEmpresaB FROM tbrelacion WHERE IDEmpresaP ='$IDEmprsa' AND date(FechaRelacion) <= '$fecha'  AND Tipo ='$Tipo'");
        }
        if( $Tiempo === 'M' ){
             $respuesta = $this->db->query("SELECT IDEmpresaB FROM tbrelacion WHERE IDEmpresaP ='$IDEmprsa' AND DATE(FechaRelacion) <= '$Fecha' AND Tipo ='$Tipo'");
        }


       
        return $respuesta->result_array();

    }

    // Riesgo General por pregunta devuelve el porcentaje dependido la fecha
	public function RiesgoPorcentajeFechaPregunta($IDEmpresa,$fecha,$IDPregunta,$Periodo){
        
       if( $Periodo === 'MA'|| $Periodo === 'AC'){
           $fecha = $fecha."-31";
            $respuesta = $this->db
        ->query(
            "SELECT round(AVG((SELECT COUNT(*) FROM tbdetallescalificaciones WHERE IDCalificacion in (SELECT  IDCalificacion FROM  tbcalificaciones WHERE tbcalificaciones.IDEmpresaReceptor ='$IDEmpresa' AND date(FechaRealizada)='$fecha') and Respuesta <> (SELECT condicion FROM preguntas_val WHERE IDPregunta = '$IDPregunta') AND IDPregunta ='$IDPregunta') / (SELECT  count(IDCalificacion) FROM  tbcalificaciones WHERE tbcalificaciones.IDEmpresaReceptor ='$IDEmpresa' AND date(FechaRealizada)='$fecha'))*100,2) as Porcentaje
             FROM tbdetallescalificaciones WHERE IDCalificacion in (SELECT  IDCalificacion FROM  tbcalificaciones WHERE tbcalificaciones.IDEmpresaReceptor ='$IDEmpresa' AND date(FechaRealizada)='$fecha')");
        
        }else{
            $respuesta = $this->db
        ->query(
            "SELECT round(AVG((SELECT COUNT(*) FROM tbdetallescalificaciones WHERE IDCalificacion in (SELECT  IDCalificacion FROM  tbcalificaciones WHERE tbcalificaciones.IDEmpresaReceptor ='$IDEmpresa' AND date(FechaRealizada)='$fecha') and Respuesta <> (SELECT condicion FROM preguntas_val WHERE IDPregunta = '$IDPregunta') AND IDPregunta ='$IDPregunta') / (SELECT  count(IDCalificacion) FROM  tbcalificaciones WHERE tbcalificaciones.IDEmpresaReceptor ='$IDEmpresa' AND date(FechaRealizada)='$fecha'))*100,2) as Porcentaje
             FROM tbdetallescalificaciones WHERE IDCalificacion in (SELECT  IDCalificacion FROM  tbcalificaciones WHERE tbcalificaciones.IDEmpresaReceptor ='$IDEmpresa' AND date(FechaRealizada)='$fecha')");
        
        }
        
        return $respuesta->row_array();
         
    }
}