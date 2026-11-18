<?php
/**
 * Pomocné funkcie: nastavenia, odkazy, ikony.
 *
 * @package Ascensio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Predvolené hodnoty nastavení (Vzhľad > Prispôsobiť > Ascensio).
 */
function asc_defaults() {
	return array(
		'asc_phone'        => '+421 917 970 708',
		'asc_email'        => 'office@ascensio.sk',
		'asc_whatsapp'     => '',
		'asc_address'      => "AS-SPOL, s.r.o.\nTovárenská 1471/7\n064 01 Stará Ľubovňa",
		'asc_company'      => 'AS-SPOL, s.r.o.',
		'asc_cta_text'     => 'Chcem prácu',
		'asc_cta_url'      => '',
		'asc_topbar'       => true,
		'asc_topbar_text'  => 'Práca v Nemecku a Francúzsku · ubytovanie, auto aj náradie zabezpečíme',
		'asc_facebook'     => 'https://www.facebook.com/ascensioholding7',
		'asc_instagram'    => 'https://www.instagram.com/ascensio_holding/',
		'asc_tiktok'       => 'https://www.tiktok.com/@ascensio_angels',
		'asc_linkedin'     => '',
		'asc_youtube'      => '',
		'asc_footer_text'  => 'Od roku 1998 pomáhame remeselníkom nájsť stabilnú prácu v zahraničí. Ubytovanie, doprava aj náradie sú u nás samozrejmosťou.',
		'asc_mobile_bar'   => true,
		'asc_apply_email'  => '',
		'asc_success_text' => 'Prihlášku sme prijali a čoskoro sa ti ozveme telefonicky.',
	);
}

/**
 * Hodnota nastavenia s predvolenou hodnotou.
 *
 * @param string $key Kľúč.
 * @return mixed
 */
function asc_opt( $key ) {
	$defaults = asc_defaults();
	return get_theme_mod( $key, isset( $defaults[ $key ] ) ? $defaults[ $key ] : '' );
}

/**
 * Odkaz tel: z telefónneho čísla.
 *
 * @param string $phone Číslo.
 */
function asc_tel( $phone = '' ) {
	$phone = $phone ? $phone : asc_opt( 'asc_phone' );
	return 'tel:' . preg_replace( '/[^0-9+]/', '', (string) $phone );
}

/**
 * Odkaz na WhatsApp.
 *
 * @param string $number Číslo.
 */
function asc_wa( $number ) {
	return 'https://wa.me/' . preg_replace( '/[^0-9]/', '', (string) $number );
}

/**
 * URL stránky podľa slugu (s rozumnou náhradou, ak stránka neexistuje).
 *
 * @param string $slug Slug stránky.
 */
function asc_page_url( $slug ) {
	$page = get_page_by_path( $slug );
	if ( $page && 'publish' === $page->post_status ) {
		return get_permalink( $page );
	}
	return home_url( '/' . $slug . '/' );
}

/**
 * Cieľ hlavného tlačidla v hlavičke.
 */
function asc_apply_url() {
	$url = asc_opt( 'asc_cta_url' );
	return $url ? $url : asc_page_url( 'prihlaska' );
}

/**
 * Sociálne siete z nastavení.
 *
 * @return array<string,string> sieť => URL
 */
function asc_socials() {
	$out = array();
	foreach ( array( 'facebook', 'instagram', 'tiktok', 'linkedin', 'youtube' ) as $net ) {
		$url = asc_opt( 'asc_' . $net );
		if ( $url ) {
			$out[ $net ] = $url;
		}
	}
	return $out;
}

/**
 * Inline SVG ikona (obrys, 24×24, farba podľa currentColor).
 *
 * @param string $name  Názov ikony.
 * @param string $class Doplnková trieda.
 * @return string
 */
