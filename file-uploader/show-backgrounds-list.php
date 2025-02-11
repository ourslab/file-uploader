<?php
  function process_backgrounds_set() {
    if(!empty($_POST['background-set-url'])){
      $background_set_url = safe_str($_POST['background-set-url']);
      $background_set_note = safe_str($_POST['background-set-note']);
      global $user_id;
      sql_insert("Backgrounds", "id,user_id,url,note", "0,{$user_id},'{$background_set_url}','{$background_set_note}'");
      redirect(get_full_url(), 301);
    }
  }
  function process_backgrounds_reset() {
    if(!empty($_POST['background-reset'])){
      $background_reset = intval($_POST['background-reset']);
      $query = sql_select("Backgrounds", "*", "id={$background_reset}");
      if ($data = sql_data($query)) {
        global $admin, $user_id;
        if ($data['user_id'] == $user_id || $admin) {
          sql_delete("Backgrounds", "id={$background_reset}");
          global $msg;
          array_push($msg, "Specified background image is removed!");
        } else {
          global $warn;
          array_push($warn, "Specified background image is not yours");
        }
      } else {
        global $warn;
        array_push($warn, "Specified background image is not found");
      }
    }
  }
  function show_backgrounds_list_row($background_id, $background_user_id, $background_url, $background_note) {
    $query = sql_select("Users", "*", "id={$background_user_id}");
    if ($data = sql_data($query)) {
      $background_user = $data['name'];
    } else {
      $background_user = "Unknown";
    }
    echo "<ul class=\"backgrounds-list\">";
    echo "<li class=\"backgrounds-list-user\">{$background_user}</li>";
    echo "<li class=\"backgrounds-list-url\"><a href=\"{$background_url}\">{$background_url}</a></li>";
    echo "<li class=\"backgrounds-list-note\">{$background_note}</li>";
    echo "<li class=\"backgrounds-list-command\"><a onclick=\"background_reset({$background_id})\">Reset</a></li>";
    echo "</ul>";
  }
  function show_backgrounds_list() {
    $query = sql_select("Backgrounds");
    while ($data = sql_data($query)) {
      show_backgrounds_list_row($data['id'], $data['user_id'], $data['url'], $data['note']);
    }
  }
?>
