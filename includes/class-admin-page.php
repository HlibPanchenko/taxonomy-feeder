<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Taxonomy_Feeder_Admin {

    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_page']);
        add_action('admin_notices', [$this, 'show_admin_notice']);
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
        ?>
        <div class="wrap">
            <h1>Taxonomy Feeder</h1>
            <form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
                <input type="hidden" name="action" value="tf_import">

                <p>
                    <label>Google Sheet URL:</label><br>
                    <input type="text" name="sheet_url" style="width:400px" placeholder="https://docs.google.com/spreadsheets/d/...">
                </p>

                <p>
                    <label>Taxonomy:</label><br>
                    <input type="text" name="taxonomy_name" placeholder="options">
                </p>

                <p>
                    <label>
                        <input type="checkbox" name="overwrite" value="1"> Overwrite existing data
                    </label>
                </p>

                <p>
                    <label>
                        <input type="checkbox" name="import_meta" value="1" checked> Import meta data
                    </label>
                </p>

                <p>
                    <label>
                        <input type="checkbox" name="multilang" value="1" id="multilang-checkbox"> Import for second language (WPML)
                    </label>
                </p>

                <p id="lang-field" style="display:none;">
                    <label>Language code:</label><br>
                    <input type="text" name="lang_code" placeholder="uk" style="width:80px">
                    <small>Example: en, uk, ru</small>
                </p>

                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const checkbox = document.getElementById('multilang-checkbox');
                        const langField = document.getElementById('lang-field');
                        checkbox.addEventListener('change', function() {
                            langField.style.display = checkbox.checked ? 'block' : 'none';
                        });
                    });
                </script>

                <?php submit_button('Import Taxonomies'); ?>
            </form>
        </div>
        <?php
    }

    public function show_admin_notice() {
        if (!current_user_can('manage_options')) return;

        if (!isset($_GET['status'])) return;

        switch ($_GET['status']) {
            case 'success':
                echo '<div class="notice notice-success is-dismissible"><p>✅ Taxonomies imported successfully!</p></div>';
                break;
            case 'fail':
                echo '<div class="notice notice-error is-dismissible"><p>❌ Import failed. Check Google Sheet URL and taxonomy name.</p></div>';
                break;
            case 'error':
                echo '<div class="notice notice-warning is-dismissible"><p>⚠️ Please fill in all fields.</p></div>';
                break;
            case 'lang_missing':
                echo '<div class="notice notice-warning is-dismissible"><p>⚠️ Language code is required for WPML import.</p></div>';
                break;
        }
    }
}
