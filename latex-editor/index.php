<?php
  include(dirname(__FILE__)."/../devel/initialize.php");
  include(dirname(__FILE__)."/latex-editor.php");
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <title>LaTeX Editor</title>
    <link rel="stylesheet" href="/latex-editor/latex-editor.css"></link>
    <script type="text/javascript" src="/latex-editor/latex-editor.js"></script>
    <script type="text/javascript">
      <?php echo $js_init; ?>
      window.onload = function() {
        <?php echo $js_onload; ?>
        document.getElementById("editor-container").style = "background-image: url('<?php echo $background_url; ?>');";
      }
    </script>
  </head>
  <body>
    <div id="side-bar">
      <div id="mode-container">
        <img id="mode"></img>
      </div>
      <div id="files">
        <?php show_files(); ?>
      </div>
    </div>
    <div id="top-bar">
      <iframe id="uploader" src="<?php echo "/file-uploader/?code={$code}&embed&reload=0"; ?>"></iframe>
    </div>
    <div id="editor-container">
      <textarea id="editor"></textarea>
    </div>
  </body>
</html>
