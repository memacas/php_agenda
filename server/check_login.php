<?php
  session_start();

  require_once("conector.php");

  //Respuesta por defecto
  $retorno = array('msg' => 'NO EXISTE');

  $baseDatos = new ConectorBD('localhost', 'root', '');
  $baseDatos->initConexion('agenda');

  unset($_SESSION['ID_USUARIO']);

  //Consultar cuantos usuarios existen
  $usuarios =  $baseDatos->consultaToArray(array('paramConsulta' => array('tablas' => array('usuarios'), 'campos' => array('EMAIL'))));

  //Crear usuarios si la tabla es vacia
  if ($usuarios['numRows'] == 0){
    $usuarios = array();

    array_push($usuarios, array('NOMBRE' => '"Prueba1"', 'EMAIL' => '"prueba1@gmail.com"', 'PASSWORD' => '"' . password_hash('prueba1', PASSWORD_DEFAULT) . '"'));
    array_push($usuarios, array('NOMBRE' => '"Prueba2"', 'EMAIL' => '"prueba2@gmail.com"', 'PASSWORD' => '"' . password_hash('prueba2', PASSWORD_DEFAULT) . '"'));
    array_push($usuarios, array('NOMBRE' => '"Prueba3"', 'EMAIL' => '"prueba3@gmail.com"', 'PASSWORD' => '"' . password_hash('prueba3', PASSWORD_DEFAULT) . '"'));

    foreach ($usuarios as $usuario) $baseDatos->insertData('usuarios', $usuario);
  }else{

    //Si hay usuarios creados, comparar con los datos del formulario de login
    if (isset($_POST['username']) &&
        isset($_POST['password'])){
          $usuarios =  $baseDatos->consultaToArray(array('paramConsulta' => array('tablas' => array('usuarios'),
                                                                                  'campos' => array( 'ID', 'EMAIL', 'PASSWORD'),
                                                                                  'condicion' => 'TRIM(UPPER("' . $_POST['username'] . '")) = TRIM(UPPER(EMAIL))')));

          foreach ($usuarios['data'] as $usuario){
            //Si el password y email corresponde al login, retorna OK e inicializa la variable de sesion con el ID
            if (password_verify($_POST['password'], $usuario['PASSWORD'])){
              $retorno = array('msg' => 'OK');
              $_SESSION['ID_USUARIO'] = $usuario['ID'];
            }
          }
    }
  }
  $baseDatos->cerrarConexion();

  echo json_encode($retorno);

 ?>
