<?php

/*
 * Base class for models
 */

abstract class Model extends DB {
  public $data = Array ( Array() );
  public $vars = Array ();
  
  function __construct($user = NULL) {
    // If a user object i provided - connect it to the models user
    if(is_object($user)) {
      $this->user = &$user;
      $this->uid = &$user->id;
    }
    // Database connectivity
    return parent::__construct();
  }
  
  /*
   * Public (CRUD) functions
   */
  
  // Fetches data from id
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
  
  // Fetches data from a where clause
  public function search($whereClause = "") {
    $result = $this->query($this->searchQuery($whereClause));
    $this->data = $this->getRows($result);
  }
  
  // Inserts data 
  public function create($vars) {
    foreach(array_keys($this->vars) as $varName) {
      if(isset($vars[$varName])) {
        $save[$varName] = $this->escape_string($vars[$varName]);
      } 
    }
    return json_encode($this->insert(array($save)));
  }
  
  // Deletes data
  public function delete($id) {
    $this->query($this->deleteQuery($id));
  }
  
  // Updates data (might not be needed)
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
  
  // Makes a prepares statement replace query to the database
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
  
  // Formats result into data
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
   * Standard queries - should mostly be overridden by model classes.
   */

  protected function selectQuery($id) {
    return "SELECT * FROM $this->table WHERE user = '{$this->uid}' = $id";
  }
  
  protected function searchQuery($whereClause) {
    return "SELECT * FROM $this->table WHERE user = '{$this->uid}' $whereClause";
  }
}

