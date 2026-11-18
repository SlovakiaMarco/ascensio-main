<?php
/**
 * Detail článku.
 *
 * @package Ascensio
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	if ( asc_is_elementor() ) :
		the_content();
	else :
		?>
		<header class="asc-pagehead">
			<div class="asc-container">
				<p class="asc-post__meta" style="color:rgba(255,255,255,.65);margin:0 0 8px"><?php echo esc_html( get_the_date() ); ?></p>
				<h1><?php the_title(); ?></h1>
			</div>
		</header>
		<article <?php post_class( 'asc-prose entry-content' ); ?>>
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'large' );
			}
			the_content();
			wp_link_pages();
			?>
		</article>
		<?php
	endif;
endwhile;

get_footer();
