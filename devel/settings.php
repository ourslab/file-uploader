<?php
  date_default_timezone_set('Asia/Tokyo');
  $debug = intval(sql_data(sql_select("ServerConfig", "*", "name='debug'"))['value']);
  $https_only = intval(sql_data(sql_select("ServerConfig", "*", "name='https_only'"))['value']);
  $domain_name = sql_data(sql_select("ServerConfig", "*", "name='domain_name'"))['value'];
  $domain_only = intval(sql_data(sql_select("ServerConfig", "*", "name='domain_only'"))['value']);
  $ip_address = sql_data(sql_select("ServerConfig", "*", "name='ip_address'"))['value'];
  $ip_only = intval(sql_data(sql_select("ServerConfig", "*", "name='ip_only'"))['value']);
  $public_mode = intval(sql_data(sql_select("ServerConfig", "*", "name='public_mode'"))['value']);
  $default_backgrounds = array();
  $default_background_public = sql_data(sql_select("ServerConfig", "*", "name='default_background_public'"))['value'];
  $img_exts = explode(":", sql_data(sql_select("ServerConfig", "*", "name='image_extensions'"))['value']);
  $js_init = "";
  $js_onload = "";
  $msg = array();
  $notice = array();
  $warn = array();
  $reload_time = intval(sql_data(sql_select("ServerConfig", "*", "name='reload_time'"))['value']);
  $generate_enabled = intval(sql_data(sql_select("ServerConfig", "*", "name='generate_enabled'"))['value']);
  $files_per_page = intval(sql_data(sql_select("ServerConfig", "*", "name='files_per_page'"))['value']);
?>
