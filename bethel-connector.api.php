<?php

class Bethel_API {
  
  public function __construct() {
    add_filter('query_vars', array($this, 'add_query_vars'), 0);
    add_action('parse_request', array($this, 'parse_request'), 0);
  } 
  
  public function add_query_vars($vars) {
    $vars[] = '__bethel';
    $vars[] = '__api';
    return $vars;
  }

  public function parse_request() {
    global $wp;

    if (!isset($wp->query_vars['__bethel']) || !isset($wp->query_vars['__api']))
      return;

    $class = 'Bethel_' . $wp->query_vars['__api'];
    $api = new $class();
    $api->respond();
  }

}
