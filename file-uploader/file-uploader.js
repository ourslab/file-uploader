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
  notice.onclick = function() {notice.style["display"] = "none"; notice_cancel = true;}
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
      notice_event = setTimeout(() => {notice.children[0].style['backgroundColor'] = color[1];}, 500);
      notice_event = setTimeout(() => {notice.children[0].style['backgroundColor'] = color[0];}, 1000);
      notice_event = setTimeout(() => {notice.children[0].style['backgroundColor'] = color[1];}, 1500);
      notice_event = setTimeout(() => {notice.children[0].style['backgroundColor'] = color[0];}, 2000);
      notice_event = setTimeout(() => {notice.children[0].style['backgroundColor'] = color[1];}, 2500);
      notice_event = setTimeout(() => {notice_event = null;}, 3000);
      if (notice_hidden != null) {
        clearTimeout(notice_hidden);
      }
      notice_hidden = setTimeout(() => {notice.style.display = "none";}, 3100);
    }
  }
}
function show_background(url) {
  if (url) {
    document.getElementById("body").style = "background-image: url(\""+url+"\");";
  }
}
function set_background(url) {
  let background_url;
  if (url) {
    background_url = url
  } else {
    background_url = prompt("URL");
  }
  if (background_url) {
    document.getElementById("background-set-url").value = background_url;
    document.getElementById("background-set-form").submit();
  }
}
function reset_background() {
  document.getElementById("background-reset-form").submit();
}
function add_link() {
  clearTimeout(reload_event);
  let link_url = prompt("URL");
  if (link_url) {
    let link_name = prompt("Display name");
    if (link_name) {
      document.getElementById("link-url").value = link_url;
      document.getElementById("link-name").value = link_name;
    }
  }
  document.getElementById("link-form").submit();
}
function delete_link() {
  clearTimeout(reload_event);
  let link_id = prompt("Link ID");
  if (link_id) {
    document.getElementById("dellink-id").value = link_id;
  }
  document.getElementById("dellink-form").submit();
}
function change_tag() {
  clearTimeout(reload_event);
  let tag = prompt("Create new tag");
  if (tag) {
    location.href = `/file-uploader/?tag=${tag}`;
  }
  location.href = location.href;
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
function file_send() {
  clearTimeout(reload_event);
  if (!is_removable_tag) {
    let proceed = confirm("You cannot remove the file you sent. Would you still like to continue?")
    if (!proceed) {
      location.href = location.href;
      return;
    }
  }
  document.getElementById("file-uploader-form").submit();
}
function file_remove(file_id) {
  document.getElementById("file-remove-id").value = file_id;
  document.getElementById("file-remove-form").submit();
}
function file_all_remove() {
  document.getElementById("file-all-remove-form").submit();
}
function file_revive(file_id) {
  document.getElementById("file-revive-id").value = file_id;
  document.getElementById("file-revive-form").submit();
}
function change_user_name() {
  let new_name = prompt("New user name");
  if (new_name) {
    document.getElementById("user-name").value = new_name;
    document.getElementById("user-name-form").submit();
  }
}
function login() {
  document.getElementById("login-form").submit();
}
function logout() {
  document.getElementById("logout-form").submit();
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
      birthday_date = prompt("Birthday");
    }
    if (birthday_date) {
      birthday_date = birthday_date.split("-");
      if(birthday_date.length == 3){
        document.getElementById("birthday-name").value = birthday_name;
        document.getElementById("birthday-year").value = birthday_date[0];
        document.getElementById("birthday-month").value = birthday_date[1];
        document.getElementById("birthday-day").value =  birthday_date[2];
        document.getElementById("birthday-form").submit();
      }
    }
  }
}
