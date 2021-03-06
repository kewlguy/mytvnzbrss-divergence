<? 
include "settings.php";
include "functions.php";
header('Content-type: application/rss+xml; charset=UTF-8');
ob_start("ob_gzhandler"); //Enable Gzip

$show_array = create_show_array('id','title',1);

/*
	Make array of values
	x264 value
	Make sure they are integers
*/
$id = mysql_real_escape_string(strip_tags($_GET["id"]));
$sql = "SELECT * FROM mytvnzb WHERE ps_id = ".$id." LIMIT 1";
$query = mysql_query ($sql) or mysql_error ();
$show = mysql_fetch_array($query)  or mysql_error ();
$id_array = explode(" ", $show["str"]);

// X264 option
$num=count($id_array)-1;
$x264 = trim($id_array[$num]);
if ($x264 != "no" AND $x264 != "only") {$x264 = "both";}

// make url array
$id_ar = array();
foreach ($id_array as $id_value) {
	if($id_value != 0) array_push($id_ar, (int)trim($id_value));
}

//	Search name from show based on ID
for ($z = 0; $z < count($id_ar); $z++) {
  $show_names .= "  ".$show_array[$id_ar[$z]]."<br>"; 
}

// make option readable
 if ($x264 == "only") $x264txt = "Only showing x264";
 elseif ($x264 == "no") $x264txt = "Not showing x264";
 else $x264txt = "Showing x264 and non-x264";

/* 
	Header output;
	first line has to be print'ed,  else you get probs with php   
*/

print '<?xml version="1.0" encoding="UTF-8"?>'; 
?>
  <rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom">
	  <channel>
		  <title>MyTvNZB Feed v3.0</title>
			<link><?php echo $domain; ?>index.php?id=<? echo $id; ?></link>
			<description>MyTvNZB rss - <? echo $x264txt.htmlspecialchars($show_names); ?></description>
			<atom:link href="<? echo "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']; ?>" rel="self" type="application/rss+xml" />
<?php

// Start XML parsing
$xml_feed = file_get_contents($tvnzbfeed);
$xml_parser = xml_parser_create();

// Process XML data
xml_parse_into_struct($xml_parser,$xml_feed,$xml_sleutel,$xml_index);
xml_parser_free($xml_parser);


// This is how each <item> will look
function item($show_title,$show_id,$link,$description,$tvnzb_pubdate,$tvnzb_url,$tvnzb_length,$tvnzb_type) {
	echo "<item>\n";
	echo "\t<title>".$show_title."</title>\n";
	echo "\t<pubDate>".$tvnzb_pubdate."</pubDate>\n";
	echo "\t<link>".$link."</link>\n";
	//  echo "\t<show_id>".$show_id."</show_id>\n";
	echo "\t<description>".$description."</description>\n";
	echo "\t<guid isPermaLink=\"false\">".$link."</guid>\n";
	echo "\t<enclosure url=\"".$tvnzb_url."\" length=\"".$tvnzb_length."\" type=\"".$tvnzb_type."\" />\n";
	echo "</item>\n\n";
	return true;
}

// Start loop to generate <item> elements 
for($i = 0; !empty($xml_index['TITLE'][$i]); $i++){
	//  Make variables readable
  $show_title     = htmlspecialchars(trim($xml_sleutel[$xml_index['TITLE'][$i]]['value']));
  $show_id        = (int)trim($xml_sleutel[$xml_index['SHOW_ID'][$i-1]]['value']);  // little fix
  $link           = $xml_sleutel[$xml_index['LINK'][$i]]['value'];
  $description    = htmlspecialchars($xml_sleutel[$xml_index['DESCRIPTION'][$i]]['value']);
  $tvnzb_pubdate  = date("r", strtotime($xml_sleutel[$xml_index['PUBDATE'][$i]]['value']));
  $tvnzb_url    =  $xml_sleutel[$xml_index['LINK'][$i]]['value'];
  $tvnzb_length =  $xml_sleutel[$xml_index['ENCLOSURE'][$i]]["attributes"]['LENGTH'];
  $tvnzb_type   =  $xml_sleutel[$xml_index['ENCLOSURE'][$i]]["attributes"]['TYPE'];
  
  // ID control  &&  nuke filter
  if (in_array($show_id, $id_ar) && !eregi("nuke", $rss_channel["ITEMS"][$i]["TITLE"])) {
    //  Second condition:  x264 optie
    if ($x264 == "only" AND (eregi("x264", $show_title) OR eregi("\.ts", $show_title)  )  ) {
      //  Print feed:  only X264
      item($show_title,$show_id,$link,$description,$tvnzb_pubdate,$tvnzb_url,$tvnzb_length,$tvnzb_type);
    }if ($x264 == "no" AND !(eregi("x264", $show_title) OR eregi("\.ts", $show_title)  )  ) {
      //  Print feed:  no X264
      item($show_title,$show_id,$link,$description,$tvnzb_pubdate,$tvnzb_url,$tvnzb_length,$tvnzb_type);
    }if ($x264 == "both") {
      //  Print feed:  both x264 and non-x264
      item($show_title,$show_id,$link,$description,$tvnzb_pubdate,$tvnzb_url,$tvnzb_length,$tvnzb_type);
    }
  } //  End ID controle && nuke filter
} // End For loop
?>
</channel>
</rss>