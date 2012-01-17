<?php

// Handles user login and registration

class user {
	var $salt = "Dapocalypsewill0besubtitled";

	function loggedIn() {
		if(!isset($_COOKIE['user_name']) OR !isset($_COOKIE['pwd'])) {
			return false;
		}

		$c_user = make_nice($_COOKIE['user_name']);
		$c_pwd = make_nice($_COOKIE['pwd']);

		$sql = "SELECT id, pwd FROM user WHERE user_name = '$c_user'";
		$result = mysql_query($sql);

		if(!$result) return false;

		$db = mysql_fetch_assoc($result);

		if($db['pwd'] != $c_pwd) return false;
		
		$this->id = $db['id'];

		return true;
	}

	function login() {
		if(	!isset($_POST['user_name']) OR !isset($_POST['pwd']) ) {
			return false;
		}

		$p_user = make_nice($_POST['user_name']);
		$p_pwd = md5($this->salt . $_POST['pwd'] . $this->salt);	
		
		$sql = "SELECT id FROM user WHERE user_name = '$p_user' AND pwd = '$p_pwd'";
		$result = mysql_query($sql);

		if(!$result) return false;

		$db = mysql_fetch_assoc($result);
		
		$this->id = $db['id'];

		$future = time() + 60 * 60 * 24 * 31;
		setcookie('user_name', $p_user, $future);
		setcookie('pwd', $p_pwd, $future);

		return true;	
	}

	function register() {

		if(!isset($_POST['user_name']) OR !isset($_POST['pwd']) OR !isset($_POST['pwd2'])) {
			return false;
		}

		if($_POST['pwd'] != $_POST['pwd2']) {
			return false;
		}

		$p_user = make_nice($_POST['user_name']);
		$p_pwd = md5($this->salt . $_POST['pwd'] . $this->salt);	
	
		// @TODO add - requirements to pwd and user name
	
		// Is username taken?

		$sql = "SELECT count(id) AS taken FROM user WHERE user_name = '$p_user'"; 
		$result = mysql_query($sql);
		$row = mysql_fetch_assoc($result);

		if($row['taken']) {	return false; }

		// Insert user data into user table
		$sql = "INSERT INTO user (user_name, pwd) VALUES (?,?)";
		$vars = Array('user_name' => $p_user, 'pwd' => $p_pwd);

		$result = critical_query_handler($sql, $vars);
		if($result['error']) return false;

		$this->id = $result['id'];

		$future = time() + 60 * 60 * 24 * 31;
		setcookie('user_name', $p_user, $future);
		setcookie('pwd', $p_pwd, $future);

		return true;	
	}

	function logout() {
		$past = time() - 60 * 60 * 24 * 31;
		setcookie('user_name', '', $past);
		setcookie('pwd', '', $past);
		
	}

}

// strips strings of potentially harmful code
function make_nice($string) {
	$magic_quotes = get_magic_quotes_gpc();
	return htmlentities($magic_quotes ? stripslashes($string) : $string);
}


function critical_query_handler($statement, $vars) {
	foreach($vars as $label => $value)
	{
		// @TODO: protect from injection here
		$set_query_parts[]= "@$label = '$value'";
		$exec_query_parts[] = "@$label";
	}
	
	$query = "PREPARE statement FROM \"$statement\"";
	mysql_query($query);
	
	$query = "SET " . implode($set_query_parts, ", ");
	mysql_query($query);

	$query = "EXECUTE statement USING " . implode($exec_query_parts, ", ");
	mysql_query($query);
	
	$result = Array();
	$result['affected_rows'] = mysql_affected_rows();
	$result['id'] = mysql_insert_id();
	$result['error'] = mysql_error();

	$query = "DEALLOCATE PREPARE statement";
	mysql_query($query);
				
	return $result;

}