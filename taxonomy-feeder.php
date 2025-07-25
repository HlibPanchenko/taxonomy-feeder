<?php
/*
Plugin Name: Taxonomy Feeder
Description: Import taxonomy terms from Google Sheets into WordPress.
Version: 0.1
Author: Petr
*/

if ( ! defined( 'ABSPATH' ) ) exit;

require_once plugin_dir_path( __FILE__ ) . 'includes/class-taxonomy-feeder.php';

add_action('plugins_loaded', function() {
    new Taxonomy_Feeder();
});
