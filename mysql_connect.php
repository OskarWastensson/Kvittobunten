<?php
// Connect to mysql database.
$connected = mysql_connect('localhost', 'kvittobunten', 'FzhdZpAYMpyBSEWT');
if(!$connected) {
	$message = $debug ? mysql_error() : " Sorry!";
	die("Check connection to mysql. " . $message);
}

if(!mysql_select_db('kvittobunten')) {
	$message = $debug ? mysql_error() : " Sorry!";
	die("Database missing. " . $message);
	
}
 