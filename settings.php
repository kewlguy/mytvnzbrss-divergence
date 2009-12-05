<?php
$tvnzbfeed = "http://www.tvnzb.com/tvnzb_new.rss"; 
$tvnzbshowlist = "http://www.tvnzb.com/shows";
$tvnzbshow_regex = '<td align="left"><a href="http://www.tvnzb.com/shows/([0-9]{1,3})" class="result_blue_bold">([0-9a-z A-Z .&!()-:?`\']{0,60})</a></td>';

$tvrage_link = "http://services.tvrage.com/tools/quickinfo.php?show=";
$tvrage_status_regex = "Status@(.*)";
$tvrage_status_canceled = "Canceled/Ended";

$domain = "http://www.website.com/"; // End with slash

// Database Config
$user = "";
$dbpass = "";
$host = "localhost";
$dbdb = "mytvnzb";
mysql_select_db($dbdb, mysql_connect($host, $user, $dbpass)) or die ("No connection with database.");
?>