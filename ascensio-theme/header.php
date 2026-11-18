<?php
/**
 * Hlavička webu.
 *
 * Upraviť: Vzhľad > Menu (položky), Vzhľad > Prispôsobiť > Ascensio (telefón, tlačidlo, horná lišta).
 * S Elementor Pro alebo pluginom „Elementor Header & Footer Builder“ ju možno nahradiť vlastnou.
 *
 * @package Ascensio
 */

defined( 'ABSPATH' ) || exit;

$asc_phone = asc_opt( 'asc_phone' );
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link" href="#obsah"><?php esc_html_e( 'Preskočiť na obsah', 'ascensio' ); ?></a>

<?php if ( ! asc_builder_location( 'header' ) ) : ?>

	<?php if ( asc_opt( 'asc_topbar' ) ) : ?>
		<div class="asc-topbar">
			<div class="asc-container asc-topbar__inner">
				<p class="asc-topbar__text"><?php echo asc_icon( 'globe' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php echo esc_html( asc_opt( 'asc_topbar_text' ) ); ?></p>
				<?php if ( has_nav_menu( 'topbar' ) ) : ?>
					<div class="asc-topbar__right" style="display:flex;align-items:center;gap:4px">
						<span class="asc-topbar__label"><?php esc_html_e( 'Pre firmy:', 'ascensio' ); ?></span>
						<?php
						wp_nav_menu(
							array(
								'theme_location' => 'topbar',
								'container'      => false,
								'menu_class'     => 'asc-topbar__links',
								'depth'          => 1,
								'fallback_cb'    => false,
							)
						);
						?>
					</div>
				<?php endif; ?>
			</div>
		</div>
	<?php endif; ?>

	<header class="asc-header" id="asc-header">
		<div class="asc-container asc-header__inner">
			<?php echo asc_logo(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>

			<nav class="asc-nav" id="asc-nav" aria-label="<?php esc_attr_e( 'Hlavné menu', 'ascensio' ); ?>">
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'container'      => false,
						'menu_class'     => 'asc-menu',
						'depth'          => 2,
						'fallback_cb'    => 'asc_menu_fallback',
					)
				);
				?>
				<div class="asc-nav__extra">
					<a class="asc-btn" href="<?php echo esc_url( asc_apply_url() ); ?>"><?php echo esc_html( asc_opt( 'asc_cta_text' ) ); ?> <?php echo asc_icon( 'arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></a>
					<?php if ( $asc_phone ) : ?>
						<a class="asc-btn asc-btn--outline" href="<?php echo esc_url( asc_tel() ); ?>"><?php echo asc_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> <?php echo esc_html( $asc_phone ); ?></a>
					<?php endif; ?>
					<?php if ( has_nav_menu( 'topbar' ) ) : ?>
						<div class="asc-nav__langs">
							<?php
							wp_nav_menu(
								array(
									'theme_location' => 'topbar',
									'container'      => false,
									'menu_class'     => 'asc-topbar__links',
									'depth'          => 1,
									'fallback_cb'    => false,
								)
							);
							?>
						</div>
					<?php endif; ?>
				</div>
			</nav>

			<div class="asc-header__actions">
				<?php if ( $asc_phone ) : ?>
					<a class="asc-header__phone" href="<?php echo esc_url( asc_tel() ); ?>" aria-label="<?php echo esc_attr( sprintf( /* translators: %s telefón */ __( 'Zavolať %s', 'ascensio' ), $asc_phone ) ); ?>">
						<span class="asc-header__phoneicon"><?php echo asc_icon( 'phone' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></span>
						<span class="asc-header__phonetext"><small><?php esc_html_e( 'Zavolajte nám', 'ascensio' ); ?></small><?php echo esc_html( $asc_phone ); ?></span>
					</a>
				<?php endif; ?>
				<a class="asc-btn asc-header__cta" href="<?php echo esc_url( asc_apply_url() ); ?>"><?php echo esc_html( asc_opt( 'asc_cta_text' ) ); ?></a>
				<button class="asc-burger" type="button" aria-controls="asc-nav" aria-expanded="false" aria-label="<?php esc_attr_e( 'Otvoriť menu', 'ascensio' ); ?>">
					<?php echo asc_icon( 'menu', 'asc-icon--menu' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					<?php echo asc_icon( 'close', 'asc-icon--close' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</button>
			</div>
		</div>
	</header>

<?php endif; ?>

<main id="obsah" class="asc-main">
