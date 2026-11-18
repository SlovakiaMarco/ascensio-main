<?php
/**
 * Pätička webu.
 *
 * Upraviť: Vzhľad > Menu (Pätička, Právne odkazy), Vzhľad > Prispôsobiť > Ascensio (kontakty, text, siete).
 *
 * @package Ascensio
 */

defined( 'ABSPATH' ) || exit;

$asc_phone = asc_opt( 'asc_phone' );
$asc_email = asc_opt( 'asc_email' );
$asc_wa    = asc_opt( 'asc_whatsapp' );
?>
</main>

<?php if ( ! asc_builder_location( 'footer' ) ) : ?>
	<footer class="asc-footer">
		<div class="asc-container asc-footer__main">
			<div class="asc-footer__brand">
				<div class="asc-footer__logo"><?php echo asc_logo( true ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<p class="asc-footer__about"><?php echo esc_html( asc_opt( 'asc_footer_text' ) ); ?></p>
				<?php $asc_socials = asc_socials(); ?>
				<?php if ( $asc_socials ) : ?>
					<ul class="asc-social-links">
						<?php foreach ( $asc_socials as $asc_net => $asc_url ) : ?>
							<li><a href="<?php echo esc_url( $asc_url ); ?>" target="_blank" rel="noopener" aria-label="<?php echo esc_attr( ucfirst( $asc_net ) ); ?>"><?php echo asc_icon( $asc_net ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a></li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>
			</div>

			<div>
				<h2><?php esc_html_e( 'Pre uchádzačov', 'ascensio' ); ?></h2>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'footer',
						'container'      => false,
						'menu_class'     => 'asc-footer__menu',
						'depth'          => 1,
						'fallback_cb'    => 'asc_menu_fallback',
					)
				);
				?>
			</div>

			<div>
				<h2><?php esc_html_e( 'Kontakt', 'ascensio' ); ?></h2>
				<ul class="asc-footer__contact">
					<?php if ( $asc_phone ) : ?>
						<li><?php echo asc_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><a href="<?php echo esc_url( asc_tel() ); ?>"><?php echo esc_html( $asc_phone ); ?></a></li>
					<?php endif; ?>
					<?php if ( $asc_wa ) : ?>
						<li><?php echo asc_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><a href="<?php echo esc_url( asc_wa( $asc_wa ) ); ?>" target="_blank" rel="noopener">WhatsApp</a></li>
					<?php endif; ?>
					<?php if ( $asc_email ) : ?>
						<li><?php echo asc_icon( 'mail' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><a href="<?php echo esc_url( 'mailto:' . $asc_email ); ?>"><?php echo esc_html( $asc_email ); ?></a></li>
					<?php endif; ?>
					<?php if ( asc_opt( 'asc_address' ) ) : ?>
						<li><?php echo asc_icon( 'pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?><address><?php echo esc_html( asc_opt( 'asc_address' ) ); ?></address></li>
					<?php endif; ?>
				</ul>
			</div>

			<div>
				<h2><?php esc_html_e( 'Pre firmy', 'ascensio' ); ?></h2>
				<div class="asc-footer__b2b">
					<strong><?php esc_html_e( 'Hľadáte remeselníkov?', 'ascensio' ); ?></strong>
					<p><?php esc_html_e( 'Dodávame skúsených pracovníkov pre stavebné projekty v Nemecku a Francúzsku.', 'ascensio' ); ?></p>
					<a class="asc-btn asc-btn--sm" href="<?php echo esc_url( asc_page_url( 'pre-firmy' ) ); ?>"><?php esc_html_e( 'Spolupráca', 'ascensio' ); ?> <?php echo asc_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
					<?php if ( has_nav_menu( 'topbar' ) ) : ?>
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'topbar',
								'container'      => false,
								'menu_class'     => 'asc-footer__langs',
								'depth'          => 1,
								'fallback_cb'    => false,
							)
						);
						?>
					<?php endif; ?>
				</div>
			</div>
		</div>

		<div class="asc-footer__bottom">
			<div class="asc-container">
				<p>&copy; <?php echo esc_html( wp_date( 'Y' ) ); ?> <?php echo esc_html( asc_opt( 'asc_company' ) ); ?> · Ascensio</p>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'legal',
						'container'      => false,
						'menu_class'     => 'asc-footer__legal',
						'depth'          => 1,
						'fallback_cb'    => false,
					)
				);
				?>
				<a href="#obsah" class="asc-footer__top"><?php esc_html_e( 'Hore', 'ascensio' ); ?> ↑</a>
			</div>
		</div>
	</footer>
<?php endif; ?>

<?php if ( asc_opt( 'asc_mobile_bar' ) ) : ?>
	<div class="asc-mbar<?php echo $asc_wa ? ' asc-mbar__wa' : ''; ?>" id="asc-mbar">
		<?php if ( $asc_phone ) : ?>
			<a class="asc-btn asc-btn--outline" href="<?php echo esc_url( asc_tel() ); ?>"><?php echo asc_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php esc_html_e( 'Zavolať', 'ascensio' ); ?></a>
		<?php endif; ?>
		<?php if ( $asc_wa ) : ?>
			<a class="asc-btn asc-btn--outline" href="<?php echo esc_url( asc_wa( $asc_wa ) ); ?>" target="_blank" rel="noopener"><?php echo asc_icon( 'whatsapp' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> WhatsApp</a>
		<?php endif; ?>
		<a class="asc-btn" href="<?php echo esc_url( asc_apply_url() ); ?>"><?php echo esc_html( asc_opt( 'asc_cta_text' ) ); ?> <?php echo asc_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
	</div>
<?php endif; ?>

<?php wp_footer(); ?>
</body>
</html>
