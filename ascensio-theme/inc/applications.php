<?php
/**
 * Prihlášky uchádzačov a dopyty firiem: formulár, spracovanie, administrácia.
 *
 * Každé odoslanie sa uloží do menu „Prihlášky“ a zároveň odošle e-mailom.
 *
 * @package Ascensio
 */

defined( 'ABSPATH' ) || exit;

/* ---------- Typ obsahu ---------- */

add_action( 'init', 'asc_register_applications' );
/**
 * Prihlášky (neverejné, iba pre editorov a administrátorov).
 */
function asc_register_applications() {
	$cap = 'edit_others_posts';
	register_post_type(
		'asc_application',
		array(
			'labels'              => array(
				'name'          => __( 'Prihlášky', 'ascensio' ),
				'singular_name' => __( 'Prihláška', 'ascensio' ),
				'menu_name'     => __( 'Prihlášky', 'ascensio' ),
				'all_items'     => __( 'Všetky prihlášky', 'ascensio' ),
				'edit_item'     => __( 'Detail prihlášky', 'ascensio' ),
				'search_items'  => __( 'Hľadať prihlášky', 'ascensio' ),
				'not_found'     => __( 'Zatiaľ žiadne prihlášky', 'ascensio' ),
			),
			'public'              => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_ui'             => true,
			'show_in_menu'        => true,
			'show_in_rest'        => false,
			'show_in_nav_menus'   => false,
			'menu_position'       => 6,
			'menu_icon'           => 'dashicons-groups',
			'supports'            => array( 'title' ),
			'rewrite'             => false,
			'query_var'           => false,
			'capability_type'     => 'post',
			'map_meta_cap'        => true,
			'capabilities'        => array(
				'edit_posts'             => $cap,
				'edit_others_posts'      => $cap,
				'edit_published_posts'   => $cap,
				'edit_private_posts'     => $cap,
				'publish_posts'          => $cap,
				'read_private_posts'     => $cap,
				'delete_posts'           => $cap,
				'delete_others_posts'    => $cap,
				'delete_published_posts' => $cap,
				'delete_private_posts'   => $cap,
				'create_posts'           => 'do_not_allow',
			),
		)
	);
}

/**
 * Stavy prihlášky.
 */
function asc_application_statuses() {
	return array(
		'nova'         => __( 'Nová', 'ascensio' ),
		'kontaktovany' => __( 'Kontaktovaný', 'ascensio' ),
		'pohovor'      => __( 'Pohovor', 'ascensio' ),
		'prijaty'      => __( 'Prijatý', 'ascensio' ),
		'zamietnuty'   => __( 'Zamietnutý', 'ascensio' ),
	);
}

/* ---------- Texty formulára ---------- */

/**
 * Texty formulára podľa variantu a jazyka.
 *
 * @param string $variant quick|full|partner.
 * @param string $lang    sk|de|fr|en.
 */
