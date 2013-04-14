<?php

/*
 * Database connectivity
 * 
 * Requires the file api/settings.php to look something like this
 * 
 * define('db_host', 'localhost');
 * define('db_username', '');
 * define('db_pwd', '');
 * define('db_table', 'kvittobunten');
 * 
 */

require_once('api/settings.php');

class DB extends mysqli {
  
  function __construct() {
    // Connect to mysql database.
    return parent::__construct(db_host, db_username, db_pwd, db_table);
  }
  
  
} 