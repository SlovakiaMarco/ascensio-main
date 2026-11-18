<?php
/**
 * Základné nastavenie témy: podpora funkcií, menu, štýly a skripty.
 *
 * @package Ascensio
 */

defined( 'ABSPATH' ) || exit;

add_action( 'after_setup_theme', 'asc_theme_setup' );
/**
 * Podpora funkcií WordPressu.
 */
function asc_theme_setup() {
	load_theme_textdomain( 'ascensio', ASC_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 72,
			'width'       => 380,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);
	// Podpora pluginu „Elementor Header & Footer Builder“ (bezplatná alternatíva k Elementor Pro).
	add_theme_support( 'header-footer-elementor' );

	add_image_size( 'asc-card', 720, 480, true );

	register_nav_menus(
		array(
			'primary' => __( 'Hlavné menu', 'ascensio' ),
			'topbar'  => __( 'Horná lišta (jazyky pre firmy)', 'ascensio' ),
			'footer'  => __( 'Pätička – odkazy', 'ascensio' ),
			'legal'   => __( 'Pätička – právne odkazy', 'ascensio' ),
		)
	);
}

add_action( 'after_setup_theme', 'asc_content_width', 0 );
/**
 * Šírka obsahu pre vložené médiá.
 */
function asc_content_width() {
	$GLOBALS['content_width'] = 1200;
}

add_action( 'wp_enqueue_scripts', 'asc_enqueue_assets' );
/**
 * Štýly a skripty.
 */
function asc_enqueue_assets() {
	// Písma z Bunny Fonts (náhrada Google Fonts bez prenosu IP adries do USA – vhodné pre GDPR).
	wp_enqueue_style( 'asc-fonts', 'https://fonts.bunny.net/css?family=archivo:600,700,800|inter:400,500,600,700&display=swap', array(), null ); // phpcs:ignore WordPress.WP.EnqueuedResourceParameters.MissingVersion

	$css_ver = ASC_VERSION . '.' . (string) filemtime( ASC_DIR . '/assets/css/main.css' );
	$js_ver  = ASC_VERSION . '.' . (string) filemtime( ASC_DIR . '/assets/js/main.js' );

	wp_enqueue_style( 'asc-main', ASC_URI . '/assets/css/main.css', array(), $css_ver );
	wp_enqueue_script( 'asc-main', ASC_URI . '/assets/js/main.js', array(), $js_ver, true );
	wp_localize_script(
		'asc-main',
		'ascensio',
		array(
			'ajaxUrl' => admin_url( 'admin-post.php' ),
			'i18n'    => array(
				'required' => __( 'Toto pole je povinné.', 'ascensio' ),
				'phone'    => __( 'Zadajte platné telefónne číslo.', 'ascensio' ),
				'email'    => __( 'Zadajte platný e-mail.', 'ascensio' ),
				'consent'  => __( 'Pre odoslanie potrebujeme váš súhlas.', 'ascensio' ),
				'error'    => __( 'Odoslanie sa nepodarilo. Skúste to znova alebo nám zavolajte.', 'ascensio' ),
				'sending'  => __( 'Odosielam…', 'ascensio' ),
			),
		)
	);
}

add_filter( 'wp_resource_hints', 'asc_resource_hints', 10, 2 );
/**
 * Preconnect pre písma.
 *
 * @param array  $urls URL.
 * @param string $relation Typ.
 */
function asc_resource_hints( $urls, $relation ) {
	if ( 'preconnect' === $relation ) {
		$urls[] = array(
			'href' => 'https://fonts.bunny.net',
			'crossorigin',
		);
	}
	return $urls;
}

add_filter( 'body_class', 'asc_body_classes' );
/**
 * Triedy na <body>.
 *
 * @param array $classes Triedy.
 */
function asc_body_classes( $classes ) {
	if ( asc_opt( 'asc_mobile_bar' ) ) {
		$classes[] = 'asc-has-mbar';
	}
	if ( get_option( 'asc_kit_synced' ) ) {
		$classes[] = 'asc-kit-synced';
	}
	if ( is_singular() && asc_is_elementor() ) {
		$classes[] = 'asc-elementor-page';
	}
	return $classes;
}

add_action( 'wp_head', 'asc_favicon_fallback', 2 );
/**
 * Ikona webu, ak nie je nastavená v Prispôsobiť > Identita webu.
 */
function asc_favicon_fallback() {
	if ( ! has_site_icon() ) {
		echo '<link rel="icon" type="image/svg+xml" href="' . esc_url( ASC_URI . '/assets/img/ascensio-ikona.svg' ) . '">' . "\n";
	}
	echo '<meta name="theme-color" content="#14263F">' . "\n";
}

add_action( 'wp_head', 'asc_meta_description', 3 );
/**
 * Meta popis stránky (ak nie je aktívny SEO plugin).
 */
function asc_meta_description() {
	if ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || class_exists( 'All_in_One_SEO_Pack' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) ) {
		return;
	}
	$desc = '';
	if ( is_singular() ) {
		$desc = get_post_meta( get_the_ID(), '_asc_seo', true );
		if ( ! $desc && has_excerpt() ) {
			$desc = get_the_excerpt();
		}
	} elseif ( is_front_page() || is_home() ) {
		$desc = get_bloginfo( 'description' );
	}
	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( wp_strip_all_tags( $desc ) ) . '">' . "\n";
	}
}

/**
 * Náhradné menu, kým nie je vytvorené v Vzhľad > Menu.
 *
 * @param array $args Argumenty wp_nav_menu (použije sa menu_class).
 */
function asc_menu_fallback( $args = array() ) {
	$items = array(
		'pracovne-ponuky' => __( 'Pracovné ponuky', 'ascensio' ),
		'o-nas'           => __( 'O nás', 'ascensio' ),
		'pre-firmy'       => __( 'Pre firmy', 'ascensio' ),
		'kontakt'         => __( 'Kontakt', 'ascensio' ),
	);
	$class = ( is_array( $args ) && ! empty( $args['menu_class'] ) ) ? $args['menu_class'] : 'asc-menu';
	echo '<ul class="' . esc_attr( $class ) . '">';
	foreach ( $items as $slug => $label ) {
		echo '<li><a href="' . esc_url( asc_page_url( $slug ) ) . '">' . esc_html( $label ) . '</a></li>';
	}
	echo '</ul>';
}

add_filter( 'excerpt_length', 'asc_excerpt_length' );
/**
 * Dĺžka úryvku.
 */
function asc_excerpt_length() {
	return 26;
}

add_filter( 'excerpt_more', 'asc_excerpt_more' );
/**
 * Koniec úryvku.
 */
function asc_excerpt_more() {
	return '…';
}

add_action( 'widgets_init', 'asc_widgets_init' );
/**
 * Bočný panel pre blog (voliteľné).
 */
function asc_widgets_init() {
	register_sidebar(
		array(
			'name'          => __( 'Bočný panel blogu', 'ascensio' ),
			'id'            => 'sidebar-1',
			'before_widget' => '<section id="%1$s" class="widget %2$s">',
			'after_widget'  => '</section>',
			'before_title'  => '<h2 class="widget-title">',
			'after_title'   => '</h2>',
		)
	);
}
