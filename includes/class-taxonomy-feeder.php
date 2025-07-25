<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Taxonomy_Feeder {

    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_page']);
    }

    public function add_admin_page() {
        add_menu_page(
            'Taxonomy Feeder',
            'Taxonomy Feeder',
            'manage_options',
            'taxonomy-feeder',
            [$this, 'render_admin_page'],
            'dashicons-database-import'
        );
    }

    public function render_admin_page() {
        echo '<div class="wrap"><h1>Taxonomy Feeder</h1><p>Import taxonomies from Google Sheets here.</p></div>';
    }
}
