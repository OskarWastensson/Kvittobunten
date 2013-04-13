<?php

/*
 * Database connectivity
 */

require_once('api/settings.php');

class DB extends mysqli {
  
  function __construct() {
    // Connect to mysql database.
    return parent::__construct(db_host, db_username, db_pwd, db_table);
  }
  
  
} 