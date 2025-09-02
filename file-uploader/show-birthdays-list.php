<?php
  function process_birthday() {
    if (!empty($_POST['birthday-name'])) {
      $birth_name = safe_str($_POST['birthday-name']);
      $birth_y = intval($_POST['birthday-year']);
      $birth_m = intval($_POST['birthday-month']);
      $birth_d = intval($_POST['birthday-day']);
      $birth_delete = false;
      if ($birth_y == 0 && $birth_m == 0 && $birth_d == 0) {
        $birth_delete = true;
        $query = sql_select("Birthday", "*", "user_name='{$birth_name}'");
        if ($query->rowCount() > 0) {
          sql_delete("Birthday", "user_name='{$birth_name}'");
          global $msg;
          array_push($msg, "{$birth_name}'s birthday is deleted!");
        } else {
          global $warn;
          array_push($warn, "{$birth_name}'s birthday is not found");
        }
      } else if ($birth_y > 1900 && $birth_m >= 1 && $birth_m <= 12 && $birth_d >= 1 && $birth_d <= 31) {
        global $user_name, $user_birth_y, $user_birth_m, $user_birth_d;
        if ($birth_name == $user_name) {
          $user_birth_y = $birth_y;
          $user_birth_m = $birth_m;
          $user_birth_d = $birth_d;
        }
        $query = sql_select("Birthday", "*", "user_name='{$birth_name}'");
        if ($query->rowCount() == 0) {
          sql_insert("Birthday", "id,user_name,year,month,day", "0,'{$birth_name}',{$birth_y},{$birth_m},{$birth_d}");
          global $msg;
          array_push($msg, "{$birth_name}'s birthday is added!");
        } else {
          sql_update("Birthday", "year={$birth_y},month={$birth_m},day={$birth_d}", "user_name='{$birth_name}'");
          global $msg;
          array_push($msg, "{$birth_name}'s birthday is updated!");
        }
      } else {
        global $warn;
        array_push($warn, "Birthday update failed");
      }
    }
    $today = intval(date("md"));
    $next_birthdays_on_this_year = array();
    $next_birthdays_on_next_year = array();
    $query = sql_select("Birthday", "*", "", "month, day");
    while ($data = sql_data($query)) {
      $data['month'] = intval($data['month']);
      $data['day'] = intval($data['day']);
      if ($data['month'] > 0 && $data['day'] > 0) {
        $data['date'] = $data['month'] * 100 + $data['day'];
        if ($data['date'] == $today) {
          global $notice;
          array_push($notice, ["Happy birthday {$data['user_name']}!", "/file-uploader/birthday-cake.gif"]);
        } else if ($data['date'] > $today) {
          array_push($next_birthdays_on_this_year, [$data['user_name'], "{$data['month']}/{$data['day']}"]);
        } else if ($data['date'] < $today) {
          array_push($next_birthdays_on_next_year, [$data['user_name'], "{$data['month']}/{$data['day']}"]);
        }
      }
    }
    $next_birthdays = array_merge($next_birthdays_on_this_year, $next_birthdays_on_next_year);
    global $msg;
    array_push($msg, "{$next_birthdays[0][0]}'s birthday, {$next_birthdays[0][1]}, is coming up.");
  }
  function show_birthday_list_row($id="", $name="", $year=null, $month=null, $day=null) {
    echo "<ul class=\"birthday-list\">";
    echo "<li class=\"birthday-list-id\">{$id}</li>";
    echo "<li class=\"birthday-list-name\">{$name}</li>";
    echo "<li class=\"birthday-list-year\">{$year}</li>";
    echo "<li class=\"birthday-list-month\">{$month}</li>";
    echo "<li class=\"birthday-list-day\">{$day}</li>";
    echo "<li class=\"birthday-list-command\">";
    if ($year !== null && $month !== null && $day !== null) {
      echo "<a onclick=\"birthday_edit('{$name}','{$year}-{$month}-{$day}')\">Edit</a>";
    } else {
      echo "<a onclick=\"birthday_edit('','')\">Add</a>";
    }
    echo "</li>";
    echo "<li class=\"birthday-list-remove\">";
    if ($year !== null && $month !== null && $day !== null) {
      echo "<a onclick=\"birthday_edit('{$name}','',true)\">Remove</a>";
    }
    echo "</li>";
    echo "</ul>";
  }
  function show_birthday_list() {
    echo "<br>";
    $query = sql_select("Birthday", "*", "", "month, day");
    while ($data = sql_data($query)) {
      show_birthday_list_row($data['id'], $data['user_name'], $data['year'], $data['month'], $data['day']);
    }
    show_birthday_list_row();
  }
?>
