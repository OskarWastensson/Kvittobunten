<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

define('ERROR_USER_ACCESS', 'No access ');
define('ERROE_ILLEGAL_METHOD', 1);


class Router {
  public function __construct($requests) {
    // Start session and verify user
    session_start();
    $user = new User();
    $user->checkin();
    
    // Fetch method and see if it's supported
    $method = $_SERVER['REQUEST_METHOD'];
    if(array_key_exists($method, $requests)) {
      // Get request parameters
      if(isset($_GET['q'])) {
        $args = explode('/', $_GET['q']);
      } else {
        // Default view
        $args = Array ('pageLogin');
      }
      $routeId = array_shift($args);
      $args = array_merge($args, $this->parameters($method));
      // Activate object (model or view)
      $route = explode('/', $requests[$method][$routeId]);
      list($objectName, $method) = $route;
      $open = isset($route[2]);

      if($open || $user->id) {
        $object = new $objectName($user);
        // Call object method with an array of arguments
        print $object->$method($args);
      } else {
        error(ERROR_USER_ACCESS, $user);
      }
      
    } else {
      error(ERROR_ILLEGAL_METHOD);
    }
  }
  
  protected function parameters($method) {
    switch ($method) {
      case 'POST': 
        return $_POST;
        break;
      case 'PUT': 
        parse_str(file_get_contents("php://input"), $parameters);
        return $parameters;
        break;
      case 'GET':
              
        return Array('id' => isset($_GET['id']) ? $_GET['id'] : NULL);
      default:
        return Array();
    }
  }
}

function error($code, $dump = NULL) {
  print json_encode(Array ('error' => $code, 'dump' => $dump));
  die();
}