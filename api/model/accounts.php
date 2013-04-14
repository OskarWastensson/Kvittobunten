<?php

/*
 * Accounts model
 */

class accounts extends Model {
  public $vars = array(
    'id', 
    'user',
    'account',
    'name'
  );
  
  protected $table = 'account';
  
  protected function searchQuery($whereClause = "") {
    return "SELECT * FROM $this->table WHERE user = '$this->uid' $whereClause";
  }
  
}