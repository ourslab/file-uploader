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
  $slack_users_map = null;
  function replace_user_id($text) {
    global $slack_users_map;
    if ($slack_users_map !== null) {
      foreach($slack_users_map as $uid => $uname){
        $text = str_replace("<@{$uid}>", "@{$uname}", $text);
      }
    }
    return $text;
  }
  function messages_list() {
    global $channel, $slack_users_map;
    if ($slack_users_map === null) {
      $slack_users_map = array();
      $query = sql_select("SlackUsers", "distinct user_id,user_name");
      $users = $query->fetchAll(PDO::FETCH_ASSOC);
      foreach($users as $data){
        $slack_users_map[$data['user_id']] = $data['user_name'];
      }
    }
    echo "<div class=\"message-list\">";
    $query = sql_select("SlackArchivedData", "*", "channel='$channel'", "date ASC, time ASC, id ASC");
    $messages = $query->fetchAll(PDO::FETCH_ASSOC);
    // Group the messages into threads so that a parent and its replies stay together
    $threads = array();
    foreach($messages as $message){
      if (!empty($message['thread_ts'])) {
        $thread_key = "thread_{$message['thread_ts']}";
      } else if (!empty($message['ts'])) {
        $thread_key = "thread_{$message['ts']}";
      } else {
        $thread_key = "single_{$message['id']}";
      }
      $message['thread_key'] = $thread_key;
      $threads[$thread_key][] = $message;
    }
    // Sort the threads by the timestamp of their first message (newest thread first)
    usort($threads, function($a, $b){
      $key_a = "{$a[0]['date']} {$a[0]['time']} ".sprintf("%020d", $a[0]['id']);
      $key_b = "{$b[0]['date']} {$b[0]['time']} ".sprintf("%020d", $b[0]['id']);
      return strcmp($key_b, $key_a);
    });
    // Flatten back into a single list: each thread keeps its own chronological order
    $messages = array();
    foreach($threads as $thread){
      foreach($thread as $message){
        $messages[] = $message;
      }
    }
    $rendered_thread_key = "";
    foreach($messages as $data){
      $body = "";
      $user = $data['user'];
      $text = safe_str(replace_user_id($data['text']));
      // Convert Slack format links <URL|Text> to HTML anchor tags
      $text = preg_replace('/&lt;(https?:\/\/(?:(?!&lt;)(?!&gt;)[^|])+)\|((?:(?!&gt;).)+?)&gt;/', '<a href="$1" target="_blank" rel="noopener noreferrer">$2</a>', $text);
      // Convert Slack format links <URL> to HTML anchor tags
      $text = preg_replace('/&lt;(https?:\/\/(?:(?!&lt;)(?!&gt;)[^|\s])+)&gt;/', '<a href="$1" target="_blank" rel="noopener noreferrer">$1</a>', $text);
      // Convert Slack format email links <mailto:email|Text> to HTML anchor tags
      $text = preg_replace('/&lt;(mailto:(?:(?!&lt;)(?!&gt;)[^|])+)\|((?:(?!&gt;).)+?)&gt;/', '<a href="$1">$2</a>', $text);
      // Convert Slack format email links <mailto:email> to HTML anchor tags
      $text = preg_replace('/&lt;(mailto:(?:(?!&lt;)(?!&gt;)[^|\s])+)&gt;/', '<a href="$1">$1</a>', $text);
      $file_url = "/slack-archiver/".$data['data'];
      $file_name = safe_str($data['name']);
      $time_stamp = "{$data['date']}_{$data['time']}";
      $display_time = substr($data['time'], 0, 5);
      
      if (!empty($text) || !empty($file_name)) {
        // The first displayed message of a thread is the parent, the rest are replies
        $is_reply = ($rendered_thread_key === $data['thread_key']);
        $rendered_thread_key = $data['thread_key'];
        $li_class = ($is_reply)? "message-item reply" : "message-item parent";

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
            echo "<a class=\"message-file message-file-image\" href=\"{$file_url}\" data-preview-url=\"{$file_url}\" target=\"_blank\">📎 {$file_name}</a>";
          } else {
            echo "<a class=\"message-file\" href=\"{$file_url}\" target=\"_blank\">📎 {$file_name}</a>";
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
