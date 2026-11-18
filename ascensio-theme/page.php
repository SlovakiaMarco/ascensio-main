<?php
/**
 * Stránka. Ak je postavená v Elementore, obsah ide na celú šírku bez obalov.
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
				<h1><?php the_title(); ?></h1>
			</div>
		</header>
		<div class="asc-prose entry-content">
			<?php
			the_content();
			wp_link_pages();
			?>
		</div>
		<?php
	endif;
endwhile;

get_footer();
