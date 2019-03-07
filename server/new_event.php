<?php
  session_start();

  require_once("conector.php");

  //Respuesta por defecto
  $retorno = array('msg' => 'NO EXISTE');

  $baseDatos = new ConectorBD('localhost', 'root', '');
  $baseDatos->initConexion('agenda');

  if (isset($_SESSION['id_usuario'])){
    //Crea el evento si existe un titulo y un usuario logueado
    if ($_POST['titulo'] != ""){
      $evento = array();
      $evento['titulo'] ='"' . $_POST['titulo'] . '"';
      $evento['start_date'] = '"' . $_POST['start_date'] . '"';
      $evento['start_hour'] = ($_POST['start_hour'] == "")?'NULL': '"' . $_POST['start_hour'] . '"';
      $evento['end_date'] = ($_POST['end_date'] == "")?'NULL': '"' . $_POST['end_date'] . '"';
      $evento['end_hour'] = ($_POST['end_hour'] == "")?'NULL': '"' . $_POST['end_hour'] . ':00"';

      //Si no hay fecha end, NULL la hora
      if ($_POST['end_date'] == "") $evento['end_hour'] = 'NULL';

      //Si el evento es todo el dia, NULL para horas de inicio y finalizacion
      if ($_POST['allDay'] == 'true') {
        $evento['end_date'] = $evento['start_date']; $evento['end_hour'] = 'NULL'; $evento['start_hour'] = 'NULL';
      }

      $evento['allDay'] = $_POST['allDay'];

      //Si no hay fecha de finalizacion y el evento NO es todo el dia, programa el evento como si fuera TODO el dia
      if ($_POST['end_date'] == "" && $_POST['allDay'] != 'true'){
        $evento['end_date'] = $evento['start_date'];
        $evento['allDay'] = true;
        $evento['start_hour'] = 'NULL';
        $evento['end_hour'] = 'NULL';
      }

      $evento['id_usuario'] = $_SESSION['id_usuario'];

      $baseDatos->insertData('eventos', $evento);

      //Captura el idEvento insertado
      $idEvento =  $baseDatos->consultaUltimoID(array('paramConsulta' => array('tablas' => array('eventos'),
                                                                              'campos' => array( 'MAX(id)'))));
      $retorno = array('msg' => 'OK', 'id' => $idEvento);
    }else{
      $retorno = array('msg' => 'NO SE PUDO CREAR EL EVENTO');
    }
  }

  $baseDatos->cerrarConexion();

  echo json_encode($retorno);
?>
