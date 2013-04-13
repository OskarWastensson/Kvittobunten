<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class User extends model {
  public $vars = Array(
    'id' => 'i',
    'uid' => 's',
    'pwd' => 's',
  );
  
  public $id = 0;
  
  public function checkin() {
    return isset($_SESSION['uname']) && isset($_SESSION['pwd']) 
      && $this->verify($_SESSION['uname'], $_SESSION['pwd']);
  }
  
	public function login($args) {
    if($this->verify($args['uname'], md5($args['pwd']))) {
      $_SESSION['uname'] = $this->data[0]['uname'];
      $_SESSION['pwd'] = $this->data[0]['pwd'];
      return json_encode(Array ('login' => 1));
    } else {
      error(0, 'Cannot verify user.');
    }
  }
  
  protected function verify($uname, $pwd) {
    if ($this->read(Array('id' => $uname)) && $this->data[0]['pwd'] == $pwd) {
      $this->id = $this->data[0]['id'];
      return true;
    } else {
      return false;
    }
  }

	protected function register($parameters) {
    if(!isset($parameters['uname']) 
        || !isset($parameters['pwd']) 
        || !isset($parameters['pwd2'])) {
            return false;
		}
    
		if($parameters['pwd'] != $parameters['pwd2']) {
			return false;
		}
	
		// @TODO validate pwd and user name
	
		// Is username taken?
    
    $accounts = new User();
		$uname = $this->escape_string($parameters['uname']);
		$pwd = md5($parameters['pwd']);	
    
    $accounts->search("uname = '$uname'");

		if(count($accounts->data)) {
      return false;
    }

		// Insert user data into user table
    if($this->create(Array('uname' => $uname, 'pwd' => $pwd))) {
      return $this->login();
    }
    
	}

	public function logout() {
		unset($_SESSION['uname']);
		unset($_SESSION['pwd']);
	}
  
  protected function selectQuery($uname) {
    return "SELECT id, uname, pwd FROM user WHERE uname = '$uname'";
  }
}