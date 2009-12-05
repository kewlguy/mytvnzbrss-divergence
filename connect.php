<?php
$user = "";
$dbpass = "";
$host = "localhost";
$dbdb = "mytvnzb";

$domain = "http://www.website.com/"; // End with slash

mysql_select_db($dbdb, mysql_connect($host, $user, $dbpass)) or die ("No connection with database.");
?>