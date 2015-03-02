<?php

class Bethel_Admin {
  
  public function __construct() {
    add_action('admin_menu', array($this, 'add_admin_menu'));
    add_action('admin_init', array($this, 'settings_init'));
  }

  public function add_admin_menu() { 
    add_options_page(
      'Bethel Connector',
      'Bethel Connector',
      'manage_options',
      'bethel_connector',
      array($this, 'settings_page')
    );
  }

  public function settings_init() { 

    register_setting('bethel_settings', 'bethel_settings');

    add_settings_section(
      'bethel_general_section', 
      'General settings', 
      nil, 
      'bethel_settings'
    );

    add_settings_field( 
      'bethel_ministry', 
      'API Key', 
      array($this, 'ministry_render'), 
      'bethel_settings', 
      'bethel_general_section' 
    );

    add_settings_section(
      'bethel_podcast_section', 
      'Podcast Connector settings', 
      array($this, 'podcast_section'), 
      'bethel_settings'
    );

    add_settings_field( 
      'bethel_podcast_taxonomy', 
      'Sermon Series taxonomy', 
      array($this, 'podcast_taxonomy_render'), 
      'bethel_settings', 
      'bethel_podcast_section' 
    );

    add_settings_field( 
      'bethel_podcast_embed_field', 
      'Embedded podcast player', 
      array($this, 'podcast_embed_field_render'), 
      'bethel_settings', 
      'bethel_podcast_section' 
    );
  }

  public function podcast_section() {
    echo 'Connect your Bethel podcast with your website.';
  }

  public function ministry_render() { 
    $options = get_option('bethel_settings');

?>
  <input name="bethel_settings[bethel_ministry]" type="text" value="<?php echo $options['bethel_ministry']; ?>" />
<?php
  }

  public function podcast_taxonomy_render() { 

    $options = get_option('bethel_settings');
    $disabled_taxonomies = array('nav_menu', 'link_category', 'post_format');

?>
  <select name="bethel_settings[bethel_podcast_taxonomy]">
    <?php foreach (get_taxonomies() as $tax): if (in_array($tax, $disabled_taxonomies)) continue; ?>
    <option value="<?php echo $tax ?>" <?php selected($options['bethel_podcast_taxonomy'], $tax); ?> /><?php echo ucwords(str_replace('_', ' ', $tax)); ?></option>
    <?php endforeach; ?>
  </select>
  <p class="description">The taxonomy that will be used to autocomplete sermon series.</p>
<?php

  }

  public function podcast_embed_field_render() { 

    $options = get_option('bethel_settings');

?>
  <select name="bethel_settings[bethel_podcast_embed_field]">
    <?php foreach (get_post_types() as $post): ?>
    <option value="<?php echo $post ?>" <?php selected($options['bethel_podcast_embed_field'], $post); ?> /><?php echo ucwords(str_replace('_', ' ', $post)); ?></option>
    <?php endforeach; ?>
  </select>
  <p class="description">Show the embedded podcast field on this post type.</p>
<?php

  }

  public function settings_page() {

?>
  <div class="wrap">
    <form action='options.php' method='post'>
      <h2>Bethel Connector</h2>
    <?php
      settings_fields('bethel_settings');
      do_settings_sections('bethel_settings');
      submit_button();
    ?>
    </form>
  </div>
<?php

  }

}
