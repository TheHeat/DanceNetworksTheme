<?php
/**
 * Jetpack Compatibility File
 * See: http://jetpack.me/
 *
 * @package Dance Networks EU
 */

/**
 * Add theme support for Infinite Scroll.
 * See: http://jetpack.me/support/infinite-scroll/
 */
function dancemap_jetpack_setup() {
	add_theme_support( 'infinite-scroll', array(
		'container' => 'main',
		'footer'    => 'page',
	) );
}
add_action( 'after_setup_theme', 'dancemap_jetpack_setup' );
