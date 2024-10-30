<?php
namespace GPLSCore\GPLS_PLUGIN_WOBTSLR\Pages\PagesBase;

use GPLSCore\GPLS_PLUGIN_WOBTSLR\Pages\SettingsPage;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Init Pages.
 */
function setup_pages() {
	SettingsPage::init();
}
