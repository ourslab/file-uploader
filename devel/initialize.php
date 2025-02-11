<?php
  include(dirname(__FILE__)."/database.php");
  include(dirname(__FILE__)."/settings.php");
  include(dirname(__FILE__)."/functions.php");
  include(dirname(__FILE__)."/users.php");
  if($domain_only){
    if($_SERVER['HTTP_HOST'] != $domain_name) {
      redirect("http://{$domain_name}{$_SERVER['REQUEST_URI']}", 301);
    }
  } else if ($ip_only) {
    if ($_SERVER['HTTP_HOST'] != $ip_address) {
      redirect("http://{$ip_address}{$_SERVER['REQUEST_URI']}", 301);
    }
  }
  if ($https_only) {
    if (empty($_SERVER['HTTPS'])) {
      redirect("https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}", 301);
    }
  }
  if ($debug && !$admin) {
    include(dirname(__FILE__)."/../maintenance.html");
    exit;
  }
  if (isset($_GET['gen'])) {
    $generate_enabled = TRUE;
  } else if (isset($_GET['nogen'])){
    $generate_enabled = FALSE;
  }
  if (isset($_GET['reload'])) {
    $reload_time = intval($_GET['reload']);
  }
  if (!($public_mode) && $user_name != "public") {
    $query = sql_select("Backgrounds", "url", "user_id={$user_id}");
    if ($query->rowCount() == 0 || isset($_GET["allbg"])) {
      $query = sql_select("Backgrounds");
      while ($data = sql_data($query)) {
        $default_backgrounds[] = $data['url'];
      }
      $background_index = mt_rand(0, count($default_backgrounds) - 1);
      $background_url = $default_backgrounds[$background_index];
    } else {
      $background_index = mt_rand(1, $query->rowCount());
      for ($i = 0; $i < $background_index; $i++) {
        $background_url = sql_data($query)['url'];
      }
    }
  } else {
    $background_url = $default_background_public;
  }
?>
