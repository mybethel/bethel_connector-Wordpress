<?php
/*
Plugin Name: Bethel Connector
Plugin URI: http://www.getbethel.com/
Description: Connecting your Wordpress website to the Bethel platform.
*/

require_once 'bethel-connector.admin.php';
require_once 'bethel-connector.api.php';
require_once 'bethel-connector.podcast.php';

new Bethel_Admin();
new Bethel_API();
new Bethel_Podcast();
