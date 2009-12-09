<?php
include "settings.php";
include "functions.php";

ob_start("ob_gzhandler"); // Gzip enable

// Add a little safety 9 feb 2009
$ps_id = strip_tags($_GET["id"]);

// RSS url gerenration
if (isset($_POST["submit"])) {
  //  generating str
  foreach ($_POST["selected_show"] as $nr){
    $ids = $ids." ".trim($nr);
  }
  $show_id = $ids." ".$_POST["x264"]."";

  // Query invoegen
  if (!empty($ps_id)) {
    $sql = "UPDATE mytvnzb SET str = '".mysql_real_escape_string($show_id)."' WHERE ps_id = ".mysql_real_escape_string($ps_id)."";
  }else {
    $lst_q = mysql_fetch_array(mysql_query("SELECT max(id) FROM mytvnzb"));
    $ps_id = substr(microtime(), 5,3).$lst_q[0].$last_id.substr(microtime(), 2,3);
    $sql = "INSERT INTO mytvnzb VALUES (NULL, '".mysql_real_escape_string($ps_id)."', '".mysql_real_escape_string($show_id)."')";
  }
  $query = mysql_query($sql) or die (mysql_error());

  if ($query) { $show_url = $domain.$ps_id.".rss"; }
  if (!$query) { echo "<h2>Database error.</h2><p>Please try again or contact the administrator.</p>"; die();}

  header("Location:". $domain."index.php?id=".$ps_id."");
  die();
}

// Creating Array of IDs,  get values from database
if (!empty($ps_id)) {
  $sql = "SELECT * FROM mytvnzb WHERE ps_id = ".mysql_real_escape_string($ps_id)." LIMIT 1";
  $query = mysql_query ($sql);
  $show = mysql_fetch_array($query);
  $url_id_array = explode(" ", $show["str"]);

  // X264 optie toewijzen
  $num=count($url_id_array)-1;
  $x264 = trim($url_id_array[$num]);

  if ($x264 != "no" AND $x264 != "only") {$x264 = "both";}

  $url_id = array();
  foreach ($url_id_array as $url_id_value) {
    if ($url_id_value != 0) array_push($url_id, (int)$url_id_value);
  }
  $show_url = $domain."feedgen.php?id=".$ps_id;
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
  <link rel="icon" href="favicon.ico" type="image/x-icon"> 
  <link rel="shortcut icon" href="favicon.ico" type="image/x-icon">
  <title>MyTvNZB rss Divergence 3.0</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
  <link media="screen" href="nzbv2.css" rel="stylesheet" type="text/css" />
  <script type="text/javascript" src="v2.js"></script>
</head>
<body>
<div id="container">
<div id="header">
  <h1><a href="/nzbrss/">MyTvNZB<sup>rss</sup> Divergence 3.0</a></h1>
  <h2><a href="/nzbrss/">The NZBs for your favorite shows</a></h2>
</div>

<?php
if (isset($show_url)) {
?>
<div class="response">
<h2>
    Your Personal Feed
</h2>
<p>
You can now add this RSS link to your RSS reader or NZB client:<br />
  <a href="<?php echo $show_url; ?>">
    <?php echo $show_url; ?>
  </a>
  <br />
  <br />

To modify your selection of shows later bookmark this page:<br />
  <a href="<?php echo $domain; ?>index.php?id=<? echo $ps_id; ?>">
    <?php echo $domain; ?>index.php?id=<? echo $ps_id; ?>
  </a>
</p>
</div>
<?php
}
?>

<div id="content">
<div id="infobox">
<img src="info_icon.png" alt="Info: " style="float: left; padding-right: 10px;"/>
Select your favorite shows below and click "Generate Feed".
You will be alerted trough the RSS feed when a new episode is aired.<br />
The NZB for usenet is automaticly inserted.
Shows don't start with "The". - e.g. "The Simpsons" --is--> "Simpons, The".<br />
No registration required.

<h3>My show is not in the list, how to get it added?</h3> 
1) Look in the <a href="http://www.tvnzb.com/shows">TVNZB.com Show List.</a>
<br>
2) If it is not in the list, make a request on the <a href="http://www.tvnzb.com/forum/viewforum.php?f=5">Episode Request</a> part of the forum first.

</div>
<form action="index.php?id=<? echo $_GET["id"]; ?>" method="post">
<div class="letter"><h2>x264 options:</h2></div>

<? if ($x264 == "only") {$slcted = " selected"; $chked = "checked=\"checked\" ";}else {$slcted = "";$chked = "";} ?>
  <div id="lbl_only" class="box<? echo $slcted; ?>">
    <label><input type="radio" name="x264" value="only" <? echo $chked; ?>onclick="changeShow(this);" /> Only show x264 or better</label>
  </div>
