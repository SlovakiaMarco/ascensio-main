<?php
/**
 * Pracovné ponuky: typ obsahu, kategórie (profesia, krajina), polia a výpis.
 *
 * @package Ascensio
 */

defined( 'ABSPATH' ) || exit;

/**
 * Polia ponuky: kľúč => [popis, typ, pomocný text].
 */
function asc_job_fields() {
	return array(
		'lokalita'   => array( __( 'Miesto práce', 'ascensio' ), 'text', __( 'Napr. Mníchov, Nemecko', 'ascensio' ) ),
		'mzda'       => array( __( 'Mzda', 'ascensio' ), 'text', __( 'Napr. od 15 € / hod. netto – konkrétna suma zvyšuje počet reakcií', 'ascensio' ) ),
		'nastup'     => array( __( 'Nástup', 'ascensio' ), 'text', __( 'Napr. Ihneď / od 1. 3. 2027', 'ascensio' ) ),
		'uvazok'     => array( __( 'Typ úväzku', 'ascensio' ), 'text', __( 'Napr. Plný úväzok', 'ascensio' ) ),
		'ubytovanie' => array( __( 'Ubytovanie', 'ascensio' ), 'text', __( 'Napr. Zabezpečené', 'ascensio' ) ),
		'poziadavky' => array( __( 'Požiadavky (každá na nový riadok)', 'ascensio' ), 'textarea', '' ),
		'ponukame'   => array( __( 'Čo ponúkame (každá položka na nový riadok)', 'ascensio' ), 'textarea', __( 'Prázdne = použijú sa štandardné benefity.', 'ascensio' ) ),
		'platnost'   => array( __( 'Platnosť ponuky do', 'ascensio' ), 'date', __( 'Nepovinné. Pomáha Google pre pracovné ponuky.', 'ascensio' ) ),
		'top'        => array( __( 'Top ponuka (zvýrazniť)', 'ascensio' ), 'checkbox', '' ),
	);
}

/**
 * Štandardné benefity ponuky.
 */
function asc_default_offer() {
	return array(
		__( 'Ubytovanie zabezpečené firmou', 'ascensio' ),
		__( 'Firemné auto na dopravu na projekt', 'ascensio' ),
		__( 'Profesionálne náradie Milwaukee a Hilti', 'ascensio' ),
		__( 'Pracovné oblečenie', 'ascensio' ),
		__( 'Férové podmienky dohodnuté vopred', 'ascensio' ),
	);
}

add_action( 'init', 'asc_register_jobs' );
/**
 * Registrácia typu obsahu a taxonómií.
 */
