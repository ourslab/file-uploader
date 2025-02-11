<?php
  $user_ip = $_SERVER['REMOTE_ADDR'];
  $query = sql_select("IP", "*", "addr='{$user_ip}'");
  if ($query->rowCount() == 0) {
    $user_id = -1;
    sql_insert("IP", "id,addr,user_id", "0,'{$user_ip}',{$user_id}");
    $query = sql_select("IP", "*", "addr='{$user_ip}'");
  }
  if ($data = sql_data($query)) {
    $user_id = $data['user_id'];
    $query = sql_select("Users", "*", "id={$user_id}");
    if ($data = sql_data($query)) {
      $user_name = $data['name'];
    } else {
      $user_name = 'Unknown';
    }
  }
  if(!empty($_POST['user-name'])){
    $user_name = safe_str($_POST['user-name']);
    $query = sql_select("Users", "*", "name='{$user_name}'");
    if ($query->rowCount() == 0) {
      sql_insert("Users", "id,name", "0,'{$user_name}'");
    }
    $query = sql_select("Users", "*", "name='{$user_name}'");
    if ($data = sql_data($query)) {
      $user_id = $data['id'];
      sql_update("IP", "user_id={$user_id}", "addr='{$user_ip}'");
    }
  }
  if ($user_name == "public" || $user_name == "Unknown") {
    $user_birth_y = 2000;
    $user_birth_m = 1;
    $user_birth_d = 1;
  } else {
    $query = sql_select("Birthday", "*", "user_name='{$user_name}'");
    if ($data = sql_data($query)) {
      $user_birth_y = intval($data['year']);
      $user_birth_m = intval($data['month']);
      $user_birth_d = intval($data['day']);
    } else {
      sql_insert("Birthday", "id,user_name,year,month,day", "0,'{$user_name}',0,0,0");
      $user_birth_y = 0;
      $user_birth_m = 0;
      $user_birth_d = 0;
    }
  }
  array_unshift($msg, "Welcome {$user_name}...");
  $admin = FALSE;
  if (isset($_POST['login'])) {
    if (empty($_SERVER['PHP_AUTH_USER']) || empty($_SERVER['PHP_AUTH_PW'])) {
      basic_auth("No Input");
    }
  }
  if (isset($_POST['logout'])) {
    header("HTTP/1.0 401 Unauthorized");
  } else if (!empty($_SERVER['PHP_AUTH_USER']) && !empty($_SERVER['PHP_AUTH_PW'])){
    $login_hash = md5("{$_SERVER['PHP_AUTH_USER']}{$_SERVER['PHP_AUTH_PW']}");
    if ($login_hash == "cf686a8b44e61858048020784fef8120") {
      $admin = TRUE;
      $msg[0] .= " [admin]";
    } else {
      basic_auth($login_hash);
    }
  }
?>
