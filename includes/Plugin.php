<?php
namespace GPLSCore\GPLS_PLUGIN_WOBTSLR;

defined( 'ABSPATH' ) || exit;

use GPLSCore\GPLS_PLUGIN_WOBTSLR\Base;
use GPLSCore\GPLS_PLUGIN_WOBTSLR\BestSellerProduct;
use GPLSCore\GPLS_PLUGIN_WOBTSLR\BestSellerShortcodes;
use function GPLSCore\GPLS_PLUGIN_WOBTSLR\Backgrounds\Base\setup_backgrounds;
use function GPLSCore\GPLS_PLUGIN_WOBTSLR\Pages\PagesBase\setup_pages;

/**
 * Plugin Class for Activation - Deactivation - Uninstall.
 */
class Plugin extends Base {

	/**
	 * Main Class Load.
	 *
	 * @return void
	 */
	public static function load() {

		setup_pages();
		setup_backgrounds();
		BestSellerProduct::init();
		BestSellerProducts::init();
		BestSellerBadge::init();
		BestSellerShortcodes::init();
	}

	/**
	 * Plugin is activated.
	 *
	 * @return void
	 */
	public static function activated() {
		// Auto Backgrounds.
		if ( function_exists( __NAMESPACE__ . '\Backgrounds\Base\setup_auto_backgrounds' ) ) {
			Backgrounds\Base\setup_auto_backgrounds();
		}

		// Activation Custom Code here...
	}

	/**
	 * Plugin is Deactivated.
	 *
	 * @return void
	 */
	public static function deactivated() {
		// Clear Regular and Auto Background.
		if ( function_exists( __NAMESPACE__ . '\Backgrounds\Base\clear_backgrounds' ) ) {
			Backgrounds\Base\clear_backgrounds();
		}

		// Deactivation Custom Code here...
	}

	/**
	 * Plugin is Uninstalled.
	 *
	 * @return void
	 */
	public static function uninstalled() {
		// Uninstall Custom Code here...
	}

	/**
	 * Is Plugin Active.
	 *
	 * @param string $plugin_basename
	 * @return boolean
	 */
	public static function is_plugin_active( $plugin_basename ) {
		require_once \ABSPATH . 'wp-admin/includes/plugin.php';
		return is_plugin_active( $plugin_basename );
	}
}
