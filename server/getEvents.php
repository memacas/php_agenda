<?php
  session_start();

  require_once("conector.php");

  //Respuesta por defecto
  $retorno = array('msg' => 'NO EXISTE');

  $baseDatos = new ConectorBD('localhost', 'root', '');
  $baseDatos->initConexion('agenda');

  if (isset($_SESSION['id_usuario'])){
    $retorno = array('msg' => 'OK');
    //Consultar eventos del usuario de la sesion
    $eventos =  $baseDatos->consultaToArray(array('paramConsulta' => array('tablas' => array('eventos'),
                                                                            'campos' => array( '*'),
                                                                            'condicion' => $_SESSION['id_usuario'] . '= id_usuario')));



    //Crear usuarios si la tabla es vacia
    if ($eventos['numRows'] > 0){
      foreach($eventos['data'] as $k => $evento){
        $eventos['data'][$k]['start'] = $eventos['data'][$k]['start_date'] . ' ' . $eventos['data'][$k]['start_hour'];
        $eventos['data'][$k]['end'] = $eventos['data'][$k]['end_date'] . ' ' . $eventos['data'][$k]['end_hour'];
        $eventos['data'][$k]['title'] = $eventos['data'][$k]['titulo'];
      }
      $retorno['eventos'] = $eventos['data'];
    }
  }

  $baseDatos->cerrarConexion();

  echo json_encode($retorno);
?>
