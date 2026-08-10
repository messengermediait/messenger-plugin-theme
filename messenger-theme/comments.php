<?php
/**
 * The template for displaying the list of comments and the comment form.
 *
 * @package MessengerTheme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! post_type_supports( get_post_type(), 'comments' ) ) {
	return;
}

if ( ! have_comments() && ! comments_open() ) {
	return;
}

?>
<!-- no comments used here -->
