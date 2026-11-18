<?php
/**
 * Stránka nenájdená.
 *
 * @package Ascensio
 */

defined( 'ABSPATH' ) || exit;

get_header();
?>
<div class="asc-container asc-404">
	<div class="asc-404__code">404</div>
	<h1><?php esc_html_e( 'Táto stránka neexistuje', 'ascensio' ); ?></h1>
	<p><?php esc_html_e( 'Možno bola ponuka už obsadená. Pozrite si aktuálne pracovné ponuky alebo nám zavolajte.', 'ascensio' ); ?></p>
	<p style="display:flex;gap:12px;justify-content:center;flex-wrap:wrap">
		<a class="asc-btn" href="<?php echo esc_url( asc_page_url( 'pracovne-ponuky' ) ); ?>"><?php esc_html_e( 'Pracovné ponuky', 'ascensio' ); ?></a>
		<a class="asc-btn asc-btn--outline" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Domov', 'ascensio' ); ?></a>
	</p>
</div>
<?php
get_footer();
