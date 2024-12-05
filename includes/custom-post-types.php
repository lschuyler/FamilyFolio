<?php
add_action( 'init', 'familyfolio_register_family_members_cpt' );

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
