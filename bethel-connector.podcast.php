<?php

class Bethel_Podcast {
  
  public function __construct() {
    add_filter('query_vars', array($this, 'add_query_vars'), 0);
    add_action('init', array($this, 'add_endpoint'), 0);
  } 
  
  public function add_query_vars($vars) {
    $vars[] = 'autocomplete';
    return $vars;
  }
  
  public function add_endpoint() {
    add_rewrite_rule('^bethel/podcaster/autocomplete/?(.+)?/?', 'index.php?__bethel=1&__api=Podcast&autocomplete=$matches[1]', 'top');
  }

  public function respond() {
    global $wp;
    $series = $wp->query_vars['autocomplete'];
    $options = get_option('bethel_settings');
    $matches = get_terms($options['bethel_podcast_taxonomy'], array('name__like' => $series));

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array('results' => $this->format_results($matches)));

    exit();
  }

  protected function format_results($matches) {
    $results = array();
    foreach ($matches as $match) {
      $results[] = $match->name . ' [id:' . $match->term_id . ']';
    }
    return $results;
  }

}
