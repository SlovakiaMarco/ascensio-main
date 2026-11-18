<?php
/**
 * Prispôsobenie (Vzhľad > Prispôsobiť > Ascensio): kontakty, hlavička, pätička, prihlášky.
 *
 * @package Ascensio
 */

defined( 'ABSPATH' ) || exit;

add_action( 'customize_register', 'asc_customize_register' );
/**
 * Registrácia nastavení.
 *
 * @param WP_Customize_Manager $wp_customize Správca.
 */
function asc_customize_register( $wp_customize ) {
	$d = asc_defaults();

	$wp_customize->add_panel(
		'asc_panel',
		array(
			'title'       => __( 'Ascensio – nastavenia webu', 'ascensio' ),
			'description' => __( 'Kontakty, hlavička, pätička a prihlášky. Obsah stránok upravujte v Elementore.', 'ascensio' ),
			'priority'    => 30,
		)
	);

	$sections = array(
		'asc_contacts' => __( 'Kontakty', 'ascensio' ),
		'asc_header'   => __( 'Hlavička a mobilná lišta', 'ascensio' ),
		'asc_social'   => __( 'Sociálne siete', 'ascensio' ),
		'asc_footer'   => __( 'Pätička', 'ascensio' ),
		'asc_forms'    => __( 'Prihlášky a formuláre', 'ascensio' ),
	);
	$p = 10;
	foreach ( $sections as $id => $title ) {
		$wp_customize->add_section(
			$id,
			array(
				'title'    => $title,
				'panel'    => 'asc_panel',
				'priority' => $p,
			)
		);
		$p += 10;
	}

	$fields = array(
		// kľúč => [sekcia, typ, popis, sanitize, doplnkový popis].
		'asc_phone'        => array( 'asc_contacts', 'text', __( 'Telefón', 'ascensio' ), 'sanitize_text_field', __( 'Zobrazí sa v hlavičke, pätičke a na mobilnej lište.', 'ascensio' ) ),
		'asc_email'        => array( 'asc_contacts', 'email', __( 'E-mail', 'ascensio' ), 'sanitize_email', '' ),
		'asc_whatsapp'     => array( 'asc_contacts', 'text', __( 'WhatsApp číslo (nepovinné)', 'ascensio' ), 'sanitize_text_field', __( 'Ak vyplníte, na mobile pribudne tlačidlo WhatsApp. Formát: +421 9xx xxx xxx', 'ascensio' ) ),
		'asc_company'      => array( 'asc_contacts', 'text', __( 'Názov firmy', 'ascensio' ), 'sanitize_text_field', '' ),
		'asc_address'      => array( 'asc_contacts', 'textarea', __( 'Adresa sídla', 'ascensio' ), 'sanitize_textarea_field', '' ),

		'asc_cta_text'     => array( 'asc_header', 'text', __( 'Text hlavného tlačidla', 'ascensio' ), 'sanitize_text_field', '' ),
		'asc_cta_url'      => array( 'asc_header', 'url', __( 'Odkaz hlavného tlačidla', 'ascensio' ), 'esc_url_raw', __( 'Prázdne = stránka Prihláška.', 'ascensio' ) ),
		'asc_topbar'       => array( 'asc_header', 'checkbox', __( 'Zobraziť hornú lištu', 'ascensio' ), 'asc_sanitize_checkbox', '' ),
		'asc_topbar_text'  => array( 'asc_header', 'text', __( 'Text v hornej lište', 'ascensio' ), 'sanitize_text_field', '' ),
		'asc_mobile_bar'   => array( 'asc_header', 'checkbox', __( 'Mobilná lišta „Zavolať / Chcem prácu“', 'ascensio' ), 'asc_sanitize_checkbox', __( 'Tlačidlá prilepené na spodku obrazovky mobilu.', 'ascensio' ) ),

		'asc_facebook'     => array( 'asc_social', 'url', 'Facebook', 'esc_url_raw', '' ),
		'asc_instagram'    => array( 'asc_social', 'url', 'Instagram', 'esc_url_raw', '' ),
		'asc_tiktok'       => array( 'asc_social', 'url', 'TikTok', 'esc_url_raw', '' ),
		'asc_linkedin'     => array( 'asc_social', 'url', 'LinkedIn', 'esc_url_raw', '' ),
		'asc_youtube'      => array( 'asc_social', 'url', 'YouTube', 'esc_url_raw', '' ),

		'asc_footer_text'  => array( 'asc_footer', 'textarea', __( 'Krátky text o firme', 'ascensio' ), 'sanitize_textarea_field', '' ),

		'asc_apply_email'  => array( 'asc_forms', 'email', __( 'Kam posielať prihlášky', 'ascensio' ), 'sanitize_email', __( 'Prázdne = e-mail administrátora webu. Všetky prihlášky sú aj v menu Prihlášky.', 'ascensio' ) ),
		'asc_success_text' => array( 'asc_forms', 'textarea', __( 'Text po odoslaní prihlášky', 'ascensio' ), 'sanitize_textarea_field', '' ),
	);

	foreach ( $fields as $key => $f ) {
		$wp_customize->add_setting(
			$key,
			array(
				'default'           => isset( $d[ $key ] ) ? $d[ $key ] : '',
				'sanitize_callback' => $f[3],
				'transport'         => 'refresh',
			)
		);
		$wp_customize->add_control(
			$key,
			array(
				'label'       => $f[2],
				'description' => $f[4],
				'section'     => $f[0],
				'type'        => $f[1],
			)
		);
	}
}

/**
 * Sanitizácia checkboxu.
 *
 * @param mixed $value Hodnota.
 */
function asc_sanitize_checkbox( $value ) {
	return (bool) $value;
}
