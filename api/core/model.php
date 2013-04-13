<?php

/*
 * 
 */

abstract class Model extends DB {
  public $data = Array ( Array() );
  public $vars = Array ();
  
  function __construct($user = NULL) {
    if(is_object($user)) {
      $this->user = &$user;
      $this->uid = &$user->id;
    }
    return parent::__construct();
  }
  
  
  /*
   * Public (CRUD) functions
   */
  public function read($args) {
    $id = $args['id'];
    $result = $this->query($this->selectQuery($id));
    if($result) { 
      $this->data = $this->getRows($result);
      return true;
    } else {
      error('DB', $this->selectQuery($id));
    }
  }
  
  public function search($whereClause = "") {
    $result = $this->query($this->searchQuery($whereClause));
    $this->data = $this->getRows($result);
  }
  
  public function create($vars) {
    foreach(array_keys($this->vars) as $varName) {
      if(isset($vars[$varName])) {
        $save[$varName] = $this->escape_string($vars[$varName]);
      } 
    }
    return json_encode($this->insert(array($save)));
  }
  
  public function delete($id) {
    $this->query($this->deleteQuery($id));
  }
  
  public function update($vars, $id) {
    foreach($vars as $varName) {
      if(in_array($varName, array_keys($this->vars))) {
        $save[$varName] = $this->escape_string($vars[$varName]);
      }
    }
    return json_encode($this->insert(array($save)));
  }
  
  /*
   * Helper functions
   */
  
  function insert($values) {
    // Always add logged in user to data
    foreach($values as &$row) {
      $row['user'] = $this->user->id;
    }
    reset($values);
    
    // Prepare statement
    $fields = implode(',', array_keys(current($values)));
    $questionmarks = implode(',', array_fill(0, count(current($values)), '?'));
    if(!$statement = $this->prepare(
        "REPLACE INTO $this->table ($fields) 
        VALUES ($questionmarks)"
    )) {
      return Array(
        'error' => $this->error,
        'errortype' => 'Prepare statement error'
      );
    }
    
    // Bind parameters with unknown parameter count
    
    $ref = new ReflectionClass('mysqli_stmt');
    $method = $ref->getMethod("bind_param");

    $bindArguments = array();
    $bindArguments[] = "";
    foreach(current($values) as $fieldName => $value) {
      $uid = uniqid();
      $$uid = NULL;
      $bindArguments[] = &$$uid;
      $bindArguments[0] .= $this->vars[$fieldName]; 
    }
    
    if(!$method->invokeArgs($statement, $bindArguments)) {
      return Array(
        'error' => $statement->error,
        'errorType' => 'Bind statement error'
      );
    }

    // Execute statement one time for each valuegroup
    foreach($values as $row) {
      $i = 1;
      foreach($row as $value) {
        $bindArguments[$i] = $value;
        $i++;
      }
      if(!$statement->execute()) {
        return Array (
          'error' => $statement->error,
          'errortype' => 'Execute statement error'
        );
      }
    }
    
    return Array('success' => 1, 'id' => $this->insert_id);
  }
  

  protected function getRows($result) {
    if($result) {
      $data = Array();
      while($row = $result->fetch_assoc()) {
        $data[] = $row;
      }
      return $data;
    } else {
      return Array ();
    }
  }
  
  /*
   * Standard quries
   */

  protected function selectQuery($id) {
    return "SELECT * FROM $this->table WHERE user = '{$this->uid}' = $id";
  }
  
  protected function searchQuery($whereClause) {
    return "SELECT * FROM $this->table WHERE user = '{$this->uid}' $whereClause";
  }
}

