let editor = {};
editor.code = "";
editor.DOM = null;
editor.buffer = false;
editor.uploader = {};
editor.uploader.DOM = false;
editor.mode = {};
editor.mode.text = "standby";
editor.mode.DOM = null;
editor.mode.color = {};
editor.mode.color.standby = "#00ff00";
editor.mode.color.error = "#aa00ff";
editor.mode.color.generating = "#ff0000";
editor.top_level = "";
editor.target_file = "";
editor.form_data = null;
editor.files = {};
editor.files.DOM = null;
editor.popup = {};
editor.popup.config = "status=no,location=no,toolbar=no,menubar=no,width=600,height=600";
editor.popup.text = {};
editor.popup.text.DOM = null;
editor.popup.text.file_name = "";
editor.popup.text.URL = "";
editor.popup.pdf = {};
editor.popup.pdf.DOM = null;
editor.popup.pdf.file_name = "";
editor.popup.pdf.URL = "";
function editor_reset() {
  if (confirm("Are you sure want to reset the editor?")) {
    editor.mode.text = "standby"
    editor.mode.DOM.style["background-color"] = editor.mode.color.standby;
  }
}
function mode_change(e=null) {
  if (editor.mode.text == "standby") {
    editor.mode.text = "standby";
    editor.mode.DOM.src = "/file-uploader/generate.png";
    editor.mode.DOM.style["background-color"] = editor.mode.color.standby;
    if (editor.popup.text.DOM == null || editor.popup.text.DOM.closed) {
      editor.popup.text.DOM = window.open(editor.popup.text.URL, "text", editor.popup.config);
    }
    if (editor.popup.pdf.DOM == null || editor.popup.pdf.DOM.closed) {
      editor.popup.pdf.DOM = window.open(editor.popup.pdf.URL, "pdf", editor.popup.config);
    }
  }
}
function file_get(file_name="") {
  if (!file_name) {
    file_name = prompt("Enter new file name");
  }
  if (file_name) {
    editor.target_file = file_name; 
    fetch(`/file-uploader/SentFiles/${editor.code}/${editor.target_file}`)
    .then((data) => {
      if (data.status == 200) {
        return data.text();
      } else {
        return "";
      }
    })
    .then((text) => {
      editor.DOM.value = text;
    })
    .catch((e) => {
      console.log(e)
    });
  }
}
function file_update() {
  if (editor.mode.text == "standby") {
    editor.mode.text = "generating";
    editor.mode.DOM.style["background-color"] = editor.mode.color.generating;
    if (editor.popup.text.DOM == null || editor.popup.text.DOM.closed) {
      editor.popup.text.DOM = window.open(editor.popup.text.URL, "text", editor.popup.config);
    }
    if (editor.popup.pdf.DOM == null || editor.popup.pdf.DOM.closed) {
      editor.popup.pdf.DOM = window.open(editor.popup.pdf.URL, "pdf", editor.popup.config);
    }
    editor.form_data = new FormData();
    editor.form_data.set("file_name", editor.target_file);
    editor.form_data.set("file_data", editor.DOM.value);
    fetch(`/latex-editor/?code=${editor.code}&top_level=${editor.top_level}`, {method:"POST", body:editor.form_data})
    .then((data) => data.text())
    .then((text) => {
      editor.files.DOM.innerHTML = text;
      editor.popup.text.DOM.window.location.href = editor.popup.text.DOM.window.location.href;
      editor.popup.pdf.DOM.window.location.href = editor.popup.pdf.DOM.window.location.href.split('&scroll')[0] + `&scroll=${editor.popup.pdf.DOM.window.scrollY}`;
      editor.mode.text = "standby";
      if (text.includes("error")) {
        editor.mode.DOM.style["background-color"] = editor.mode.color.error;
      } else {
        editor.mode.DOM.style["background-color"] = editor.mode.color.standby;
      }
      if (editor.buffer) {
        editor.buffer = false;
        file_update();
      }
    })
    .catch((e) => {
      console.log(e);
      editor.mode.text = "standby";
      file_update();
    });
  } else {
    editor.buffer = true;
  }
}
function editor_close() {
  if (editor.popup.text.DOM != null && editor.popup.text.DOM.closed == false) {
    editor.popup.text.DOM.window.close();
  }
  if (editor.popup.pdf.DOM != null && editor.popup.pdf.DOM.closed == false) {
    editor.popup.pdf.DOM.window.close();
  }
}
window.addEventListener("load", function() {
  editor.DOM = document.getElementById("editor");
  editor.DOM.addEventListener("input", file_update);
  editor.uploader.DOM = document.getElementById("uploader");
  editor.uploader.DOM.addEventListener("load", file_update);
  editor.mode.DOM = document.getElementById("mode");
  editor.mode.DOM.addEventListener("click", mode_change);
  editor.mode.DOM.src = "/file-uploader/generate.png";
  editor.mode.DOM.style["background-color"] = editor.mode.color.standby;
  editor.files.DOM = document.getElementById("files");
  file_get(editor.top_level);
  editor.popup.text.file_name = editor.top_level.replace(".tex", ".out.txt");
  editor.popup.text.URL = `/file-uploader/SentFiles/${editor.code}/${editor.popup.text.file_name}`;
  editor.popup.pdf.file_name = editor.top_level.replace(".tex", ".pdf");
  editor.popup.pdf.URL = `/latex-editor/pdf-viewer.php?code=${editor.code}&name=${editor.popup.pdf.file_name}`;
  window.addEventListener("beforeunload", editor_close);
});
