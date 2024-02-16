<?php
/**
 * Includes the composer Autoloader used for packages and classes in the src/ directory.
 *
 * @since 1.3.0
 */

namespace KoiLab\WC_360_Image;

defined( 'ABSPATH' ) || exit;

/**
 * Autoloader class.
 */
class Autoloader {

	/**
	 * Initializes the autoloader.
	 *
	 * @since 1.3.0
	 *
	 * @return bool
	 */
	public static function init() {
		$autoloader = dirname( __DIR__ ) . '/vendor/autoload.php';

		return ( is_readable( $autoloader ) && ( require $autoloader ) );
	}
}
