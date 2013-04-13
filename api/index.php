<?php

/*
 * Starting point for the api - define autoloading and routes
 */

// Include settings file
require_once 'api/settings.php';

// Enable autoloading
spl_autoload_register('load');

function load($className) {
  $folders = Array ('core', 'view', 'model');
  foreach($folders as $folder) {
    $fileName = "api/$folder/$className.php";
    if(file_exists($fileName)) {
      include $fileName;
      return;
    }
  }
}

// Define routes
new Router(Array (
  'GET' => Array (
    'pageLogin' => 'pageLogin/show/open',
    'pageBudget' => 'pageBudget/show', 
    'pageAddTransaction' => 'pageAddTransaction/show', 
    'pageOverview' => 'pageOverview/show' ,
    'pageBrowse' => 'pageBrowse/show',
    'transaction' => 'rowtransaction/showId'
  ),
  'POST' => Array (
    'transaction' => 'transaction/create',
    'budget' => 'budget/create',
  ),
  'PUT' => Array (
    'transaction' => 'transaction/update',
    'budget' => 'budget/update',
    'login' => 'user/login/open',
    'register' => 'user/register/open'
  ),
  'DELETE' => Array (
    'transaction' => 'transaction/delete',
    'budget' => 'budget/delete'
  )
));