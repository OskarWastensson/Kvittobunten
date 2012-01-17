<?php
// @file - ajax responders.php
//   Performs mysql requests and returns json data

require_once('user_lib.php');
require_once('mysql_connect.php');

$user = new user();
if(!$user->loggedIn())
{
	$data = array('error' => 'user');
}  
elseif(!isset($_POST['rid']))
{
	$data = array('error' => 'invalid');
}
else {

if(1) {	
	switch('budget_form') { // $_POST['rid']) {
		case 'test':
			$data = array("test" => "test");
			break;	
		case 'accounts':
		case 'budget_form':
			$sql = 
				"SELECT account_no as id, budget, name
				FROM account 
				WHERE user = $user->id";

			$result = mysql_query($sql);

			if($result) {
				$data = array();
				while($row = mysql_fetch_assoc($result))
				{
					$data[] = $row;
				} 
				
				if(count($data) == 0) {
					// User has no acocunts - send error AND template accounts
					$sql = 
						"SELECT account_no as id, budget, name
						FROM account 
						WHERE user = 0";

					$result = mysql_query($sql);
				
					while($row = mysql_fetch_assoc($result))
					{
						$data[] = $row;
					} 
					$data['error'] = 'empty';
					
				}
				
			} else {
				$data = array('error' => 'sql');
			}

			break;

		case 'transactions':
			$from = isset($_POST['from']) ? "'".make_nice($_POST['from'])."'" : "LASTDAY(CURDATE()- INTERVALL 1 MONTH";
			$to = isset($_POST['to']) ? "'".make_nice($_POST['to'])."'" : "CURDATE()";
			
			$sql = 
				"SELECT id, exec_date, amount, account 
				FROM transaction 
				JOIN user ON transaction.user = user.id 
				WHERE user.id = $user->id 
				AND exec_date > $from AND exed_date < $to";
			

			$result = mysql_query($sql);

			if($result) {
				while($row = mysql_fetch_assoc($result))
				{
					$data[] = $row;
				} 

			} else {
				$data = array('error' => 'empty');
			}

			break;
		
		case 'insert_transaction':
			isset($_POST['exec_date']) or ajax_error('post');
			isset($_POST['amount']) or ajax_error('post');
			isset($_POST['account']) or ajax_error('post');

			$date = make_nice($_POST['exec_date']);
			$amount = make_nice($_POST['amount']);
			$account = make_nice($_POST['acoount']);

			$sql = "INSERT INTO transaction (user, account, exec_date) VALUES (?,?,?)";
			$vars = Array(
				'user' => $user->id,
				'account' => make_nice($_POST['account']),
				'exec_date' => make_nice($_POST['amount'])
				);

			$result = critical_query_handler($sql, $vars);
			if($result['error']) ajax_error('mysql');
			
			// return inserted row

			$sql = "SELECT id, exec_date, amount, account 
				FROM transaction 
				WHERE id = " . $result['id'];
			
			$data = format_data(mysql_query($sql));
			break;
										 
		default: 
			$data = array('error' => 'Invalid');
		}
	}
}
// Use $data to output a JSON structure.

echo json_encode($data);

// Functions
function format_data($result) {
	
	while($row = mysql_fetch_assoc($result)) {
		$id = $row['id'];
		unset($row['id']);
		$data[$id] = $row;
	}
	return $data;
}