<?php
/**
 * Integrácia s Elementorom: vlastné widgety, kategória, umiestnenia pre Elementor Pro.
 *
 * @package Ascensio
 */

defined( 'ABSPATH' ) || exit;

add_action( 'elementor/elements/categories_registered', 'asc_elementor_category' );
/**
 * Kategória „Ascensio“ v paneli widgetov.
 *
 * @param \Elementor\Elements_Manager $manager Správca.
 */
function asc_elementor_category( $manager ) {
	$manager->add_category(
		'ascensio',
		array(
			'title' => __( 'Ascensio', 'ascensio' ),
			'icon'  => 'eicon-person',
		)
	);
}

add_action( 'elementor/widgets/register', 'asc_elementor_widgets' );
/**
 * Registrácia widgetov.
 *
 * @param \Elementor\Widgets_Manager $widgets_manager Správca.
 */
function asc_elementor_widgets( $widgets_manager ) {
	require_once ASC_DIR . '/inc/widgets/class-asc-jobs-widget.php';
	require_once ASC_DIR . '/inc/widgets/class-asc-apply-widget.php';
	$widgets_manager->register( new Asc_Jobs_Widget() );
	$widgets_manager->register( new Asc_Apply_Widget() );
}

add_action( 'elementor/theme/register_locations', 'asc_elementor_locations' );
/**
 * Elementor Pro: hlavičku a pätičku je možné nahradiť v Theme Builderi.
 *
 * @param \ElementorPro\Modules\ThemeBuilder\Classes\Locations_Manager $manager Správca.
 */
function asc_elementor_locations( $manager ) {
	$manager->register_core_location( 'header' );
	$manager->register_core_location( 'footer' );
}

/**
 * Vykreslí hlavičku z Elementor Pro / Header Footer Elementor, ak je nastavená.
 *
 * @param string $location header|footer.
 * @return bool True, ak ju vykreslil plugin.
 */
function asc_builder_location( $location ) {
	if ( function_exists( 'elementor_theme_do_location' ) && elementor_theme_do_location( $location ) ) {
		return true;
	}
	if ( 'header' === $location && function_exists( 'hfe_header_enabled' ) && hfe_header_enabled() ) {
		hfe_render_header();
		return true;
	}
	if ( 'footer' === $location && function_exists( 'hfe_footer_enabled' ) && hfe_footer_enabled() ) {
		hfe_render_footer();
		return true;
	}
	return false;
}

add_action( 'elementor/editor/after_enqueue_styles', 'asc_elementor_editor_styles' );
/**
 * Jemné doladenie panelu editora (ikona kategórie).
 */
function asc_elementor_editor_styles() {
	$css = '.elementor-panel .elementor-element .icon .eicon-person,.elementor-panel .elementor-element .icon .eicon-posts-grid,.elementor-panel .elementor-element .icon .eicon-form-horizontal{color:#C9A04E}';
	wp_add_inline_style( 'elementor-editor', $css );
}
