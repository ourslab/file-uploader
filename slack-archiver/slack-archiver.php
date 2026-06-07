<?php
  function channels_list() {
    global $channel;
    echo "<div class=\"sidebar-header\">Slack Archiver</div>";
    echo "<div class=\"channel-list\">";
    $query = sql_select("SlackArchivedData", "channel", "channel='$channel'");
    if ($data = sql_data($query)) {
      echo "<a class=\"channel-item active\" onclick=\"change_channel('{$data['channel']}')\"># {$data['channel']}</a>";
    }
    $query = sql_select("SlackArchivedData", " distinct channel", "channel!='$channel'", "channel DESC");
    while ($data = sql_data($query)) {
      echo "<a class=\"channel-item\" onclick=\"change_channel('{$data['channel']}')\"># {$data['channel']}</a>";
    }
    echo "</div>";
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
    echo "<div class=\"message-list\">";
    $query = sql_select("SlackArchivedData", "*", "channel='$channel'", "thread_ts DESC, date ASC, time ASC, id ASC");
    $current_thread_ts = "";
    $parent_date_time = "";
    while($data = sql_data($query)){
      $body = "";
      $user = $data['user'];
      $text = safe_str(replace_user_id($data['text']));
      // Convert Slack format links <URL|Text> to HTML anchor tags
      $text = preg_replace('/&lt;(https?:\/\/[^|]+)\|(.+?)&gt;/', '<a href="$1" target="_blank" rel="noopener noreferrer">$2</a>', $text);
      // Convert Slack format links <URL> to HTML anchor tags
      $text = preg_replace('/&lt;(https?:\/\/[^|\s]+?)&gt;/', '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>', $text);
      $file_url = "/slack-archiver/".$data['data'];
      $file_name = safe_str($data['name']);
      $time_stamp = "{$data['date']}_{$data['time']}";
      $display_time = substr($data['time'], 0, 5);
      
      $is_reply = false;
      if (empty($data['thread_ts'])) {
        $current_thread_ts = "";
        $parent_date_time = "";
      } else if ($current_thread_ts !== $data['thread_ts']) {
        $current_thread_ts = $data['thread_ts'];
        $parent_date_time = $data['date'] . $data['time'];
      } else if ($parent_date_time !== $data['date'] . $data['time']) {
        $is_reply = true;
      }
      $li_class = $is_reply ? "message-item reply" : "message-item parent";

      if (!empty($text) || !empty($file_name)) {
        $avatar_letter = mb_substr($user, 0, 1, "UTF-8");
        $hash = md5($user);
        $color = substr($hash, 0, 6);
        
        echo "<div class=\"{$li_class}\" id=\"{$time_stamp}\">";
        echo "  <div class=\"message-gutter\">";
        echo "    <div class=\"message-avatar\" style=\"background-color: #{$color};\">{$avatar_letter}</div>";
        echo "  </div>";
        echo "  <div class=\"message-content\">";
        echo "    <div class=\"message-header\">";
        echo "      <span class=\"message-name\">{$user}</span>";
        echo "      <span class=\"message-timestamp\"><a href=\"/slack-archiver/#{$time_stamp}\">{$data['date']} {$display_time}</a></span>";
        echo "    </div>";
        echo "    <div class=\"message-text\">{$text}";
        if (!empty($file_name)) {
          if (!empty($text)) {
            echo " <br>";
          }
          $ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
          $image_extensions = array('jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg');
          if (in_array($ext, $image_extensions)) {
            echo "<a class=\"message-file message-file-image\" href=\"{$file_url}\" data-preview-url=\"{$file_url}\">📎 {$file_name}</a>";
          } else {
            echo "<a class=\"message-file\" href=\"{$file_url}\">📎 {$file_name}</a>";
          }
        }
        echo "    </div>";
        echo "  </div>";
        echo "</div>";
      }
    }
    echo "</div>";
  }
  $init_set = "";
  $init_exec = "";
  if(!empty($_GET['channel'])){
    $channel = safe_str($_GET['channel']);
  }else{
    $query = sql_select("SlackArchivedData", "channel,date,time", "", "date DESC, time DESC");
    if($data = sql_data($query)){
      $channel = $data['channel'];
    }
  }
  $init_exec .= "show_background(\"{$background_url}\")\n";
?>
