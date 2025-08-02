let user_name = "Unknown";
let key_last = "Key_Q";
let background_url = "";
let is_removable_tag = true;
let reload_span = 60000;
let reload_event = null;
let notice_cancel = false;
let notice_event = null;
let notice_hidden = null;
function add_message(str) {
  let info = document.getElementById("info").children[0];
  info.innerHTML += "<li><p class=\"msg\">"+str+"</p></li>";
}
function add_warning(str) {
  let info = document.getElementById("info").children[0];
  info.innerHTML += "<li><p class=\"warn\">"+str+"</p></li>";
}
function add_notice(str, img, tried) {
  let color = ["rgb(250 50 100 / 0.7)", "rgb(250 250 50 / 0.7)"];
  let notice = document.getElementById("notice");
  let info = document.getElementById("info").children[0];
  notice.onclick = function() {
    notice.style["display"] = "none"; 
    notice_cancel = true;
  };
  if (tried == 0) {
    info.innerHTML += "<li><p class=\"notice\">"+str+"</p></li>";
  }
  if (notice_cancel == false) {
    if (notice_event != null) {
      setTimeout(() => {add_notice(str, img, tried + 1);}, 500);
    } else {
      notice.style.display = "block";
      if (img != "") {
        notice.children[0].innerHTML = "<ul><li><img></li><li>"+str+'</li><li><img></li></ul>';
        notice.children[0].children[0].children[0].children[0].src = img;
        notice.children[0].children[0].children[2].children[0].src = img;
      } else {
        notice.children[0].innerHTML = str;
      }
      notice.children[0].style['backgroundColor'] = color[0];
      notice_event = setTimeout(() => {notice.children[0].style['backgroundColor'] = color[1];}, 400);
      notice_event = setTimeout(() => {notice.children[0].style['backgroundColor'] = color[0];}, 800);
      notice_event = setTimeout(() => {notice.children[0].style['backgroundColor'] = color[1];}, 1200);
      notice_event = setTimeout(() => {notice_event = null;}, 2000);
      if (notice_hidden != null) {
        clearTimeout(notice_hidden);
      }
      notice_hidden = setTimeout(() => {notice.style.display = "none";}, 1600);
    }
  }
}
function reload_clear(e=null) {
  if (reload_event) {
    clearTimeout(reload_event);
    reload_event = null;
    console.log("Auto reload event is canceled.");
  }
}
function create_form() {
  let form = document.createElement("form");
  form.method = "POST";
  form.action = location.href;
  return form;
}
function create_form_input(name, value) {
  let form_input = document.createElement("input");
  form_input.type = "hidden";
  form_input.name = name;
  form_input.value = value;
  return form_input;
}
function zip(code) {
  document.body.appendChild(zip_form = create_form());
  zip_form.appendChild(create_form_input("zip", code));
  return zip_form.submit();
}
function show_background(url, update=true) {
  if (update) {
    background_url = url;
  }
  if (url) {
    document.getElementById("body").style = "background-image: url(\""+url+"\");";
  }
}
function set_background(bg_url=null) {
  let bg_note;
  if (!bg_url) {
    bg_url = prompt("URL");
  }
  if (bg_url) {
    bg_note = prompt("Note");
    document.body.appendChild(bg_form = create_form());
    bg_form.appendChild(create_form_input("background-set-url", bg_url));
    bg_form.appendChild(create_form_input("background-set-note", bg_note));
    return bg_form.submit();
  }
}
function background_reset(bg_id) {
  document.body.appendChild(bg_form = create_form());
  bg_form.appendChild(create_form_input("background-reset", bg_id));
  return bg_form.submit();
}
function add_link() {
  reload_clear();
  let link_url = prompt("URL");
  if (link_url) {
    let link_name = prompt("Display name");
    if (link_name) {
      document.body.appendChild(link_form = create_form());
      link_form.appendChild(create_form_input("link-url", link_url));
      link_form.appendChild(create_form_input("link-name", link_name));
      return link_form.submit();
    } 
  }
  location.href += "";
}
function delete_link() {
  reload_clear();
  let link_id = prompt("Link ID");
  if (link_id) {
    document.body.appendChild(link_form = create_form());
    link_form.appendChild(create_form_input("dellink-id", link_id));
    return link_form.submit();
  }
  location.href += "";
}
function change_tag() {
  reload_clear();
  let tag = prompt("Create new tag");
  if (tag) {
    location.href = `/file-uploader/?tag=${tag}`;
  } else {
    location.href = location.href;
  }
}
async function file_uploader_screen(e) {
  e.preventDefault();
  if (e.dataTransfer.files) {
    if (e.dataTransfer.files.length) {
      document.getElementById("file-uploader-selector").files = e.dataTransfer.files;
      file_send();
    } else {
      e = await fetch(e.dataTransfer.getData("text/plain"), {mode: 'cors'});
      if (e.ok) {
        e.data = await e.arrayBuffer();
        e.file = new File([e.data], e.url.split('/').pop());
        e.dt = new DataTransfer();
        e.dt.items.add(e.file);
        document.getElementById("file-uploader-selector").files = e.dt.files; 
        file_send();
      } else {
        add_warning(`Failed to send '${e.url}'`);
      }
    }
  }
}
function file_send(e=null) {
  reload_clear();
  if (!is_removable_tag) {
    let proceed = confirm("You cannot remove the file you sent. Would you still like to continue?")
    if (!proceed) {
      location.href += "";
      return;
    }
  }
  document.getElementById("file-uploader-form").submit();
}
function file_remove(file_id) {
  document.body.appendChild(file_form = create_form());
  file_form.appendChild(create_form_input("remove", file_id));
  return file_form.submit();
}
function file_all_remove() {
  document.body.appendChild(file_form = create_form());
  file_form.appendChild(create_form_input("all-remove", ""));
  return file_form.submit();
}
function file_revive(file_id) {
  document.body.appendChild(file_form = create_form());
  file_form.appendChild(create_form_input("revive", file_id));
  return file_form.submit();
}
function change_user_name() {
  let new_name = prompt("New user name");
  if (new_name) {
    document.body.appendChild(user_form = create_form());
    user_form.appendChild(create_form_input("user-name", new_name));
    return user_form.submit();
  }
}
function login() {
  document.body.appendChild(user_form = create_form());
  user_form.appendChild(create_form_input("login", ""));
  return user_form.submit();
}
function logout() {
  document.body.appendChild(user_form = create_form());
  user_form.appendChild(create_form_input("logout", ""));
  return user_form.submit();
}
function print_img(img_url) {
  reload_clear();
  if (img_url) {
    let original_image = new Image();
    original_image.onload = function() {
      let print_area_dom = document.createElement("div");
      let num_split_y = parseInt(prompt("Input a number of y-axis split"));
      let num_split_x = parseInt(prompt("Input a number of x-axis split"));
      let size_split_y = parseInt(original_image.height / num_split_y);
      let size_split_x = parseInt(original_image.width / num_split_x);
      for (let a = 0; a < num_split_y; a++) {
        for (let b = 0; b < num_split_x; b++) {
          let canvas_split_dom = document.createElement("canvas");
          canvas_split_dom.width = size_split_x;
          canvas_split_dom.height = size_split_y;
          canvas_split_dom.style = "display: block;";
          let canvas_split_context = canvas_split_dom.getContext('2d');
          canvas_split_context.drawImage(original_image, size_split_x * b, size_split_y * a, size_split_x, size_split_y, 0, 0, size_split_x, size_split_y);
          print_area_dom.appendChild(canvas_split_dom);
        }
      }
      document.body.innerHTML = "";
      document.body.appendChild(print_area_dom);
      window.print();
      if (confirm("Did the print complete successfully?")) {
        location.href += "";
      } else {
        original_image.src = img_url;
      }
    }
    original_image.src = img_url;
  }
}
function birthday_edit(name, date, remove=false) {
  let birthday_name, birthday_date;
  if (name) {
    birthday_name = name;
  } else {
    birthday_name = prompt("Username");
  }
  if (birthday_name) {
    if (remove) {
      let proceed = confirm(`Are you sure wan't to delete ${birthday_name}'s birthday?`);
      if (!proceed) {
        location.href = location.href;
        return;
      }
      birthday_date = "0000-00-00";
    } else if (date) {
      birthday_date = prompt("Birthday (Current: "+date+")");
    } else {
      birthday_date = prompt("Birthday (yyyy-mm-dd)");
    }
    if (birthday_date) {
      birthday_date = birthday_date.split("-");
      if(birthday_date.length == 3){
        document.body.appendChild(user_form = create_form());
        user_form.appendChild(create_form_input("birthday-name", birthday_name));
        user_form.appendChild(create_form_input("birthday-year", birthday_date[0]));
        user_form.appendChild(create_form_input("birthday-month", birthday_date[1]));
        user_form.appendChild(create_form_input("birthday-day", birthday_date[2]));
        return user_form.submit();
      }
    }
  }
}
window.addEventListener("load", function() {
  document.getElementById("file-uploader-selector").addEventListener("click", reload_clear);
  document.getElementById("file-uploader-selector").addEventListener("change", file_send);
  document.getElementById("file-uploader-screen-selector").addEventListener("change", file_send);
  window.addEventListener("dblclick", ()=>{
    window.open("https://www.momiji.ip-ddns.com/file-uploader/b.html", "b.html", "status=no,location=no,toolbar=no,menubar=no,width=600,height=600")
  })
});