function asc_register_jobs() {
	register_post_type(
		'asc_job',
		array(
			'labels'        => array(
				'name'               => __( 'Pracovné ponuky', 'ascensio' ),
				'singular_name'      => __( 'Pracovná ponuka', 'ascensio' ),
				'menu_name'          => __( 'Pracovné ponuky', 'ascensio' ),
				'add_new'            => __( 'Pridať ponuku', 'ascensio' ),
				'add_new_item'       => __( 'Pridať novú ponuku', 'ascensio' ),
				'edit_item'          => __( 'Upraviť ponuku', 'ascensio' ),
				'new_item'           => __( 'Nová ponuka', 'ascensio' ),
				'view_item'          => __( 'Zobraziť ponuku', 'ascensio' ),
				'search_items'       => __( 'Hľadať ponuky', 'ascensio' ),
				'not_found'          => __( 'Žiadne ponuky', 'ascensio' ),
				'not_found_in_trash' => __( 'V koši nie sú žiadne ponuky', 'ascensio' ),
				'all_items'          => __( 'Všetky ponuky', 'ascensio' ),
			),
			'public'        => true,
			'has_archive'   => false,
			'show_in_rest'  => true,
			'menu_position' => 5,
			'menu_icon'     => 'dashicons-id-alt',
			'supports'      => array( 'title', 'editor', 'excerpt', 'thumbnail', 'page-attributes', 'revisions' ),
			'rewrite'       => array(
				'slug'       => 'ponuka',
				'with_front' => false,
			),
		)
	);

	register_taxonomy(
		'asc_trade',
		'asc_job',
		array(
			'labels'            => array(
				'name'          => __( 'Profesie', 'ascensio' ),
				'singular_name' => __( 'Profesia', 'ascensio' ),
				'add_new_item'  => __( 'Pridať profesiu', 'ascensio' ),
				'search_items'  => __( 'Hľadať profesie', 'ascensio' ),
			),
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'public'            => false,
			'show_ui'           => true,
			'rewrite'           => false,
		)
	);

	register_taxonomy(
		'asc_country',
		'asc_job',
		array(
			'labels'            => array(
				'name'          => __( 'Krajiny', 'ascensio' ),
				'singular_name' => __( 'Krajina', 'ascensio' ),
				'add_new_item'  => __( 'Pridať krajinu', 'ascensio' ),
				'search_items'  => __( 'Hľadať krajiny', 'ascensio' ),
			),
			'hierarchical'      => true,
			'show_admin_column' => true,
			'show_in_rest'      => true,
			'public'            => false,
			'show_ui'           => true,
			'rewrite'           => false,
		)
	);

	foreach ( array_keys( asc_job_fields() ) as $key ) {
		register_post_meta(
			'asc_job',
			'_asc_' . $key,
			array(
				'type'          => 'string',
				'single'        => true,
				'show_in_rest'  => false,
				'auth_callback' => function () {
					return current_user_can( 'edit_posts' );
				},
			)
		);
	}
}

add_action( 'after_switch_theme', 'asc_flush_rewrites' );
/**
 * Obnovenie pravidiel URL po aktivácii témy.
 */
function asc_flush_rewrites() {
	asc_register_jobs();
	flush_rewrite_rules();
}

/**
 * Hodnota poľa ponuky.
 *
 * @param int    $post_id ID.
 * @param string $key     Kľúč poľa.
 */
function asc_job_meta( $post_id, $key ) {
	return (string) get_post_meta( $post_id, '_asc_' . $key, true );
}

/**
 * Riadky textového poľa ako pole.
 *
 * @param string $value Text.
 */
function asc_lines( $value ) {
	$lines = preg_split( '/\r\n|\r|\n/', (string) $value );
	return array_values( array_filter( array_map( 'trim', $lines ) ) );
}

/* ---------- Administrácia: polia ---------- */

add_action( 'add_meta_boxes', 'asc_job_metabox' );
/**
 * Box s detailmi ponuky.
 */
function asc_job_metabox() {
	add_meta_box( 'asc_job_details', __( 'Detaily ponuky', 'ascensio' ), 'asc_job_metabox_html', 'asc_job', 'normal', 'high' );
}

/**
 * Obsah boxu.
 *
 * @param WP_Post $post Ponuka.
 */
