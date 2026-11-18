<?php
/**
 * Ascensio – funkcie témy.
 *
 * @package Ascensio
 */

defined( 'ABSPATH' ) || exit;

define( 'ASC_VERSION', '1.0.0' );
define( 'ASC_DIR', get_template_directory() );
define( 'ASC_URI', get_template_directory_uri() );

require_once ASC_DIR . '/inc/helpers.php';
require_once ASC_DIR . '/inc/setup.php';
require_once ASC_DIR . '/inc/customizer.php';
require_once ASC_DIR . '/inc/jobs.php';
require_once ASC_DIR . '/inc/applications.php';
require_once ASC_DIR . '/inc/elementor.php';
require_once ASC_DIR . '/inc/schema.php';

if ( is_admin() ) {
	require_once ASC_DIR . '/inc/admin-setup.php';
}
