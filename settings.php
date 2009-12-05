<?php

$tvnzbfeed = "http://www.tvnzb.com/tvnzb_new.rss"; 
$tvnzbshowlist = "http://www.tvnzb.com/shows";
$tvrage_link = "http://services.tvrage.com/tools/quickinfo.php?show=";
$domain = "http://www.website.com/"; // End with slash

// Database Config
$user = "";
$dbpass = "";
$host = "localhost";
$dbdb = "mytvnzb";
mysql_select_db($dbdb, mysql_connect($host, $user, $dbpass)) or die ("No connection with database.");

?>