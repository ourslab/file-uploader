<?php
  function process_link_add() {
    if(!empty($_POST['link-url']) && !empty($_POST['link-name'])){
      $name = safe_str($_POST['link-name']);
      $link = safe_str($_POST['link-url']);
      sql_insert("Links", "id,name,url", "0,'{$name}','{$link}'");
    }
  }
  function process_link_remove() {
    if(!empty($_POST['dellink-id'])){
      $link = intval($_POST['dellink-id']);
      sql_delete("Links", "id='{$link}'");
    }
  }
  function show_links_list() {
    echo "<ul>";
    echo "<li><a onclick=\"add_link()\">&gt;&gt;&gt; Add new link</a></li>";
    echo "<li><a onclick=\"delete_link()\">&gt;&gt;&gt; Delete link</a></li>";
    $query = sql_select("Links");
    while($data = sql_data($query)){
      echo "<li><a href=\"{$data['url']}\" target=\"_blank\">({$data['id']}){$data['name']}</a></li>";
    }
    echo "</ul>";
  }
  function show_tags_list($file_tag) {
    echo "<ul>";
    echo "<li>";
    echo "<img class=\"tag-icon\" src=\"/favicon.ico\">";
    echo "<a class=\"highlighting\" href=\"/file-uploader/?tag={$file_tag}\">{$file_tag}</a>";
    echo "</li>";
    $query = sql_select("SentFiles", "tag", "tag!='$file_tag'", "tag desc");
    while($data = sql_data($query)){
      echo "<li>";
      echo "<img class=\"tag-icon\" src=\"/favicon.ico\">";
      echo "<a href=\"/file-uploader/?tag={$data['tag']}\">{$data['tag']}</a>";
      echo "</li>";
    }
    echo "<li><a onclick=\"change_tag()\">&gt;&gt;&gt; Create a new tag</a></li>";
    echo "<li class=\"hide\"><a onclick=\"change_user_name()\">&gt;&gt;&gt; Change username</a></li>";
    global $admin;
    if ($admin) {
      echo "<li class=\"hide\"><a onclick=\"logout()\">&gt;&gt;&gt; Logout</a></li>";
    } else {
      echo "<li class=\"hide\"><a onclick=\"login()\">&gt;&gt;&gt; Login</a></li>";
    }
    echo "<li class=\"hide\"><a href=\"/phpmyadmin\" target=\"_blank\">&gt;&gt;&gt; phpMyAdmin</a></li>";
    echo "</ul>";
  }
?>
