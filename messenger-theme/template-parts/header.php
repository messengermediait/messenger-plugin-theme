<?php
/**
 * The template for displaying header.
 *
 * @package MessengerTheme
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$header_nav_menu = wp_nav_menu( [
	'theme_location' => 'menu-1',
	'fallback_cb' => false,
	'container' => false,
	'echo' => false,
] );
?>

<header id="site-header" class="site-header xl:w-[400px]">

	<?php if ( $header_nav_menu ) : ?>
		<nav class="site-navigation" aria-label="<?php echo esc_attr__( 'Main menu', 'messenger-theme' ); ?>">
			<?php
			// PHPCS - escaped by WordPress with "wp_nav_menu"
			echo $header_nav_menu; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</nav>
	<?php endif; ?>
</header>
