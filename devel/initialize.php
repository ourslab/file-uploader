<?php
  include(dirname(__FILE__)."/settings.php");
  include(dirname(__FILE__)."/functions.php");
  include(dirname(__FILE__)."/database.php");
  include(dirname(__FILE__)."/users.php");
  if($DomainOnly){
    if($_SERVER['HTTP_HOST'] != $DomainName) {
      Redirect("http://{$DomainName}{$_SERVER['REQUEST_URI']}", 301);
    }
  } else if ($IPOnly) {
    if ($_SERVER['HTTP_HOST'] != $IPAddress) {
      Redirect("http://{$IPAddress}{$_SERVER['REQUEST_URI']}", 301);
    }
  }
  if ($HTTPSOnly) {
    if (empty($_SERVER['HTTPS'])) {
      Redirect("https://{$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}", 301);
    }
  }
  if ($Debug && !$Admin) {
    include(dirname(__FILE__)."/../maintenance.html");
    exit;
  }
  $ary = explode('?', "{$_SERVER['REQUEST_URI']}");
  $request_dir = $ary[0];
  $GET = "?";
  if (count($ary) == 2) {
    $GET = explode('#', "?{$ary[1]}")[0];
  }
  if ($HTTPSOnly) {
    $URL = "https://{$_SERVER['HTTP_HOST']}{$request_dir}";
  } else {
    $URL = "http://{$_SERVER['HTTP_HOST']}{$request_dir}";
  }
  if (isset($_GET['Generate'])) {
    $Generate = TRUE;
  } else if (isset($_GET['NoGenerate'])){
    $Generate = FALSE;
  }
  if (!($PublicMode) && $user_name != "public") {
    $query = SQLSelect("Backgrounds", "url", "user_id={$user_id}");
    if ($query->rowCount() == 0) {
      $background_index = mt_rand(0, count($default_backgrounds) - 1);
      $background_url = $default_backgrounds[$background_index];
    } else {
      $background_index = mt_rand(1, $query->rowCount());
      for ($i = 0; $i < $background_index; $i++) {
        $background_url = $query->fetch(PDO::FETCH_ASSOC)['url'];
      }
    }
  } else {
    $background_url = $default_background_public;
  }
?>
