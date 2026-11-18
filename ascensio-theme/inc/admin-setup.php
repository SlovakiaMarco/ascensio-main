<?php
/**
 * Vzhľad > Nastavenie webu Ascensio: jednoklikové vytvorenie stránok, menu,
 * ukážkových ponúk a nastavenie farieb/písma v Elementore.
 *
 * Importér nikdy neprepíše stránky, ktoré nevytvoril. Svoje stránky prepíše iba
 * na výslovnú žiadosť (zaškrtávacie pole „Prepísať“).
 *
 * @package Ascensio
 */

defined( 'ABSPATH' ) || exit;

add_action( 'admin_menu', 'asc_setup_menu' );
/**
 * Položka v menu Vzhľad.
 */
function asc_setup_menu() {
	add_theme_page(
		__( 'Nastavenie webu Ascensio', 'ascensio' ),
		__( 'Nastavenie webu Ascensio', 'ascensio' ),
		'manage_options',
		'asc-setup',
		'asc_setup_page'
	);
}

add_action( 'admin_notices', 'asc_setup_notice' );
/**
 * Upozornenie, kým nie je web nastavený.
 */
function asc_setup_notice() {
	if ( get_option( 'asc_setup_done' ) || ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$screen = get_current_screen();
	if ( $screen && 'appearance_page_asc-setup' === $screen->id ) {
		return;
	}
	echo '<div class="notice notice-info"><p><strong>' . esc_html__( 'Téma Ascensio je aktívna.', 'ascensio' ) . '</strong> ' . esc_html__( 'Dokončite nastavenie – jedným klikom vytvoríte stránky, menu a ukážkové pracovné ponuky.', 'ascensio' ) . ' <a class="button button-primary" href="' . esc_url( admin_url( 'themes.php?page=asc-setup' ) ) . '">' . esc_html__( 'Nastaviť web', 'ascensio' ) . '</a></p></div>';
}

/**
 * Stav pluginu Elementor: active|installed|missing.
 */
function asc_elementor_state() {
	if ( did_action( 'elementor/loaded' ) ) {
		return 'active';
	}
	if ( ! function_exists( 'get_plugins' ) ) {
		require_once ABSPATH . 'wp-admin/includes/plugin.php';
	}
	$plugins = get_plugins();
	return isset( $plugins['elementor/elementor.php'] ) ? 'installed' : 'missing';
}

/**
 * Stránka nastavenia.
 */
function asc_setup_page() {
	if ( ! current_user_can( 'manage_options' ) ) {
		return;
	}
	$state = asc_elementor_state();
	$done  = get_option( 'asc_setup_done' );
	$log   = get_transient( 'asc_setup_log' );
	if ( $log ) {
		delete_transient( 'asc_setup_log' );
	}
	?>
	<div class="wrap">
		<h1><?php esc_html_e( 'Nastavenie webu Ascensio', 'ascensio' ); ?></h1>
		<p style="max-width:760px;font-size:14px"><?php esc_html_e( 'Tento krok pripraví celý web: vytvorí stránky postavené v Elementore (Domov, Pracovné ponuky, Prihláška, O nás, Pre firmy v slovenčine, nemčine a francúzštine, Kontakt, Ochrana osobných údajov), menu, ukážkové pracovné ponuky a nastaví farby a písma značky v Elementore. Všetko potom môžete meniť v Elementore bez programovania.', 'ascensio' ); ?></p>

		<?php if ( $log ) : ?>
			<div class="notice notice-success" style="padding:12px 16px">
				<p><strong><?php esc_html_e( 'Hotovo. Prehľad krokov:', 'ascensio' ); ?></strong></p>
				<ul style="list-style:disc;margin-left:20px">
					<?php foreach ( (array) $log as $line ) : ?>
						<li><?php echo esc_html( $line ); ?></li>
					<?php endforeach; ?>
				</ul>
				<p><a class="button button-primary" href="<?php echo esc_url( home_url( '/' ) ); ?>" target="_blank"><?php esc_html_e( 'Zobraziť web', 'ascensio' ); ?></a></p>
			</div>
		<?php endif; ?>

		<h2><?php esc_html_e( '1. Plugin Elementor', 'ascensio' ); ?></h2>
		<?php if ( 'active' === $state ) : ?>
			<p>✅ <?php echo esc_html( sprintf( /* translators: %s verzia */ __( 'Elementor je aktívny (verzia %s).', 'ascensio' ), defined( 'ELEMENTOR_VERSION' ) ? ELEMENTOR_VERSION : '?' ) ); ?></p>
		<?php elseif ( 'installed' === $state ) : ?>
			<p>⚠️ <?php esc_html_e( 'Elementor je nainštalovaný, ale nie je aktívny.', 'ascensio' ); ?>
				<a class="button button-primary" href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=elementor/elementor.php' ), 'activate-plugin_elementor/elementor.php' ) ); ?>"><?php esc_html_e( 'Aktivovať Elementor', 'ascensio' ); ?></a></p>
		<?php else : ?>
			<p>⚠️ <?php esc_html_e( 'Elementor (bezplatná verzia stačí) nie je nainštalovaný.', 'ascensio' ); ?>
				<a class="button button-primary" href="<?php echo esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=elementor' ), 'install-plugin_elementor' ) ); ?>"><?php esc_html_e( 'Nainštalovať Elementor', 'ascensio' ); ?></a></p>
		<?php endif; ?>

		<h2><?php esc_html_e( '2. Vytvoriť obsah', 'ascensio' ); ?></h2>
		<?php if ( $done ) : ?>
			<p><?php echo esc_html( sprintf( /* translators: %s dátum */ __( 'Nastavenie bolo spustené: %s. Opätovné spustenie nevytvorí duplicitné stránky.', 'ascensio' ), wp_date( 'j. n. Y H:i', (int) $done ) ) ); ?></p>
		<?php endif; ?>
		<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
			<input type="hidden" name="action" value="asc_run_setup">
			<?php wp_nonce_field( 'asc_setup' ); ?>
			<fieldset style="display:grid;gap:10px;margin:12px 0 20px">
				<label><input type="checkbox" name="pages" value="1" checked <?php disabled( 'active' !== $state ); ?>> <?php esc_html_e( 'Vytvoriť stránky v Elementore', 'ascensio' ); ?></label>
				<label style="margin-left:24px"><input type="checkbox" name="overwrite" value="1"> <?php esc_html_e( 'Prepísať stránky, ktoré už tento importér vytvoril (vaše úpravy v nich sa stratia)', 'ascensio' ); ?></label>
				<label><input type="checkbox" name="jobs" value="1" checked> <?php esc_html_e( 'Ukážkové pracovné ponuky (7 pozícií zo súčasného webu – iba ak ešte žiadne ponuky nie sú)', 'ascensio' ); ?></label>
				<label><input type="checkbox" name="menus" value="1" checked> <?php esc_html_e( 'Vytvoriť menu (hlavné, horná lišta, pätička)', 'ascensio' ); ?></label>
				<label><input type="checkbox" name="front" value="1" checked> <?php esc_html_e( 'Nastaviť stránku Domov ako úvodnú', 'ascensio' ); ?></label>
				<label><input type="checkbox" name="kit" value="1" checked <?php disabled( 'active' !== $state ); ?>> <?php esc_html_e( 'Nastaviť farby a písma značky v Elementore (globálne farby)', 'ascensio' ); ?></label>
				<label><input type="checkbox" name="permalinks" value="1" checked> <?php esc_html_e( 'Pekné URL adresy (napr. /pracovne-ponuky/) – iba ak sú teraz vypnuté', 'ascensio' ); ?></label>
			</fieldset>
			<?php submit_button( __( 'Spustiť nastavenie', 'ascensio' ), 'primary large', 'submit', false ); ?>
		</form>

		<h2 style="margin-top:36px"><?php esc_html_e( '3. Odporúčané pluginy', 'ascensio' ); ?></h2>
		<ul style="list-style:disc;margin-left:20px;max-width:760px">
			<li><strong>WP Mail SMTP</strong> – <?php esc_html_e( 'spoľahlivé doručovanie e-mailov s prihláškami.', 'ascensio' ); ?></li>
			<li><strong>Complianz</strong> <?php esc_html_e( 'alebo', 'ascensio' ); ?> <strong>CookieYes</strong> – <?php esc_html_e( 'cookie lišta podľa GDPR (potrebná pri Google Analytics, Meta Pixel, mape).', 'ascensio' ); ?></li>
			<li><strong>Rank Math</strong> <?php esc_html_e( 'alebo', 'ascensio' ); ?> <strong>Yoast SEO</strong> – <?php esc_html_e( 'SEO titulky a popisy (téma má základné meta popisy aj bez neho).', 'ascensio' ); ?></li>
			<li><strong>Polylang</strong> – <?php esc_html_e( 'ak budete chcieť celý web vo viacerých jazykoch (teraz majú firmy samostatné stránky v DE a FR).', 'ascensio' ); ?></li>
		</ul>
	</div>
	<?php
}

add_action( 'admin_post_asc_run_setup', 'asc_run_setup' );
/**
 * Spustenie nastavenia.
 */
function asc_run_setup() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die( esc_html__( 'Nemáte oprávnenie.', 'ascensio' ) );
	}
	check_admin_referer( 'asc_setup' );

	// phpcs:disable WordPress.Security.NonceVerification.Missing -- overené vyššie.
	$opt = function ( $key ) {
		return ! empty( $_POST[ $key ] );
	};
	// phpcs:enable WordPress.Security.NonceVerification.Missing

	if ( function_exists( 'set_time_limit' ) ) {
		@set_time_limit( 120 ); // phpcs:ignore WordPress.PHP.NoSilencedErrors.Discouraged
	}

	$log      = array();
	$elem_on  = did_action( 'elementor/loaded' );
	$page_ids = asc_setup_existing_ids();

	if ( $opt( 'permalinks' ) && '' === (string) get_option( 'permalink_structure' ) ) {
		global $wp_rewrite;
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
		$log[] = __( 'Pekné URL adresy zapnuté (/%postname%/).', 'ascensio' );
	}

	asc_setup_terms();
	$log[] = __( 'Profesie a krajiny pre ponuky pripravené.', 'ascensio' );

	if ( $opt( 'kit' ) ) {
		$log[] = $elem_on ? asc_setup_elementor() : __( 'Elementor nie je aktívny – farby a písma neboli nastavené.', 'ascensio' );
	}

	if ( $opt( 'pages' ) ) {
		if ( $elem_on ) {
			$page_ids = asc_setup_pages( $opt( 'overwrite' ), $log );
		} else {
			$log[] = __( 'Elementor nie je aktívny – stránky neboli vytvorené.', 'ascensio' );
		}
	}

	if ( $opt( 'jobs' ) ) {
		$log[] = asc_setup_jobs();
	}

	if ( $opt( 'menus' ) ) {
		$log[] = asc_setup_menus( $page_ids );
	}

	if ( $opt( 'front' ) && ! empty( $page_ids['domov'] ) ) {
		update_option( 'show_on_front', 'page' );
		update_option( 'page_on_front', (int) $page_ids['domov'] );
		$log[] = __( 'Stránka Domov je nastavená ako úvodná.', 'ascensio' );
	}

	if ( ! empty( $page_ids['ochrana-osobnych-udajov'] ) ) {
		update_option( 'wp_page_for_privacy_policy', (int) $page_ids['ochrana-osobnych-udajov'] );
	}

	$tagline = (string) get_option( 'blogdescription' );
	if ( '' === $tagline || 'Just another WordPress site' === $tagline || 'Ďalšia stránka používajúca WordPress' === $tagline ) {
		update_option( 'blogdescription', 'Práca v zahraničí pre remeselníkov – Nemecko a Francúzsko' );
	}

	asc_register_jobs();
	flush_rewrite_rules();

	// Zámerne NEvoláme files_manager->clear_cache(): zmaže priečinok s CSS a Elementor sa v tej istej
	// požiadavke pokúsi zapísať CSS kitu do neexistujúceho priečinka (stránka potom ostane bez globálnych farieb).
	// Stačí zneplatniť CSS dotknutých stránok a kitu – Elementor ich pri ďalšom zobrazení vygeneruje znova.
	if ( $elem_on ) {
		foreach ( $page_ids as $asc_pid ) {
			delete_post_meta( (int) $asc_pid, '_elementor_css' );
		}
		$asc_kit = (int) get_option( 'elementor_active_kit' );
		if ( $asc_kit ) {
			delete_post_meta( $asc_kit, '_elementor_css' );
		}
	}

	update_option( 'asc_setup_done', time() );
	set_transient( 'asc_setup_log', $log, 10 * MINUTE_IN_SECONDS );
	wp_safe_redirect( admin_url( 'themes.php?page=asc-setup' ) );
	exit;
}