<? if ($x264 == "no") {$slcted = " selected"; $chked = "checked=\"checked\" ";}else {$slcted = "";$chked = "";} ?>
  <div id="lbl_no" class="box<? echo $slcted; ?>">
    <label><input type="radio" name="x264" value="no" <? echo $chked; ?>onclick="changeShow(this);" /> Don't show x264</label>
  </div>
<? if ($x264 == "both") {$slcted = " selected"; $chked = "checked=\"checked\" ";}else {$slcted = "";$chked = "";} ?>
  <div id="lbl_both" class="box<? echo $slcted; ?>">
    <label><input type="radio" name="x264" value="both" <? echo $chked; ?>onclick="changeShow(this);" /> Show x264 and non-x264</label>
  </div>
<hr />
<h3>What is x264?</h3>
<p>
  If an episode is x264, it is High-Definition.<br />
  x264 is the open source encoder for HD material.<br />
  A normal episode is 350MB, a HD version can be bigger than 1GB.
</p>
<?php

$show_array = create_show_array('id','title',1);

// Get Numbers
echo "<div class=\"letter\"><h2>#</h2></div>\n";
foreach ($show_array as $key => $value){
  $word = trim($value);
  $id = trim($key);

  $expr = "/^[0-9]".$letter."/";
  if (preg_match($expr, strtolower($word))) {
    $id = (int)$id;
    if (!empty($_GET["id"])) {
      if (in_array($id, $url_id)) {$slcted = " selected"; $chked = "checked=\"checked\" ";}
      else {$slcted = "";$chked = "";}
    }
    echo "\t<div class=\"box".$slcted."\" id=\"lbl_".$id."\">";
    echo "<label>";
    echo "<input type=\"checkbox\" name=\"selected_show[]\" value=\"".$id."\" ".$chked."onclick=\"changeShow(this);\" />";
    echo htmlspecialchars($word);
    echo "</label>";
    echo "</div>\n";
  }
}

// Get Alfabet
foreach (range('a', 'z') as $letter) {
  echo "<div class=\"letter\"><h2>".strtoupper($letter)."</h2></div>\n";
  foreach ($show_array as $key => $value){
    $word = trim($value);
    $id = (int)trim($key);
    $expr = "/^".$letter."/";
    if (preg_match($expr, strtolower($word))) {
      if (!empty($_GET["id"])) {
        if (in_array($id, $url_id)) {$slcted = " selected"; $chked = "checked=\"checked\" ";}
        else {$slcted = "";$chked = "";}
      }
      echo "\t<div class=\"box".$slcted."\" id=\"lbl_".$id."\">";
      echo "<label>";
      echo "<input type=\"checkbox\" name=\"selected_show[]\" value=\"".$id."\" ".$chked."onclick=\"changeShow(this);\" />";
      echo htmlspecialchars($word);
      echo "</label>";
      echo "</div>\n";
    }
  }
}

if (isset($show_url)) {
?>
<input type="submit" name="submit" value="Update Feed" />
<?php
} else {
?>
<input type="submit" name="submit" value="Generate Feed" />
<?php
}
?>
</form>

<?php
if (isset($show_url)) {
?>
<div class="response">
<h2>
    Your Personal Feed
</h2>

<p style="clear: left;">
You can now add this RSS link to your RSS reader or NZB client:<br />
  <a href="<?php echo $show_url; ?>">
    <?php echo $show_url; ?>
  </a>
  <br />
  <br />

To modify your selection of shows later bookmark this page:<br />
  <a href="<?php echo $domain; ?>?id=<? echo $ps_id; ?>">
    <?php echo $domain; ?>?id=<? echo $ps_id; ?>
  </a>
  <br />
  <br />
</p>
</div>
<?php
}
?>
</div><!-- End Content -->
<div id="footer">
  <div class="marginleft">
    <h2>Disclaimer</h2>
    <p>The information given on the feed is generated without any human interface of the site administrator.<br />
    No NZB files are stored on this server.
    NZB (xml/txt) files link to content on Usenet.<br />
    We have no control nor influence on files posted on Usenet nor NZB's posted on tvnzb.com.<br />
    We therefore cannot prevent that you might find objectionable material by using this site.</p>
  <p>
    <a href="http://validator.w3.org/check?uri=<? echo "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>" target="_blank">
      xHTML Valid
    </a>
    -
    <a href="http://jigsaw.w3.org/css-validator/validator?uri=<? echo "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>" target="_blank">
      CSS Valid
    </a>
    -
      Valid RSS
  </p>
  </div>
</div><!-- End Footer -->
</div><!-- End Container -->
</body>
</html>