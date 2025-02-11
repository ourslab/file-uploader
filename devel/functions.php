<?php
  function get_full_url() {
    $full_url = get_url()."?";
    foreach($_GET as $key => $val){
      $full_url .= "{$key}={$val}&";
    }
    return $full_url;
  }
  function get_url() {
    global $https_only;
    $request_dir = explode('?', "{$_SERVER['REQUEST_URI']}")[0];
    if ($https_only) {
       return "https://{$_SERVER['HTTP_HOST']}{$request_dir}";
    }
    return "http://{$_SERVER['HTTP_HOST']}{$request_dir}";
  }
  function get_temp_full_url($get_param_name, $get_param_temp_value=null) {
    if (isset($_GET[$get_param_name])) {
      $get_param_current_value = $_GET[$get_param_name];
    } else {
      $get_param_current_value = null;
    }
    if (is_null($get_param_temp_value)) {
      unset($_GET[$get_param_name]);
    } else {
      $_GET[$get_param_name] = $get_param_temp_value;
    }
    $temp_full_url = get_full_url();
    if (is_null($get_param_current_value)) {
      if (isset($_GET[$get_param_name])) {
        unset($_GET[$get_param_name]);
      }
    } else {
      $_GET[$get_param_name] = $get_param_current_value;
    }
    return $temp_full_url; 
  }
  function redirect($URL, $status){
    header("Location: $URL", true, $status);
    exit;
  }
  function make_code(){
    $code = "";
    $cand = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    for($i = 0; $i < 9; $i++){
      if($i == 3 || $i == 6 || $i == 9){
        $code .= "-";
      }
      $code .= substr($cand, mt_rand(0, 61), 1);
    }
    return $code;
  }
  function safe_str($str){
    $rep = unsafe_str($str);
    $rep = str_replace(' ', '_', $rep);
    $rep = str_replace('&', '&amp;', $rep);
    $rep = str_replace('"', '&quot;', $rep);
    $rep = str_replace('\'', '&apos;', $rep);
    $rep = str_replace('\\', '&#092;', $rep);
    $rep = str_replace('<', '&lt;', $rep);
    $rep = str_replace('>', '&gt;', $rep);
    return $rep;
  }
  function unsafe_str($str){
    $rep = str_replace('&#092;', '\\', $str);
    $rep = str_replace('&quot;', '"', $rep);
    $rep = str_replace('&apos;', '\'', $rep);
    $rep = str_replace('&lt;', '<', $rep);
    $rep = str_replace('&gt;', '>', $rep);
    $rep = str_replace('&amp;', '&', $rep);
    return $rep;
  }
  function error_page($title="", $init_set="", $init_exec="", $body="") {
    if (empty($title)) {
      $title = "Unknown error occurred";
    }
    if (empty($init_exec)) {
      $init_exec = "alert('Unknown error occurred.');";
      $init_exec .= "setTimeout(() => {location.href = '/';}, 1000);";
    }
    echo "<!DOCTYPE html>";
    echo "<html lang=\"ja\">";
    echo "<head>";
    echo "<title>{$title}</title>";
    echo "<script type=\"text/javascript\">{$init_set}; window.onload = function() { {$init_exec} }</script>";
    echo "</head>";
    echo "<body>{$body}</body>";
    echo "</html>";
    exit;
  }
  function basic_auth($login_hash) {
    header("WWW-Authenticate: Basic realm=\"momiji\"");
    header("HTTP/1.0 401 Unauthorized");
    header("Login-Hash: {$login_hash}");
    unauthorized();
  }
  function unauthorized() {
    $title = "Authentication failed";
    $js_onload = "alert('Authentication failed.');";
    $js_onload .= "setTimeout(() => {location.href = '/';}, 1000);";
    error_page($title, "", $js_onload, "Authentication failed.");
  }
  function tex_compile($dst_dir, $src, $code, $tag, $remove_src=true) {
    $aux = str_replace(".tex", ".aux", $src);
    $dvi = str_replace(".tex", ".dvi", $src);
    $log = str_replace(".tex", ".log", $src);
    $pdf = str_replace(".tex", ".pdf", $src);
    $out = str_replace(".tex", ".out.txt", $src);
    exec("/home/nfs1/tex2pdf.sh $src", $output, $status);
    if ($remove_src) {
      sql_update("SentFiles", "stt=0", "code='{$code}'");
    }
    exec("rm -rf $aux $dvi $log");
    if (!empty($dst_dir)) {
      exec("cp {$out} /home/nfs1/Nginx/file-uploader/{$dst_dir}/");
      exec("cp {$pdf} /home/nfs1/Nginx/file-uploader/{$dst_dir}/");
    }
    if ($status == 0) {
      $query = sql_select("SentFiles", "*", "name='{$out}'");
      if ($query->rowCount() == 0) {
        sql_insert("SentFiles", "id,name,path,code,tag,stt", "0,'{$out}','{$dst_dir}/{$out}','{$code}','{$tag}',0");
      }
      $query = sql_select("SentFiles", "*", "name='{$pdf}'");
      if ($query->rowCount() == 0) {
        sql_insert("SentFiles", "id,name,path,code,tag,stt", "0,'{$pdf}','{$dst_dir}/{$pdf}','{$code}','{$tag}',1");
      }
    } else {
      $query = sql_select("SentFiles", "*", "name='{$out}'");
      if ($query->rowCount() == 0) {
        sql_insert("SentFiles", "id,name,path,code,tag,stt", "0,'{$out}','{$dst_dir}/{$out}','{$code}','{$tag}',1");
      }
    }
    unset($output);
    exec("grep -e \"No pages of output\" -e \"Emergency stop\" -e \"pt too wide\" {$out}", $output);
    if (count($output)) {
      return 1;
    }
    return 0;
  }
  function tex_compile_recursive($dir, $code, $tag) {
    $contents = scandir("./");
    for ($a = 0; $a < count($contents); $a++) {
      if (is_file($contents[$a])) {
        $file_ext = explode('.', $contents[$a]);
        $file_ext = array_pop($file_ext);
        if($file_ext == "tex"){
          tex_compile($dir, $contents[$a], $code, $tag, FALSE);
        }
      } else if (is_dir($contents[$a])) {
        if ($contents[$a] != '.' && $contents[$a] != '..') {
          chdir("./{$contents[$a]}");
          tex_compile_recursive($dir, $code, $tag);
          chdir("../");
        }
      }
    }
  }
?>
