<?php
  include(dirname(__FILE__)."/show-tags-list.php");
  include(dirname(__FILE__)."/show-files-list.php");
  include(dirname(__FILE__)."/show-backgrounds-list.php");
  include(dirname(__FILE__)."/show-birthdays-list.php");
  if (file_exists(dirname(__FILE__)."/file-uploader-tricks.php")) {
    include(dirname(__FILE__)."/file-uploader-tricks.php");
  } else {
    $js_onload .= "show_background(\"{$background_url}\");";
  }
  function show_body($files_per_page, $show_all, $file_tag) {
    if (isset($_GET['birthday'])) {
      show_birthday_list();
    } else if (isset($_GET['background-reset'])) {
      show_backgrounds_list();
    } else {
      show_files_list($files_per_page, $show_all, $file_tag);
    }
  }
  if (!empty($_GET['tag'])) {
    $file_tag = safe_str($_GET['tag']);
  } else {
    if ($user_name == "Unknown") {
      $query = sql_select("SentFiles", "tag");
      if ($data = sql_data($query)) {
        $file_tag = $data['tag'];
      }
    } else if($user_name == "public") {
      $file_tag = "public";
    } else {
      $file_tag = $user_name;
    }
  }
  $code = null;
  if (!empty($_GET['code'])) {
    $code_specified = safe_str($_GET['code']);
    $ary = str_split($code_specified);
    if (count($ary) == 11 || $ary[3] == '-' || $ary[7] == '-') {
      $ary = explode('-', "$code_specified");
      if (count($ary) == 3 || ctype_alnum($ary[0]) || ctype_alnum($ary[1]) || ctype_alnum($ary[2])) {
        $code = $code_specified;
        $generate_enabled = false;
        array_push($msg, "The File upload code is manually specified as {$code}.");
        array_push($notice, ["You cannot generate any file.", ""]);
      }
    }
  }
  if (isset($_GET['show-less'])) {
    $show_all = false;
    $show_mode_img = "/file-uploader/show-less.png";
    $show_mode_url = get_temp_full_url('show-less');
  } else {
    $show_all = true;
    $show_mode_img = "/file-uploader/show-all.png";
    $show_mode_url = get_temp_full_url('show-less', "");
  }
  if (isset($_GET['background-reset'])) {
    $background_reset_url = get_temp_full_url('background-reset');
  } else {
    $background_reset_url = get_temp_full_url('background-reset', "");
  }
  if (isset($_GET['no-generate']) || $generate_enabled == false) {
    $generate_enabled = false;
    $generate_mode_img = "/file-uploader/nogenerate.png";
    $generate_mode_url = get_temp_full_url('no-generate');
  } else {
    $generate_enabled = true;
    $generate_mode_img = "/file-uploader/generate.png";
    $generate_mode_url = get_temp_full_url('no-generate', "");
  }
  if (isset($_GET['birthday'])) {
    $birthday_url = get_temp_full_url("birthday");
  } else {
    $birthday_url = get_temp_full_url("birthday", "");
  }
  process_backgrounds_set();
  process_backgrounds_reset();
  process_files_zip($file_tag);
  process_files_sent($file_tag, $code, $generate_enabled);
  process_files_revive($file_tag);
  process_files_remove($file_tag);
  process_link_add();
  process_link_remove();
  process_birthday();
  if ($reload_time > 0) {
    $js_onload .= "reload_event = setTimeout(function(){location.href += \"\";}, {$reload_time});";
  }
  $js_onload .= "document.body.ondragover = function(e){e.preventDefault();document.getElementById(\"file-uploader-screen\").style[\"display\"] = \"block\";};";
  $js_onload .= "document.body.ondrop = file_uploader_screen;";
  $js_onload .= "document.body.onmouseover = function(){document.getElementById(\"file-uploader-screen\").style[\"display\"] = \"none\";};";
  if ($user_name != "public" && $public_mode == false) {
    if ($user_name == "Unknown") {
      $js_onload .= "change_user_name();";
    } else if ($user_birth_y == 0 || $user_birth_m == 0 || $user_birth_d == 0) {
      $js_onload .= "birthday_edit(\"{$user_name}\");";
    }
  }
  if (is_removable_file($file_tag)) {
    $js_init .= "is_removable_tag = true;";
  } else {
    $js_init .= "is_removable_tag = false;";
  }
  $js_init .= "user_name = \"{$user_name}\";";
  for ($i = 0; $i < count($msg); $i++) {
    $js_onload .= "add_message(\"{$msg[$i]}\");";
  }
  for ($i = 0; $i < count($warn); $i++) {
    $js_onload .= "add_warning(\"{$warn[$i]}\");";
  }
  for ($i = 0; $i < count($notice); $i++) {
    $js_onload .= "add_notice(\"{$notice[$i][0]}\", \"{$notice[$i][1]}\", 0);";
  }
  if (isset($_GET['embed'])) {
    $js_onload = "";
  }
?>
