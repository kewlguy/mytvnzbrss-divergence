<?php
include "connect.php";
header('Content-type: text/plain; charset=ISO-8859-1');

$sql = 'SELECT * FROM shows';
$query = mysql_query($sql) or die (mysql_error());

while( $result_row = mysql_fetch_array($query)) {
  $show_array_id[$result_row[id]] = $result_row[active];
  $show_array_title[$result_row[title]] = $result_row[id];
}

update_show_list($show_array_id);
update_show_status($show_array_title);

function update_show_list($show_array) {
  $lines = file('http://www.tvnzb.com/shows');
  foreach ($lines as $line_num => $line) {
     if (eregi('<td align="left"><a href="http://www.tvnzb.com/shows/([0-9]{1,3})" class="result_blue_bold">([0-9a-z A-Z .&!()-:?`\']{0,60})</a></td>', $line, $data)) {
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

function update_show_status($show_array) {
  foreach($show_array as $key => $value) {
    $show_title = trim($key); 
    $show_id = (int)trim($value);
    
    $lines = file("http://services.tvrage.com/tools/quickinfo.php?show=".urlencode($show_title));
    foreach ($lines as $line_num => $line) {
      if (eregi('Status@(.*)',$line,$data)) { 
        print $show_title." -- ".$data[1];
        if (trim($data[1]) == "Canceled/Ended") {
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