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
      foreach($eventos as $evento){

      }

    }
  }

  $baseDatos->cerrarConexion();

  echo json_encode($retorno);
?>
