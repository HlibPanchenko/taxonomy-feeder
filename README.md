# Taxonomy Feeder

**Taxonomy Feeder** is a WordPress plugin that imports taxonomy terms and metadata from Google Sheets into your WordPress site.  
It supports Rank Math SEO fields, ACF custom fields, and WPML multilingual sites.

## Features
- Import taxonomy terms from Google Sheets
- Supports HTML content in term descriptions
- Option to overwrite existing terms or only update empty fields
- Import Rank Math meta fields (`rank_math_title`, `rank_math_description`)
- Import ACF custom fields (e.g. `term_title`)
- WPML support:
    - Update terms for a specific language (e.g. `en`, `uk`, `ru`)
    - Handles multiple translations

## Installation
1. Upload the plugin to `/wp-content/plugins/taxonomy-feeder`
2. Activate it via **Plugins** in the WordPress admin panel
3. A new menu item **Taxonomy Feeder** will appear in the dashboard

## Usage
1. Go to **Taxonomy Feeder** in the WP admin
2. Enter the Google Sheet URL (use the "Publish to Web" or normal sheet link)
3. Enter the taxonomy name (e.g. `categories`, `options`, `area`)
4. (Optional) Check:
    - **Overwrite existing data** to update all terms
    - **Import meta data** to include Rank Math and ACF fields
    - **WPML Import** and specify the language code (e.g. `uk`) to update only that language
5. Click **Import Taxonomies**

## Google Sheet format
- **Column 0**: Term Name
- **Column 4**: Description (supports HTML)
- **Column 5**: Meta Title
- **Column 6**: Meta Description
- **Column 7**: ACF Term Title

> **Note:** Additional columns can be mapped in the importer class if needed.

## Requirements
- WordPress 5.8+
- PHP 7.4+
- Rank Math SEO (optional, for meta fields)
- Advanced Custom Fields (optional, for term_title)
- WPML (optional, for multilingual support)

## Development
- Version: **1.0.1**
- Author: Petr
- License: GPLv2 or later

## Changelog
### 1.0.1
- Added WPML language-specific import
- Added admin notice for missing language code
- Improved HTML support for descriptions
- Structured plugin files into separate classes

### 1.0.0
- Initial release with Google Sheets import and Rank Math/ACF support
