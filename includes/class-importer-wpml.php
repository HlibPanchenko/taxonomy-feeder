<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Taxonomy_Feeder_Importer_WPML {

    public function run_import($sheet_url, $taxonomy_name, $lang_code, $overwrite_description, $import_meta) {
        global $wpdb;

        // ✅ allow HTML
        remove_filter('pre_term_description', 'wp_filter_kses');
        remove_filter('term_description', 'wp_kses_data');

        // Get terms for a specific language
        $terms_for_lang = $this->get_terms_for_language($taxonomy_name, $lang_code);

        // Parsing link to Google Sheets
        preg_match('/\/d\/([^\/]+)\//', $sheet_url, $id_match);
        preg_match('/gid=(\d+)/', $sheet_url, $gid_match);

        if (empty($id_match[1]) || empty($gid_match[1])) {
            return false;
        }

        $id  = $id_match[1];
        $gid = $gid_match[1];
        $url = "https://docs.google.com/spreadsheets/d/$id/gviz/tq?tqx=out:json&gid=$gid";

        $json = @file_get_contents($url);
        if ($json === false) return false;

        $jsonTrimmed = substr($json, 47, -2);
        $data = json_decode($jsonTrimmed, true);
        if (json_last_error() !== JSON_ERROR_NONE) return false;

        $rows = $data['table']['rows'] ?? [];
        $columns = $data['table']['cols'] ?? [];
        if (empty($rows)) return false;

        $headers = [];
        foreach ($columns as $index => $col) {
            $headers[] = !empty($col['label']) ? $col['label'] : "Column_$index";
        }

        $updated = 0;

        foreach ($rows as $row) {
            $rowData = [];
            foreach ($row['c'] as $index => $cell) {
                $rowData[$headers[$index]] = $cell['v'] ?? null;
            }

            $term_name        = trim($rowData['Column_0'] ?? '');
            $description      = trim($rowData['Column_4'] ?? '');
            $meta_title       = trim($rowData['Column_5'] ?? '');
            $meta_description = trim($rowData['Column_6'] ?? '');
            $term_title       = trim($rowData['Column_7'] ?? '');

            if (!$term_name) continue;

            // Looking for a name match only among the specified languages
            $matched_term_id = null;
            foreach ($terms_for_lang as $term_id => $term_info) {
                if (mb_strtolower($term_info['name']) === mb_strtolower($term_name)) {
                    $matched_term_id = $term_id;
                    break;
                }
            }

            if ($matched_term_id) {
                // Updating the translation for a specific language
                if ($overwrite_description) {
                    wp_update_term($matched_term_id, $taxonomy_name, ['description' => $description]);
                }

                if ($import_meta) {
                    if ($overwrite_description || !get_term_meta($matched_term_id, 'rank_math_title', true)) {
                        update_term_meta($matched_term_id, 'rank_math_title', $meta_title);
                    }
                    if ($overwrite_description || !get_term_meta($matched_term_id, 'rank_math_description', true)) {
                        update_term_meta($matched_term_id, 'rank_math_description', $meta_description);
                    }
                    if ($overwrite_description || !get_field('term_title', "term_$matched_term_id")) {
                        update_field('term_title', $term_title, "term_$matched_term_id");
                        update_field('term_description', $description, "term_$matched_term_id");
                    }
                }

                $updated++;
            }
        }

        return $updated > 0;
    }

    private function get_terms_for_language($taxonomy, $lang_code) {
        global $wpdb;
        $results = $wpdb->get_results($wpdb->prepare("
            SELECT 
                t.term_id,
                t.name,
                icl.language_code
            FROM {$wpdb->terms} t
            INNER JOIN {$wpdb->term_taxonomy} tx ON t.term_id = tx.term_id
            INNER JOIN {$wpdb->prefix}icl_translations icl ON icl.element_id = tx.term_taxonomy_id
            WHERE tx.taxonomy = %s
              AND icl.element_type = %s
              AND icl.language_code = %s
        ", $taxonomy, "tax_$taxonomy", $lang_code));

        $grouped = [];
        foreach ($results as $row) {
            $grouped[$row->term_id] = [
                'name' => $row->name,
                'lang' => $row->language_code,
            ];
        }
        return $grouped;
    }
}
