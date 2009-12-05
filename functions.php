<?php

/* Creates an associative array where the given key
   and value are supplied into the function.
   active_flag: 1 = active, 0 = not active, -1 = show all */
function create_show_array($key, $value, $active_flag) {
  include "settings.php";

  if ($active_flag == 1) {
    $sql = "SELECT * FROM shows WHERE active='1'";
  } else if ($active_flag == 0) {
    $sql = "SELECT * FROM shows WHERE active='0'";
  } else if ($active_flag == -1) {
    $sql = "SELECT * FROM shows";
  }
  $query = mysql_query($sql) or die (mysql_error());

  while( $result_row = mysql_fetch_array($query)) {
    $show_array[$result_row[$key]] = $result_row[$value];
  }  
  return $show_array;  
}

/* Updates show table with current listing of
   tv shows from tvnzb. */
function update_show_list($show_array) {
  include "settings.php";
  $lines = file($tvnzbshowlist);
  foreach ($lines as $line_num => $line) {
    if (eregi($tvnzbshow_regex, $line, $data)) {
      $showid = mysql_real_escape_string($data[1]);
      $showname = mysql_real_escape_string($data[2]);
      if (!array_key_exists($showid,$show_array)) {
        if (substr($showname,0, 4) == "The ") {
          $showname = substr($showname, 4).", The";
        }
        print "Adding the following show(s):\r\n";
        print $showname."\r\n";
        $sql = 'INSERT INTO shows VALUES ("'.mysql_real_escape_string($showid).'", "'.mysql_real_escape_string($showname).'", "1")';
        $query = mysql_query($sql) or die (mysql_error());
      } 
    }
  }
}

/* Updates show table with current show status
   from tvrage.com */
function update_show_status($show_array) {
  include "settings.php";
  foreach($show_array as $key => $value) {
    $show_title = trim($key); 
    $show_id = (int)trim($value);
    $lines = file($tvrage_link.urlencode($show_title));
    foreach ($lines as $line_num => $line) {
      if (eregi($tvrage_status_regex,$line,$data)) { 
        print $show_title." -- ".$data[1];
        if (trim($data[1]) == $tvrage_status_canceled) {
          $sql = "UPDATE shows SET active='0' WHERE id='".mysql_real_escape_string($show_id)."'";
        } else {
          $sql = "UPDATE shows SET active='1' WHERE id='".mysql_real_escape_string($show_id)."'";
        }
        $query = mysql_query($sql) or die (mysql_error());
      }
    }
  }
}  

?>