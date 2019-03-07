<?php
  session_start();

  require_once("conector.php");

  //Respuesta por defecto
  $retorno = array('msg' => 'NO EXISTE');

  $baseDatos = new ConectorBD('localhost', 'root', '');
  $baseDatos->initConexion('agenda');

  if (isset($_SESSION['id_usuario'])){
    $evento = array();
    $evento['titulo'] ='"' . $_POST['titulo'] . '"';
    $evento['start_date'] = '"' . $_POST['start_date'] . '"';
    $evento['start_hour'] = ($_POST['start_hour'] == "")?'NULL': '"' . $_POST['start_hour'] . '"';
    $evento['end_date'] = ($_POST['end_date'] == "")?'NULL': '"' . $_POST['end_date'] . ':00"';
    $evento['end_hour'] = ($_POST['end_hour'] == "")?'NULL': '"' . $_POST['end_hour'] . ':00"';
    if ($_POST['allDay'] == 'true') {
      $evento['end_date'] = 'NULL'; $evento['end_hour'] = 'NULL'; $evento['start_hour'] = 'NULL';
    }
    $evento['allDay'] = $_POST['allDay'];
    $evento['id_usuario'] = $_SESSION['id_usuario'];

    $baseDatos->insertData('eventos', $evento);

    $retorno = array('msg' => 'OK');

  }

  $baseDatos->cerrarConexion();

  echo json_encode($retorno);
?>