function asc_job_metabox_html( $post ) {
	wp_nonce_field( 'asc_job_save', 'asc_job_nonce' );
	echo '<style>.asc-mb{display:grid;grid-template-columns:1fr 1fr;gap:16px 24px}.asc-mb p{margin:0}.asc-mb label{display:block;font-weight:600;margin-bottom:4px}.asc-mb input[type=text],.asc-mb input[type=date],.asc-mb textarea{width:100%}.asc-mb .full{grid-column:1/-1}.asc-mb .desc{color:#646970;font-size:12px;margin-top:3px}@media(max-width:782px){.asc-mb{grid-template-columns:1fr}}</style>';
	echo '<p style="margin:0 0 14px;color:#646970">' . esc_html__( 'Text ponuky (popis práce) píšte do hlavného editora vyššie. Profesiu a krajinu vyberte v bočnom paneli.', 'ascensio' ) . '</p>';
	echo '<div class="asc-mb">';
	foreach ( asc_job_fields() as $key => $f ) {
		$value = asc_job_meta( $post->ID, $key );
		$id    = 'asc_' . $key;
		$full  = in_array( $f[1], array( 'textarea', 'checkbox' ), true ) ? ' full' : '';
		echo '<p class="' . esc_attr( trim( $full ) ) . '">';
		if ( 'checkbox' === $f[1] ) {
			echo '<label><input type="checkbox" name="' . esc_attr( $id ) . '" value="1" ' . checked( $value, '1', false ) . '> ' . esc_html( $f[0] ) . '</label>';
		} else {
			echo '<label for="' . esc_attr( $id ) . '">' . esc_html( $f[0] ) . '</label>';
			if ( 'textarea' === $f[1] ) {
				$placeholder = 'ponukame' === $key ? implode( "\n", asc_default_offer() ) : '';
				echo '<textarea id="' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '" rows="5" placeholder="' . esc_attr( $placeholder ) . '">' . esc_textarea( $value ) . '</textarea>';
			} else {
				echo '<input type="' . esc_attr( $f[1] ) . '" id="' . esc_attr( $id ) . '" name="' . esc_attr( $id ) . '" value="' . esc_attr( $value ) . '">';
			}
		}
		if ( $f[2] ) {
			echo '<span class="desc">' . esc_html( $f[2] ) . '</span>';
		}
		echo '</p>';
	}
	echo '</div>';
}

add_action( 'save_post_asc_job', 'asc_job_save', 10, 2 );
/**
 * Uloženie polí.
 *
 * @param int     $post_id ID.
 * @param WP_Post $post    Ponuka.
 */
function asc_job_save( $post_id, $post ) {
	if ( ! isset( $_POST['asc_job_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['asc_job_nonce'] ) ), 'asc_job_save' ) ) {
		return;
	}
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	foreach ( asc_job_fields() as $key => $f ) {
		$name = 'asc_' . $key;
		if ( 'checkbox' === $f[1] ) {
			$value = isset( $_POST[ $name ] ) ? '1' : '';
		} elseif ( 'textarea' === $f[1] ) {
			$value = isset( $_POST[ $name ] ) ? sanitize_textarea_field( wp_unslash( $_POST[ $name ] ) ) : '';
		} else {
			$value = isset( $_POST[ $name ] ) ? sanitize_text_field( wp_unslash( $_POST[ $name ] ) ) : '';
		}
		if ( '' === $value ) {
			delete_post_meta( $post_id, '_asc_' . $key );
		} else {
			update_post_meta( $post_id, '_asc_' . $key, $value );
		}
	}
}

add_filter( 'manage_asc_job_posts_columns', 'asc_job_columns' );
/**
 * Stĺpce v zozname ponúk.
 *
 * @param array $cols Stĺpce.
 */
function asc_job_columns( $cols ) {
	$new = array();
	foreach ( $cols as $k => $v ) {
		$new[ $k ] = $v;
		if ( 'title' === $k ) {
			$new['asc_lokalita'] = __( 'Miesto', 'ascensio' );
			$new['asc_mzda']     = __( 'Mzda', 'ascensio' );
			$new['asc_top']      = __( 'Top', 'ascensio' );
		}
	}
	return $new;
}

add_action( 'manage_asc_job_posts_custom_column', 'asc_job_column_value', 10, 2 );
/**
 * Hodnoty stĺpcov.
 *
 * @param string $col     Stĺpec.
 * @param int    $post_id ID.
 */
function asc_job_column_value( $col, $post_id ) {
	if ( 'asc_lokalita' === $col ) {
		echo esc_html( asc_job_meta( $post_id, 'lokalita' ) );
	} elseif ( 'asc_mzda' === $col ) {
		echo esc_html( asc_job_meta( $post_id, 'mzda' ) );
	} elseif ( 'asc_top' === $col ) {
		echo asc_job_meta( $post_id, 'top' ) ? '★' : '–';
	}
}

/* ---------- Výpis ponúk ---------- */

/**
 * Načítanie ponúk.
 *
 * @param array $args count, only_featured, trades (slugy), countries (slugy), exclude.
 * @return WP_Post[]
 */
function asc_get_jobs( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'count'         => 50,
			'only_featured' => false,
			'trades'        => array(),
			'countries'     => array(),
			'exclude'       => array(),
		)
	);
	$q = array(
		'post_type'      => 'asc_job',
		'post_status'    => 'publish',
		'posts_per_page' => max( 1, (int) $args['count'] ),
		'orderby'        => array(
			'menu_order' => 'ASC',
			'date'       => 'DESC',
		),
		'no_found_rows'  => true,
		'post__not_in'   => array_map( 'intval', (array) $args['exclude'] ),
	);
	$tax = array();
	if ( ! empty( $args['trades'] ) ) {
		$tax[] = array(
			'taxonomy' => 'asc_trade',
			'field'    => 'slug',
			'terms'    => (array) $args['trades'],
		);
	}
	if ( ! empty( $args['countries'] ) ) {
		$tax[] = array(
			'taxonomy' => 'asc_country',
			'field'    => 'slug',
			'terms'    => (array) $args['countries'],
		);
	}
	if ( $tax ) {
		$q['tax_query'] = $tax; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query
	}
	if ( $args['only_featured'] ) {
		$q['meta_key']   = '_asc_top'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
		$q['meta_value'] = '1'; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
	}
	$posts = get_posts( $q );

	// Top ponuky navrch, ostatné ponechajú poradie.
	usort(
		$posts,
		function ( $a, $b ) {
			$ta = asc_job_meta( $a->ID, 'top' ) ? 1 : 0;
			$tb = asc_job_meta( $b->ID, 'top' ) ? 1 : 0;
			return $tb - $ta;
		}
	);
	return $posts;
}

