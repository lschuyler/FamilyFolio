<?php

add_action( 'add_meta_boxes', 'familyfolio_add_family_member_meta_box' );
add_action( 'save_post', 'familyfolio_save_family_member_metadata' );
add_filter( 'manage_family_member_posts_columns', 'familyfolio_add_family_member_columns' );
add_action( 'manage_family_member_posts_custom_column', 'familyfolio_render_family_member_columns', 10, 2 );

/**
 * Remove the default content editor.
 */
add_action( 'init', function () {
	remove_post_type_support( 'family_member', 'editor' );
} );

add_filter( 'enter_title_here', 'familyfolio_change_title_placeholder', 10, 2 );

/**
 * Change the placeholder text for the title field.
 *
 * @param string $placeholder The default placeholder text.
 * @param WP_Post $post The current post object.
 *
 * @return string The modified placeholder text.
 */
function familyfolio_change_title_placeholder( $placeholder, $post ) {
	if ( 'family_member' === $post->post_type ) {
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
function familyfolio_render_family_member_meta_box( $post ) {
	wp_nonce_field( 'familyfolio_save_meta_box', 'familyfolio_meta_box_nonce' );

	// Retrieve existing metadata
	$first_name  = get_post_meta( $post->ID, '_gedcom_first_name', true );
	$last_name   = get_post_meta( $post->ID, '_gedcom_last_name', true );
	$birth_date  = get_post_meta( $post->ID, '_gedcom_birth_date', true );
	$birth_place = get_post_meta( $post->ID, '_gedcom_birth_place', true );
	$death_date  = get_post_meta( $post->ID, '_gedcom_death_date', true );
	$death_place = get_post_meta( $post->ID, '_gedcom_death_place', true );
	$gender      = get_post_meta( $post->ID, '_gedcom_sex', true );

	// Fetch other family members for relationships
	$family_members = get_posts( [
		'post_type'   => 'family_member',
		'numberposts' => - 1,
		'orderby'     => 'title',
		'order'       => 'ASC',
		'exclude'     => [ $post->ID ],
	] );

	?>
    <p>
        <label for="gedcom_first_name">First Name:</label>
        <input type="text" id="gedcom_first_name" name="gedcom_first_name"
               value="<?php echo esc_attr( $first_name ); ?>"/>
    </p>
    <p>
        <label for="gedcom_last_name">Last Name:</label>
        <input type="text" id="gedcom_last_name" name="gedcom_last_name" value="<?php echo esc_attr( $last_name ); ?>"/>
    </p>
    <p>
        <label for="gedcom_birth_date">Birth Date:</label>
        <input type="date" id="gedcom_birth_date" name="gedcom_birth_date"
               value="<?php echo esc_attr( $birth_date ); ?>"/>
    </p>
    <p>
        <label for="gedcom_birth_place">Birth Place:</label>
        <input type="text" id="gedcom_birth_place" name="gedcom_birth_place"
               value="<?php echo esc_attr( $birth_place ); ?>"/>
    </p>
    <p>
        <label for="gedcom_death_date">Death Date:</label>
        <input type="date" id="gedcom_death_date" name="gedcom_death_date"
               value="<?php echo esc_attr( $death_date ); ?>"/>
    </p>
    <p>
        <label for="gedcom_death_place">Death Place:</label>
        <input type="text" id="gedcom_death_place" name="gedcom_death_place"
               value="<?php echo esc_attr( $death_place ); ?>"/>
    </p>
    <p>
        <label for="gedcom_gender">Gender:</label>
        <select id="gedcom_gender" name="gedcom_gender">
            <option value="M" <?php selected( $gender, 'M' ); ?>>Male</option>
            <option value="F" <?php selected( $gender, 'F' ); ?>>Female</option>
            <option value="U" <?php selected( $gender, 'U' ); ?>>Unknown</option>
        </select>
    </p>
	<?php
}

/**
 * Save metadata for Family Members.
 */
function familyfolio_save_family_member_metadata( $post_id ) {
	if ( ! isset( $_POST['familyfolio_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['familyfolio_meta_box_nonce'], 'familyfolio_save_meta_box' ) ) {
		return;
	}

	$fields = [
		'_gedcom_first_name'  => 'gedcom_first_name',
		'_gedcom_last_name'   => 'gedcom_last_name',
		'_gedcom_birth_date'  => 'gedcom_birth_date',
		'_gedcom_birth_place' => 'gedcom_birth_place',
		'_gedcom_death_date'  => 'gedcom_death_date',
		'_gedcom_death_place' => 'gedcom_death_place',
		'_gedcom_sex'         => 'gedcom_gender',
	];

	foreach ( $fields as $meta_key => $input_name ) {
		if ( isset( $_POST[ $input_name ] ) ) {
			update_post_meta( $post_id, $meta_key, sanitize_text_field( $_POST[ $input_name ] ) );
		}
	}
}

/**
 * Add custom columns for Family Members.
 */
function familyfolio_add_family_member_columns( $columns ) {
	$columns['dc_date']     = 'Birthdate';
	$columns['dc_relation'] = 'Relationship';

	return $columns;
}

/**
 * Render custom columns for Family Members.
 */
function familyfolio_render_family_member_columns( $column, $post_id ) {
	switch ( $column ) {
		case 'dc_date':
			echo esc_html( get_post_meta( $post_id, '_dc_date', true ) );
			break;
		case 'dc_relation':
			echo esc_html( get_post_meta( $post_id, '_dc_relation', true ) );
			break;
	}
}

add_filter( 'gettext', 'familyfolio_change_publish_button', 10, 2 );

/**
 * Change the "Publish" button text to "Save Person" for Family Members post type.
 *
 * @param string $translated_text The translated text.
 * @param string $text The original text.
 *
 * @return string Modified text for the button.
 */
function familyfolio_change_publish_button( $translated_text, $text ) {
	global $post;

	// Check if the current post type is 'family_member'
	if ( isset( $post->post_type ) && 'family_member' === $post->post_type ) {
		if ( $text === 'Publish' ) {
			return 'Save Person';
		}
		if ( $text === 'Update' ) {
			return 'Save Changes';
		}
	}

	return $translated_text;
}

add_action( 'post_submitbox_misc_actions', 'familyfolio_remove_schedule_option' );
add_filter( 'display_post_states', 'familyfolio_hide_future_status', 10, 2 );

/**
 * Remove the ability to publish later for Family Members post type.
 */
function familyfolio_remove_schedule_option() {
	global $post;

	// Only apply for Family Member post type
	if ( 'family_member' === $post->post_type ) {
		?>
        <style>
            #misc-publishing-actions #visibility,
            #misc-publishing-actions .misc-pub-post-status,
            #misc-publishing-actions .misc-pub-visibility,
            #misc-publishing-actions .misc-pub-curtime {
                display: none;
            }
        </style>
		<?php
	}
}

/**
 * Hide future status for Family Members.
 *
 * @param array $post_states Array of post display states.
 * @param WP_Post $post The current post object.
 *
 * @return array Modified post states.
 */
function familyfolio_hide_future_status( $post_states, $post ) {
	if ( 'family_member' === $post->post_type && isset( $post_states['future'] ) ) {
		unset( $post_states['future'] );
	}

	return $post_states;
}

add_action( 'do_meta_boxes', 'familyfolio_rename_featured_image' );
add_filter( 'post_type_labels_family_member', 'familyfolio_update_family_member_labels' );

/**
 * Rename Featured Image to Profile Image on Family Member pages.
 */
function familyfolio_rename_featured_image() {
	global $post_type;

	if ( 'family_member' === $post_type ) {
		remove_meta_box( 'postimagediv', 'family_member', 'side' );
		add_meta_box(
			'postimagediv',
			__( 'Profile Image' ),
			'post_thumbnail_meta_box',
			'family_member',
			'side',
			'low'
		);
	}
}

/**
 * Update Family Member post type labels.
 *
 * @param object $labels The labels object for the post type.
 *
 * @return object Modified labels object.
 */
function familyfolio_update_family_member_labels( $labels ) {
	$labels->featured_image        = __( 'Profile Image' );
	$labels->set_featured_image    = __( 'Set Profile Image' );
	$labels->remove_featured_image = __( 'Remove Profile Image' );
	$labels->use_featured_image    = __( 'Use as Profile Image' );

	return $labels;
}

// RELATIONSHIPS

add_action( 'add_meta_boxes', 'familyfolio_add_relationship_meta_box' );
add_action( 'save_post', 'familyfolio_save_relationship_metadata' );

/**
 * Add a meta box for managing relationships.
 */
function familyfolio_add_relationship_meta_box() {
	add_meta_box(
		'family_member_relationships',
		'Relationships',
		'familyfolio_render_relationship_meta_box',
		'family_member',
		'normal',
		'high'
	);
}

/**
 * Render the relationship management meta box.
 */
function familyfolio_render_relationship_meta_box($post) {
	wp_nonce_field('familyfolio_save_relationship_meta_box', 'familyfolio_relationship_meta_box_nonce');

	// Retrieve existing relationships
	$father_id = get_post_meta($post->ID, '_gedcom_father', true);
	$mother_id = get_post_meta($post->ID, '_gedcom_mother', true);
	$spouse_ids = get_post_meta($post->ID, '_gedcom_fams', true); // Spouse IDs

	// Fetch all family members except the current one
	$family_members = get_posts([
		'post_type'   => 'family_member',
		'numberposts' => -1,
		'orderby'     => 'title',
		'order'       => 'ASC',
		'exclude'     => [$post->ID],
	]);

	if (empty($family_members)) {
		echo '<p><strong>No other family members added yet.</strong></p>';
		echo '<p>Add at least one more person to start building relationships.</p>';
		return;
	}

	?>
    <p>
        <label for="familyfolio_father">Father:</label>
        <select id="familyfolio_father" name="familyfolio_father">
            <option value="">Select a Father</option>
			<?php foreach ($family_members as $member) : ?>
                <option value="<?php echo esc_attr($member->ID); ?>" <?php selected($father_id, $member->ID); ?>>
					<?php echo esc_html($member->post_title); ?>
                </option>
			<?php endforeach; ?>
        </select>
    </p>
    <p>
        <label for="familyfolio_mother">Mother:</label>
        <select id="familyfolio_mother" name="familyfolio_mother">
            <option value="">Select a Mother</option>
			<?php foreach ($family_members as $member) : ?>
                <option value="<?php echo esc_attr($member->ID); ?>" <?php selected($mother_id, $member->ID); ?>>
					<?php echo esc_html($member->post_title); ?>
                </option>
			<?php endforeach; ?>
        </select>
    </p>
    <p>
        <label for="familyfolio_spouses">Spouses:</label>
        <select id="familyfolio_spouses" name="familyfolio_spouses[]" multiple>
            <option value="">Select a Spouse</option>
			<?php foreach ($family_members as $member) : ?>
                <option value="<?php echo esc_attr($member->ID); ?>" <?php echo in_array($member->ID, (array)$spouse_ids) ? 'selected' : ''; ?>>
					<?php echo esc_html($member->post_title); ?>
                </option>
			<?php endforeach; ?>
        </select>
        <small>Hold down Ctrl (Windows) or Cmd (Mac) to select multiple spouses.</small>
    </p>
	<?php
}

/**
 * Save relationships for family members.
 */
function familyfolio_save_relationship_metadata($post_id) {
	if (!isset($_POST['familyfolio_relationship_meta_box_nonce']) || !wp_verify_nonce($_POST['familyfolio_relationship_meta_box_nonce'], 'familyfolio_save_relationship_meta_box')) {
		return;
	}

	// Save Father
	if (isset($_POST['familyfolio_father'])) {
		update_post_meta($post_id, '_gedcom_father', sanitize_text_field($_POST['familyfolio_father']));
	}

	// Save Mother
	if (isset($_POST['familyfolio_mother'])) {
		update_post_meta($post_id, '_gedcom_mother', sanitize_text_field($_POST['familyfolio_mother']));
	}

	// Save Spouses
	if (isset($_POST['familyfolio_spouses'])) {
		$spouse_ids = array_map('intval', $_POST['familyfolio_spouses']);
		update_post_meta($post_id, '_gedcom_fams', $spouse_ids);

		// Add reciprocal spouse relationships
		foreach ($spouse_ids as $spouse_id) {
			$spouses = get_post_meta($spouse_id, '_gedcom_fams', true) ?: [];
			if (!in_array($post_id, $spouses)) {
				$spouses[] = $post_id;
				update_post_meta($spouse_id, '_gedcom_fams', $spouses);
			}
		}
	}
}

