<?php
/*
Plugin Name: FamilyFolio
Plugin URI: https://example.com
Description: A plugin to create a family-focused WordPress site.
Version: 1.0
Author: Lisa Schuyler
Author URI: https://lisa.blog
License: GPL2
*/

// Prevent direct access to this file
if (!defined('ABSPATH')) {
    exit;
}

class FamilyFolio {
    // Singleton instance
    private static $instance = null;

    // Constructor
    private function __construct() {
        // Initialize plugin
        add_action('init', array($this, 'register_post_types'));
        add_action('add_meta_boxes', array($this, 'add_dublin_core_meta_boxes'));
        add_action('save_post', array($this, 'save_dublin_core_metadata'));
    }

    // Singleton pattern implementation
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    // Register custom post types
    public function register_post_types() {
        register_post_type('family_recipe', array(
            'labels' => array('name' => 'Recipes', 'singular_name' => 'Recipe'),
            'public' => true,
            'supports' => array('title', 'editor', 'custom-fields', 'thumbnail'),
            'rewrite' => array('slug' => 'recipes'),
        ));
        
        register_post_type('family_photo', array(
            'labels' => array('name' => 'Photos', 'singular_name' => 'Photo'),
            'public' => true,
            'supports' => array('title', 'editor', 'custom-fields', 'thumbnail'),
            'rewrite' => array('slug' => 'photos'),
        ));
    }

    // Add meta boxes
    public function add_dublin_core_meta_boxes() {
        add_meta_box(
            'dublin_core_meta',
            'Dublin Core Metadata',
            array($this, 'render_dublin_core_meta_box'),
            ['family_photo', 'family_recipe'],
            'normal',
            'high'
        );
    }

    // Render meta box
    public function render_dublin_core_meta_box($post) {
        wp_nonce_field('dublin_core_meta_box', 'dublin_core_meta_box_nonce');
        ?>
        <label for="dc_creator">Creator</label>
        <input type="text" id="dc_creator" name="dc_creator" value="<?php echo esc_attr(get_post_meta($post->ID, 'dc_creator', true)); ?>" />
        <br />
        <label for="dc_date">Date</label>
        <input type="date" id="dc_date" name="dc_date" value="<?php echo esc_attr(get_post_meta($post->ID, 'dc_date', true)); ?>" />
        <?php
    }

    // Save meta box data
    public function save_dublin_core_metadata($post_id) {
        // Check if our nonce is set and verify it
        if (!isset($_POST['dublin_core_meta_box_nonce']) ||
            !wp_verify_nonce($_POST['dublin_core_meta_box_nonce'], 'dublin_core_meta_box')) {
            return;
        }

        // Check autosave
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }

        // Check permissions
        if (!current_user_can('edit_post', $post_id)) {
            return;
        }

        // Save the data
        if (array_key_exists('dc_creator', $_POST)) {
            update_post_meta($post_id, 'dc_creator', sanitize_text_field($_POST['dc_creator']));
        }
        if (array_key_exists('dc_date', $_POST)) {
            update_post_meta($post_id, 'dc_date', sanitize_text_field($_POST['dc_date']));
        }
    }
}

// Initialize the plugin
function familyfolio_init() {
    FamilyFolio::get_instance();
}
add_action('plugins_loaded', 'familyfolio_init');
