<?php
  include(dirname(__FILE__)."/../devel/initialize.php");
  if (!empty($_GET['scroll'])) {
    $scroll = intval($_GET['scroll']);
  } else {
    $scroll = 0;
  }
  function show_pdf() {
    if (!empty($_GET['code']) && !empty($_GET['name'])) {
      $code = safe_str($_GET['code']);
      $name = safe_str($_GET['name']);
      echo "<div style=\"width:97vw;height:10000px;overflow:auto;margin:auto;\">";
      echo "<iframe src=\"/file-uploader/SentFiles/{$code}/{$name}\" style=\"width:99%;height:99%;margin:0px;padding:0px;\"></iframe>";
      echo "</div>";
    }
  }
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <title>PDF Viewer</title>
    <script type="text/javascript">
      window.onload = function() {
        window.scrollTo(0, <?php echo $scroll; ?>);
      }
    </script>
  </head>
  <body style="margin:0px;padding:0px;background-color:#000000;">
    <?php show_pdf(); ?>
  </body>
</html>
