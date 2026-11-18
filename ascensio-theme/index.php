<?php
/**
 * Výpis článkov (blog, archívy, vyhľadávanie).
 *
 * @package Ascensio
 */

defined( 'ABSPATH' ) || exit;

get_header();

if ( is_search() ) {
	/* translators: %s hľadaný výraz */
	$asc_title = sprintf( __( 'Výsledky hľadania: %s', 'ascensio' ), get_search_query() );
} elseif ( is_archive() ) {
	$asc_title = wp_strip_all_tags( get_the_archive_title() );
} elseif ( is_home() && get_option( 'page_for_posts' ) ) {
	$asc_title = get_the_title( (int) get_option( 'page_for_posts' ) );
} else {
	$asc_title = __( 'Novinky', 'ascensio' );
}
?>
<header class="asc-pagehead">
	<div class="asc-container">
		<h1><?php echo esc_html( $asc_title ); ?></h1>
		<?php if ( is_archive() && get_the_archive_description() ) : ?>
			<?php the_archive_description( '<p>', '</p>' ); ?>
		<?php endif; ?>
	</div>
</header>

<div class="asc-container">
	<?php if ( have_posts() ) : ?>
		<div class="asc-posts">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<article <?php post_class( 'asc-post' ); ?>>
					<?php if ( has_post_thumbnail() ) : ?>
						<a class="asc-post__img" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true"><?php the_post_thumbnail( 'asc-card' ); ?></a>
					<?php endif; ?>
					<div class="asc-post__body">
						<p class="asc-post__meta"><?php echo esc_html( get_the_date() ); ?></p>
						<h2><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
						<?php the_excerpt(); ?>
					</div>
				</article>
			<?php endwhile; ?>
		</div>
		<div class="asc-pagination">
			<?php
			echo paginate_links( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				array(
					'prev_text' => '←',
					'next_text' => '→',
				)
			);
			?>
		</div>
	<?php else : ?>
		<div class="asc-404">
			<h2><?php esc_html_e( 'Nič sme nenašli', 'ascensio' ); ?></h2>
			<p><?php esc_html_e( 'Skúste iné slovo alebo si pozrite pracovné ponuky.', 'ascensio' ); ?></p>
			<a class="asc-btn" href="<?php echo esc_url( asc_page_url( 'pracovne-ponuky' ) ); ?>"><?php esc_html_e( 'Pracovné ponuky', 'ascensio' ); ?></a>
		</div>
	<?php endif; ?>
</div>
<?php
get_footer();
