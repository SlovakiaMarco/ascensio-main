<?php
/**
 * Detail pracovnej ponuky.
 *
 * Obsah: text z editora + polia z boxu „Detaily ponuky“. Formulár má predvyplnenú pozíciu.
 *
 * @package Ascensio
 */

defined( 'ABSPATH' ) || exit;

get_header();

while ( have_posts() ) :
	the_post();
	$asc_id      = get_the_ID();
	$asc_trades  = asc_job_terms( $asc_id, 'asc_trade' );
	$asc_country = asc_job_terms( $asc_id, 'asc_country' );
	$asc_where   = asc_job_meta( $asc_id, 'lokalita' ) ? asc_job_meta( $asc_id, 'lokalita' ) : implode( ', ', $asc_country );
	$asc_mzda    = asc_job_meta( $asc_id, 'mzda' );
	$asc_nastup  = asc_job_meta( $asc_id, 'nastup' );
	$asc_uvazok  = asc_job_meta( $asc_id, 'uvazok' );
	$asc_ubyt    = asc_job_meta( $asc_id, 'ubytovanie' );
	$asc_req     = asc_lines( asc_job_meta( $asc_id, 'poziadavky' ) );
	$asc_offer   = asc_lines( asc_job_meta( $asc_id, 'ponukame' ) );
	$asc_offer   = $asc_offer ? $asc_offer : asc_default_offer();
	$asc_pos     = $asc_trades ? reset( $asc_trades ) : get_the_title();
	// Ak niekto ponuku upraví v Elementore, vypíše sa jeho obsah namiesto panelu „Popis práce“.
	$asc_has_elementor = asc_is_elementor( $asc_id );
	?>
	<section class="asc-jobhero asc-pattern-bg">
		<div class="asc-container">
			<nav class="asc-crumbs" aria-label="<?php esc_attr_e( 'Omrvinková navigácia', 'ascensio' ); ?>">
				<a href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Domov', 'ascensio' ); ?></a> <span aria-hidden="true">/</span>
				<a href="<?php echo esc_url( asc_page_url( 'pracovne-ponuky' ) ); ?>"><?php esc_html_e( 'Pracovné ponuky', 'ascensio' ); ?></a> <span aria-hidden="true">/</span>
				<span aria-current="page"><?php the_title(); ?></span>
			</nav>
			<h1><?php the_title(); ?></h1>
			<div class="asc-jobhero__badges">
				<?php if ( asc_job_meta( $asc_id, 'top' ) ) : ?>
					<span class="asc-badge asc-badge--top"><?php esc_html_e( 'TOP ponuka', 'ascensio' ); ?></span>
				<?php endif; ?>
				<?php if ( $asc_where ) : ?>
					<span class="asc-badge"><?php echo asc_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php echo esc_html( $asc_where ); ?></span>
				<?php endif; ?>
				<?php if ( $asc_mzda ) : ?>
					<span class="asc-badge"><?php echo asc_icon( 'euro' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php echo esc_html( $asc_mzda ); ?></span>
				<?php endif; ?>
				<?php if ( $asc_nastup ) : ?>
					<span class="asc-badge"><?php echo asc_icon( 'calendar' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php echo esc_html( sprintf( /* translators: %s nástup */ __( 'Nástup: %s', 'ascensio' ), $asc_nastup ) ); ?></span>
				<?php endif; ?>
				<?php if ( $asc_ubyt ) : ?>
					<span class="asc-badge"><?php echo asc_icon( 'home' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php echo esc_html( sprintf( /* translators: %s ubytovanie */ __( 'Ubytovanie: %s', 'ascensio' ), $asc_ubyt ) ); ?></span>
				<?php endif; ?>
			</div>
			<div class="asc-jobhero__cta">
				<a class="asc-btn" href="#reagovat"><?php esc_html_e( 'Reagovať na ponuku', 'ascensio' ); ?> <?php echo asc_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
				<?php if ( asc_opt( 'asc_phone' ) ) : ?>
					<a class="asc-btn asc-btn--outline-light" href="<?php echo esc_url( asc_tel() ); ?>"><?php echo asc_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php echo esc_html( asc_opt( 'asc_phone' ) ); ?></a>
				<?php endif; ?>
			</div>
		</div>
	</section>

	<section class="asc-jobbody">
		<div class="asc-container asc-jobbody__grid">
			<div class="asc-jobbody__main">
				<?php if ( $asc_has_elementor ) : ?>
					<?php the_content(); ?>
				<?php else : ?>
					<div class="asc-panel">
						<h2><?php esc_html_e( 'Popis práce', 'ascensio' ); ?></h2>
						<div class="asc-prose-inline entry-content"><?php the_content(); ?></div>
					</div>
				<?php endif; ?>

				<?php if ( $asc_req ) : ?>
					<div class="asc-panel">
						<h2><?php esc_html_e( 'Požiadavky', 'ascensio' ); ?></h2>
						<ul class="asc-ticks asc-ticks--navy">
							<?php foreach ( $asc_req as $asc_line ) : ?>
								<li><?php echo esc_html( $asc_line ); ?></li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

				<div class="asc-panel">
					<h2><?php esc_html_e( 'Čo ti zabezpečíme', 'ascensio' ); ?></h2>
					<ul class="asc-ticks">
						<?php foreach ( $asc_offer as $asc_line ) : ?>
							<li><?php echo esc_html( $asc_line ); ?></li>
						<?php endforeach; ?>
					</ul>
				</div>

				<div id="reagovat">
					<?php
					echo asc_render_form( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						array(
							'variant'  => 'full',
							/* translators: %s pozícia */
							'title'    => sprintf( __( 'Reagovať na ponuku: %s', 'ascensio' ), get_the_title() ),
							'subtitle' => __( 'Vyplň krátky formulár – ozveme sa ti telefonicky. Polia označené * sú povinné.', 'ascensio' ),
							'button'   => __( 'Odoslať prihlášku', 'ascensio' ),
							'position' => $asc_pos,
							'job_id'   => $asc_id,
						)
					);
					?>
				</div>
			</div>

			<aside class="asc-summary" aria-label="<?php esc_attr_e( 'Zhrnutie ponuky', 'ascensio' ); ?>">
				<h2><?php esc_html_e( 'Zhrnutie', 'ascensio' ); ?></h2>
				<dl>
					<?php
					$asc_rows = array(
						array( 'briefcase', __( 'Pozícia', 'ascensio' ), get_the_title() ),
						array( 'pin', __( 'Miesto', 'ascensio' ), $asc_where ),
						array( 'euro', __( 'Mzda', 'ascensio' ), $asc_mzda ),
						array( 'calendar', __( 'Nástup', 'ascensio' ), $asc_nastup ),
						array( 'clock', __( 'Úväzok', 'ascensio' ), $asc_uvazok ),
						array( 'home', __( 'Ubytovanie', 'ascensio' ), $asc_ubyt ),
					);
					foreach ( $asc_rows as $asc_row ) :
						if ( ! $asc_row[2] ) {
							continue;
						}
						?>
						<div><dt><?php echo asc_icon( $asc_row[0] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><?php echo esc_html( $asc_row[1] ); ?></dt><dd><?php echo esc_html( $asc_row[2] ); ?></dd></div>
					<?php endforeach; ?>
				</dl>
				<a class="asc-btn" href="#reagovat"><?php esc_html_e( 'Reagovať na ponuku', 'ascensio' ); ?></a>
				<?php if ( asc_opt( 'asc_phone' ) ) : ?>
					<a class="asc-btn asc-btn--outline" href="<?php echo esc_url( asc_tel() ); ?>"><?php echo asc_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php echo esc_html( asc_opt( 'asc_phone' ) ); ?></a>
				<?php endif; ?>
				<p class="asc-summary__help"><?php esc_html_e( 'Máš otázku? Zavolaj – všetko ti vysvetlíme.', 'ascensio' ); ?></p>
			</aside>
		</div>
	</section>

	<?php
	$asc_related = asc_get_jobs(
		array(
			'count'   => 3,
			'exclude' => array( $asc_id ),
		)
	);
	if ( $asc_related ) :
		?>
		<section class="asc-related">
			<div class="asc-container">
				<h2><?php esc_html_e( 'Ďalšie pracovné ponuky', 'ascensio' ); ?></h2>
				<div class="asc-jobs__grid">
					<?php
					foreach ( $asc_related as $asc_job ) {
						echo asc_job_card( $asc_job ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
					}
					?>
				</div>
			</div>
		</section>
		<?php
	endif;
endwhile;

get_footer();