function asc_form_texts( $variant, $lang ) {
	$sk = array(
		'name'      => __( 'Meno a priezvisko', 'ascensio' ),
		'phone'     => __( 'Telefón', 'ascensio' ),
		'email'     => __( 'E-mail', 'ascensio' ),
		'position'  => __( 'Pozícia', 'ascensio' ),
		'choose'    => __( 'Vyber pozíciu', 'ascensio' ),
		'other'     => __( 'Iná pozícia', 'ascensio' ),
		'exp'       => __( 'Prax v odbore', 'ascensio' ),
		'start'     => __( 'Kedy môžeš nastúpiť?', 'ascensio' ),
		'license'   => __( 'Mám vodičský preukaz sk. B', 'ascensio' ),
		'message'   => __( 'Správa (nepovinné)', 'ascensio' ),
		'message_p' => __( 'Napr. aké máš skúsenosti, odkiaľ si, kedy sa ti môžeme ozvať…', 'ascensio' ),
		'consent'   => __( 'Súhlasím so spracovaním osobných údajov na účely výberového konania.', 'ascensio' ),
		'privacy'   => __( 'Viac o ochrane údajov', 'ascensio' ),
		'note'      => __( 'Tvoje údaje použijeme iba na to, aby sme ťa kontaktovali.', 'ascensio' ),
		'success_h' => __( 'Ďakujeme!', 'ascensio' ),
		'success'   => asc_opt( 'asc_success_text' ),
		'company'   => '',
		'country'   => '',
		'req'       => __( 'Toto pole je povinné.', 'ascensio' ),
		'bad_phone' => __( 'Zadajte platné telefónne číslo.', 'ascensio' ),
		'bad_email' => __( 'Zadajte platný e-mail.', 'ascensio' ),
		'need_ok'   => __( 'Pre odoslanie potrebujeme váš súhlas.', 'ascensio' ),
		'fail'      => __( 'Odoslanie sa nepodarilo. Skúste to znova alebo nám zavolajte.', 'ascensio' ),
		'sending'   => __( 'Odosielam…', 'ascensio' ),
	);
	if ( 'partner' !== $variant ) {
		return $sk;
	}
	$partner = array(
		'sk' => array(
			'name'      => 'Kontaktná osoba',
			'company'   => 'Názov firmy',
			'phone'     => 'Telefón',
			'email'     => 'E-mail',
			'country'   => 'Krajina projektu',
			'message'   => 'Koho hľadáte?',
			'message_p' => 'Profesie, počet ľudí, miesto a termín projektu…',
			'consent'   => 'Súhlasím so spracovaním osobných údajov na účely odpovede na dopyt.',
			'privacy'   => 'Ochrana osobných údajov',
			'note'      => 'Vaše údaje použijeme iba na odpoveď na dopyt.',
			'success_h' => 'Ďakujeme!',
			'success'   => 'Dopyt sme prijali a ozveme sa vám čo najskôr.',
			'req'       => 'Toto pole je povinné.',
			'bad_phone' => 'Zadajte platné telefónne číslo.',
			'bad_email' => 'Zadajte platný e-mail.',
			'need_ok'   => 'Pre odoslanie potrebujeme váš súhlas.',
			'fail'      => 'Odoslanie sa nepodarilo. Skúste to znova alebo nám zavolajte.',
			'sending'   => 'Odosielam…',
		),
		'de' => array(
			'name'      => 'Ansprechpartner',
			'company'   => 'Firmenname',
			'phone'     => 'Telefon',
			'email'     => 'E-Mail',
			'country'   => 'Land des Projekts',
			'message'   => 'Wen suchen Sie?',
			'message_p' => 'Gewerke, Anzahl der Mitarbeiter, Einsatzort und Zeitraum…',
			'consent'   => 'Ich stimme der Verarbeitung meiner personenbezogenen Daten zur Beantwortung meiner Anfrage zu.',
			'privacy'   => 'Datenschutz',
			'note'      => 'Ihre Daten verwenden wir ausschließlich zur Beantwortung Ihrer Anfrage.',
			'success_h' => 'Vielen Dank!',
			'success'   => 'Wir haben Ihre Anfrage erhalten und melden uns schnellstmöglich bei Ihnen.',
			'req'       => 'Dieses Feld ist ein Pflichtfeld.',
			'bad_phone' => 'Bitte geben Sie eine gültige Telefonnummer ein.',
			'bad_email' => 'Bitte geben Sie eine gültige E-Mail-Adresse ein.',
			'need_ok'   => 'Für den Versand benötigen wir Ihre Zustimmung.',
			'fail'      => 'Senden fehlgeschlagen. Bitte versuchen Sie es erneut oder rufen Sie uns an.',
			'sending'   => 'Wird gesendet…',
		),
		'fr' => array(
			'name'      => 'Personne de contact',
			'company'   => 'Nom de l’entreprise',
			'phone'     => 'Téléphone',
			'email'     => 'E-mail',
			'country'   => 'Pays du projet',
			'message'   => 'Quels profils recherchez-vous ?',
			'message_p' => 'Métiers, effectif, lieu et période du chantier…',
			'consent'   => 'J’accepte le traitement de mes données personnelles afin de répondre à ma demande.',
			'privacy'   => 'Protection des données',
			'note'      => 'Vos données sont utilisées uniquement pour répondre à votre demande.',
			'success_h' => 'Merci !',
			'success'   => 'Nous avons bien reçu votre demande et revenons vers vous rapidement.',
			'req'       => 'Ce champ est obligatoire.',
			'bad_phone' => 'Veuillez saisir un numéro de téléphone valide.',
			'bad_email' => 'Veuillez saisir une adresse e-mail valide.',
			'need_ok'   => 'Votre consentement est nécessaire pour l’envoi.',
			'fail'      => 'L’envoi a échoué. Veuillez réessayer ou nous appeler.',
			'sending'   => 'Envoi en cours…',
		),
		'en' => array(
			'name'      => 'Contact person',
			'company'   => 'Company name',
			'phone'     => 'Phone',
			'email'     => 'E-mail',
			'country'   => 'Project country',
			'message'   => 'Who are you looking for?',
			'message_p' => 'Trades, number of workers, location and period…',
			'consent'   => 'I agree to the processing of my personal data for the purpose of answering my inquiry.',
			'privacy'   => 'Privacy policy',
			'note'      => 'We only use your data to respond to your inquiry.',
			'success_h' => 'Thank you!',
			'success'   => 'We have received your inquiry and will get back to you shortly.',
			'req'       => 'This field is required.',
			'bad_phone' => 'Please enter a valid phone number.',
			'bad_email' => 'Please enter a valid e-mail address.',
			'need_ok'   => 'We need your consent to send the form.',
			'fail'      => 'Sending failed. Please try again or call us.',
			'sending'   => 'Sending…',
		),
	);
	return isset( $partner[ $lang ] ) ? $partner[ $lang ] : $partner['sk'];
}

/**
 * Zoznam pozícií pre výber (z profesií ponúk).
 */
