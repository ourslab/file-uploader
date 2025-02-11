<?php
  $server = "mysql:host=localhost;dbname=LAA1177434;charset=utf8mb4";

  try {
    $db_user = "webserver";
    $db_pass = "password";
    $DBH = new PDO($server, $db_user, $db_pass);
    $DBH->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $DBH->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
  } catch (PDOException $e){
    echo('*** Database Connection Failed');
    exit;
  }

  function SQLInsert($table, $keys, $vals){
    global $DBH; $DBH->query("INSERT INTO $table($keys) VALUES($vals)");
  }
  function SQLSelect($table, $keys="*", $where="", $sort="", $limit="", $like=""){
    global $DBH; $like = explode(",", $like);
    $sql = "SELECT DISTINCT $keys FROM $table";
    if(!empty($where))    $sql .= " WHERE $where";
    if(!empty($sort))     $sql .= " ORDER BY $sort";
    if(!empty($limit))    $sql .= " LIMIT $limit";
    if(count($like) == 2) $sql .= " {$like[0]} LIKE '{$like[1]}%'";
    return $DBH->query($sql);
  }
  function SQLUpdate($table, $keys, $where=""){
    global $DBH; $sql = "UPDATE $table SET $keys"; 
    if(!empty($where)) $sql .= " WHERE $where"; $DBH->query($sql);
  }
  function SQLDelete($table, $where){
    global $DBH; $DBH->query("DELETE FROM $table WHERE $where");
  }
  function SQLAI($table, $ai){
    global $DBH; $DBH->query("ALTER TABLE $table AUTO_INCREMENT = $ai");
  }
?>
