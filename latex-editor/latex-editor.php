<?php
  function is_removable() {
    global $file_tag, $user_name, $Admin;
    $query = sql_select("Users", "*", "name='{$file_tag}'");
    if ($query->rowCount() == 0) {
      return TRUE;
    } else if ($file_tag == $user_name) {
      return TRUE;
    } else if ($file_tag == "public") {
      return TRUE;
    } else if ($Admin) {
      return TRUE;
    }
    return FALSE;
  }
  function show_files() {
    global $img_exts, $img_dir, $code;
    $files_image = array();
    $files_others = array();
    $query = sql_select("SentFiles", "*", "code='{$code}'");
    while ($data = $query->fetch(PDO::FETCH_ASSOC)) {
      $flag_others = true;
      $ary = explode('.', $data['name']);
      $file_ext = $ary[count($ary) - 1];
      for ($a = 0; $a < count($img_exts); $a++) {
        if ($file_ext == $img_exts[$a]) {
          array_push($files_image, $data['name']);
          $flag_others = false;
          break;
        }
      }
      if ($flag_others) {
        array_push($files_others, $data['name']);
      }
    }
    echo "<details><summary><img src=\"/favicon.ico\"></img>{$img_dir}</summary><ul>";
    for ($a = 0; $a < count($files_image); $a++) {
      echo "<li><ul>";
      echo "<li><img src=\"/favicon.ico\"></img></li>";
      echo "<li><a onclick=\"\">{$files_image[$a]}</a></li>";
      echo "</ul></li>";
    }
    echo "</ul></details>";
    echo "<ul>";
    for ($a = 0; $a < count($files_others); $a++) {
      echo "<li><ul>";
      echo "<li><img src=\"/favicon.ico\"></img></li>";
      echo "<li><a onclick=\"file_get('{$files_others[$a]}')\">{$files_others[$a]}</a></li>";
      echo "</ul></li>";
    }
    echo "</ul>";
    echo "<ul><li><ul>";
    echo "<li><img src=\"/favicon.ico\"></img></li>";
    echo "<li><a onclick=\"file_get()\">New File</a></li>";
    echo "</ul></li></ul>";
    echo "</ul>";
    echo "<ul><li><ul>";
    echo "<li><img src=\"/favicon.ico\"></img></li>";
    echo "<li><a onclick=\"editor_reset()\">Reset</a></li>";
    echo "</ul></li></ul>";
  }
  $js_init = "";
  $js_onload = "";
  $code = "";
  if (!empty($_GET['code'])) {
    $code = safe_str($_GET['code']);
    $js_init .= "editor.code = \"{$code}\";";
  } else {
    redirect("/", 301);
  }
  $img_dir = "fig";
  if (!empty($_GET['img_dir'])) {
    $img_dir = safe_str($_GET['img_dir']);
  }
  if (!empty($_GET['top_level'])) {
    $top_level = safe_str($_GET['top_level']);
    $js_init .= "editor.top_level = \"{$top_level}\";";
  } else {
    redirect("/", 301);
  }
  if (!empty($_POST['file_name'])) {
    exec("mkdir -p ../file-uploader/SentFiles/{$code}");
    chdir("../file-uploader/SentFiles/{$code}");
    exec("mkdir -p {$img_dir}");
    chdir($img_dir);
    for ($a = 0; $a < count($img_exts); $a++) {
      if (glob("../*.{$img_exts[$a]}")) {
        exec("ln -s ../*.{$img_exts[$a]} .");
      }
    }
    chdir("../");
    $dir_name = "SentFiles/{$code}";
    $file_name = safe_str($_POST['file_name']);
    $file_data = str_replace("\r", "\n", str_replace("\r\n", "\n", $_POST['file_data']));
    $query = sql_select("SentFiles", "*", "code='{$code}'");
    if ($data = $query->fetch(PDO::FETCH_ASSOC)) {
      $file_tag = $data['tag'];
    } else {
      $file_tag = $user_name;
    }
    if (is_removable()) {
      $file_path = "SentFiles/{$code}/{$file_name}";
      $query = sql_select("SentFiles", "*", "code='{$code}' and name='{$file_name}'");
      if ($query->rowCount() == 0) {
        sql_insert("SentFiles", "id,name,path,code,tag,stt", "0,'$file_name','$file_path','$code','$file_tag',1"); 
      }
      $file = fopen("{$file_name}", "w");
      fwrite($file, $file_data);
      fclose($file);
      $result_code = tex_compile($dir_name, $top_level, $code, $file_tag, false);
      exec("rm -rf {$img_dir}");
      show_files();
      if ($result_code) {
        echo "<!-- error -->";
      } 
    }
    exit;
  }
?>