function asc_icon( $name, $class = '' ) {
	static $icons = null;
	if ( null === $icons ) {
		$icons = array(
			'phone'     => '<path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.5 19.5 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1.9.4 1.8.7 2.7a2 2 0 0 1-.5 2.1L8 9.8a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4c.9.3 1.8.6 2.7.7a2 2 0 0 1 1.7 2z"/>',
			'mail'      => '<rect x="2.5" y="4.5" width="19" height="15" rx="2.5"/><path d="m3 6.5 9 6.5 9-6.5"/>',
			'pin'       => '<path d="M12 21.5s7-6.1 7-11.5a7 7 0 1 0-14 0c0 5.4 7 11.5 7 11.5z"/><circle cx="12" cy="10" r="2.6"/>',
			'arrow'     => '<path d="M5 12h14M13 6l6 6-6 6"/>',
			'check'     => '<path d="M20 6 9 17l-5-5"/>',
			'menu'      => '<path d="M4 7h16M4 12h16M4 17h16"/>',
			'close'     => '<path d="M6 6l12 12M18 6 6 18"/>',
			'euro'      => '<path d="M17.5 7.2A6.5 6.5 0 1 0 17.5 16.8M4.5 10.2h9M4.5 13.8h9"/>',
			'calendar'  => '<rect x="3" y="4.5" width="18" height="16.5" rx="2.5"/><path d="M16 2.5v4M8 2.5v4M3 10h18"/>',
			'home'      => '<path d="M3 10.8 12 3.5l9 7.3"/><path d="M5.5 9.2V20.5h13V9.2"/><path d="M10 20.5v-5.5h4v5.5"/>',
			'clock'     => '<circle cx="12" cy="12" r="9"/><path d="M12 7v5l3.2 2"/>',
			'briefcase' => '<rect x="3" y="7" width="18" height="13" rx="2.5"/><path d="M9 7V5.5A2 2 0 0 1 11 3.5h2a2 2 0 0 1 2 2V7M3 12.5h18"/>',
			'user'      => '<circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>',
			'lock'      => '<rect x="4.5" y="10.5" width="15" height="10.5" rx="2.5"/><path d="M8 10.5V7.5a4 4 0 0 1 8 0v3"/>',
			'globe'     => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3c2.5 2.7 3.8 5.7 3.8 9s-1.3 6.3-3.8 9c-2.5-2.7-3.8-5.7-3.8-9S9.5 5.7 12 3z"/>',
			'tool'      => '<path d="M14.7 6.3a4 4 0 0 0 5 5L21 12.6l-1.3 1.3-1.4-1.4-6.6 6.6a2 2 0 0 1-2.8-2.8l6.6-6.6-1.4-1.4L15.4 7z"/><path d="M14.7 6.3 12.5 4a4.5 4.5 0 0 0-6 .3"/>',
			'whatsapp'  => '<path d="M3.5 20.5l1.4-4.3A8.6 8.6 0 1 1 8 19.3z"/><path d="M9 8.6c0 3.4 3 6.4 6.4 6.4l1-1.5-2-1-1 .8c-1-.4-2.3-1.7-2.7-2.7l.8-1-1-2z"/>',
			'facebook'  => '<path d="M14.5 8.5h3V4.5h-3a4 4 0 0 0-4 4V11h-3v4h3v6.5h4V15h3l.8-4h-3.8V9a.5.5 0 0 1 .5-.5z"/>',
			'instagram' => '<rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><circle cx="17.5" cy="6.5" r=".9" fill="currentColor" stroke="none"/>',
			'tiktok'    => '<path d="M14 3v11.6a3.6 3.6 0 1 1-3.6-3.6"/><path d="M14 3c.4 2.8 2.3 4.7 5.2 5"/>',
			'linkedin'  => '<rect x="3" y="3" width="18" height="18" rx="3"/><path d="M8 10.5V17M8 7.2v.1M12 17v-3.8a2.2 2.2 0 0 1 4.4 0V17M12 10.5V17"/>',
			'youtube'   => '<rect x="2.5" y="5" width="19" height="14" rx="4"/><path d="m10 9 5 3-5 3z"/>',
			'up'        => '<path d="m6 15 6-6 6 6"/>',
		);
	}
	if ( ! isset( $icons[ $name ] ) ) {
		return '';
	}
	$cls = trim( 'asc-icon asc-icon--' . $name . ' ' . $class );
	return '<svg class="' . esc_attr( $cls ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $icons[ $name ] . '</svg>';
}

/**
 * Ikona pre profesiu (slug termínu) – použitá na kartách ponúk.
 *
 * @param string $trade_slug Slug profesie.
 */
function asc_trade_icon( $trade_slug ) {
	$map = array(
		'elektrikar' => '<path d="M13 2 4.5 13.5H12L11 22l8.5-11.5H12z"/>',
		'vodar'      => '<path d="M12 3.5s5.5 6.2 5.5 10a5.5 5.5 0 0 1-11 0c0-3.8 5.5-10 5.5-10z"/>',
		'zvarac'     => '<path d="M12 22c4 0 7-2.7 7-6.6 0-3.2-2-5.3-3.5-7.4-.4 1.8-1.4 3-2.7 3.4C13.6 8 12.4 5 9.5 2.5 9.8 6.2 5 8.8 5 15.4 5 19.3 8 22 12 22z"/>',
		'maliar'     => '<rect x="3" y="3" width="15" height="6" rx="2"/><path d="M18 6h2.5v5.5H11v3"/><rect x="9" y="14.5" width="4" height="7" rx="1"/>',
		'betonar'    => '<path d="M3 7.5 12 3l9 4.5-9 4.5z"/><path d="M3 12l9 4.5 9-4.5M3 16.5 12 21l9-4.5"/>',
		'sadrokartonista' => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 12h18M12 3v18"/>',
	);
	if ( isset( $map[ $trade_slug ] ) ) {
		return '<svg class="asc-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $map[ $trade_slug ] . '</svg>';
	}
	return asc_icon( 'tool' );
}

/**
 * Logo: vlastné logo z Prispôsobiť, inak predvolené SVG z témy.
 *
 * @param bool $light Svetlá verzia (na tmavé pozadie).
 */
function asc_logo( $light = false ) {
	if ( ! $light && has_custom_logo() ) {
		$logo_id = get_theme_mod( 'custom_logo' );
		$img     = wp_get_attachment_image( $logo_id, 'full', false, array( 'class' => 'custom-logo', 'alt' => get_bloginfo( 'name' ) ) );
		return '<a class="asc-logo" href="' . esc_url( home_url( '/' ) ) . '" rel="home">' . $img . '</a>';
	}
	$file = $light ? 'ascensio-logo-white.svg' : 'ascensio-logo.svg';
	return '<a class="asc-logo" href="' . esc_url( home_url( '/' ) ) . '" rel="home"><img src="' . esc_url( ASC_URI . '/assets/img/' . $file ) . '" width="190" height="36" alt="' . esc_attr( get_bloginfo( 'name' ) ? get_bloginfo( 'name' ) : 'Ascensio' ) . '"></a>';
}

/**
 * Je aktuálna stránka postavená v Elementore?
 *
 * @param int $post_id ID.
 */
function asc_is_elementor( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	if ( ! $post_id || ! did_action( 'elementor/loaded' ) ) {
		return false;
	}
	return 'builder' === get_post_meta( $post_id, '_elementor_edit_mode', true );
}
