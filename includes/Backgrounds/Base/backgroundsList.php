<?php
namespace GPLSCore\GPLS_PLUGIN_WOBTSLR\Backgrounds\Base;

use GPLSCore\GPLS_PLUGIN_WOBTSLR\Backgrounds\AutoBackgrounds\BestSellersBackground;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Init Backgrounds.
 */
function setup_backgrounds() {
	BestSellersBackground::init();
}

/**
 * Setup Auto backgrounds Cron.
 *
 * @return void
 */
function setup_auto_backgrounds() {
    BestSellersBackground::setup_auto_cron();
}

/**
 * Clear Backgrounds Cron.
 *
 * @return void
 */
function clear_backgrounds() {
	BestSellersBackground::clear_cron();
}
