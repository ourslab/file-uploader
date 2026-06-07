<?php
  include(dirname(__FILE__)."/../devel/initialize.php");
  include(dirname(__FILE__)."/slack-archiver.php");
?>
<!DOCTYPE html>
<html>
  <head>
    <title>Slack Archiver</title>
    <link rel="stylesheet" href="/slack-archiver/slack-archiver.css">
    <script type="text/javascript">
      function change_channel(channel_name) {
        location.href = (location.href.split("?")[0]).split("#")[0]+"?channel="+channel_name;
      }
      function show_background(background_url) {
        document.getElementById("background").style = "background-image: url("+background_url+");";
      }
      <?php echo $init_set; ?>
      window.onload = function() {
        <?php echo $init_exec; ?>
      }
    </script>
  </head>
  <body>
    <div id="sidebar">
      <?php channels_list(); ?>
    </div>
    <div id="background">
    </div>
    <div id="body">
      <?php messages_list(); ?>
    </div>
  </body>
</html>