function asc_position_options() {
	$names = array();
	$terms = get_terms(
		array(
			'taxonomy'   => 'asc_trade',
			'hide_empty' => false,
		)
	);
	if ( $terms && ! is_wp_error( $terms ) ) {
		foreach ( $terms as $t ) {
			$names[] = $t->name;
		}
	}
	if ( ! $names ) {
		$names = array( 'Montér', 'Elektrikár', 'Vodár – inštalatér', 'Zvárač', 'Sadrokartonista', 'Betonár', 'Maliar' );
	}
	return $names;
}

/* ---------- Formulár ---------- */

/**
 * HTML formulára.
 *
 * @param array $args variant, title, subtitle, button, lang, position, job_id.
 */
function asc_render_form( $args = array() ) {
	static $n = 0;
	++$n;
	$a = wp_parse_args(
		$args,
		array(
			'variant'  => 'full',
			'title'    => '',
			'subtitle' => '',
			'button'   => '',
			'lang'     => 'sk',
			'position' => '',
			'job_id'   => 0,
		)
	);
	$variant = in_array( $a['variant'], array( 'quick', 'full', 'partner' ), true ) ? $a['variant'] : 'full';
	$t       = asc_form_texts( $variant, $a['lang'] );
	$id      = 'asc-form-' . $n;
	$button  = $a['button'] ? $a['button'] : __( 'Odoslať prihlášku', 'ascensio' );
	$privacy = get_privacy_policy_url();
	if ( ! $privacy ) {
		$privacy = asc_page_url( 'ochrana-osobnych-udajov' );
	}
	$sent = isset( $_GET['prihlaska'] ) && 'ok' === $_GET['prihlaska']; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	$data_attrs = array(
		'data-msg-required' => $t['req'],
		'data-msg-phone'    => $t['bad_phone'],
		'data-msg-email'    => $t['bad_email'],
		'data-msg-consent'  => $t['need_ok'],
		'data-msg-error'    => $t['fail'],
		'data-msg-sending'  => $t['sending'],
	);
	$attr_html  = '';
	foreach ( $data_attrs as $k => $v ) {
		$attr_html .= ' ' . $k . '="' . esc_attr( $v ) . '"';
	}

	$req = '&nbsp;<span class="req" aria-hidden="true">*</span>';

	ob_start();
	?>
	<div class="asc-form asc-form--<?php echo esc_attr( $variant ); ?>" id="<?php echo esc_attr( $id ); ?>" lang="<?php echo esc_attr( $a['lang'] ); ?>">
		<div class="asc-form__body"<?php echo $sent ? ' hidden' : ''; ?>>
			<?php if ( $a['title'] || $a['subtitle'] ) : ?>
				<div class="asc-form__head">
					<?php if ( $a['title'] ) : ?>
						<h3 class="asc-form__title"><?php echo esc_html( $a['title'] ); ?></h3>
					<?php endif; ?>
					<?php if ( $a['subtitle'] ) : ?>
						<p class="asc-form__sub"><?php echo esc_html( $a['subtitle'] ); ?></p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
			<form class="asc-form__form" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" novalidate<?php echo $attr_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
				<input type="hidden" name="action" value="asc_apply">
				<input type="hidden" name="asc_variant" value="<?php echo esc_attr( $variant ); ?>">
				<input type="hidden" name="asc_lang" value="<?php echo esc_attr( $a['lang'] ); ?>">
				<input type="hidden" name="asc_job" value="<?php echo (int) $a['job_id']; ?>">
				<input type="hidden" name="asc_form" value="<?php echo esc_attr( $id ); ?>">
				<input type="hidden" name="asc_t" value="<?php echo (int) time(); ?>">
				<?php echo wp_referer_field( false ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				<div class="asc-hp" aria-hidden="true"><label>Web <input type="text" name="asc_web" tabindex="-1" autocomplete="off"></label></div>

				<div class="asc-form__grid">
					<?php if ( 'partner' === $variant ) : ?>
						<div class="asc-field"><label for="<?php echo esc_attr( $id ); ?>-company"><?php echo esc_html( $t['company'] ); ?><?php echo $req; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></label><input type="text" id="<?php echo esc_attr( $id ); ?>-company" name="asc_company" autocomplete="organization" required></div>
					<?php endif; ?>

					<div class="asc-field<?php echo 'full' === $variant ? ' asc-field--full' : ''; ?>"><label for="<?php echo esc_attr( $id ); ?>-name"><?php echo esc_html( $t['name'] ); ?><?php echo $req; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></label><input type="text" id="<?php echo esc_attr( $id ); ?>-name" name="asc_name" autocomplete="name" required></div>

					<div class="asc-field"><label for="<?php echo esc_attr( $id ); ?>-phone"><?php echo esc_html( $t['phone'] ); ?><?php echo 'partner' === $variant ? '' : $req; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></label><input type="tel" id="<?php echo esc_attr( $id ); ?>-phone" name="asc_phone" autocomplete="tel" inputmode="tel" placeholder="+421 9xx xxx xxx" <?php echo 'partner' === $variant ? 'data-optional-phone' : 'required'; ?>></div>

					<?php if ( 'quick' !== $variant ) : ?>
						<div class="asc-field"><label for="<?php echo esc_attr( $id ); ?>-email"><?php echo esc_html( $t['email'] ); ?><?php echo 'partner' === $variant ? $req : ''; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></label><input type="email" id="<?php echo esc_attr( $id ); ?>-email" name="asc_email" autocomplete="email" <?php echo 'partner' === $variant ? 'required' : ''; ?>></div>
					<?php endif; ?>

					<?php if ( 'partner' === $variant ) : ?>
						<div class="asc-field"><label for="<?php echo esc_attr( $id ); ?>-country"><?php echo esc_html( $t['country'] ); ?></label><input type="text" id="<?php echo esc_attr( $id ); ?>-country" name="asc_country" autocomplete="country-name"></div>
					<?php else : ?>
						<?php
						$positions = asc_position_options();
						if ( $a['position'] && ! in_array( $a['position'], $positions, true ) ) {
							array_unshift( $positions, $a['position'] );
						}
						?>
						<div class="asc-field"><label for="<?php echo esc_attr( $id ); ?>-position"><?php echo esc_html( $t['position'] ); ?><?php echo $req; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></label>
							<select id="<?php echo esc_attr( $id ); ?>-position" name="asc_position" required>
								<option value=""><?php echo esc_html( $t['choose'] ); ?></option>
								<?php foreach ( $positions as $pos ) : ?>
									<option value="<?php echo esc_attr( $pos ); ?>" <?php selected( $a['position'], $pos ); ?>><?php echo esc_html( $pos ); ?></option>
								<?php endforeach; ?>
								<option value="<?php echo esc_attr( $t['other'] ); ?>"><?php echo esc_html( $t['other'] ); ?></option>
							</select>
						</div>
					<?php endif; ?>

					<?php if ( 'full' === $variant ) : ?>
						<div class="asc-field"><label for="<?php echo esc_attr( $id ); ?>-exp"><?php echo esc_html( $t['exp'] ); ?></label>
							<select id="<?php echo esc_attr( $id ); ?>-exp" name="asc_exp">
								<option value=""><?php esc_html_e( '– vyber –', 'ascensio' ); ?></option>
								<option><?php esc_html_e( 'Bez praxe', 'ascensio' ); ?></option>
								<option><?php esc_html_e( 'Do 1 roka', 'ascensio' ); ?></option>
								<option><?php esc_html_e( '1 – 3 roky', 'ascensio' ); ?></option>
								<option><?php esc_html_e( 'Viac ako 3 roky', 'ascensio' ); ?></option>
							</select>
						</div>
						<div class="asc-field"><label for="<?php echo esc_attr( $id ); ?>-start"><?php echo esc_html( $t['start'] ); ?></label>
							<select id="<?php echo esc_attr( $id ); ?>-start" name="asc_start">
								<option value=""><?php esc_html_e( '– vyber –', 'ascensio' ); ?></option>
								<option><?php esc_html_e( 'Ihneď', 'ascensio' ); ?></option>
								<option><?php esc_html_e( 'Do 2 týždňov', 'ascensio' ); ?></option>
								<option><?php esc_html_e( 'Do mesiaca', 'ascensio' ); ?></option>
								<option><?php esc_html_e( 'Neskôr', 'ascensio' ); ?></option>
							</select>
						</div>
						<label class="asc-check asc-check--inline"><input type="checkbox" name="asc_license" value="1"> <span><?php echo esc_html( $t['license'] ); ?></span></label>
					<?php endif; ?>

					<?php if ( 'quick' !== $variant ) : ?>
						<div class="asc-field asc-field--full"><label for="<?php echo esc_attr( $id ); ?>-msg"><?php echo esc_html( $t['message'] ); ?></label><textarea id="<?php echo esc_attr( $id ); ?>-msg" name="asc_message" rows="4" placeholder="<?php echo esc_attr( $t['message_p'] ); ?>"></textarea></div>
					<?php endif; ?>

					<label class="asc-check asc-check--consent"><input type="checkbox" name="asc_consent" value="1" required> <span><?php echo esc_html( $t['consent'] ); ?> <a href="<?php echo esc_url( $privacy ); ?>" target="_blank" rel="noopener"><?php echo esc_html( $t['privacy'] ); ?></a><?php echo $req; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span></label>
				</div>

				<button type="submit" class="asc-btn asc-form__submit"><span><?php echo esc_html( $button ); ?></span> <?php echo asc_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></button>
				<p class="asc-form__note"><?php echo asc_icon( 'lock' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php echo esc_html( $t['note'] ); ?></p>
				<div class="asc-form__msg" role="alert" aria-live="assertive"></div>
			</form>
		</div>
		<div class="asc-form__success" role="status" aria-live="polite"<?php echo $sent ? '' : ' hidden'; ?>>
			<span class="asc-form__badge"><?php echo asc_icon( 'check' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
			<h3><?php echo esc_html( $t['success_h'] ); ?></h3>
			<p><?php echo esc_html( $t['success'] ); ?></p>
		</div>
	</div>
	<?php
	return ob_get_clean();
}

add_shortcode( 'ascensio_prihlaska', 'asc_form_shortcode' );
/**
 * Shortcode [ascensio_prihlaska typ="full|quick|partner" nadpis="" popis="" tlacidlo="" jazyk="sk|de|fr|en"].
 *
 * @param array $atts Atribúty.
 */
function asc_form_shortcode( $atts ) {
	$a = shortcode_atts(
		array(
			'typ'      => 'full',
			'nadpis'   => '',
			'popis'    => '',
			'tlacidlo' => '',
			'jazyk'    => 'sk',
			'pozicia'  => '',
		),
		$atts,
		'ascensio_prihlaska'
	);
	return asc_render_form(
		array(
			'variant'  => $a['typ'],
			'title'    => $a['nadpis'],
			'subtitle' => $a['popis'],
			'button'   => $a['tlacidlo'],
			'lang'     => $a['jazyk'],
			'position' => $a['pozicia'],
		)
	);
}

/* ---------- Spracovanie ---------- */

add_action( 'admin_post_nopriv_asc_apply', 'asc_handle_apply' );
add_action( 'admin_post_asc_apply', 'asc_handle_apply' );
/**
 * Spracovanie odoslaného formulára (AJAX aj klasické odoslanie).
 */
function asc_handle_apply() {
	// phpcs:disable WordPress.Security.NonceVerification.Missing -- verejný formulár: chránený honeypotom, časovou pascou a limitom pokusov.
	$is_ajax = ! empty( $_POST['asc_ajax'] );
	$variant = isset( $_POST['asc_variant'] ) ? sanitize_key( wp_unslash( $_POST['asc_variant'] ) ) : 'full';
	$variant = in_array( $variant, array( 'quick', 'full', 'partner' ), true ) ? $variant : 'full';
	$lang    = isset( $_POST['asc_lang'] ) ? sanitize_key( wp_unslash( $_POST['asc_lang'] ) ) : 'sk';
	$form_id = isset( $_POST['asc_form'] ) ? sanitize_html_class( wp_unslash( $_POST['asc_form'] ) ) : '';
	$t       = asc_form_texts( $variant, $lang );

	// Ochrana proti spamu: skryté pole a príliš rýchle odoslanie => tváriť sa ako úspech.
	$started = isset( $_POST['asc_t'] ) ? (int) $_POST['asc_t'] : 0;
	if ( ! empty( $_POST['asc_web'] ) || ( $started && ( time() - $started ) < 3 ) ) {
		asc_apply_respond( true, $t['success'], array(), $is_ajax, $form_id );
	}

	// Limit pokusov z jednej IP adresy (max 6 za 10 minút).
	$ip      = isset( $_SERVER['REMOTE_ADDR'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REMOTE_ADDR'] ) ) : '';
	$rl_key  = 'asc_rl_' . md5( $ip . wp_salt( 'nonce' ) );
	$attempt = (int) get_transient( $rl_key );
	if ( $attempt >= 6 ) {
		asc_apply_respond( false, $t['fail'], array(), $is_ajax, $form_id );
	}
	set_transient( $rl_key, $attempt + 1, 10 * MINUTE_IN_SECONDS );

	$f = function ( $key, $type = 'text' ) {
		if ( ! isset( $_POST[ $key ] ) ) {
			return '';
		}
		$raw = wp_unslash( $_POST[ $key ] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		if ( 'email' === $type ) {
			return sanitize_email( $raw );
		}
		if ( 'textarea' === $type ) {
			return sanitize_textarea_field( $raw );
		}
		return sanitize_text_field( $raw );
	};

	$data = array(
		'meno'     => $f( 'asc_name' ),
		'telefon'  => $f( 'asc_phone' ),
		'email'    => $f( 'asc_email', 'email' ),
		'pozicia'  => $f( 'asc_position' ),
		'prax'     => $f( 'asc_exp' ),
		'nastup'   => $f( 'asc_start' ),
		'vodicak'  => ! empty( $_POST['asc_license'] ) ? __( 'Áno', 'ascensio' ) : '',
		'sprava'   => $f( 'asc_message', 'textarea' ),
		'firma'    => $f( 'asc_company' ),
		'krajina'  => $f( 'asc_country' ),
		'ponuka'   => isset( $_POST['asc_job'] ) ? absint( $_POST['asc_job'] ) : 0,
		'jazyk'    => $lang,
		'stranka'  => esc_url_raw( asc_absolute_url( (string) wp_get_referer() ) ),
	);
	$consent = ! empty( $_POST['asc_consent'] );
	// phpcs:enable WordPress.Security.NonceVerification.Missing

	$errors = array();
	if ( '' === $data['meno'] ) {
		$errors['asc_name'] = $t['req'];
	}
	$digits = preg_replace( '/\D/', '', $data['telefon'] );
	if ( 'partner' !== $variant ) {
		if ( '' === $data['telefon'] ) {
			$errors['asc_phone'] = $t['req'];
		} elseif ( strlen( $digits ) < 9 ) {
			$errors['asc_phone'] = $t['bad_phone'];
		}
		if ( '' === $data['pozicia'] ) {
			$errors['asc_position'] = $t['req'];
		}
	} else {
		if ( '' === $data['firma'] ) {
			$errors['asc_company'] = $t['req'];
		}
		if ( '' === $data['email'] ) {
			$errors['asc_email'] = $t['req'];
		}
		if ( '' !== $data['telefon'] && strlen( $digits ) < 6 ) {
			$errors['asc_phone'] = $t['bad_phone'];
		}
	}
	if ( isset( $_POST['asc_email'] ) && '' !== trim( (string) wp_unslash( $_POST['asc_email'] ) ) && ! is_email( $data['email'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Missing,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$errors['asc_email'] = $t['bad_email'];
	}
	if ( ! $consent ) {
		$errors['asc_consent'] = $t['need_ok'];
	}
	if ( $errors ) {
		asc_apply_respond( false, $t['fail'], $errors, $is_ajax, $form_id );
	}

	$type = 'partner' === $variant ? 'partner' : 'uchadzac';
	if ( 'partner' === $type ) {
		/* translators: 1: firma, 2: meno */
		$title = sprintf( __( 'Dopyt: %1$s (%2$s)', 'ascensio' ), $data['firma'], $data['meno'] );
	} else {
		$title = $data['meno'] . ' – ' . $data['pozicia'];
	}

	$post_id = wp_insert_post(
		array(
			'post_type'   => 'asc_application',
			'post_status' => 'publish',
			'post_title'  => $title,
		),
		true
	);
	if ( is_wp_error( $post_id ) ) {
		asc_apply_respond( false, $t['fail'], array(), $is_ajax, $form_id );
	}
	update_post_meta( $post_id, '_asc_data', $data );
	update_post_meta( $post_id, '_asc_type', $type );
	update_post_meta( $post_id, '_asc_status', 'nova' );
	delete_transient( 'asc_new_apps' );

	asc_apply_notify( $post_id, $data, $type );

	/**
	 * Po uložení prihlášky (napr. pre napojenie na CRM).
	 *
	 * @param int    $post_id ID prihlášky.
	 * @param array  $data    Údaje.
	 * @param string $type    uchadzac|partner.
	 */
	do_action( 'ascensio_application_submitted', $post_id, $data, $type );

	asc_apply_respond( true, $t['success'], array(), $is_ajax, $form_id );
}

/**
 * Relatívnu adresu (napr. „/kontakt/“) doplní o doménu webu.
 *
 * @param string $url Adresa.
 */
function asc_absolute_url( $url ) {
	if ( '' === $url || 0 !== strpos( $url, '/' ) || 0 === strpos( $url, '//' ) ) {
		return $url;
	}
	$origin = preg_replace( '#^(https?://[^/]+).*$#', '$1', home_url() );
	return $origin . $url;
}

/**
 * Odpoveď po odoslaní.
 *
 * @param bool   $ok      Úspech.
 * @param string $message Správa.
 * @param array  $errors  Chyby polí.
 * @param bool   $is_ajax AJAX.
 * @param string $form_id ID formulára (kotva).
 */
function asc_apply_respond( $ok, $message, $errors, $is_ajax, $form_id ) {
	if ( $is_ajax ) {
		if ( $ok ) {
			wp_send_json_success( array( 'message' => $message ) );
		}
		wp_send_json_error(
			array(
				'message' => $message,
				'fields'  => $errors,
			),
			422
		);
	}
	$back = wp_get_referer();
	if ( ! $back ) {
		$back = home_url( '/' );
	}
	$back = remove_query_arg( 'prihlaska', $back );
	$url  = add_query_arg( 'prihlaska', $ok ? 'ok' : 'chyba', $back );
	if ( $form_id ) {
		$url .= '#' . $form_id;
	}
	wp_safe_redirect( $url );
	exit;
}

/**
 * E-mailové upozornenie.
 *
 * @param int    $post_id ID.
 * @param array  $data    Údaje.
 * @param string $type    Typ.
 */
function asc_apply_notify( $post_id, $data, $type ) {
	$to = asc_opt( 'asc_apply_email' );
	if ( ! $to || ! is_email( $to ) ) {
		$to = get_option( 'admin_email' );
	}
	$labels = asc_application_labels();
	$lines  = array();
	foreach ( $labels as $key => $label ) {
		if ( empty( $data[ $key ] ) ) {
			continue;
		}
		$value = $data[ $key ];
		if ( 'ponuka' === $key ) {
			$value = get_the_title( (int) $value ) . ' – ' . get_permalink( (int) $value );
		}
		$lines[] = $label . ': ' . $value;
	}
	$lines[] = '';
	$lines[] = __( 'Detail v administrácii:', 'ascensio' ) . ' ' . admin_url( 'post.php?post=' . (int) $post_id . '&action=edit' );

	if ( 'partner' === $type ) {
		/* translators: %s firma */
		$subject = sprintf( __( 'Nový dopyt firmy: %s', 'ascensio' ), $data['firma'] );
	} else {
		/* translators: 1: meno, 2: pozícia */
		$subject = sprintf( __( 'Nová prihláška: %1$s – %2$s', 'ascensio' ), $data['meno'], $data['pozicia'] );
	}
	$headers = array( 'Content-Type: text/plain; charset=UTF-8' );
	if ( ! empty( $data['email'] ) && is_email( $data['email'] ) ) {
		$headers[] = 'Reply-To: ' . $data['meno'] . ' <' . $data['email'] . '>';
	}
	wp_mail( $to, $subject, implode( "\n", $lines ), $headers );
}

/**
 * Popisy polí prihlášky.
 */
function asc_application_labels() {
	return array(
		'firma'   => __( 'Firma', 'ascensio' ),
		'meno'    => __( 'Meno', 'ascensio' ),
		'telefon' => __( 'Telefón', 'ascensio' ),
		'email'   => __( 'E-mail', 'ascensio' ),
		'pozicia' => __( 'Pozícia', 'ascensio' ),
		'prax'    => __( 'Prax', 'ascensio' ),
		'nastup'  => __( 'Nástup', 'ascensio' ),
		'vodicak' => __( 'Vodičský preukaz B', 'ascensio' ),
		'krajina' => __( 'Krajina projektu', 'ascensio' ),
		'sprava'  => __( 'Správa', 'ascensio' ),
		'ponuka'  => __( 'Reaguje na ponuku', 'ascensio' ),
		'jazyk'   => __( 'Jazyk formulára', 'ascensio' ),
		'stranka' => __( 'Odoslané zo stránky', 'ascensio' ),
	);
}

/* ---------- Administrácia ---------- */

add_filter( 'manage_asc_application_posts_columns', 'asc_application_columns' );
/**
 * Stĺpce zoznamu prihlášok.
 *
 * @param array $cols Stĺpce.
 */
function asc_application_columns( $cols ) {
	return array(
		'cb'         => isset( $cols['cb'] ) ? $cols['cb'] : '',
		'title'      => __( 'Meno / pozícia', 'ascensio' ),
		'asc_type'   => __( 'Typ', 'ascensio' ),
		'asc_phone'  => __( 'Telefón', 'ascensio' ),
		'asc_email'  => __( 'E-mail', 'ascensio' ),
		'asc_status' => __( 'Stav', 'ascensio' ),
		'date'       => __( 'Dátum', 'ascensio' ),
	);
}

add_action( 'manage_asc_application_posts_custom_column', 'asc_application_column_value', 10, 2 );
/**
 * Hodnoty stĺpcov.
 *
 * @param string $col     Stĺpec.
 * @param int    $post_id ID.
 */
function asc_application_column_value( $col, $post_id ) {
	$data = (array) get_post_meta( $post_id, '_asc_data', true );
	switch ( $col ) {
		case 'asc_type':
			echo 'partner' === get_post_meta( $post_id, '_asc_type', true ) ? esc_html__( 'Firma', 'ascensio' ) : esc_html__( 'Uchádzač', 'ascensio' );
			break;
		case 'asc_phone':
			if ( ! empty( $data['telefon'] ) ) {
				echo '<a href="' . esc_url( asc_tel( $data['telefon'] ) ) . '">' . esc_html( $data['telefon'] ) . '</a>';
			}
			break;
		case 'asc_email':
			if ( ! empty( $data['email'] ) ) {
				echo '<a href="' . esc_url( 'mailto:' . $data['email'] ) . '">' . esc_html( $data['email'] ) . '</a>';
			}
			break;
		case 'asc_status':
			$statuses = asc_application_statuses();
			$status   = get_post_meta( $post_id, '_asc_status', true );
			$status   = isset( $statuses[ $status ] ) ? $status : 'nova';
			$colors   = array(
				'nova'         => '#b26b00',
				'kontaktovany' => '#2271b1',
				'pohovor'      => '#6b3fa0',
				'prijaty'      => '#00a32a',
				'zamietnuty'   => '#8c8f94',
			);
			echo '<span style="display:inline-block;padding:2px 9px;border-radius:99px;font-weight:600;font-size:12px;color:#fff;background:' . esc_attr( $colors[ $status ] ) . '">' . esc_html( $statuses[ $status ] ) . '</span>';
			break;
	}
}

add_action( 'add_meta_boxes_asc_application', 'asc_application_metaboxes' );
/**
 * Boxy v detaile prihlášky.
 */
function asc_application_metaboxes() {
	add_meta_box( 'asc_app_data', __( 'Údaje z formulára', 'ascensio' ), 'asc_application_data_box', 'asc_application', 'normal', 'high' );
	add_meta_box( 'asc_app_status', __( 'Stav prihlášky', 'ascensio' ), 'asc_application_status_box', 'asc_application', 'side', 'high' );
}

/**
 * Tabuľka údajov.
 *
 * @param WP_Post $post Prihláška.
 */
function asc_application_data_box( $post ) {
	$data = (array) get_post_meta( $post->ID, '_asc_data', true );
	echo '<table class="widefat striped"><tbody>';
	foreach ( asc_application_labels() as $key => $label ) {
		if ( empty( $data[ $key ] ) ) {
			continue;
		}
		$value = $data[ $key ];
		echo '<tr><th style="width:200px">' . esc_html( $label ) . '</th><td>';
		if ( 'telefon' === $key ) {
			echo '<a href="' . esc_url( asc_tel( $value ) ) . '">' . esc_html( $value ) . '</a>';
		} elseif ( 'email' === $key ) {
			echo '<a href="' . esc_url( 'mailto:' . $value ) . '">' . esc_html( $value ) . '</a>';
		} elseif ( 'ponuka' === $key ) {
			echo '<a href="' . esc_url( (string) get_edit_post_link( (int) $value ) ) . '">' . esc_html( get_the_title( (int) $value ) ) . '</a>';
		} elseif ( 'stranka' === $key ) {
			echo '<a href="' . esc_url( $value ) . '" target="_blank" rel="noopener">' . esc_html( $value ) . '</a>';
		} else {
			echo nl2br( esc_html( $value ) );
		}
		echo '</td></tr>';
	}
	echo '</tbody></table>';
	echo '<p style="color:#646970">' . esc_html__( 'Osobné údaje uchovávajte len po dobu uvedenú v zásadách ochrany osobných údajov. Staré prihlášky vymažte (Presunúť do koša → Vyprázdniť kôš).', 'ascensio' ) . '</p>';
}

/**
 * Výber stavu.
 *
 * @param WP_Post $post Prihláška.
 */
function asc_application_status_box( $post ) {
	wp_nonce_field( 'asc_app_status', 'asc_app_status_nonce' );
	$current = get_post_meta( $post->ID, '_asc_status', true );
	echo '<select name="asc_status" style="width:100%">';
	foreach ( asc_application_statuses() as $key => $label ) {
		echo '<option value="' . esc_attr( $key ) . '" ' . selected( $current, $key, false ) . '>' . esc_html( $label ) . '</option>';
	}
	echo '</select><p class="description">' . esc_html__( 'Po zmene kliknite na Aktualizovať.', 'ascensio' ) . '</p>';
}

add_action( 'save_post_asc_application', 'asc_application_save_status' );
/**
 * Uloženie stavu.
 *
 * @param int $post_id ID.
 */
function asc_application_save_status( $post_id ) {
	if ( ! isset( $_POST['asc_app_status_nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['asc_app_status_nonce'] ) ), 'asc_app_status' ) ) {
		return;
	}
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return;
	}
	$status = isset( $_POST['asc_status'] ) ? sanitize_key( wp_unslash( $_POST['asc_status'] ) ) : 'nova';
	if ( array_key_exists( $status, asc_application_statuses() ) ) {
		update_post_meta( $post_id, '_asc_status', $status );
		delete_transient( 'asc_new_apps' );
	}
}

add_action( 'admin_menu', 'asc_application_menu_badge', 99 );
/**
 * Počet nových prihlášok v menu.
 */
function asc_application_menu_badge() {
	global $menu;
	if ( ! current_user_can( 'edit_others_posts' ) || ! is_array( $menu ) ) {
		return;
	}
	$count = get_transient( 'asc_new_apps' );
	if ( false === $count ) {
		$q     = new WP_Query(
			array(
				'post_type'      => 'asc_application',
				'post_status'    => 'publish',
				'posts_per_page' => 1,
				'fields'         => 'ids',
				'meta_key'       => '_asc_status', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_key
				'meta_value'     => 'nova', // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_meta_value
			)
		);
		$count = (int) $q->found_posts;
		set_transient( 'asc_new_apps', $count, HOUR_IN_SECONDS );
	}
	if ( ! $count ) {
		return;
	}
	foreach ( $menu as $i => $item ) {
		if ( isset( $item[2] ) && 'edit.php?post_type=asc_application' === $item[2] ) {
			$menu[ $i ][0] .= ' <span class="awaiting-mod"><span class="pending-count">' . (int) $count . '</span></span>'; // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
			break;
		}
	}
}

add_filter( 'post_row_actions', 'asc_application_row_actions', 10, 2 );
/**
 * Bez rýchlej úpravy pri prihláškach.
 *
 * @param array   $actions Akcie.
 * @param WP_Post $post    Príspevok.
 */
function asc_application_row_actions( $actions, $post ) {
	if ( 'asc_application' === $post->post_type ) {
		unset( $actions['inline hide-if-no-js'], $actions['view'] );
	}
	return $actions;
}
