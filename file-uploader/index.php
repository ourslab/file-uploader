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
      <?php echo $init_set; ?>
      window.onload = function() {
        reload_event = setTimeout(()=>{
          location.href = "<?php echo GetFullURL(); ?>";
        }, 60000);
        document.body.ondragover = function(e) {
          e.preventDefault();
          document.getElementById("file-uploader-screen").style["display"] = "block";
        }
        document.body.ondrop = file_uploader_screen; 
        document.body.onmouseover = function() {
          document.getElementById("file-uploader-screen").style["display"] = "none";
        }
        <?php echo $init_exec; ?>
      }
    </script>
  </head>
  <body>
    <div id="info"><ul></ul></div>
    <div id="notice"><p></p></div>
    <div id="file-uploader-screen">
      <label class="overlap">
        <img src="upload-file.png">
        <input type="file" id="file-uploader-screen-selector" class="contents" name="files[]" multiple onchange="file_send()">
      </label>
    </div>
    <div id="side-bar">
      <div class="logo">
        <a href="/file-uploader/">
          <img src="/file-uploader/logo.png">
        </a>
      </div>
      <div class="tags-list">
        <?php tags_list(); ?>
      </div>
    </div>
    <div id="top-bar">
      <ul>
        <li>
          <label class="overlap">
            <form id="file-uploader-form" method="POST" enctype="multipart/form-data" action="<?php echo GetFullURL(); ?>">
              <img src="upload-file.png">
              <input type="file" id="file-uploader-selector" class="contents" name="files[]" multiple onchange="file_send()">
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
          <a onclick="reset_background()">
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
      <?php show_body(); ?>
    </div>
    <form id="user-name-form" method="POST" action="<?php echo GetFullURL(); ?>">
      <input id="user-name" type="hidden" name="user-name">
    </form>
    <form id="login-form" method="POST" action="<?php echo GetFullURL(); ?>">
      <input id="login" type="hidden" name="login">
    </form>
    <form id="logout-form" method="POST" action="<?php echo GetFullURL(); ?>">
      <input id="logout" type="hidden" name="logout">
    </form>
    <form id="file-remove-form" method="POST" action="<?php echo GetFullURL(); ?>">
      <input id="file-remove-id" type="hidden" name="remove">
    </form>
    <form id="file-all-remove-form" method="POST" action="<?php echo GetFullURL(); ?>">
      <input id="file-all-remove-id" type="hidden" name="all-remove">
    </form>
    <form id="file-revive-form" method="POST" action="<?php echo GetFullURL(); ?>">
      <input id="file-revive-id" type="hidden" name="revive">
    </form>
    <form id="background-set-form" method="POST" action="<?php echo GetFullURL(); ?>">
      <input id="background-set-url" type="hidden" name="background">
    </form>
    <form id="background-reset-form" method="POST" action="<?php echo GetFullURL(); ?>">
      <input id="background-reset-flag" type="hidden" name="background-reset">
    </form>
    <form id="link-form" method="POST" action="<?php echo GetFullURL(); ?>">
      <input id="link-name" type="hidden" name="link-name">
      <input id="link-url" type="hidden" name="link-url">
    </form>
    <form id="dellink-form" method="POST" action="<?php echo GetFullURL(); ?>">
      <input id="dellink-id" type="hidden" name="dellink-id">
    </form>
    <form id="birthday-form" method="POST" action="<?php echo GetFullURL(); ?>">
      <input id="birthday-name" type="hidden" name="birthday-name" value="">
      <input id="birthday-year" type="hidden" name="birthday-year" value="0">
      <input id="birthday-month" type="hidden" name="birthday-month" value="0">
      <input id="birthday-day" type="hidden" name="birthday-day" value="0">
    </form>
  </body>
</html>
