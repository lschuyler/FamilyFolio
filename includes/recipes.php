<?php
add_action( 'add_meta_boxes', 'familyfolio_add_recipe_instructions_meta_box' );

/**
 * Add an instructional meta box to the Family Recipe editor.
 */
function familyfolio_add_recipe_instructions_meta_box() {
	add_meta_box(
		'recipe_instructions',
		'How to Add a Recipe',
		'familyfolio_render_recipe_instructions_meta_box',
		'familyfolio_recipe',
		'side',
		'default'
	);
}

/**
 * Render the recipe instructions meta box.
 */
function familyfolio_render_recipe_instructions_meta_box() {
	echo '<p>Use the <strong>Recipe Block</strong> to add structured recipe content:</p>';
	echo '<ol>';
	echo '<li>Click the <strong>+</strong> icon in the editor.</li>';
	echo '<li>Search for <strong>Recipe</strong> and select the block.</li>';
	echo '<li>Fill in the fields for ingredients, instructions, and other details.</li>';
	echo '<li>Publish the recipe to share it with your family!</li>';
	echo '</ol>';
}

add_filter( 'use_block_editor_for_post_type', 'familyfolio_enable_block_editor_for_recipes', 10, 2 );

function familyfolio_enable_block_editor_for_recipes( $use_block_editor, $post_type ) {
	if ( 'familyfolio_recipe' === $post_type ) {
		return true; // Force the Block Editor
	}

	return $use_block_editor;
}

add_filter( 'default_content', 'familyfolio_recipe_default_content', 10, 2 );

/**
 * Preload the Recipe Block in the editor for Family Recipes.
 *
 * @param string $content The default post content.
 * @param WP_Post $post The current post object.
 *
 * @return string Modified post content.
 */
function familyfolio_recipe_default_content( $content, $post ) {
	// Only apply to the 'family_recipe' post type
	if ( 'familyfolio_recipe' === get_post_type( $post ) ) {
		$content = '<!-- wp:familyfolio/recipe -->
            <div class="wp-block-familyfolio-recipe">
                <h2 class="wp-block-recipe__title">Recipe Title</h2>
                <ul class="wp-block-recipe__ingredients">
                    <li>Ingredient 1</li>
                    <li>Ingredient 2</li>
                    <li>Ingredient 3</li>
                </ul>
                <ol class="wp-block-recipe__steps">
                    <li>Step 1</li>
                    <li>Step 2</li>
                    <li>Step 3</li>
                </ol>
            </div>
            <!-- /wp:familyfolio/recipe -->';
	}

	return $content;
}

add_action( 'enqueue_block_editor_assets', 'familyfolio_register_recipe_block' );

function familyfolio_register_recipe_block() {
	wp_enqueue_script(
		'familyfolio-recipe-block',
		plugin_dir_url( __FILE__ ) . 'js/recipe-block.js',
		[ 'wp-blocks', 'wp-element', 'wp-editor' ],
		filemtime( plugin_dir_path( __FILE__ ) . 'js/recipe-block.js' ),
		true // Load in footer
	);
}
