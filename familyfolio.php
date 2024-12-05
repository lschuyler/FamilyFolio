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
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// At the top of the file, after the plugin header
if ( ! defined( 'FAMILYFOLIO_VERSION' ) ) {
	define( 'FAMILYFOLIO_VERSION', '1.0' );
}
if ( ! defined( 'FAMILYFOLIO_PLUGIN_DIR' ) ) {
	define( 'FAMILYFOLIO_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
}

class FamilyFolio {
	// Singleton instance
	private static $instance = null;

	// Constructor
	private function __construct() {
		// Include necessary files
		require_once plugin_dir_path( __FILE__ ) . 'includes/admin-settings.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/custom-post-types.php';
		require_once plugin_dir_path( __FILE__ ) . 'includes/meta-boxes.php';

		// Initialize plugin
		add_action( 'init', array( $this, 'register_post_types' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate_plugin' ) );
		register_uninstall_hook( __FILE__, array( __CLASS__, 'uninstall_plugin' ) );

		// Hooks
		add_action( 'admin_menu', [ $this, 'add_admin_menu' ] );
		add_action( 'admin_init', 'familyfolio_register_settings' ); // Can call standalone function

    }

	public function add_admin_menu() {
		add_menu_page( 'FamilyFolio', 'FamilyFolio', 'manage_options', 'familyfolio', [
			$this,
			'main_page'
		], 'dashicons-networking', 6 );
		add_submenu_page( 'familyfolio', 'Settings', 'Settings', 'manage_options', 'familyfolio-settings', [
			$this,
			'settings_page'
		] );
	}

	public function main_page() {
		echo '<h1>Welcome to FamilyFolio</h1>';
	}

	public function settings_page() {
		echo '<h1>FamilyFolio Settings</h1>';
	}

	// Singleton pattern implementation
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	// Register custom post types
	public function register_post_types() {
		register_post_type( 'family_recipe', array(
			'labels'   => array(
				'name'          => __( 'Recipes', 'familyfolio' ),
				'singular_name' => __( 'Recipe', 'familyfolio' )
			),
			'public'   => true,
			'supports' => array( 'title', 'editor', 'custom-fields', 'thumbnail' ),
			'rewrite'  => array( 'slug' => 'recipes' ),
		) );

		register_post_type( 'family_photo', array(
			'labels'   => array( 'name' => 'Photos', 'singular_name' => 'Photo' ),
			'public'   => true,
			'supports' => array( 'title', 'editor', 'custom-fields', 'thumbnail' ),
			'rewrite'  => array( 'slug' => 'photos' ),
		) );
	}

	// Add meta boxes
	public function add_dublin_core_meta_boxes() {
		// Check user capabilities
		if ( ! current_user_can( 'edit_posts' ) ) {
			return;
		}

		add_meta_box(
			'dublin_core_meta',
			__( 'Dublin Core Metadata', 'familyfolio' ),
			array( $this, 'render_dublin_core_meta_box' ),
			[ 'family_photo', 'family_recipe' ],
			'normal',
			'high'
		);
	}

	function familyfolio_main_page() {
		?>
        <div class="wrap">
            <h1>Welcome to FamilyFolio</h1>
            <p>Start building your family archive here!</p>
        </div>
		<?php
	}

	function familyfolio_settings_page() {
		?>
        <div class="wrap">
            <h1>FamilyFolio Settings</h1>
            <form method="post" action="options.php">
				<?php
				// Output security fields for the registered setting
				settings_fields( 'familyfolio_settings_group' );
				// Output setting sections and their fields
				do_settings_sections( 'familyfolio-settings' );
				// Output the save settings button
				submit_button();
				?>
            </form>
        </div>
		<?php
	}

	/**
	 * Callback for the settings section.
	 */
	function familyfolio_settings_section_callback() {
		echo '<p>Adjust the general settings for FamilyFolio.</p>';
	}

	/**
	 * Callback for the example field.
	 */
	function familyfolio_example_field_callback() {
		$options = get_option( 'familyfolio_options' );
		?>
        <input type="text" name="familyfolio_options[example_field]"
               value="<?php echo esc_attr( isset( $options['example_field'] ) ? $options['example_field'] : '' ); ?>"/>
		<?php
	}

	// Render meta box
	public function render_dublin_core_meta_box( $post ) {
		wp_nonce_field( 'dublin_core_meta_box', 'dublin_core_meta_box_nonce' );
		?>
        <div class="dublin-core-fields">
            <p>
                <label for="dc_creator"><?php _e( 'Creator', 'familyfolio' ); ?></label>
                <input type="text" id="dc_creator" name="dc_creator" class="widefat"
                       value="<?php echo esc_attr( get_post_meta( $post->ID, 'dc_creator', true ) ); ?>"/>
            </p>
            <p>
                <label for="dc_date"><?php _e( 'Date', 'familyfolio' ); ?></label>
                <input type="date" id="dc_date" name="dc_date" class="widefat"
                       value="<?php echo esc_attr( get_post_meta( $post->ID, 'dc_date', true ) ); ?>"/>
            </p>
        </div>
		<?php
	}

	// Save meta box data
	public function save_dublin_core_metadata( $post_id ) {
		try {
			// Check if our nonce is set and verify it
			if ( ! isset( $_POST['dublin_core_meta_box_nonce'] ) ||
			     ! wp_verify_nonce( $_POST['dublin_core_meta_box_nonce'], 'dublin_core_meta_box' ) ) {
				return;
			}

			// Check autosave
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}

			// Check permissions
			if ( ! current_user_can( 'edit_post', $post_id ) ) {
				return;
			}

			// Save the data with error checking
			if ( array_key_exists( 'dc_creator', $_POST ) ) {
				$creator = sanitize_text_field( $_POST['dc_creator'] );
				if ( ! update_post_meta( $post_id, 'dc_creator', $creator ) ) {
					error_log( 'Failed to update dc_creator for post ' . $post_id );
				}
			}
			if ( array_key_exists( 'dc_date', $_POST ) ) {
				$date = sanitize_text_field( $_POST['dc_date'] );
				if ( ! update_post_meta( $post_id, 'dc_date', $date ) ) {
					error_log( 'Failed to update dc_date for post ' . $post_id );
				}
			}
		} catch ( Exception $e ) {
			error_log( 'Error saving Dublin Core metadata: ' . $e->getMessage() );
		}
	}

	// Add these methods
	public function deactivate_plugin() {
		// Clean up any temporary data
		flush_rewrite_rules();
	}

	public static function uninstall_plugin() {
		// Clean up all plugin data from database
		// Delete post meta, custom post types, etc.
	}

}

// Initialize the plugin
function familyfolio_init() {
	FamilyFolio::get_instance();
}

add_action( 'plugins_loaded', 'familyfolio_init' );
