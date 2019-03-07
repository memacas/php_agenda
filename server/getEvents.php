<?php
  session_start();

  require_once("conector.php");

  //Respuesta por defecto
  $retorno = array('msg' => 'NO EXISTE');

  $baseDatos = new ConectorBD('localhost', 'root', '');
  $baseDatos->initConexion('agenda');

  print_r($_SESSION);

  //Consultar cuantos usuarios existen
  $usuarios =  $baseDatos->consultaToArray(array('paramConsulta' => array('tablas' => array('usuarios'), 'campos' => array('EMAIL'))));

  //Crear usuarios si la tabla es vacia
  if ($usuarios['numRows'] == 0){
    $usuarios = array();

  }

?>
