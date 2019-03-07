<?php
  session_start();

  require_once("conector.php");

  //Respuesta por defecto
  $retorno = array('msg' => 'NO EXISTE');

  $baseDatos = new ConectorBD('localhost', 'root', '');
  $baseDatos->initConexion('agenda');

  if (isset($_SESSION['id_usuario'])){
    if (isset($_POST['id'])){
      $retorno = array('msg' => 'OK');

      //Consultar eventos del usuario de la sesion y el id relacionado
      $eventos =  $baseDatos->consultaToArray(array('paramConsulta' => array('tablas' => array('eventos'),
                                                                              'campos' => array( '*'),
                                                                            'condicion' => $_SESSION['id_usuario'] . '= id_usuario AND
                                                                                           id = ' . $_POST['id'])));

      //Elimina el registro en caso de que exista
      if ($eventos['numRows'] > 0) $baseDatos->eliminarRegistro('eventos', $_SESSION['id_usuario'] . '= id_usuario AND id = ' . $_POST['id']);
      else $retorno = array('msg' => 'NO HAY EVENTO A ELIMINAR');
    }else $retorno = array('msg' => 'ID NO EXISTE');
  }

  $baseDatos->cerrarConexion();

  echo json_encode($retorno);
?>
