<?php

/*
  View base class
 */


define('AS_JSON', TRUE);
define('AS_ARRAY', FALSE);

abstract class View {
  // Attributes to set in extension
  protected $modelName = NULL;
  protected $components = array();
  protected $single = FALSE;
  
  // View attributes
  protected $template;
  protected $data = Array ();
  protected $static = false;
  
  /*
   * Overrrideable functions
   */
  protected function init() {
    // Runs once directly after construction
  }
  
  protected function preprocess($args) {
    // Runs before every call to show(), package() etc. 
  }
  
  /*
   * Constructor
   */
  public function __construct($user, $model = NULL) {
    require_once 'api/libraries/Twig/Autoloader.php';
    Twig_Autoloader::register();
    $loader = new Twig_Loader_String();
    $this->twig = new Twig_Environment($loader);
    $this->template = file_get_contents('api/templates/' . get_class($this) . '.twig');
    $this->user = $user;
    
    if($this->modelName) {
      if(is_object($model) && get_class($model) == $this->modelName) {
        // Use given model
        $this->model = $model;
        // Connect view data and model data
        $this->data = &$this->model->data;
      } else {
        // Start up a new model
        $this->model = new $this->modelName($user);
        // Connect view data and model data
        $this->data = &$this->model->data;
      }
    } else {
      // One row with an empty array for views 
      // without models.
      $this->data = Array( Array('nomodel' => TRUE) );
      reset($this->data);
      
    }
    
    $this->addSubViews();
    $this->init();
  }
  
  /*
   * Delivery functions
   */
  public function show($args = NULL) {
    $this->preprocess($args);
    if(!current($this->data)) {
      error(ERROR_DATA, get_class($this));
    }
    return $this->twig->render($this->template, current($this->data));
  }
  
  public function showAll($args = NULL) {
    do {
      $rows[] = $this->show();
    } while(next($this->data));
    return $rows;
  }
  
  public function showId($args) {
    if(isset($args["id"]) && is_object($this->model)) {
      $this->model->read($args);
    }
    reset($this->data);
    return $this->show($args);
  }
  
  public function package($args = NULL, $json = TRUE) {
    $this->preprocess($args);
    $package = Array (
      'html' => $this->show(),
      'template' => $this->template,
      'data' => $this->data);
    
    if($json) {
      return json_encode($package);
    } else {
      return $package;
    }
  }
  
  public function data($args = NULL, $json = TRUE) {
    $this->preprocess($args);
    $package = Array (
      'data' => $this->data);
    
    if($json) {
      return json_encode($package);
    } else {
      return $package;
    }
  } 
  
  public function template($json = TRUE) {
    $package = Array(
      'template' => $this->template);
    
    if($json) {
      return json_encode($package);
    } else {
      return $package;
    }
  }
  
  /*
   * Helper functions
   */
  
  protected function addValue($name, $value) {
    if($this->single) {
      $this->data[0][$name] = $value;
    } else {
      foreach($this->data as &$data) {
        $data[$name] = $value;
      }
      
     reset($this->data);
    }
  }
  
  protected function addSubViews() {
    foreach($this->components as $componentName) {
      $component = new $componentName($this->user);
      $this->addValue($componentName, $component->show());
    }
  }
}
