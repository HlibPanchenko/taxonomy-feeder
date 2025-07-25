<?php
/*
Plugin Name: Taxonomy Feeder
Description: Import taxonomy terms from Google Sheets into WordPress.
Version: 0.2
Author: Petr
*/

if ( ! defined( 'ABSPATH' ) ) exit;

require_once plugin_dir_path(__FILE__) . 'includes/class-admin-page.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-handler.php';
require_once plugin_dir_path(__FILE__) . 'includes/class-importer.php';

add_action('plugins_loaded', function() {
    new Taxonomy_Feeder_Admin();
    new Taxonomy_Feeder_Handler();
});

