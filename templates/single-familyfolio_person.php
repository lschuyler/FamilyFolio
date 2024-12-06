<?php
$first_name  = get_post_meta( get_the_ID(), '_gedcom_first_name', true );
$last_name   = get_post_meta( get_the_ID(), '_gedcom_last_name', true );
$maiden_name = get_post_meta( get_the_ID(), '_gedcom_maiden_name', true );

echo '<p>Name: ' . esc_html( $first_name ) . ' ' . esc_html( $last_name ) . '</p>';
if ( ! empty( $maiden_name ) ) {
	echo '<p>Maiden Name: ' . esc_html( $maiden_name ) . '</p>';
}
