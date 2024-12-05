<?php

/**
 * Register the FamilyFolio admin menu.
 */
function familyfolio_add_admin_menu() {
	add_menu_page(
		'FamilyFolio',          // Page title
		'FamilyFolio',          // Menu title
		'manage_options',       // Capability
		'familyfolio',          // Menu slug
		'familyfolio_main_page', // Callback function
		'dashicons-networking', // Icon URL (dashicon for the menu)
		6                       // Position
	);

	// Add a settings submenu
	add_submenu_page(
		'familyfolio',          // Parent slug
		'FamilyFolio Settings', // Page title
		'Settings',             // Submenu title
		'manage_options',       // Capability
		'familyfolio-settings', // Menu slug
		'familyfolio_settings_page' // Callback function
	);
}

function familyfolio_settings_section_callback() {
	echo '<p>Adjust the general settings for FamilyFolio.</p>';
}

function familyfolio_example_field_callback() {
	$options = get_option('familyfolio_options');
	?>
	<input type="text" name="familyfolio_options[example_field]" value="<?php echo esc_attr($options['example_field'] ?? ''); ?>" />
	<?php
}

/**
 * Register FamilyFolio settings.
 */
function familyfolio_register_settings() {
	register_setting(
		'familyfolio_settings_group', // Option group
		'familyfolio_options'         // Option name
	);

	add_settings_section(
		'familyfolio_general_settings', // ID
		'General Settings',             // Title
		'familyfolio_settings_section_callback', // Callback
		'familyfolio-settings'          // Page
	);

	add_settings_field(
		'familyfolio_example_field',   // ID
		'Example Field',               // Title
		'familyfolio_example_field_callback', // Callback
		'familyfolio-settings',        // Page
		'familyfolio_general_settings' // Section
	);
}
