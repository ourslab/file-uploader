<?php
  include(dirname(__FILE__)."/../devel/initialize.php");
  include(dirname(__FILE__)."/file-uploader.php");
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <title>File uploader</title>
    <link rel="icon" href="/favicon.ico">
    <link rel="stylesheet" href="/file-uploader/file-uploader.css">
    <script type="text/javascript" src="/file-uploader/file-uploader.js"></script>
    <script type="text/javascript">
      <?php echo $js_init; ?>
      window.onload = function() {
        <?php echo $js_onload; ?>
      }
    </script>
  </head>
  <body>
    <div id="info"><ul></ul></div>
    <div id="notice"><p></p></div>
    <div id="file-uploader-screen">
      <label class="overlap">
        <img src="/file-uploader/upload-file.png">
        <input type="file" id="file-uploader-screen-selector" class="contents" name="files[]" multiple>
      </label>
    </div>
    <div id="side-bar">
      <div class="logo">
        <a href="/file-uploader/">
          <img src="/file-uploader/logo.png">
        </a>
      </div>
      <div class="tags-list">
        <?php show_tags_list($file_tag); ?>
        <?php show_links_list(); ?>
      </div>
    </div>
    <div id="top-bar">
      <ul>
        <li>
          <label id="file-uploader-form-label" class="overlap">
            <form id="file-uploader-form" method="POST" enctype="multipart/form-data" action="<?php echo get_full_url(); ?>">
              <img src="/file-uploader/upload-file.png">
              <input type="file" id="file-uploader-selector" class="contents" name="files[]" multiple>
            </form>
          </label>
        </li>
        <li>
          <a href="<?php echo $show_mode_url; ?>">
            <img src="<?php echo $show_mode_img; ?>">
          </a>
        </li>
        <li>
          <a onclick="file_all_remove()">
            <img src="/file-uploader/all-remove.png">
          </a>
        <li>
          <a href="<?php echo $generate_mode_url; ?>">
            <img src="<?php echo $generate_mode_img; ?>">
          </a>
        </li>
        <li>
          <a onclick="set_background()">
            <img src="/file-uploader/set-background.png">
          </a>
        </li>
        <li>
          <a href="<?php echo $background_reset_url; ?>">
            <img src="/file-uploader/reset-background.png">
          </a>
        </li>
        <li>
          <a href="<?php echo $birthday_url; ?>">
            <img src="/file-uploader/birthday.png">
          </a>
        </li>
      </ul>
    </div>
    <div id="body">
      <?php show_body($files_per_page, $show_all, $file_tag); ?>
    </div>    
  </body>
</html>
