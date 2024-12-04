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

function family_centered_register_post_types() {
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
add_action('init', 'family_centered_register_post_types');

function add_dublin_core_meta_boxes() {
	add_meta_box('dublin_core_meta', 'Dublin Core Metadata', 'render_dublin_core_meta_box', ['family_photo', 'family_recipe'], 'normal', 'high');
}
add_action('add_meta_boxes', 'add_dublin_core_meta_boxes');

function render_dublin_core_meta_box($post) {
	// Example fields
	?>
	<label for="dc_creator">Creator</label>
	<input type="text" id="dc_creator" name="dc_creator" value="<?php echo get_post_meta($post->ID, 'dc_creator', true); ?>" />
	<br />
	<label for="dc_date">Date</label>
	<input type="date" id="dc_date" name="dc_date" value="<?php echo get_post_meta($post->ID, 'dc_date', true); ?>" />
	<?php
}

function save_dublin_core_metadata($post_id) {
	if (array_key_exists('dc_creator', $_POST)) {
		update_post_meta($post_id, 'dc_creator', sanitize_text_field($_POST['dc_creator']));
	}
	if (array_key_exists('dc_date', $_POST)) {
		update_post_meta($post_id, 'dc_date', sanitize_text_field($_POST['dc_date']));
	}
}
add_action('save_post', 'save_dublin_core_metadata');
