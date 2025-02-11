<?php
  function tags_list() {
    global $Admin, $file_tag;
    echo "<ul>\n";
    $query = SQLSelect("SentFiles", "tag", "tag='$file_tag'");
    if ($data = $query->fetch(PDO::FETCH_ASSOC)) {
      $tag = $data['tag'];
      echo "<li>";
      echo "<img class=\"tag-icon\" src=\"/favicon.ico\">";
      echo "<a class=\"highlighting\" href=\"/file-uploader/?tag={$tag}\">{$tag}</a>";
      echo "</li>\n";
    }
    $query = SQLSelect("SentFiles", "tag", "tag!='$file_tag'", "tag desc");
    while($data = $query->fetch(PDO::FETCH_ASSOC)){
      $tag = $data['tag'];
      echo "<li>";
      echo "<img class=\"tag-icon\" src=\"/favicon.ico\">";
      echo "<a href=\"/file-uploader/?tag={$tag}\">{$tag}</a>";
      echo "</li>\n";
    }
    echo "<li><a onclick=\"change_tag()\">&gt;&gt;&gt; Create a new tag</a></li>";
    echo "<li class=\"hide\"><a onclick=\"change_user_name()\">&gt;&gt;&gt; Change username</a></li>";
    if ($Admin) {
      echo "<li class=\"hide\"><a onclick=\"logout()\">&gt;&gt;&gt; Logout</a></li>";
    } else {
      echo "<li class=\"hide\"><a onclick=\"login()\">&gt;&gt;&gt; Login</a></li>";
    }
    echo "</ul>\n";
    echo "<ul>";
    echo "<li><a onclick=\"add_link()\">&gt;&gt;&gt; Add new link</a></li>";
    echo "<li><a onclick=\"delete_link()\">&gt;&gt;&gt; Delete link</a></li>";
    $query = SQLSelect("Links");
    while($data = $query->fetch(PDO::FETCH_ASSOC)){
      echo "<li><a href=\"{$data['url']}\" target=\"_blank\">({$data['id']}){$data['name']}</a></li>";
    }
    echo "</ul>";
  }
  function files_list() {
    global $URL, $GET, $file_tag, $show_all, $img_exts;
    $files_per_page = 25;
    $file_list_page = 1;
    if (!empty($_GET['page'])) {
      $file_list_page = intval($_GET['page']);
    }
    $file_id_start = $files_per_page * ($file_list_page - 1);
    $file_id_end = $file_id_start + $files_per_page; 
    if ($show_all) {
      $query = SQLSelect("SentFiles", "*", "tag='$file_tag'", "id DESC");
    } else {
      $query = SQLSelect("SentFiles", "*", "stt=1 and tag='$file_tag'", "id DESC");
    }
    $files_count = $query->rowCount();
    if ($file_id_start < 0 || $files_count < $file_id_start) {
      $file_list_page = 1;
      $file_id_start = 0;
      $file_id_end = $files_per_page;
    }
    $file_list_total_pages = ceil($files_count / $files_per_page);
    $file_list_prev_page = $file_list_page - 1;
    $file_list_next_page = $file_list_page + 1;
    if ($file_list_next_page > $file_list_total_pages) {
      $file_list_next_page = $file_list_total_pages;
    }
    echo "<p>{$file_list_page}/{$file_list_total_pages}</p>";
    $_GET['page'] = $file_list_prev_page;
    $file_list_prev_page_url = GetFullURL();
    echo "<a class=\"files-list-page-jump\" href=\"{$file_list_prev_page_url}\">&lt;&lt;&lt; Previous</a>";
    echo "&emsp;";
    $_GET['page'] = $file_list_next_page;
    $file_list_next_page_url = GetFullURL();
    echo "<a class=\"files-list-page-jump\" href=\"{$file_list_next_page_url}\">Next &gt;&gt;&gt;</a>";
    $_GET['page'] = $file_list_page;
    for ($i = 0; $data = $query->fetch(PDO::FETCH_ASSOC); $i++) {
      if ($i >= $file_id_start && $i < $file_id_end) {
        $file_id = $data['id'];
        $file_path = $data['path'];
        $file_name = $data['name'];
        $ary = explode('.', $file_name);
        $file_ext = $ary[count($ary) - 1];
        $file_status = intval($data['stt']);
        echo "<ul>";
        echo "<li class=\"files-list-id\">{$file_id}</li>";
        echo "<li class=\"files-list-link\"><a href=\"/file-uploader/{$file_path}\" target=\"_blank\">{$file_name}</a></li>";
        $set_background_path = "";
        for ($j = 0; $j < count($img_exts); $j++) {
          if ($file_ext == $img_exts[$j]) {
            $set_background_path = "/file-uploader/{$file_path}";
            break;
          }
        }
        if (!empty($set_background_path)) {
          echo "<li class=\"files-list-command\"><a onclick=\"set_background('{$set_background_path}')\">Set back</a></li>";
        } else {
          echo "<li class=\"files-list-command\"></li>";
        }
        if ($file_status == 1) {
          echo "<li class=\"files-list-command\"><a onclick=\"file_remove({$data['id']})\">Hide</a></li>";
          if ($show_all) {
            echo "<li class=\"files-list-remove\"></li>";
          }
        } else {
          echo "<li class=\"files-list-command\"><a onclick=\"file_revive({$data['id']})\">Revive</a></li>";
          echo "<li class=\"files-list-remove\"><a onclick=\"file_remove({$data['id']})\">Remove</a></li>";
        }
        echo "</ul>";
      }
    }
  }
  function birthday_list() {
    function birthday_list_row($id="", $name="", $year=Null, $month=Null, $day=Null) {
      echo "<ul class=\"birthday-list\">";
      echo "<li class=\"birthday-list-id\">{$id}</li>";
      echo "<li class=\"birthday-list-name\">{$name}</li>";
      echo "<li class=\"birthday-list-year\">{$year}</li>";
      echo "<li class=\"birthday-list-month\">{$month}</li>";
      echo "<li class=\"birthday-list-day\">{$day}</li>";
      echo "<li class=\"birthday-list-command\">";
      if ($year !== Null && $month !== Null && $day !== Null) {
        echo "<a onclick=\"birthday_edit('{$name}','{$year}-{$month}-{$day}')\">Edit</a>";
      } else {
        echo "<a onclick=\"birthday_edit('','')\">Add</a>";
      }
      echo "</li>";
      echo "<li class=\"birthday-list-remove\">";
      if ($year !== Null && $month !== Null && $day !== Null) {
        echo "<a onclick=\"birthday_edit('{$name}','',true)\">Remove</a>";
      }
      echo "</li>";
      echo "</ul>";
    }
    echo "<br>";
    $query = SQLSelect("Birthday", "*", "", "month, day");
    while ($data = $query->fetch(PDO::FETCH_ASSOC)) {
      birthday_list_row($data['id'], $data['user_name'], $data['year'], $data['month'], $data['day']);
    }
    birthday_list_row();
  }
  function show_body() {
    if (isset($_GET['birthday'])) {
      birthday_list();
    } else {
      files_list();
    }
  }
  $init_set = "";
  $init_exec = "";
  if (isset($_GET['ShowLess'])) {
    $show_all = False;
    $show_mode_img = "/file-uploader/show-less.png";
    unset($_GET['ShowLess']);
    $show_mode_url = GetFullURL();
    $_GET['ShowLess'] = "";
  } else {
    $show_all = True;
    $show_mode_img = "/file-uploader/show-all.png";
    $_GET['ShowLess'] = "";
    $show_mode_url = GetFullURL();
    unset($_GET['ShowLess']);
  }
  if ($Generate) {
    $generate_mode_img = "/file-uploader/generate.png";
    if (isset($_GET['Generate'])) {
      unset($_GET['Generate']);
    }
    $_GET['NoGenerate'] = "";
    $generate_mode_url = GetFullURL();
    unset($_GET['NoGenerate']);
    $_GET['Generate'] = "";
  } else {
    $generate_mode_img = "/file-uploader/nogenerate.png";
    if (isset($_GET['NoGenerate'])) {
      unset($_GET['NoGenerate']);
    }
    $_GET['Generate'] = "";
    $generate_mode_url = GetFullURL();
    unset($_GET['Generate']);
    $_GET['NoGenerate'] = "";
  }
  if (!empty($_GET['tag'])) { 
    $file_tag = SafeStr($_GET['tag']);
  } else {
    if ($user_name == "Unknown") {
      $init_exec = "change_user_name();\n";
      $query = SQLSelect("SentFiles", "tag");
      if ($data = $query->fetch(PDO::FETCH_ASSOC)) {
        $file_tag = $data['tag'];
      }
    } else if($user_name == "public") {
      $file_tag = "public";
    } else {
      $file_tag = $user_name;
    }
  }
  if (!empty($_POST['remove'])) {
    $file_id = intval($_POST['remove']);
    $query = SQLSelect("SentFiles", "*", "id={$file_id}");
    if ($data = $query->fetch(PDO::FETCH_ASSOC)) {
      $file_tag = $data['tag'];
      $file_status = intval($data['stt']);
      $file_path = $data['path'];
      $query = SQLSelect("Users", "*", "name='{$file_tag}'");
      $is_removable = FALSE;
      if ($query->rowCount() == 0) {
        $is_removable = TRUE;
      } else if ($file_tag == $user_name) {
        $is_removable = TRUE;
      } else if ($file_tag == "public") {
        $is_removable = TRUE;
      } else if ($Admin) {
        $is_removable = TRUE;
      }
      if ($is_removable) {
        if ($file_status == 0) {
          SQLDelete("SentFiles", "id={$file_id}");
          unlink($file_path);
          array_push($msg, "The file is removed.");
        } else {
          SQLUpdate("SentFiles", "stt=0", "id={$file_id}");
          array_push($msg, "The file is not displayed.");
        }
      } else {
        array_push($warn, "This operation is not permitted.");
      }
    }
  }
  if (isset($_POST['all-remove'])) {
    $query = SQLSelect("Users", "*", "name='{$file_tag}'");
    if ($file_tag == $user_name || $query->rowCount() == 0 || $Admin) {
      $query = SQLSelect("SentFiles", "*", "stt=0 and tag='{$file_tag}'");
      $removed_count = $query->rowCount();
      if ($removed_count > 0) {
        while ($data = $query->fetch(PDO::FETCH_ASSOC)) {
          $file_id = intval($data['id']);
          $file_path = $data['path'];
          SQLDelete("SentFiles", "id={$file_id}");
          unlink($file_path);
        }
        if ($removed_count == 1) {
          array_push($msg, "Your hidden file is removed.");
        } else {
          array_push($msg, "Your hidden {$removed_count} files are removed.");
        }
      } else {
        array_push($warn, "Your hidden file is not found.");
      }
    } else {
      array_push($warn, "You must enter your own page.");
    }
  }
  if (!empty($_POST['revive'])) {
    $file_id = intval($_POST['revive']);
    $query = SQLSelect("SentFiles", "id,tag", "id={$file_id}");
    if ($data = $query->fetch(PDO::FETCH_ASSOC)) {
      $file_tag = $data['tag'];
      $query = SQLSelect("Users", "*", "name='{$file_tag}'");
      $is_revivable = FALSE;
      if ($query->rowCount() == 0) {
        $is_revivable = TRUE;
      } else if ($file_tag == $user_name) {
        $is_revivable = TRUE;
      } else if ($file_tag == "public") {
        $is_revivable = TRUE;
      } else if ($Admin) {
        $is_revivable = TRUE;
      }
      if ($is_revivable) {
        SQLUpdate("SentFiles", "stt=1", "id={$file_id}");
        array_push($msg, "The file is revived.");
      } else {
        array_push($warn, "This operation is not permitted.");
      }
    }
  }
  if (!empty($_FILES['files'])) {
    $tex_files = array();
    $pdf_files = array();
    $zip_files = array();
    $c_files = array();
    $img_files = array();
    while (1) {
      $code = MakeCode();
      $query = SQLSelect("SentFiles", "id", "code='$code'");
      if ($query->rowCount() == 0) {
        break;
      }
    }
    $file_count = count($_FILES['files']['name']);
    $uploaded_file_count = 0;
    $dir_name_dst = "SentFiles/{$code}";
    for ($i = 0; $i < $file_count; $i++) {
      $file_path_src = $_FILES['files']['tmp_name'][$i];
      $file_name_dst = SafeStr($_FILES['files']['name'][$i]);
      $file_path_dst = "{$dir_name_dst}/{$file_name_dst}";
      if (is_uploaded_file($file_path_src)) {
        $uploaded_file_count = $uploaded_file_count + 1;
        if(!is_dir($dir_name_dst)){
          mkdir($dir_name_dst, 0700, true);
        }
        $ary = explode('.', $file_name_dst);
        $file_ext = $ary[count($ary) - 1];
        if ($file_ext == "php") {
          $file_name_dst .= ".txt";
          $file_path_dst = "{$dir_name_dst}/{$file_name_dst}";
        }
        move_uploaded_file($file_path_src, $file_path_dst);
        SQLInsert("SentFiles", "id,name,path,code,tag,stt", "0,'$file_name_dst','$file_path_dst','$code','$file_tag',1");
        if (!$Generate) {
          continue;
        } else if ($file_ext == "tex") {
          array_push($tex_files, $file_name_dst);
        } else if ($file_ext == "pdf") {
          array_push($pdf_files, $file_name_dst);
        } else if ($file_ext == "zip") {
          array_push($zip_files, $file_name_dst);
        } else if ($file_ext == "c") {
          array_push($c_files, $file_name_dst);
        } else if (in_array($file_ext, $img_exts)) {
          array_push($img_files, $file_name_dst);
        }
      }
    }
    if ($uploaded_file_count > 0) {
      if ($uploaded_file_count > 1) {
        array_push($msg, "Your files are uploaded!");
      } else {
        array_push($msg, "Your file is uploaded!");
      } 
      chdir($dir_name_dst);
      for($i = 0; $i < count($tex_files); $i++){
        tex_compile($dir_name_dst, $tex_files[$i], $code, $file_tag);
      }
      for($i = 0; $i < count($zip_files); $i++){
        if(mkdir("_temp", 0700)){
          chdir("_temp");
          $zip = $zip_files[$i];
          exec("cp ../$zip ./");
          exec("unzip $zip");
          tex_compile_recursive($dir_name_dst, $code, $file_tag);
          chdir("../");
          exec("rm -rf ./_temp");
        }
      }
      if(count($pdf_files) > 1){
        $united_src = "";
        for($i = 0; $i < count($pdf_files); $i++){
          $src = $pdf_files[$i];
          $dst = str_replace(".pdf", ".resized.pdf", $src);
          exec("pdfjam --paper a4paper --no-landscape --fitpaper false --outfile {$dst} {$src}");
          $united_src .= "$dst ";
        }
        $dst = "united_sorted_by_name.pdf";
        exec("pdfjam --paper a4paper --no-landscape --fitpaper false --outfile {$dst} \$(ls *.resized.pdf)");
        SQLInsert("SentFiles", "id,name,path,code,tag,stt", "0,'$dst','$dir_name_dst/$dst','$code','$file_tag',1");
        $dst = "united_sorted_by_sent.pdf";
        exec("pdfjam --paper a4paper --no-landscape --fitpaper false --outfile {$dst} {$united_src}");
        SQLInsert("SentFiles", "id,name,path,code,tag,stt", "0,'$dst','$dir_name_dst/$dst','$code','$file_tag',1");
        exec("rm -rf {$united_src}");
      }
      for($i = 0; $i < count($img_files); $i++){
        $src = $img_files[$i];
        $ary = explode('.', $src);
        $file_ext = $ary[count($ary) - 1];
        $dst = str_replace($file_ext, "resized.png", $src);
        exec("convert $src -resize 1024x $dst");
        SQLInsert("SentFiles", "id,name,path,code,tag,stt", "0,'$dst','$dir_name_dst/$dst','$code','$file_tag',1");
        // $dst = str_replace($file_ext, "auto-level.png", $src);
        // exec("convert $src -auto-level $dst");
        // SQLInsert("SentFiles", "id,name,path,code,tag,stt", "0,'$dst','$dir_name_dst/$dst','$code','$file_tag',1");
        // $dst = str_replace($file_ext, "negate.png", $src);
        // exec("convert $src -negate $dst");
        // SQLInsert("SentFiles", "id,name,path,code,tag,stt", "0,'$dst','$dir_name_dst/$dst','$code','$file_tag',1");
        $dst = str_replace($file_ext, "gray2white.png", $src);
        exec("convert $src -fill \"white\" -opaque \"#808080\" $dst");
        SQLInsert("SentFiles", "id,name,path,code,tag,stt", "0,'$dst','$dir_name_dst/$dst','$code','$file_tag',1");
      }
    }
  }
  if(!empty($_POST['background'])){
    $background = SafeStr($_POST['background']);
    SQLInsert("Backgrounds", "id,user_id,url", "0,{$user_id},'{$background}'");
    Redirect(GetFullURL(), 301);
  }
  if(isset($_POST['background-reset'])){
    SQLDelete("Backgrounds", "user_id='{$user_id}'");
    Redirect(GetFullURL(), 301);
  }
  if(!empty($_POST['link-url']) && !empty($_POST['link-name'])){
    $name = SafeStr($_POST['link-name']);
    $link = SafeStr($_POST['link-url']);
    SQLInsert("Links", "id,name,url", "0,'{$name}','{$link}'");
  }
  if(!empty($_POST['dellink-id'])){
    $link = intval($_POST['dellink-id']);
    SQLDelete("Links", "id='{$link}'");
  }
  if(!empty($_POST['birthday-name'])){
    $birth_name = SafeStr($_POST['birthday-name']);
    $birth_y = intval($_POST['birthday-year']);
    $birth_m = intval($_POST['birthday-month']);
    $birth_d = intval($_POST['birthday-day']);
    $birth_delete = FALSE;
    if ($birth_y == 0 && $birth_m == 0 && $birth_d == 0) {
      $birth_delete = TRUE;
      $query = SQLSelect("Birthday", "*", "user_name='{$birth_name}'");
      if ($query->rowCount() > 0) {
        SQLDelete("Birthday", "user_name='{$birth_name}'");
        array_push($msg, "{$birth_name}'s birthday is deleted!");
      } else {
        array_push($warn, "{$birth_name}'s birthday is not found");
      }
    } else if ($birth_y > 1900 && $birth_m >= 1 && $birth_m <= 12 && $birth_d >= 1 && $birth_d <= 31) {
      $user_birth_y = $birth_y;
      $user_birth_m = $birth_m;
      $user_birth_d = $birth_d;
      $query = SQLSelect("Birthday", "*", "user_name='{$birth_name}'");
      if ($query->rowCount() == 0) {
        SQLInsert("Birthday", "id,user_name,year,month,day", "0,'{$birth_name}',{$birth_y},{$birth_m},{$birth_d}");
        array_push($msg, "{$birth_name}'s birthday is added!");
      } else {
        SQLUpdate("Birthday", "year={$birth_y},month={$birth_m},day={$birth_d}", "user_name='{$birth_name}'");
        array_push($msg, "{$birth_name}'s birthday is updated!");
      }
    } else {
      array_push($warn, "Birthday update failed");
    }
  }
  if ($user_birth_y == 0) {
    $init_exec = "birthday_edit('{$user_name}', '');\n";
  }
  $query = SQLSelect("Birthday");
  while ($data = $query->fetch(PDO::FETCH_ASSOC)) {
    $data['month'] = str_pad($data['month'], 2, "0", STR_PAD_LEFT);
    $data['day'] = str_pad($data['day'], 2, "0", STR_PAD_LEFT);
    if (date("mj") == "{$data['month']}{$data['day']}") {
      array_push($notice, ["Happy birthday {$data['user_name']}!", "/file-uploader/birthday-cake.gif"]);
    }
  }
  if (isset($_GET['birthday'])) {
    unset($_GET['birthday']);
    $birthday_url = GetFullURL();
    $_GET['birthday'] = "";
  } else {
    $_GET['birthday'] = "";
    $birthday_url = GetFullURL();
    unset($_GET['birthday']);
  }
  $query = SQLSelect("Users", "*", "name='{$file_tag}'");
  $is_removable = FALSE;
  if ($query->rowCount() == 0) {
    $is_removable = TRUE;
  } else if ($file_tag == $user_name) {
    $is_removable = TRUE;
  } else if ($file_tag == "public") {
    $is_removable = TRUE;
  } else if ($Admin) {
    $is_removable = TRUE;
  }
  if ($is_removable) {
    $init_set .= "is_removable_tag = true;";
  } else {
    $init_set .= "is_removable_tag = false;";
  }
  $init_exec .= "show_background(\"{$background_url}\")\n";
  for ($i = 0; $i < count($msg); $i++) {
    $init_exec .= "add_message(\"{$msg[$i]}\");\n";
  }
  for ($i = 0; $i < count($warn); $i++) {
    $init_exec .= "add_warning(\"{$warn[$i]}\");\n";
  }
  for ($i = 0; $i < count($notice); $i++) {
    $init_exec .= "add_notice(\"{$notice[$i][0]}\", \"{$notice[$i][1]}\", 0);\n";
  }
?>
