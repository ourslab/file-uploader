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

        // Image preview tooltip setup
        const tooltip = document.createElement('div');
        tooltip.id = 'image-preview-tooltip';
        const img = document.createElement('img');
        tooltip.appendChild(img);
        document.body.appendChild(tooltip);

        const fileLinks = document.querySelectorAll('.message-file-image');
        fileLinks.forEach(link => {
          link.addEventListener('mouseenter', function() {
            const url = this.getAttribute('data-preview-url');
            img.src = url;
            tooltip.classList.add('visible');
          });

          link.addEventListener('mousemove', function(e) {
            const tooltipWidth = tooltip.offsetWidth || 200;
            const tooltipHeight = tooltip.offsetHeight || 200;
            
            let x = e.clientX + 15;
            let y = e.clientY + 15;

            // Prevent going off-screen horizontally
            if (x + tooltipWidth > window.innerWidth) {
              x = e.clientX - tooltipWidth - 15;
            }
            // Prevent going off-screen vertically
            if (y + tooltipHeight > window.innerHeight) {
              y = e.clientY - tooltipHeight - 15;
            }

            tooltip.style.left = x + 'px';
            tooltip.style.top = y + 'px';
          });

          link.addEventListener('mouseleave', function() {
            tooltip.classList.remove('visible');
            img.src = '';
          });
        });
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
