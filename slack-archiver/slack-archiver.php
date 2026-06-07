<?php
  function channels_list() {
    global $channel;
    echo "<ul>";
    $query = sql_select("ArchivedData", "channel", "channel='$channel'");
    if ($data = sql_data($query)) {
      echo "<li>";
      echo "<img class=\"channel-icon\" src=\"/favicon.ico\">";
      echo "<a class=\"channel-top\" onclick=\"change_channel('{$data['channel']}')\">{$data['channel']}</a>";
      echo "</li>";
    }
    $query = sql_select("ArchivedData", " distinct channel", "channel!='$channel'", "channel DESC");
    while ($data = sql_data($query)) {
      echo "<li>";
      echo "<img class=\"channel-icon\" src=\"/favicon.ico\">";
      echo "<a onclick=\"change_channel('{$data['channel']}')\">{$data['channel']}</a>";
      echo "</li>";
    }
    echo "</ul>";
  }
  function replace_user_id($text) {
    global $channel;
    $query = sql_select("SlackUsers", "distinct user_id,user_name");
    while($data = sql_data($query)){
      $text = str_replace("<@{$data['user_id']}>", "@{$data['user_name']}", $text);
    }
    return $text;
  }
  function messages_list() {
    global $channel;
    echo "<ul>";
    $query = sql_select("ArchivedData", "*", "channel='$channel'", "date DESC, time DESC, id DESC");
    while($data = sql_data($query)){
      $body = "";
      $user = $data['user'];
      $text = safe_str(replace_user_id($data['text']));
      $file_url = "/slack-archiver/".$data['data'];
      $file_name = safe_str($data['name']);
      $time_stamp = "{$data['date']}_{$data['time']}";
      if (!empty($text) || !empty($file_name)) {
        echo "<li><ul id={$time_stamp}>";
        echo "<li class=\"message-name\">{$user}</li>";
        echo "<li class=\"message-text\">{$text}<a href=\"{$file_url}\">{$file_name}</a></li>";
        echo "<li class=\"message-timestamp\"><a href=\"/slack-archiver/#{$time_stamp}\">{$time_stamp}</a></li>";
        echo "</ul></li>";
      }
    }
    echo "</ul>";
  }
  $init_set = "";
  $init_exec = "";
  if(!empty($_GET['channel'])){
    $channel = safe_str($_GET['channel']);
  }else{
    $query = sql_select("ArchivedData", "channel,date,time", "", "date DESC, time DESC");
    if($data = sql_data($query)){
      $channel = $data['channel'];
    }
  }
  $init_exec .= "show_background(\"{$background_url}\")\n";
?>
