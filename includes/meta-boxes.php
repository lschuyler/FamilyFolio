<?php

add_action( 'add_meta_boxes', 'familyfolio_add_family_member_meta_box' );
add_action( 'save_post', 'familyfolio_save_family_member_metadata' );
add_filter('manage_family_member_posts_columns', 'familyfolio_add_family_member_columns');
add_action('manage_family_member_posts_custom_column', 'familyfolio_render_family_member_columns', 10, 2);

/**
 * Remove the default content editor.
 */
add_action('init', function () {
	remove_post_type_support('family_member', 'editor');
});

add_filter('enter_title_here', 'familyfolio_change_title_placeholder', 10, 2);

/**
 * Change the placeholder text for the title field.
 *
 * @param string $placeholder The default placeholder text.
 * @param WP_Post $post The current post object.
 * @return string The modified placeholder text.
 */
function familyfolio_change_title_placeholder($placeholder, $post) {
	if ('family_member' === $post->post_type) {
		return 'Full Name';
	}
	return $placeholder;
}

/**
 * Add custom meta box for Family Members.
 */
function familyfolio_add_family_member_meta_box() {
	add_meta_box(
		'family_member_dublin_core',
		'Dublin Core Metadata',
		'familyfolio_render_family_member_meta_box',
		'family_member',
		'normal',
		'high'
	);
}

/**
 * Render the Dublin Core metadata fields.
 */
function familyfolio_render_family_member_meta_box($post) {
	wp_nonce_field('familyfolio_save_meta_box', 'familyfolio_meta_box_nonce');

	// Retrieve existing metadata
	$first_name = get_post_meta($post->ID, '_dc_first_name', true);
    $middle_name = get_post_meta($post->ID, '_dc_middle_name', true);
	$last_name = get_post_meta($post->ID, '_dc_last_name', true);
	$birth_date = get_post_meta($post->ID, '_dc_birth_date', true);
	$death_date = get_post_meta($post->ID, '_dc_death_date', true);
	$location = get_post_meta($post->ID, '_dc_location', true);
	$relationship = get_post_meta($post->ID, '_dc_relationship', true);

	// Fetch other family members for the relationship dropdown
	$family_members = get_posts([
		'post_type'   => 'family_member',
		'numberposts' => -1,
		'orderby'     => 'title',
		'order'       => 'ASC',
		'exclude'     => [$post->ID], // Exclude current member
	]);

	?>
    <p>
        <label for="dc_first_name">First Name:</label>
        <input type="text" id="dc_first_name" name="dc_first_name" value="<?php echo esc_attr($first_name); ?>" />
    </p>
    <p>
        <label for="dc_middle_name">Middle Name(s):</label>
        <input type="text" id="dc_middle_name" name="dc_middle_name" value="<?php echo esc_attr($middle_name); ?>" />
    </p>
    <p>
        <label for="dc_last_name">Last Name:</label>
        <input type="text" id="dc_last_name" name="dc_last_name" value="<?php echo esc_attr($last_name); ?>" />
    </p>
    <p>
        <label for="dc_birth_date">Birth Date:</label>
        <input type="date" id="dc_birth_date" name="dc_birth_date" value="<?php echo esc_attr($birth_date); ?>" />
    </p>
    <p>
        <label for="dc_death_date">Death Date:</label>
        <input type="date" id="dc_death_date" name="dc_death_date" value="<?php echo esc_attr($death_date); ?>" />
    </p>
    <p>
        <label for="dc_location">Location:</label>
        <input type="text" id="dc_location" name="dc_location" value="<?php echo esc_attr($location); ?>" />
    </p>
    <p>
        <label for="dc_relationship">Relationship:</label>
        <select id="dc_relationship" name="dc_relationship">
            <option value="">Select a Family Member</option>
			<?php foreach ($family_members as $member) : ?>
                <option value="<?php echo esc_attr($member->ID); ?>" <?php selected($relationship, $member->ID); ?>>
					<?php echo esc_html($member->post_title); ?>
                </option>
			<?php endforeach; ?>
        </select>
    </p>
	<?php
}

/**
 * Save Dublin Core metadata for Family Members.
 */
function familyfolio_save_family_member_metadata($post_id) {
	// Verify nonce
	if (!isset($_POST['familyfolio_meta_box_nonce']) || !wp_verify_nonce($_POST['familyfolio_meta_box_nonce'], 'familyfolio_save_meta_box')) {
		return;
	}

	// Save metadata
	$fields = [
		'_dc_first_name' => 'dc_first_name',
		'_dc_last_name'  => 'dc_last_name',
		'_dc_birth_date' => 'dc_birth_date',
		'_dc_death_date' => 'dc_death_date',
		'_dc_location'   => 'dc_location',
		'_dc_relationship' => 'dc_relationship',
	];

	foreach ($fields as $meta_key => $input_name) {
		if (isset($_POST[$input_name])) {
			update_post_meta($post_id, $meta_key, sanitize_text_field($_POST[$input_name]));
		}
	}
}

/**
 * Add custom columns for Family Members.
 */
function familyfolio_add_family_member_columns($columns) {
	$columns['dc_date'] = 'Birthdate';
	$columns['dc_relation'] = 'Relationship';
	return $columns;
}

/**
 * Render custom columns for Family Members.
 */
function familyfolio_render_family_member_columns($column, $post_id) {
	switch ($column) {
		case 'dc_date':
			echo esc_html(get_post_meta($post_id, '_dc_date', true));
			break;
		case 'dc_relation':
			echo esc_html(get_post_meta($post_id, '_dc_relation', true));
			break;
	}
}
