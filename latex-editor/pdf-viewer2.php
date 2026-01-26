<?php
  include(dirname(__FILE__)."/../devel/initialize.php");
  if (!empty($_GET['page'])) {
    $page = intval($_GET['page']);
    setcookie("page", $page);
  } else if (!empty($_COOKIE['page'])) {
    $page = intval($_COOKIE['page']);
  } else {
    $page = 1;
  }
  $code = "";
  $name = "";
  if (!empty($_GET['code']) && !empty($_GET['name'])) {
    $code = safe_str($_GET['code']);
    $name = safe_str($_GET['name']);
  }
  function url($page) {
    global $code, $name;
    $url_base = get_url();
    return "{$url_base}?code={$code}&name={$name}&page={$page}";
  }
  $pdf_url = "/file-uploader/SentFiles/{$code}/{$name}";
?>
<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8" />
    <title>PDF.js</title>
    <script type="module" src="pdfjs/build/pdf.mjs"></script>
    <script type="module">
      let busy = false;
      let scrollCount = 0;
      let pageNumber = <?php echo $page; ?>;
      pdfjsLib.GlobalWorkerOptions.workerSrc = "pdfjs/build/pdf.worker.mjs";
      const loadingTask = pdfjsLib.getDocument({ 
        url: "<?php echo $pdf_url; ?>",
        cMapUrl: 'https://cdn.jsdelivr.net/npm/pdfjs-dist@latest/cmaps/',
        cMapPacked: true
      });
      const pdf = await loadingTask.promise;
      const outputScale = window.devicePixelRatio || 1;
      const transform = (outputScale !== 1)? [outputScale, 0, 0, outputScale, 0, 0] : null;
      const scale = 3.0;
      const canvas = document.createElement("canvas");
      canvas.width = 595 * scale;
      canvas.height = 842 * scale;
      canvas.style.width = "100%";
      canvas.style.height = "auto";
      const canvasContext = canvas.getContext("2d");
      document.body.style.backgroundColor = "black";  
      document.body.style.width = "100%";  
      document.body.style.padding = "0px";
      document.body.style.margin = "0px auto";
      document.body.appendChild(canvas);
      async function printPage(n) {
        try {
          scrollCount = 0;
          if (!busy) {
            busy = true;
            pageNumber = n;
            document.cookie = `page=${n}`;
            const page = await pdf.getPage(n);
            const viewport = page.getViewport({scale});
            page.render({canvasContext, transform, viewport});
            busy = false;
          }
        } catch (e) {
          busy = false;
        }
      }
      printPage(pageNumber);
      window.addEventListener("keydown", function (e) {
        const scroll_top = window.scrollY;
        const scroll_bottom = window.scrollY + window.innerHeight;
        const page_height = window.document.documentElement.scrollHeight;
        if (scroll_bottom >= page_height - 1) {
          if (e.key == "ArrowDown" || e.key == "ArrowRight") {
            printPage(pageNumber + 1);
          }
        }
        if (scroll_top < 1) {
          if (e.key == "ArrowUp" || e.key == "ArrowLeft") {
            if (pageNumber > 1) {
              printPage(pageNumber - 1);
            } else {
              alert("これ以上前のページはありません");
            }
          }
        }
      });
      window.addEventListener("wheel", function (e) {
        const scroll_top = window.scrollY;
        const scroll_bottom = window.scrollY + window.innerHeight;
        const page_height = window.document.documentElement.scrollHeight;
        if (scroll_bottom >= page_height - 1) {
          if (e.deltaY > 0) {
            scrollCount = scrollCount + 1;
            if (scrollCount > 2) {
              printPage(pageNumber + 1);
            }
          }
        }
        if (scroll_top < 1) {
          if (e.deltaY < 0) {
            if (pageNumber > 1) {
              scrollCount = scrollCount - 1;
              if (scrollCount < -2) {
                printPage(pageNumber - 1);
              }
            } else {
              alert("これ以上前のページはありません");
            }
          }
        }
      });
    </script>
  </head>
  <body>
  </body>
</html>
