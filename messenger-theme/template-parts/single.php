<?php
/**
 * The template for displaying singular post-types: posts, pages and user-defined custom post types.
 *
 * @package MessengerTheme
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

$site_name = get_bloginfo( 'name' );
$tagline   = get_bloginfo( 'description', 'display' );

while ( have_posts() ) :
	the_post();
	?>

<main id="content" <?php post_class( 'site-main mx-auto' ); ?>>

	<div class="page-content w-[320px] sm:w-[532px] md:w-[804px] lg:w-[960px] mx-auto xl:mx-[32px]">
		<?php if ( apply_filters( 'messenger_theme_page_title', true ) ) : ?>
			<div class="page-header">
				<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
			</div>
		<?php endif; ?>
		<?php the_content(); ?>

		<?php wp_link_pages(); ?>

		<?php if ( has_tag() ) : ?>
		<div class="post-tags">
			<?php the_tags( '<span class="tag-links">' . esc_html__( 'Tagged ', 'messenger-theme' ), ', ', '</span>' ); ?>
		</div>
		<?php endif; ?>
	</div>

	<div class="site-header w-[320px] sm:w-[532px] md:w-[804px] lg:w-[960px] mx-auto xl:mx-[32px] text-center">
		<div class="site-branding">
		<?php
		if ( has_custom_logo() ) {
			the_custom_logo();
		} elseif ( $site_name ) {
			?>
			<div class="site-title m-2 w-full">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>" title="<?php echo esc_attr__( 'Home', 'messenger-theme' ); ?>" rel="home">
					<?php echo esc_html( $site_name ); ?>
				</a>
			</div>
			<?php if ( $tagline ) : ?>
			<p class="site-description">
				<?php echo esc_html( $tagline ); ?>
			</p>
			<?php endif; ?>
		<?php } ?>
		</div>
	</div>

	<?php comments_template(); ?>

</main>

	<?php
endwhile;