/**
 * Definície stránok zo súborov templates/pages/*.json.
 *
 * @return array<string,array>
 */
function asc_setup_definitions() {
	$defs  = array();
	$files = glob( ASC_DIR . '/templates/pages/*.json' );
	foreach ( (array) $files as $file ) {
		$def = json_decode( (string) file_get_contents( $file ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( is_array( $def ) && ! empty( $def['key'] ) ) {
			$defs[ $def['key'] ] = $def;
		}
	}
	uasort(
		$defs,
		function ( $a, $b ) {
			return (int) $a['order'] - (int) $b['order'];
		}
	);
	return $defs;
}

/**
 * Stránka vytvorená importérom podľa kľúča.
 *
 * @param string $key Kľúč.
 * @return int ID alebo 0.
 */
function asc_setup_find_page( $key ) {
	$found = get_posts(
		array(
			'post_type'      => 'page',
			'post_status'    => array( 'publish', 'draft', 'private', 'pending' ),
			'posts_per_page' => 1,
			'fields'         => 'ids',
			'meta_key'       => '_asc_import_key', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
			'meta_value'     => $key, // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
		)
	);
	return $found ? (int) $found[0] : 0;
}

/**
 * ID už vytvorených stránok (pre menu, ak sa stránky tentoraz nevytvárajú).
 */
function asc_setup_existing_ids() {
	$ids = array();
	foreach ( array_keys( asc_setup_definitions() ) as $key ) {
		$id = asc_setup_find_page( $key );
		if ( $id ) {
			$ids[ $key ] = $id;
		}
	}
	return $ids;
}

/**
 * Nahradenie {{placeholderov}}.
 *
 * @param string $str  Text.
 * @param array  $ids  Mapa kľúč => ID stránky.
 * @param bool   $json Ide o JSON reťazec (hodnoty treba escapovať pre JSON).
 */
function asc_setup_placeholders( $str, $ids, $json ) {
	$enc = function ( $value ) use ( $json ) {
		return $json ? substr( (string) wp_json_encode( (string) $value ), 1, -1 ) : esc_html( $value );
	};
	$str = preg_replace_callback(
		'/\{\{page:([a-z0-9-]+)\}\}/',
		function ( $m ) use ( $ids, $enc ) {
			if ( 'domov' === $m[1] ) {
				$url = home_url( '/' );
			} elseif ( isset( $ids[ $m[1] ] ) ) {
				$url = get_permalink( (int) $ids[ $m[1] ] );
			} else {
				$url = home_url( '/' . $m[1] . '/' );
			}
			return $enc( $url );
		},
		$str
	);
	$map = array(
		'{{img}}'   => ASC_URI . '/assets/img',
		'{{tel}}'   => asc_tel(),
		'{{phone}}' => asc_opt( 'asc_phone' ),
		'{{email}}' => asc_opt( 'asc_email' ),
		'{{home}}'  => untrailingslashit( home_url() ),
	);
	foreach ( $map as $k => $v ) {
		$str = str_replace( $k, $enc( $v ), $str );
	}
	return $str;
}

/**
 * Vytvorenie / naplnenie stránok.
 *
 * @param bool  $overwrite Prepísať stránky importéra.
 * @param array $log       Záznam krokov.
 * @return array<string,int> kľúč => ID stránky
 */
function asc_setup_pages( $overwrite, &$log ) {
	$defs    = asc_setup_definitions();
	$ids     = array();
	$fill    = array();
	$created = 0;
	$updated = 0;
	$skipped = 0;

	// 1. krok: zabezpečiť existenciu stránok (kvôli vzájomným odkazom).
	foreach ( $defs as $key => $def ) {
		$id = asc_setup_find_page( $key );
		if ( $id ) {
			$fill[ $key ] = (bool) $overwrite;
		} else {
			$id = wp_insert_post(
				array(
					'post_type'    => 'page',
					'post_status'  => 'publish',
					'post_title'   => $def['title'],
					'post_name'    => $def['slug'],
					'menu_order'   => (int) $def['order'],
					'post_content' => '',
				),
				true
			);
			if ( is_wp_error( $id ) || ! $id ) {
				/* translators: %s názov stránky */
				$log[] = sprintf( __( 'Stránku „%s“ sa nepodarilo vytvoriť.', 'ascensio' ), $def['title'] );
				continue;
			}
			update_post_meta( $id, '_asc_import_key', $key );
			$fill[ $key ] = true;
			++$created;
		}
		$ids[ $key ] = (int) $id;
	}

	// 2. krok: obsah.
	foreach ( $defs as $key => $def ) {
		if ( empty( $ids[ $key ] ) ) {
			continue;
		}
		if ( empty( $fill[ $key ] ) ) {
			++$skipped;
			continue;
		}
		$id = $ids[ $key ];
		if ( isset( $def['html'] ) ) {
			wp_update_post(
				array(
					'ID'           => $id,
					'post_content' => asc_setup_placeholders( $def['html'], $ids, false ),
				)
			);
		} else {
			$json     = asc_setup_placeholders( (string) wp_json_encode( $def['elementor'] ), $ids, true );
			$elements = json_decode( $json, true );
			if ( is_array( $elements ) ) {
				asc_setup_save_elementor( $id, $elements );
			}
		}
		if ( ! empty( $def['seo'] ) ) {
			update_post_meta( $id, '_asc_seo', $def['seo'] );
		}
		++$updated;
	}

	/* translators: 1: vytvorené, 2: naplnené, 3: preskočené */
	$log[] = sprintf( __( 'Stránky: %1$d nových, %2$d naplnených obsahom, %3$d ponechaných bez zmeny.', 'ascensio' ), $created, $updated, $skipped );
	return $ids;
}

/**
 * Uloženie Elementor dát na stránku.
 *
 * @param int   $post_id  ID.
 * @param array $elements Elementy.
 */
function asc_setup_save_elementor( $post_id, $elements ) {
	update_post_meta( $post_id, '_elementor_edit_mode', 'builder' );
	update_post_meta( $post_id, '_elementor_template_type', 'wp-page' );
	if ( defined( 'ELEMENTOR_VERSION' ) ) {
		update_post_meta( $post_id, '_elementor_version', ELEMENTOR_VERSION );
	}
	update_post_meta( $post_id, '_elementor_data', wp_slash( (string) wp_json_encode( $elements ) ) );
	update_post_meta( $post_id, '_elementor_page_settings', array( 'hide_title' => 'yes' ) );
	update_post_meta( $post_id, '_wp_page_template', 'default' );
	delete_post_meta( $post_id, '_elementor_css' );
	delete_post_meta( $post_id, '_elementor_element_cache' );
}

/**
 * Profesie a krajiny.
 */
function asc_setup_terms() {
	$data = json_decode( (string) file_get_contents( ASC_DIR . '/templates/terms.json' ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	if ( ! is_array( $data ) ) {
		return;
	}
	foreach ( $data as $taxonomy => $terms ) {
		foreach ( (array) $terms as $term ) {
			if ( ! term_exists( $term['slug'], $taxonomy ) ) {
				wp_insert_term( $term['name'], $taxonomy, array( 'slug' => $term['slug'] ) );
			}
		}
	}
}

/**
 * Ukážkové ponuky (iba ak ešte žiadne nie sú).
 */
function asc_setup_jobs() {
	$existing = get_posts(
		array(
			'post_type'      => 'asc_job',
			'post_status'    => 'any',
			'posts_per_page' => 1,
			'fields'         => 'ids',
		)
	);
	if ( $existing ) {
		return __( 'Pracovné ponuky už existujú – ukážkové neboli pridané.', 'ascensio' );
	}
	$jobs = json_decode( (string) file_get_contents( ASC_DIR . '/templates/sample-jobs.json' ), true ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
	$n    = 0;
	foreach ( (array) $jobs as $i => $job ) {
		$id = wp_insert_post(
			array(
				'post_type'    => 'asc_job',
				'post_status'  => 'publish',
				'post_title'   => $job['title'],
				'post_content' => $job['content'],
				'post_excerpt' => $job['excerpt'],
				'menu_order'   => (int) $i,
			),
			true
		);
		if ( is_wp_error( $id ) || ! $id ) {
			continue;
		}
		wp_set_object_terms( $id, array( $job['trade'] ), 'asc_trade' );
		wp_set_object_terms( $id, (array) $job['countries'], 'asc_country' );
		foreach ( (array) $job['meta'] as $key => $value ) {
			if ( '' !== (string) $value ) {
				update_post_meta( $id, '_asc_' . sanitize_key( $key ), $value );
			}
		}
		++$n;
	}
	/* translators: %d počet */
	return sprintf( __( 'Pridaných %d ukážkových pracovných ponúk. Skontrolujte a doplňte mzdy a miesta práce.', 'ascensio' ), $n );
}

/**
 * Menu.
 *
 * @param array $ids Mapa stránok.
 */
function asc_setup_menus( $ids ) {
	$home  = home_url( '/' );
	$menus = array(
		'primary' => array(
			'name'  => 'Hlavné menu',
			'items' => array(
				array( 'page', 'pracovne-ponuky', '' ),
				array( 'custom', $home . '#benefity', 'Čo zabezpečíme' ),
				array( 'custom', $home . '#postup', 'Ako to funguje' ),
				array( 'page', 'o-nas', '' ),
				array( 'page', 'pre-firmy', '' ),
				array( 'page', 'kontakt', '' ),
			),
		),
		'topbar'  => array(
			'name'  => 'Horná lišta – pre firmy',
			'items' => array(
				array( 'page', 'fur-unternehmen', 'Für Unternehmen' ),
				array( 'page', 'pour-les-entreprises', 'Pour les entreprises' ),
			),
		),
		'footer'  => array(
			'name'  => 'Pätička',
			'items' => array(
				array( 'page', 'pracovne-ponuky', '' ),
				array( 'page', 'prihlaska', '' ),
				array( 'custom', $home . '#benefity', 'Čo zabezpečíme' ),
				array( 'custom', $home . '#otazky', 'Časté otázky' ),
				array( 'page', 'o-nas', '' ),
				array( 'page', 'kontakt', '' ),
			),
		),
		'legal'   => array(
			'name'  => 'Právne odkazy',
			'items' => array(
				array( 'page', 'ochrana-osobnych-udajov', '' ),
			),
		),
	);

	$locations = get_theme_mod( 'nav_menu_locations' );
	$locations = is_array( $locations ) ? $locations : array();
	$made      = 0;

	foreach ( $menus as $location => $menu ) {
		if ( ! empty( $locations[ $location ] ) && wp_get_nav_menu_object( $locations[ $location ] ) ) {
			continue; // Menu už je priradené – nemeníme ho.
		}
		$obj     = wp_get_nav_menu_object( $menu['name'] );
		$menu_id = $obj ? (int) $obj->term_id : wp_create_nav_menu( $menu['name'] );
		if ( is_wp_error( $menu_id ) || ! $menu_id ) {
			continue;
		}
		if ( ! $obj ) {
			foreach ( $menu['items'] as $pos => $item ) {
				list( $type, $target, $title ) = $item;
				if ( 'page' === $type ) {
					if ( empty( $ids[ $target ] ) ) {
						continue;
					}
					$args = array(
						'menu-item-object-id' => (int) $ids[ $target ],
						'menu-item-object'    => 'page',
						'menu-item-type'      => 'post_type',
						'menu-item-status'    => 'publish',
						'menu-item-position'  => $pos + 1,
					);
					if ( $title ) {
						$args['menu-item-title'] = $title;
					}
				} else {
					$args = array(
						'menu-item-title'    => $title,
						'menu-item-url'      => $target,
						'menu-item-type'     => 'custom',
						'menu-item-status'   => 'publish',
						'menu-item-position' => $pos + 1,
					);
				}
				wp_update_nav_menu_item( $menu_id, 0, $args );
			}
		}
		$locations[ $location ] = $menu_id;
		++$made;
	}
	set_theme_mod( 'nav_menu_locations', $locations );
	/* translators: %d počet */
	return sprintf( __( 'Menu priradené: %d.', 'ascensio' ), $made );
}

/**
 * Nastavenia Elementoru: globálne farby, písma, šírka obsahu.
 */
function asc_setup_elementor() {
	update_option( 'elementor_disable_color_schemes', 'yes' );
	update_option( 'elementor_disable_typography_schemes', 'yes' );
	// Písma načítava téma z Bunny Fonts (GDPR) – Elementor ich nemusí sťahovať z Google.
	update_option( 'elementor_google_font', '0' );
	update_option( 'elementor_font_display', 'swap' );
	// CSS stránok vkladať priamo do HTML: web nezávisí od zápisu súborov do uploads/elementor
	// (pri probléme s právami na hostingu by inak stratil vzhľad). Dá sa prepnúť v Elementor > Nastavenia > Výkon.
	update_option( 'elementor_css_print_method', 'internal' );
	update_option( 'elementor_experiment-container', 'active' );
	update_option( 'elementor_experiment-container_grid', 'active' );
	update_option( 'elementor_experiment-nested-elements', 'active' );

	$kit_id = (int) get_option( 'elementor_active_kit' );
	if ( ! $kit_id && isset( \Elementor\Plugin::$instance->kits_manager ) && method_exists( \Elementor\Plugin::$instance->kits_manager, 'get_active_id' ) ) {
		$kit_id = (int) \Elementor\Plugin::$instance->kits_manager->get_active_id();
	}
	if ( ! $kit_id ) {
		return __( 'Elementor kit sa nenašiel – farby nastavte ručne v Elementor > Nastavenia stránky.', 'ascensio' );
	}

	$settings = get_post_meta( $kit_id, '_elementor_page_settings', true );
	$settings = is_array( $settings ) ? $settings : array();

	$custom_colors = array(
		array( '_id' => 'ascsand', 'title' => 'Svetlé pozadie', 'color' => '#F5F2EB' ),
		array( '_id' => 'ascnavy2', 'title' => 'Tmavá navy (pätička)', 'color' => '#0C1A2E' ),
		array( '_id' => 'ascwhite', 'title' => 'Biela', 'color' => '#FFFFFF' ),
		array( '_id' => 'ascmuted', 'title' => 'Sivý text', 'color' => '#5F6B7C' ),
		array( '_id' => 'ascgoldink', 'title' => 'Tmavá zlatá (text)', 'color' => '#8B6B25' ),
		array( '_id' => 'ascgreen', 'title' => 'Zelená (nástup ihneď)', 'color' => '#16794D' ),
	);
	$ours     = wp_list_pluck( $custom_colors, '_id' );
	$existing = isset( $settings['custom_colors'] ) && is_array( $settings['custom_colors'] ) ? $settings['custom_colors'] : array();
	foreach ( $existing as $c ) {
		if ( isset( $c['_id'] ) && ! in_array( $c['_id'], $ours, true ) ) {
			$custom_colors[] = $c;
		}
	}

	$font = function ( $id, $title, $family, $weight ) {
		return array(
			'_id'                    => $id,
			'title'                  => $title,
			'typography_typography'  => 'custom',
			'typography_font_family' => $family,
			'typography_font_weight' => $weight,
		);
	};

	$settings = array_merge(
		$settings,
		array(
			'system_colors'                  => array(
				array( '_id' => 'primary', 'title' => 'Navy (primárna)', 'color' => '#14263F' ),
				array( '_id' => 'secondary', 'title' => 'Zlatá (sekundárna)', 'color' => '#C9A04E' ),
				array( '_id' => 'text', 'title' => 'Text', 'color' => '#334155' ),
				array( '_id' => 'accent', 'title' => 'Akcent (tlačidlá)', 'color' => '#C9A04E' ),
			),
			'custom_colors'                  => $custom_colors,
			'system_typography'              => array(
				$font( 'primary', 'Nadpisy', 'Archivo', '800' ),
				$font( 'secondary', 'Podnadpisy', 'Archivo', '700' ),
				$font( 'text', 'Text', 'Inter', '400' ),
				$font( 'accent', 'Tlačidlá', 'Inter', '700' ),
			),
			'body_color'                     => '#334155',
			'body_typography_typography'     => 'custom',
			'body_typography_font_family'    => 'Inter',
			'h1_typography_typography'       => 'custom',
			'h1_typography_font_family'      => 'Archivo',
			'h2_typography_typography'       => 'custom',
			'h2_typography_font_family'      => 'Archivo',
			'h3_typography_typography'       => 'custom',
			'h3_typography_font_family'      => 'Archivo',
			'container_width'                => array(
				'unit'  => 'px',
				'size'  => 1200,
				'sizes' => array(),
			),
			'site_name'                      => get_bloginfo( 'name' ),
		)
	);
	update_post_meta( $kit_id, '_elementor_page_settings', $settings );
	delete_post_meta( $kit_id, '_elementor_css' );
	update_option( 'asc_kit_synced', 1 );
	return __( 'Farby (navy, zlatá) a písma (Archivo, Inter) nastavené v Elementore ako globálne.', 'ascensio' );
}
