<?php
  function basic_auth() {
    global $Debug;
    header("WWW-Authenticate: Basic realm=\"momiji\"");
    header("HTTP/1.0 401 Unauthorized");
    unauthorized();
  }
  $user_ip = $_SERVER['REMOTE_ADDR'];
  $query = SQLSelect("Users", "*", "ip='{$user_ip}'");
  if($query->rowCount() == 0){
    SQLInsert("Users", "id,name,ip", "0,'Unknown','{$user_ip}'");
  }
  $query = SQLSelect("Users", "*", "ip='{$user_ip}'");
  if($data = $query->fetch(PDO::FETCH_ASSOC)){
    $user_id = $data['id'];
    $user_name = $data['name'];
    if ($user_name == "Unknown" || $user_name == "public") {
      $user_birth_y = 2000;
      $user_birth_m = 1;
      $user_birth_d = 1;
    } else {
      $query = SQLSelect("Birthday", "*", "user_name='{$user_name}'");
      if($data = $query->fetch(PDO::FETCH_ASSOC)){
        $user_birth_y = intval($data['year']);
        $user_birth_m = intval($data['month']);
        $user_birth_d = intval($data['day']);
      }else{
        SQLInsert("Birthday", "id,user_name,year,month,day", "0,'{$user_name}',0,0,0");
        $user_birth_y = 0;
        $user_birth_m = 0;
        $user_birth_d = 0;
      }
    }
  }else{
    $user_id = -1;
    $user_name = 'public';
  }
  if(!empty($_POST['user-name'])){
    $user_name = SafeStr($_POST['user-name']);
    SQLUpdate("Users", "name='{$user_name}'", "id={$user_id}");
  }
  array_unshift($msg, "Welcome {$user_name}...");
  $Admin = FALSE;
  if (isset($_POST['login'])) {
    if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])) {
      basic_auth();
    }
  }
  if (isset($_POST['logout'])) {
    header("HTTP/1.0 401 Unauthorized");
  } else if (!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW'])){
    $login_hash = md5("{$_SERVER['PHP_AUTH_USER']}{$_SERVER['PHP_AUTH_PW']}");
    if ($login_hash == "cf686a8b44e61858048020784fef8120") {
      $Admin = TRUE;
      $msg[0] .= " [admin]";
    } else {
      basic_auth();
    }
  }
?>