/**
 * Slugy termínov ponuky.
 *
 * @param int    $post_id  ID.
 * @param string $taxonomy Taxonómia.
 * @return array<string,string> slug => názov
 */
function asc_job_terms( $post_id, $taxonomy ) {
	$terms = get_the_terms( $post_id, $taxonomy );
	$out   = array();
	if ( $terms && ! is_wp_error( $terms ) ) {
		foreach ( $terms as $t ) {
			$out[ $t->slug ] = $t->name;
		}
	}
	return $out;
}

/**
 * HTML karty ponuky.
 *
 * @param WP_Post $post        Ponuka.
 * @param string  $button_text Text odkazu.
 */
function asc_job_card( $post, $button_text = '' ) {
	$button_text = $button_text ? $button_text : __( 'Zobraziť ponuku', 'ascensio' );
	$trades      = asc_job_terms( $post->ID, 'asc_trade' );
	$countries   = asc_job_terms( $post->ID, 'asc_country' );
	$lokalita    = asc_job_meta( $post->ID, 'lokalita' );
	$mzda        = asc_job_meta( $post->ID, 'mzda' );
	$nastup      = asc_job_meta( $post->ID, 'nastup' );
	$ubyt        = asc_job_meta( $post->ID, 'ubytovanie' );
	$top         = asc_job_meta( $post->ID, 'top' );
	$excerpt     = has_excerpt( $post ) ? get_the_excerpt( $post ) : wp_trim_words( wp_strip_all_tags( $post->post_content ), 22, '…' );
	$where       = $lokalita ? $lokalita : implode( ', ', $countries );
	$first_trade = $trades ? array_key_first( $trades ) : '';

	ob_start();
	?>
	<article class="asc-job" data-trade="<?php echo esc_attr( implode( ' ', array_keys( $trades ) ) ); ?>" data-country="<?php echo esc_attr( implode( ' ', array_keys( $countries ) ) ); ?>">
		<div class="asc-job__top">
			<span class="asc-job__icon"><?php echo asc_trade_icon( $first_trade ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<?php if ( $top ) : ?>
				<span class="asc-badge asc-badge--top"><?php esc_html_e( 'TOP', 'ascensio' ); ?></span>
			<?php endif; ?>
			<?php if ( $nastup ) : ?>
				<span class="asc-badge asc-badge--green"><?php echo esc_html( sprintf( /* translators: %s nástup */ __( 'Nástup: %s', 'ascensio' ), $nastup ) ); ?></span>
			<?php endif; ?>
		</div>
		<h3 class="asc-job__title"><a href="<?php echo esc_url( get_permalink( $post ) ); ?>"><?php echo esc_html( get_the_title( $post ) ); ?></a></h3>
		<?php if ( $excerpt ) : ?>
			<p class="asc-job__excerpt"><?php echo esc_html( $excerpt ); ?></p>
		<?php endif; ?>
		<ul class="asc-job__meta">
			<?php if ( $where ) : ?>
				<li><?php echo asc_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( $where ); ?></span></li>
			<?php endif; ?>
			<?php if ( $mzda ) : ?>
				<li><?php echo asc_icon( 'euro' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><strong><?php echo esc_html( $mzda ); ?></strong></span></li>
			<?php endif; ?>
			<?php if ( $ubyt ) : ?>
				<li><?php echo asc_icon( 'home' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><span><?php echo esc_html( sprintf( /* translators: %s stav ubytovania */ __( 'Ubytovanie: %s', 'ascensio' ), $ubyt ) ); ?></span></li>
			<?php endif; ?>
		</ul>
		<div class="asc-job__foot"><span><?php echo esc_html( $button_text ); ?></span><?php echo asc_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
	</article>
	<?php
	return ob_get_clean();
}

/**
 * Výpis ponúk s filtrom (používa widget aj shortcode).
 *
 * @param array $args count, filters, columns, only_featured, trades, countries, button_text, empty_text.
 */
function asc_render_jobs( $args = array() ) {
	$args = wp_parse_args(
		$args,
		array(
			'count'         => 50,
			'filters'       => true,
			'columns'       => 3,
			'only_featured' => false,
			'trades'        => array(),
			'countries'     => array(),
			'button_text'   => '',
			'empty_text'    => __( 'Momentálne tu nemáme zverejnenú vhodnú ponuku. Pošli nám všeobecnú prihlášku – ozveme sa ti, keď sa niečo objaví.', 'ascensio' ),
		)
	);

	$jobs = asc_get_jobs( $args );

	// Predvolený filter z URL (?profesia=monter&krajina=nemecko).
	$pre_trade   = isset( $_GET['profesia'] ) ? sanitize_title( wp_unslash( $_GET['profesia'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	$pre_country = isset( $_GET['krajina'] ) ? sanitize_title( wp_unslash( $_GET['krajina'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	$trade_counts   = array();
	$country_counts = array();
	$trade_names    = array();
	$country_names  = array();
	foreach ( $jobs as $job ) {
		foreach ( asc_job_terms( $job->ID, 'asc_trade' ) as $slug => $name ) {
			$trade_counts[ $slug ] = isset( $trade_counts[ $slug ] ) ? $trade_counts[ $slug ] + 1 : 1;
			$trade_names[ $slug ]  = $name;
		}
		foreach ( asc_job_terms( $job->ID, 'asc_country' ) as $slug => $name ) {
			$country_counts[ $slug ] = isset( $country_counts[ $slug ] ) ? $country_counts[ $slug ] + 1 : 1;
			$country_names[ $slug ]  = $name;
		}
	}
	if ( $pre_trade && ! isset( $trade_counts[ $pre_trade ] ) ) {
		$pre_trade = '';
	}
	if ( $pre_country && ! isset( $country_counts[ $pre_country ] ) ) {
		$pre_country = '';
	}

	$cols = (int) $args['columns'] === 2 ? 2 : 3;

	ob_start();
	?>
	<div class="asc-jobs" data-asc-jobs data-pre-trade="<?php echo esc_attr( $pre_trade ); ?>" data-pre-country="<?php echo esc_attr( $pre_country ); ?>">
		<?php if ( $args['filters'] && count( $jobs ) > 1 ) : ?>
			<div class="asc-jobs__bar">
				<div class="asc-filter" role="group" aria-label="<?php esc_attr_e( 'Filtrovať podľa profesie', 'ascensio' ); ?>">
					<button type="button" class="asc-chip<?php echo $pre_trade ? '' : ' is-active'; ?>" data-filter-trade="" aria-pressed="<?php echo $pre_trade ? 'false' : 'true'; ?>"><?php esc_html_e( 'Všetky', 'ascensio' ); ?> <span class="asc-chip__count"><?php echo (int) count( $jobs ); ?></span></button>
					<?php foreach ( $trade_counts as $slug => $count ) : ?>
						<button type="button" class="asc-chip<?php echo $pre_trade === $slug ? ' is-active' : ''; ?>" data-filter-trade="<?php echo esc_attr( $slug ); ?>" aria-pressed="<?php echo $pre_trade === $slug ? 'true' : 'false'; ?>"><?php echo esc_html( $trade_names[ $slug ] ); ?> <span class="asc-chip__count"><?php echo (int) $count; ?></span></button>
					<?php endforeach; ?>
				</div>
				<?php if ( count( $country_counts ) > 1 ) : ?>
					<label class="asc-jobs__select"><?php esc_html_e( 'Krajina', 'ascensio' ); ?>
						<select data-filter-country>
							<option value=""><?php esc_html_e( 'Všetky krajiny', 'ascensio' ); ?></option>
							<?php foreach ( $country_names as $slug => $name ) : ?>
								<option value="<?php echo esc_attr( $slug ); ?>" <?php selected( $pre_country, $slug ); ?>><?php echo esc_html( $name ); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<?php if ( $jobs ) : ?>
			<div class="asc-jobs__grid<?php echo 2 === $cols ? ' asc-jobs__grid--2' : ''; ?>">
				<?php
				foreach ( $jobs as $job ) {
					echo asc_job_card( $job, $args['button_text'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				}
				?>
			</div>
		<?php endif; ?>
		<div class="asc-jobs__empty" <?php echo $jobs ? 'hidden' : ''; ?>>
			<h3><?php esc_html_e( 'Nenašli sme ponuku pre tento výber', 'ascensio' ); ?></h3>
			<p><?php echo esc_html( $args['empty_text'] ); ?></p>
			<a class="asc-btn" href="<?php echo esc_url( asc_page_url( 'prihlaska' ) ); ?>"><?php esc_html_e( 'Poslať prihlášku', 'ascensio' ); ?> <?php echo asc_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

add_shortcode( 'ascensio_ponuky', 'asc_jobs_shortcode' );
/**
 * Shortcode [ascensio_ponuky pocet="12" filter="ano" stlpce="3" top="nie" profesia="monter,zvarac" krajina="nemecko"].
 *
 * @param array $atts Atribúty.
 */
function asc_jobs_shortcode( $atts ) {
	$a = shortcode_atts(
		array(
			'pocet'    => 50,
			'filter'   => 'ano',
			'stlpce'   => 3,
			'top'      => 'nie',
			'profesia' => '',
			'krajina'  => '',
			'tlacidlo' => '',
		),
		$atts,
		'ascensio_ponuky'
	);
	return asc_render_jobs(
		array(
			'count'         => (int) $a['pocet'],
			'filters'       => 'ano' === $a['filter'],
			'columns'       => (int) $a['stlpce'],
			'only_featured' => 'ano' === $a['top'],
			'trades'        => array_filter( array_map( 'trim', explode( ',', $a['profesia'] ) ) ),
			'countries'     => array_filter( array_map( 'trim', explode( ',', $a['krajina'] ) ) ),
			'button_text'   => $a['tlacidlo'],
		)
	);
}
