<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN"
"http://www.w3.org/TR/html4/loose.dtd">
<?php
$debug = true;

require_once('mysql_connect.php');
require_once('user_lib.php');

$user = new user();
if(isset($_GET['logout'])) 
{
	$user->logout();
}

if(isset($_POST['login']) and $user->login()) 
{
	header('location: index.php');
} 
elseif(isset($_POST['reg']) and $user->register()) 
{
	header('location: index.php');
}

?>
<html lang="se">
<head>
	<link rel="stylesheet" type="text/css" href="screen.css" />
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"
type="text/javascript"></script>
	<script src='functions.js'></script>
	<script type="text/javascript">
		function validate(form) {
			// @TODO Javascript validation with jquery error reporting

			if(form.user_name.value == "")
			{
				$(form.name + "_user_name_err").Append("<td id='login_user_name_err'>Användarnamnet saknas!</td>") 
			}


			return true;
		}
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
			</div>
			<div id='content'>
				<table class='form_multiline'>
					<th colspan='3'>Logga in</th>
					<tr><td colspan='3'>Fyll i användarnamn och lösenord.<td></tr>
					<form method='post' onSubmit='return validate(this)' name='login'>
					<tr><td>Användarnamn</td><td><input name='user_name' type='text' maxlength='16'></td>
					<td id='login_user_name_err'></td></tr>
					<tr><td>Lösenord</td><td><input name='pwd' type='text' maxlength='12' /></td>
					<td id='login_pwd_err'></td></tr>
					<tr><td colspan='3'>
						<input type='hidden' name='login' value='1'>
						<input type='submit' value='Logga in' />
					</td></tr>
					</form>
				</table>
			</div>
			<div id='content'>
				<table class='form_multiline'>
					<th colspan='3'>Registrera dig</th>
					<tr><td colspan='3'>Vill du pröva? Välj ett användarnamn och ett lösenord, så kan du börja kontroll över 
				dina pengar på en gång!<td></tr>
					<form method='post' action='login.php' onSubmit='return validate(this)' name='reg'>
					<tr><td>Användarnamn</td><td><input name='user_name' type='text' maxlength='16' /></td>
					<td id='reg_user_name_err'></td></tr>
					<tr><td>Lösenord</td><td><input name='pwd' type='text' maxlength='12' /></td>
					<td id='reg_pwd_err'></td></tr>
					<tr><td>Upprepa Lösenord</td><td><input name='pwd2' type='text' maxlength='12' ></td>
					<td id='reg_pwd2_err'></td></tr>
					<tr><td colspan='3'>
						<input type='hidden' name='reg' value='1'>
						<input type='submit' value='Registrera' />
					</td></tr>
					</form>
				</table>
			</div>
		</div>

		<div id='side2'>
			<p></p>
		</div>
		
	</div>
</body>
