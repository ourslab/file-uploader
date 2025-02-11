<?php
  function GetFullURL() {
    global $URL;
    $FullURL = $URL."?";
    foreach($_GET as $key => $val){
      $FullURL .= "{$key}={$val}&";
    }
    return $FullURL;
  }
  function Redirect($URL, $status){
    header("Location: $URL", true, $status);
    exit;
  }
  function Cookie($key, $val){
    setcookie($key, $val, 0, '/', $_SERVER['HTTP_HOST'], false, true);
  }
  function MakeCode(){
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
  function SafeStr($str){
    $rep = UnsafeStr($str);
    $rep = str_replace(' ', '_', $rep);
    $rep = str_replace('&', '&amp;', $rep);
    $rep = str_replace('"', '&quot;', $rep);
    $rep = str_replace('\'', '&apos;', $rep);
    $rep = str_replace('\\', '&#092;', $rep);
    $rep = str_replace('<', '&lt;', $rep);
    $rep = str_replace('>', '&gt;', $rep);
    return $rep;
  }
  function UnsafeStr($str){
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
  function unauthorized() {
    $title = "Authentication failed";
    $init_exec = "alert('Authentication failed.');";
    $init_exec .= "setTimeout(() => {location.href = '/';}, 1000);";
    error_page($title, "", $init_exec, "Authentication failed.");
  }
  function tex_compile($dir, $src, $code, $tag, $remove_src=TRUE) {
    $aux = str_replace(".tex", ".aux", $src);
    $dvi = str_replace(".tex", ".dvi", $src);
    $log = str_replace(".tex", ".log", $src);
    $pdf = str_replace(".tex", ".pdf", $src);
    $out = str_replace(".tex", ".out.txt", $src);
    exec("/home/nfs1/tex2pdf.sh $src", $output, $ret);
    if ($remove_src) {
      SQLUpdate("SentFiles", "stt=0", "code='{$code}'");
    }
    exec("rm -rf $aux $dvi $log");
    if ($ret == 0) {
      exec("cp {$out} /home/nfs1/Nginx/file-uploader/{$dir}/");
      SQLInsert("SentFiles", "id,name,path,code,tag,stt", "0,'$out','$dir/$out','$code','$tag',0");
      exec("cp {$pdf} /home/nfs1/Nginx/file-uploader/{$dir}/");
      SQLInsert("SentFiles", "id,name,path,code,tag,stt", "0,'$pdf','$dir/$pdf','$code','$tag',1");
    } else {
      exec("cp {$out} /home/nfs1/Nginx/file-uploader/{$dir}/");
      SQLInsert("SentFiles", "id,name,path,code,tag,stt", "0,'$out','$dir/$out','$code','$tag',1");
    }
  }
  function tex_compile_recursive($dir, $code, $tag) {
    $contents = scandir("./");
    for ($i = 0; $i < count($contents); $i++) {
      if (is_file($contents[$i])) {
        $ary = explode('.', $contents[$i]);
        if($ary[count($ary) - 1] == "tex"){
          tex_compile($dir, $contents[$i], $code, $tag, FALSE);
        }
      } else if (is_dir($contents[$i])) {
        if ($contents[$i] != '.' && $contents[$i] != '..') {
          chdir("./{$contents[$i]}");
          tex_compile_recursive($dir, $code, $tag);
          chdir("../");
        }
      }
    }
  }
?>
