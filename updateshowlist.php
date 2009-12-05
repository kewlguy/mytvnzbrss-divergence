<?php
include "settings.php";
include "functions.php";
header('Content-type: text/plain; charset=ISO-8859-1');

$show_array_id = create_show_array('id','active',-1);
$show_array_title = create_show_array('title','id',-1);

update_show_list($show_array_id);
update_show_status($show_array_title);

?>