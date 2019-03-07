<?php


  class ConectorBD
  {
    private $host;
    private $user;
    private $password;
    private $conexion;

    function __construct($host, $user, $password){
      $this->host = $host;
      $this->user = $user;
      $this->password = $password;
    }

    function initConexion($nombre_db){
      $this->conexion = new mysqli($this->host, $this->user, $this->password, $nombre_db);
      if ($this->conexion->connect_error) {
        return "Error:" . $this->conexion->connect_error;
      }else {
        return "OK";
      }
    }

    function ejecutarQuery($query){
      return $this->conexion->query($query);
    }

    function cerrarConexion(){
      $this->conexion->close();
    }

    function newTable($nombre_tbl, $campos){
      $sql = 'CREATE TABLE '.$nombre_tbl.' (';
      $length_array = count($campos);
      $i = 1;
      foreach ($campos as $key => $value) {
        $sql .= $key.' '.$value;
        if ($i!= $length_array) {
          $sql .= ', ';
        }else {
          $sql .= ');';
        }
        $i++;
      }
      return $this->ejecutarQuery($sql);
    }

    function nuevaRestriccion($tabla, $restriccion){
      $sql = 'ALTER TABLE '.$tabla.' '.$restriccion;
      return $this->ejecutarQuery($sql);
    }

    function nuevaRelacion($from_tbl, $to_tbl, $from_field, $to_field){
      $sql = 'ALTER TABLE '.$from_tbl.' ADD FOREIGN KEY ('.$from_field.') REFERENCES '.$to_tbl.'('.$to_field.');';
      return $this->ejecutarQuery($sql);
    }

    function insertData($tabla, $data){
      $sql = 'INSERT INTO '.$tabla.' (';
      $i = 1;
      foreach ($data as $key => $value) {
        $sql .= $key;
        if ($i<count($data)) {
          $sql .= ', ';
        }else $sql .= ')';
        $i++;
      }
      $sql .= ' VALUES (';
      $i = 1;
      foreach ($data as $key => $value) {
        $sql .= $value;
        if ($i<count($data)) {
          $sql .= ', ';
        }else $sql .= ');';
        $i++;
      }

      return $this->ejecutarQuery($sql);

    }

    function getConexion(){
      return $this->conexion;
    }

    function actualizarRegistro($tabla, $data, $condicion){
      $sql = 'UPDATE '.$tabla.' SET ';
      $i=1;
      foreach ($data as $key => $value) {
        $sql .= $key.'='.$value;
        if ($i<sizeof($data)) {
          $sql .= ', ';
        }else $sql .= ' WHERE '.$condicion.';';
        $i++;
      }
      return $this->ejecutarQuery($sql);
    }

    function eliminarRegistro($tabla, $condicion){
      $sql = "DELETE FROM ".$tabla." WHERE ".$condicion.";";
      return $this->ejecutarQuery($sql);
    }

    function consultaToArray($ctaParam = array()){
      $retorno = array('numRows' => 0, 'data' => array());
      if (isset($ctaParam['paramConsulta'])){
        if (isset($ctaParam['paramConsulta']['tablas']) &&
            isset($ctaParam['paramConsulta']['campos'])){
          $condicion = (isset($ctaParam['paramConsulta']['condicion'])) ? $ctaParam['paramConsulta']['condicion'] : "";
          $tmpData = $this->consultar($ctaParam['paramConsulta']['tablas'],
                                      $ctaParam['paramConsulta']['campos'],
                                      $condicion);

          $retorno['numRows'] = $tmpData->num_rows;
          if ($retorno['numRows'] > 0) $retorno['data'] = mysqli_fetch_all($tmpData, MYSQLI_ASSOC);
        }
      }
      return $retorno;
    }

    function consultaUltimoID($cUidParam = array()){
      $retorno = "";
      if (isset($cUidParam['paramConsulta'])){
        if (isset($cUidParam['paramConsulta']['tablas']) &&
            isset($cUidParam['paramConsulta']['campos'])){
          $tmpData = $this->consultar($cUidParam['paramConsulta']['tablas'],
                                      $cUidParam['paramConsulta']['campos']);

          if ($tmpData->num_rows > 0) $retorno = mysqli_fetch_all($tmpData)[0][0];
        }
      }
      return $retorno;
    }

    function consultar($tablas, $campos, $condicion = ""){
      $sql = "SELECT ";
      $array_keys = array_keys($campos);
      $ultima_key = end($array_keys);
      foreach ($campos as $key => $value) {
        $sql .= $value;
        if ($key!=$ultima_key) {
          $sql.=", ";
        }else $sql .=" FROM ";
      }

      $array_tablas = array_keys($tablas);
      $ultima_key = end($array_tablas);
      foreach ($tablas as $key => $value) {
        $sql .= $value;
        if ($key!=$ultima_key) {
          $sql.=", ";
        }else $sql .= " ";
      }

      if ($condicion == "") {
        $sql .= ";";
      }else {
        $sql .= " WHERE " . $condicion.";";
      }

      return $this->ejecutarQuery($sql);
    }

    function consultaCompuesta($tablas, $campos, $relaciones, $condicion = ""){
      $sql = "SELECT ";
      $ultima_key = end(array_keys($campos));
      foreach ($campos as $key => $value) {
        $sql .= $value;
        if ($key!=$ultima_key) {
          $sql.=", ";
        }else $sql .=" FROM ";
      }
      $sql .= $tablas[0]." ";
      $ultima_key = end(array_keys($tablas));
      foreach ($tablas as $key => $value) {
        if ($key != 0) {
          $sql .= "JOIN ".$value." ON ".$relaciones[$key-1]." \n";
        }
      }
      if ($condicion == "") {
        $sql .= ";";
      }else {
        $sql .= $condicion.";";
      }
      return $this->ejecutarQuery($sql);
    }


  }

 ?>
