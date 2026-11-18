<?php
/**
 * Štruktúrované dáta: JobPosting (Google pre pracovné ponuky) a Organization.
 *
 * @package Ascensio
 */

defined( 'ABSPATH' ) || exit;

/**
 * ISO kód krajiny podľa slugu termínu.
 *
 * @param string $slug Slug.
 */
function asc_country_code( $slug ) {
	$map = array(
		'nemecko'      => 'DE',
		'francuzsko'   => 'FR',
		'rakusko'      => 'AT',
		'svajciarsko'  => 'CH',
		'holandsko'    => 'NL',
		'belgicko'     => 'BE',
		'luxembursko'  => 'LU',
		'slovensko'    => 'SK',
		'cesko'        => 'CZ',
		'polsko'       => 'PL',
		'dansko'       => 'DK',
		'svedsko'      => 'SE',
		'norsko'       => 'NO',
	);
	return isset( $map[ $slug ] ) ? $map[ $slug ] : '';
}

add_action( 'wp_head', 'asc_schema_output', 20 );
/**
 * Výstup JSON-LD.
 */
function asc_schema_output() {
	$org = array(
		'@type' => 'Organization',
		'name'  => 'Ascensio',
		'url'   => home_url( '/' ),
		'logo'  => ASC_URI . '/assets/img/ascensio-ikona.svg',
	);
	$same = array_values( asc_socials() );
	if ( $same ) {
		$org['sameAs'] = $same;
	}

	if ( is_front_page() ) {
		$org['@context']  = 'https://schema.org';
		$org['legalName'] = asc_opt( 'asc_company' );
		$org['telephone'] = asc_opt( 'asc_phone' );
		$org['email']     = asc_opt( 'asc_email' );
		$org['address']   = array(
			'@type'           => 'PostalAddress',
			'streetAddress'   => 'Továrenská 1471/7',
			'postalCode'      => '064 01',
			'addressLocality' => 'Stará Ľubovňa',
			'addressCountry'  => 'SK',
		);
		echo '<script type="application/ld+json">' . wp_json_encode( $org, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
		return;
	}

	if ( ! is_singular( 'asc_job' ) ) {
		return;
	}

	$post_id   = get_queried_object_id();
	$countries = asc_job_terms( $post_id, 'asc_country' );
	$locations = array();
	foreach ( array_keys( $countries ) as $slug ) {
		$code = asc_country_code( $slug );
		if ( $code ) {
			$locations[] = array(
				'@type'   => 'Place',
				'address' => array(
					'@type'           => 'PostalAddress',
					'addressLocality' => asc_job_meta( $post_id, 'lokalita' ) ? asc_job_meta( $post_id, 'lokalita' ) : $countries[ $slug ],
					'addressCountry'  => $code,
				),
			);
		}
	}

	// Bez filtra the_content (v hlavičke by mohol spustiť shortcody alebo Elementor dvakrát).
	$desc  = wpautop( wp_kses_post( strip_shortcodes( (string) get_post_field( 'post_content', $post_id ) ) ) );
	$req   = asc_lines( asc_job_meta( $post_id, 'poziadavky' ) );
	$offer = asc_lines( asc_job_meta( $post_id, 'ponukame' ) );
	if ( ! $offer ) {
		$offer = asc_default_offer();
	}
	if ( $req ) {
		$desc .= '<p><strong>' . esc_html__( 'Požiadavky', 'ascensio' ) . '</strong></p><ul><li>' . implode( '</li><li>', array_map( 'esc_html', $req ) ) . '</li></ul>';
	}
	$desc .= '<p><strong>' . esc_html__( 'Ponúkame', 'ascensio' ) . '</strong></p><ul><li>' . implode( '</li><li>', array_map( 'esc_html', $offer ) ) . '</li></ul>';

	$job = array(
		'@context'           => 'https://schema.org',
		'@type'              => 'JobPosting',
		'title'              => get_the_title( $post_id ),
		'description'        => $desc,
		'datePosted'         => get_the_date( 'c', $post_id ),
		'employmentType'     => 'FULL_TIME',
		'hiringOrganization' => $org,
		'directApply'        => true,
	);
	$until = asc_job_meta( $post_id, 'platnost' );
	if ( $until ) {
		$job['validThrough'] = $until . 'T23:59:59';
	}
	if ( $locations ) {
		$job['jobLocation'] = 1 === count( $locations ) ? $locations[0] : $locations;
	}
	echo '<script type="application/ld+json">' . wp_json_encode( $job, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . '</script>' . "\n";
}
