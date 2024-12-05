<?php
add_action( 'init', 'family_folio_register_custom_post_types' );

/**
 * Register the Family Members custom post type.
 */
function familyfolio_register_family_members_cpt() {
	$labels = [
		'name'               => 'Family Members',
		'singular_name'      => 'Family Member',
		'menu_name'          => 'Family Members',
		'name_admin_bar'     => 'Family Member',
		'add_new'            => 'Add New',
		'add_new_item'       => 'Add New Family Member',
		'new_item'           => 'New Family Member',
		'edit_item'          => 'Edit Family Member',
		'view_item'          => 'View Family Member',
		'all_items'          => 'All Family Members',
		'search_items'       => 'Search Family Members',
		'not_found'          => 'No family members found.',
		'not_found_in_trash' => 'No family members found in Trash.',
	];

	$args = [
		'labels'             => $labels,
		'public'             => false, // Not visible on the front end by default
		'show_ui'            => true,  // Show in admin interface
		'show_in_menu'       => 'familyfolio', // Set parent menu slug
		'capability_type'    => 'post',
		'supports'           => ['title', 'editor', 'thumbnail'], // Enable these features
		'menu_icon'          => 'dashicons-id', // Icon for the admin menu
	];

	register_post_type('family_member', $args);
}

function familyfolio_register_family_photos_cpt() {
	$labels = [
		'name'               => 'Family Photos',
		'singular_name'      => 'Family Photo',
		'menu_name'          => 'Family Photos',
		'name_admin_bar'     => 'Family Photo',
		'add_new'            => 'Add New',
		'add_new_item'       => 'Add New Family Photo',
		'new_item'           => 'New Family Photo',
		'edit_item'          => 'Edit Family Photo',
		'view_item'          => 'View Family Photo',
		'all_items'          => 'All Family Photos',
		'search_items'       => 'Search Family Photos',
		'not_found'          => 'No family photos found.',
		'not_found_in_trash' => 'No family photos found in Trash.',
	];

	$args = [
		'labels'             => $labels,
		'public'             => false, // Not visible on the front end by default
		'show_ui'            => true,  // Show in admin interface
		'show_in_menu'       => 'familyfolio', // Set parent menu slug
		'capability_type'    => 'post',
		'supports'           => ['title', 'editor', 'thumbnail'], // Enable these features
		'menu_icon'          => 'dashicons-format-image', // Icon for the admin menu
	];

	register_post_type('family_photo', $args);
}

function familyfolio_register_family_recipe_cpt() {
	$labels = [
		'name'               => 'Family Recipes',
		'singular_name'      => 'Family Recipe',
		'menu_name'          => 'Family Recipes',
		'name_admin_bar'     => 'Family Recipe',
		'add_new'            => 'Add New',
		'add_new_item'       => 'Add New Family Recipe',
		'new_item'           => 'New Family Recipe',
		'edit_item'          => 'Edit Family Recipe',
		'view_item'          => 'View Family Recipe',
		'all_items'          => 'All Family Recipes',
		'search_items'       => 'Search Family Recipes',
		'not_found'          => 'No family recipes found.',
		'not_found_in_trash' => 'No family recipes found in Trash.',
	];

	$args = [
		'labels'             => $labels,
		'public'             => true, // Visible on the front end
		'show_ui'            => true,  // Show in admin interface
		'show_in_menu'       => 'familyfolio', // Set parent menu slug
		'capability_type'    => 'post',
		'supports'           => ['title', 'editor', 'custom-fields', 'thumbnail'], // Enable these features
		'menu_icon'          => 'dashicons-carrot', // Icon for the admin menu
	];

	register_post_type('family_recipe', $args);
}

/**
 * Register custom post types.
 */

function family_folio_register_custom_post_types() {
	familyfolio_register_family_members_cpt();
	familyfolio_register_family_photos_cpt();
	familyfolio_register_family_recipe_cpt();
}
