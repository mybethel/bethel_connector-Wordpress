<?php

class Bethel_Podcast {
  
  public function __construct() {
    add_filter('query_vars', array($this, 'add_query_vars'));
    add_action('init', array($this, 'add_endpoint'));
    add_action('add_meta_boxes', array($this, 'add_meta_box'));
    add_action('save_post', array($this, 'podcast_meta_box_save'));
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
    header('Access-Control-Allow-Origin: *');
    echo json_encode(array('results' => $this->format_results($matches)));

    exit();
  }

  public function add_meta_box() {
    $options = get_option('bethel_settings');
    if (empty($options['bethel_podcast_embed_field']))
      return;

    add_meta_box('my-meta-box-id', 'Embedded Podcast Media', array($this, 'podcast_meta_box'), $options['bethel_podcast_embed_field'], 'normal', 'high' );
  }

  public function podcast_meta_box($post) {
    $options = get_option('bethel_settings');
    if (empty($options['bethel_ministry']))
      return;

    $podcasts = wp_remote_get('https://my.bethel.io/podcast/list/' . $options['bethel_ministry'] . '?episodes=true');
    $podcasts = json_decode(wp_remote_retrieve_body($podcasts));

    if (empty($podcasts)) {
?>
  <p>There was an error communicating with the Bethel platform. <a href="https://mybethel.zendesk.com/hc/en-us/requests/new" target="_blank">File a ticket.</a></p>
<?php
      return;
    }

    $values = get_post_custom($post->ID);
    $values = unserialize($values['embedded_podcast'][0]);
?>
  <table cellspacing="10">
    <thead>
      <tr>
        <th align="left">Podcast</th>
        <th align="left">Episode to Embed</th>
      </tr>
    </thead>
    <tbody>
<?php
    foreach ($podcasts as $podcast) {
?>
      <tr>
        <td><?php echo $podcast->name; ?></td>
        <td>
          <select name="embedded_podcast[<?php echo $podcast->id; ?>]">
            <option value="">select an episode...</option>
            <?php foreach ($podcast->media as $media): ?>
            <option value="<?php echo $media->id ?>" <?php selected($values[$podcast->id], $media->id); ?> /><?php echo $media->name; ?></option>
            <?php endforeach; ?>
          </select>
        </td>
      </tr>
<?php } ?>
    </tbody>
  </table>
<?php
  }

  public function podcast_meta_box_save($post_id) {
    if (!current_user_can( 'edit_post' ) || (defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE)) return;
    
    if(isset($_POST['embedded_podcast']))
      update_post_meta($post_id, 'embedded_podcast', $_POST['embedded_podcast']);
  }

  protected function format_results($matches) {
    $results = array();
    foreach ($matches as $match) {
      $results[] = $match->name . ' [id:' . $match->term_id . ']';
    }
    return $results;
  }

}
