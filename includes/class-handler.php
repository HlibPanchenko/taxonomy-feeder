<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Taxonomy_Feeder_Handler {

    public function __construct() {
        add_action('admin_post_tf_import', [$this, 'handle_import']);
    }

    public function handle_import() {
        if ( ! current_user_can('manage_options') ) wp_die('Access denied');

        $sheet_url   = sanitize_text_field($_POST['sheet_url'] ?? '');
        $taxonomy    = sanitize_text_field($_POST['taxonomy_name'] ?? '');
        $overwrite   = !empty($_POST['overwrite']);
        $import_meta = !empty($_POST['import_meta']);

        if (empty($sheet_url) || empty($taxonomy)) {
            wp_redirect(add_query_arg('status', 'error', wp_get_referer()));
            exit;
        }

        $importer = new Taxonomy_Feeder_Importer();
        $success  = $importer->run_import($sheet_url, $taxonomy, $overwrite, $import_meta);

        $status = $success ? 'success' : 'fail';
        wp_redirect(add_query_arg('status', $status, wp_get_referer()));
        exit;
    }
}
