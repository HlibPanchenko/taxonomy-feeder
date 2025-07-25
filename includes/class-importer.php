<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Taxonomy_Feeder_Importer {

    public function run_import($sheet_url, $taxonomy_name, $overwrite_description, $import_meta) {
        preg_match('/\/d\/([^\/]+)\//', $sheet_url, $id_match);
        preg_match('/gid=(\d+)/', $sheet_url, $gid_match);

        if (empty($id_match[1]) || empty($gid_match[1])) {
            return false;
        }

        $id  = $id_match[1];
        $gid = $gid_match[1];
        $url = "https://docs.google.com/spreadsheets/d/$id/gviz/tq?tqx=out:json&gid=$gid";

        $json = @file_get_contents($url);
        if ($json === false) {
            return false;
        }

        $jsonTrimmed = substr($json, 47, -2);
        $data = json_decode($jsonTrimmed, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            return false;
        }

        $rows = $data['table']['rows'] ?? [];
        $columns = $data['table']['cols'] ?? [];

        if (empty($rows)) {
            return false;
        }

        $headers = [];
        foreach ($columns as $index => $col) {
            $headers[] = !empty($col['label']) ? $col['label'] : "Column_$index";
        }

        foreach ($rows as $row) {
            $rowData = [];
            foreach ($row['c'] as $index => $cell) {
                $rowData[$headers[$index]] = $cell['v'] ?? null;
            }

            $term_name         = trim($rowData['Column_0'] ?? '');
            $new_description   = trim($rowData['Column_4'] ?? '');
            $new_meta_title    = trim($rowData['Column_5'] ?? '');
            $new_meta_description = trim($rowData['Column_6'] ?? '');
            $new_term_title    = trim($rowData['Column_7'] ?? '');

            if (!$term_name) continue;

            $existing_term = term_exists($term_name, $taxonomy_name);

            if (!$existing_term) {
                $new_term = wp_insert_term($term_name, $taxonomy_name, ['description' => $new_description]);
                if (!is_wp_error($new_term)) {
                    $term_id = $new_term['term_id'];
                    if ($import_meta) {
                        update_term_meta($term_id, 'rank_math_title', $new_meta_title);
                        update_term_meta($term_id, 'rank_math_description', $new_meta_description);
                        update_field('term_title', $new_term_title, "term_$term_id");
                    }
                }
            } else {
                $term_id = $existing_term['term_id'];
                if ($overwrite_description) {
                    wp_update_term($term_id, $taxonomy_name, ['description' => $new_description]);
                }
                if ($import_meta && $overwrite_description) {
                    update_term_meta($term_id, 'rank_math_title', $new_meta_title);
                    update_term_meta($term_id, 'rank_math_description', $new_meta_description);
                    update_field('term_title', $new_term_title, "term_$term_id");
                }
            }
        }

        return true;
    }
}
