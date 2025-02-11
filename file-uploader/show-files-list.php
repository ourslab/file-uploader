<?php 
  function is_removable_file($file_tag) {
    global $user_name, $admin;
    $query = sql_select("Users", "*", "name='{$file_tag}'");
    if ($query->rowCount() == 0) {
      return TRUE;
    } else if ($file_tag == $user_name) {
      return TRUE;
    } else if ($file_tag == "public") {
      return TRUE;
    } else if ($admin) {
      return TRUE;
    }
    return FALSE;
  }
  function process_files_zip($zip_tag) {
    if (!empty($_POST['zip'])) {
      $zip_code = safe_str($_POST['zip']);
      $zip_name = "{$zip_code}.zip";
      $zip_path = "SentFiles/{$zip_code}/{$zip_name}";
      chdir("SentFiles/{$zip_code}");
      exec("rm -rf {$zip_name}");
      exec("zip {$zip_name} `ls | grep -v {$zip_name}`");
      chdir("../../");
      if (is_removable_file($zip_tag)) {
        sql_update("SentFiles", "stt=0", "code='{$zip_code}'");
      }
      $query = sql_select("SentFiles", "*", "code='{$zip_code}' and name='{$zip_name}'");
      if ($query->rowCount() == 0) {
        sql_insert("SentFiles", "id,name,path,code,tag,stt", "0,'$zip_name','$zip_path','$zip_code','$zip_tag',1");
      }
    }
  }
  function process_files_sent_tex($dst_dir, $code, $file_tag, $files_tex) {
    for ($a = 0; $a < count($files_tex); $a++) {
      tex_compile($dst_dir, $files_tex[$a], $code, $file_tag);
    }
  }
  function process_files_sent_pdf($dst_dir, $code, $file_tag, $files_pdf) {
    if (count($files_pdf) > 1) {
      $united_srcs = "";
      for ($a = 0; $a < count($files_pdf); $a++) {
        $pdf_resized = str_replace(".pdf", ".resized.pdf", $files_pdf[$a]);
        exec("pdfjam --paper a4paper --no-landscape --fitpaper false --outfile {$pdf_resized} {$files_pdf[$a]}");
        $united_srcs .= "{$pdf_resized} ";
      }
      $pdf_dst = "united_sorted_by_name.pdf";
      exec("pdfjam --paper a4paper --no-landscape --fitpaper false --outfile {$pdf_dst} \$(ls *.resized.pdf)");
      sql_insert("SentFiles", "id,name,path,code,tag,stt", "0,'{$pdf_dst}','{$dst_dir}/{$pdf_dst}','{$code}','{$file_tag}',1");
      $pdf_dst = "united_sorted_by_sent.pdf";
      exec("pdfjam --paper a4paper --no-landscape --fitpaper false --outfile {$pdf_dst} {$united_srcs}");
      sql_insert("SentFiles", "id,name,path,code,tag,stt", "0,'{$pdf_dst}','{$dst_dir}/{$pdf_dst}','{$code}','{$file_tag}',1");
      exec("rm -rf {$united_srcs}");
    }
  }
  function process_files_sent_zip($dst_dir, $code, $file_tag, $files_zip) {
    for ($a = 0; $a < count($files_zip); $a++) {
      mkdir("_temp", 0700);
      chdir("_temp");
      exec("cp ../{$files_zip[$a]} ./");
      exec("unzip {$files_zip[$a]}");
      tex_compile_recursive($dst_dir, $code, $file_tag);
      chdir("../");
      exec("rm -rf _temp");
    }
  }
  function process_files_sent_img($dst_dir, $code, $file_tag, $files_img) {
    for($a = 0; $a < count($files_img); $a++){
      $file_src = $files_img[$a];
      $file_ext = explode('.', $file_src);
      $file_ext = array_pop($file_ext);

       $file_dst = str_replace($file_ext, "eps", $file_src);
       exec("convert \"{$file_src}\" \"{$file_dst}\"");
       sql_insert("SentFiles", "id,name,path,code,tag,stt", "0,'{$file_dst}','{$dst_dir}/{$file_dst}','{$code}','{$file_tag}',1");

      // $file_dst = str_replace($file_ext, "resized.jpg", $file_src);
      // exec("convert \"{$file_src}\" -resize 1024x \"{$file_dst}\"");
      // sql_insert("SentFiles", "id,name,path,code,tag,stt", "0,'{$file_dst}','{$dst_dir}/{$file_dst}','{$code}','{$file_tag}',1");

      // $file_dst = str_replace($file_ext, "auto-level.png", $file_src);
      // exec("convert \"{$file_src}\" -auto-level \"{$file_dst}\"");
      // sql_insert("SentFiles", "id,name,path,code,tag,stt", "0,'{$file_dst}','{$dst_dir}/{$file_dst}','{$code}','{$file_tag}',1");

      // $file_dst = str_replace($file_ext, "negate.png", $file_src);
      // exec("convert \"{$file_src}\" -negate \"{$file_dst}\"");
      // sql_insert("SentFiles", "id,name,path,code,tag,stt", "0,'{$file_dst}','{$dst_dir}/{$file_dst}','{$code}','{$file_tag}',1");

      // $file_dst = str_replace($file_ext, "color-convert.png", $file_src);
      // exec("convert -fuzz 10% -fill \"#33ff33\" -opaque \"#00ff00\" \"{$file_src}\" \"{$file_dst}\"");
      // exec("convert -fuzz 10% -fill \"#0378ed\" -opaque \"#0000ff\" \"{$file_dst}\" \"{$file_dst}\"");
      // sql_insert("SentFiles", "id,name,path,code,tag,stt", "0,'{$file_dst}','{$dst_dir}/{$file_dst}','{$code}','{$file_tag}',1");
    }
    if (count($files_img) > 1) {
      $united_srcs = "";
      for ($a = 0; $a < count($files_img); $a++) {
        exec("convert {$files_img[$a]} -density 300 -units pixelsperinch -resize 3840x {$files_img[$a]}.resized.pdf");
        $united_srcs .= "{$files_img[$a]}.resized.pdf ";
      }
      $pdf_dst = "{$files_img[0]}.pdf";
      exec("convert {$united_srcs} {$pdf_dst}");
      sql_insert("SentFiles", "id,name,path,code,tag,stt", "0,'{$pdf_dst}','{$dst_dir}/{$pdf_dst}','{$code}','{$file_tag}',1");
      exec("rm -rf {$united_srcs}");
    }
  }
  function process_files_sent($file_tag, $code, $generate_enabled) {
    if (is_null($code)) {
      while (1) {
        $code = make_code();
        $query = sql_select("SentFiles", "id", "code='$code'");
        if ($query->rowCount() == 0) {
          break;
        }
      }
    } else {
      $query = sql_select("SentFiles", "tag", "code='{$code}'");
      if ($data = sql_data($query)) {
        if (is_removable_file($data['tag']) == false) {
          return null;
        }
      }
    }
    if (!empty($_FILES['files'])) {
      $files_tex = array();
      $files_pdf = array();
      $files_zip = array();
      $files_img = array();
      $dst_dir = "SentFiles/{$code}";
      if (is_dir($dst_dir) == false) {
        mkdir($dst_dir, 0700, true);
      }
      $files_count = 0;
      for ($a = 0; $a < count($_FILES['files']['name']); $a++) {
        $temp_path = $_FILES['files']['tmp_name'][$a];
        $file_name = safe_str($_FILES['files']['name'][$a]);
        if (is_uploaded_file($temp_path) == false) {
          continue;
        } else {
          $files_count += 1;
        }
        $file_ext = explode('.', $file_name);
        $file_ext = array_pop($file_ext);
        if ($file_ext == "php") {
          $file_name .= ".txt";
        }
        $file_path = "{$dst_dir}/{$file_name}";
        move_uploaded_file($temp_path, $file_path);
        $query = sql_select("SentFiles", "*", "name='{$file_name}' and code='{$code}'");
        if ($query->rowCount() == 0) {
          sql_insert("SentFiles", "id,name,path,code,tag,stt", "0,'$file_name','$file_path','$code','$file_tag',1");
        }
        global $img_exts;
        if ($generate_enabled == false) {
          continue;
        } else if ($file_ext == "tex") {
          array_push($files_tex, $file_name);
        } else if ($file_ext == "pdf") {
          array_push($files_pdf, $file_name);
        } else if ($file_ext == "zip") {
          array_push($files_zip, $file_name);
        } else if (in_array($file_ext, $img_exts)) {
          array_push($files_img, $file_name);
        }
      }
      chdir($dst_dir);
      process_files_sent_tex($dst_dir, $code, $file_tag, $files_tex);
      process_files_sent_pdf($dst_dir, $code, $file_tag, $files_pdf);
      process_files_sent_zip($dst_dir, $code, $file_tag, $files_zip);
      process_files_sent_img($dst_dir, $code, $file_tag, $files_img);
      chdir("../../");
      if ($files_count == 0) {
        global $warn;
        array_push($warn, "Some error occurred while submitting your file.");
      } else if ($files_count == 1) {
        global $msg;
        array_push($msg, "Your file has been successfully submitted.");
      } else {
        global $msg;
        array_push($msg, "Your files have been successfully submitted.");
      }
    }
  }
  function process_files_revive($file_tag) {
    if (!empty($_POST['revive'])) {
      $file_id = intval($_POST['revive']);
      $query = sql_select("SentFiles", "id,tag", "id={$file_id}");
      if ($data = sql_data($query)) {
        $file_tag = $data['tag'];
        if (is_removable_file($file_tag)) {
          sql_update("SentFiles", "stt=1", "id={$file_id}");
          global $msg;
          array_push($msg, "The file is revived.");
        } else {
          global $warn;
          array_push($warn, "This operation is not permitted.");
        }
      }
    }
  }
  function process_files_remove($file_tag) {
    if (!empty($_POST['remove'])) {
      $file_id = intval($_POST['remove']);
      $query = sql_select("SentFiles", "*", "id={$file_id}");
      if ($data = sql_data($query)) {
        if (is_removable_file($file_tag)) {
          if (intval($data['stt']) == 0) {
            sql_delete("SentFiles", "id={$file_id}");
            unlink($data['path']);
            global $msg;
            array_push($msg, "The file is removed.");
          } else {
            sql_update("SentFiles", "stt=0", "id={$file_id}");
            global $msg;
            array_push($msg, "The file is not displayed.");
          }
        } else {
          global $warn;
          array_push($warn, "This operation is not permitted.");
        }
      }
    }
    if (isset($_POST['all-remove'])) {
      if (is_removable_file($file_tag)) {
        $query = sql_select("SentFiles", "*", "stt=0 and tag='{$file_tag}'");
        $removed_count = $query->rowCount();
        if ($removed_count > 0) {
          while ($data = sql_data($query)) {
            $file_id = intval($data['id']);
            sql_delete("SentFiles", "id={$file_id}");
            unlink($data['path']);
          }
          if ($removed_count == 1) {
            global $msg;
            array_push($msg, "Your hidden file is removed.");
          } else {
            global $msg;
            array_push($msg, "Your hidden {$removed_count} files are removed.");
          }
        } else {
          global $warn;
          array_push($warn, "Your hidden file is not found.");
        }
      } else {
        global $warn;
        array_push($warn, "You must enter your own page.");
      }
    }
  }
  function show_page_number($files_per_page, $file_tag, $files_count) {
    $files_list_page = 1;
    if (!empty($_GET['page'])) {
      $files_list_page = intval($_GET['page']);
    }
    $files_count_start = $files_per_page * ($files_list_page - 1);
    $files_count_end = $files_count_start + $files_per_page;
    if ($files_count_start < 0 || $files_count < $files_count_start) {
      $files_list_page = 1;
      $files_count_start = 0;
      $files_count_end = $files_per_page;
    }
    $files_list_prev_page = $files_list_next_page = $files_list_page;
    $files_list_total_pages = ceil($files_count / $files_per_page);
    if ($files_list_page < $files_list_total_pages) {
      $files_list_next_page = $files_list_page + 1;
    }
    if ($files_list_page > 1) {
      $files_list_prev_page = $files_list_page - 1;
    }
    echo "<p>{$files_list_page}/{$files_list_total_pages}</p>";
    echo "<a class=\"files-list-page-jump\" href=\"".get_temp_full_url('page', $files_list_prev_page)."\">&lt;&lt;&lt; Previous</a>";
    echo "&emsp;";
    echo "<a class=\"files-list-page-jump\" href=\"".get_temp_full_url('page', $files_list_next_page)."\">&gt;&gt;&gt; Next</a>";
    return [$files_count_start, $files_count_end];
  }
  function show_files_list($files_per_page, $show_all, $file_tag) {
    if ($show_all) {
      $query = sql_select("SentFiles", "*", "tag='{$file_tag}'", "id DESC");
    } else {
      $query = sql_select("SentFiles", "*", "stt=1 and tag='{$file_tag}'", "id DESC");
    }
    $files_count = $query->rowCount();
    [$files_count_start, $files_count_end] = show_page_number($files_per_page, $file_tag, $files_count);
    for ($a = 0; $data = sql_data($query); $a++) {
      if ($a >= $files_count_start && $a < $files_count_end) {
        $file_ext = explode('.', $data['name']);
        $file_ext = array_pop($file_ext);
        echo "<ul>";
        echo "<li class=\"files-list-id\">{$data['id']}</li>";
        echo "<li class=\"files-list-link\"><a href=\"/file-uploader/{$data['path']}\" target=\"_blank\">{$data['name']}</a></li>";
        global $img_exts;
        if (in_array($file_ext, $img_exts)) {
          echo "<li class=\"files-list-command\"><a onclick=\"set_background('/file-uploader/{$data['path']}')\">Set back</a></li>";
        } else if ($file_ext == "tex") {
          echo "<li class=\"files-list-command\"><a href=\"/latex-editor/?code={$data['code']}&top_level={$data['name']}\" target=\"_blank\">Editor</a></li>";
        } else {
          echo "<li class=\"files-list-command\"></li>";
        }
        if (intval($data['stt']) == 1) {
          echo "<li class=\"files-list-command\">";
          echo "<a onclick=\"file_remove({$data['id']})\">Hide</a>";
          echo "<a onclick=\"zip('{$data['code']}')\">(Pack)</a>";
          echo "</li>";
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
?>
