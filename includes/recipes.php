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

add_filter('use_block_editor_for_post_type', 'familyfolio_enable_block_editor_for_recipes', 10, 2);

function familyfolio_enable_block_editor_for_recipes($use_block_editor, $post_type) {
	if ('familyfolio_recipe' === $post_type) {
		return true; // Force the Block Editor
	}
	return $use_block_editor;
}
