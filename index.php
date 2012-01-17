<?php
$debug = true;

require 'mysql_connect.php';
require 'user_lib.php';

$user = new user();
$user->loggedIn() or header('location: login.php');

// Display main page
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<html lang="se">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
	<link rel="stylesheet" type="text/css" href="screen.css" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"
type="text/javascript"></script>
	<script src='functions.js'></script>
	<script type="text/javascript">
	$(document).ready(function() {
		// Fetch accounts
		$.post('ajax_responders.php', { rid: 'accounts' }, function(data) {
			if(data.error == 'empty') {
				delete data.error;
				$("#content").html(budget_form(data));
			} else {
				render_transactions(data);
			}
		}, 'json');
	});

	</script> 
</head>
<body>
	<div id='main_wrapper'>
		<div id='side1'>
			<p></p>
		</div>
		
		<div id='content_wrapper'>
			<div id='headbar'>
				<h1>Kvittobunten</h1>
				<p>Sidan som avgör om der blir fiskpinnar eller sushi till middag.</p>
				<p><a href='login.php?logout=1'>Logga ut</a></p>
			</div>
			<div id='content'>
				<p>Hinner du läsa det här är något fel.</p>
			</div>
		</div>

		<div id='side2'>
			<p></p>
		</div>
		
	</div>
</body>
