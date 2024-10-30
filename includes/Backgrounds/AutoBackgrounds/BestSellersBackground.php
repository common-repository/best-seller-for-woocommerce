<?php
namespace GPLSCore\GPLS_PLUGIN_WOBTSLR\Backgrounds\AutoBackgrounds;

use GPLSCore\GPLS_PLUGIN_WOBTSLR\Backgrounds\AutoBackgrounds\AutoBackgroundProcess;

use GPLSCore\GPLS_PLUGIN_WOBTSLR\BestSellerProducts;
use GPLSCore\GPLS_PLUGIN_WOBTSLR\Data\BestSellersData;
use GPLSCore\GPLS_PLUGIN_WOBTSLR\Settings\MainSettings;

/**
 * Best Sellers Background Class.
 */
class BestSellersBackground extends AutoBackgroundProcess {

	/**
	 * Singular Instance.
	 *
	 * @var self
	 */
	protected static $instance = null;

	/**
	 * Best Sellers Data.
	 *
	 * @var BestSellersData
	 */
	private $best_sellers_data;

	/**
	 * Best Seller Products.
	 *
	 * @var BestSellerProducts
	 */
	private $best_seller_products;

	/**
	 * Background Interval.
	 *
	 * @var int
	 */
	protected $interval = \HOUR_IN_SECONDS * 12;

	/**
	 * Best Seller Settings.
	 *
	 * @var MainSettings
	 */
	private $settings;

	/**
	 * Background Process ID.
	 *
	 * @return string
	 */
	protected static function get_id() {
		return self::$plugin_info['name'] . '-best-sellers-background';
	}

	/**
	 * Background Hooks.
	 */
	protected function background_hooks() {
		add_action( $this->settings->get_id() . '-after-settings-save', array( $this, 'run_best_seller_data_background' ), 100, 5 );
		add_action( 'woocommerce_order_status_completed', array( $this, 'run_update_best_sellers_data' ), PHP_INT_MAX );
		add_action( 'woocommerce_order_status_processing', array( $this, 'run_update_best_sellers_data' ), PHP_INT_MAX );
	}

	/**
	 * Process Setup.
	 *
	 * @return void
	 */
	protected function setup() {
		$this->best_sellers_data    = new BestSellersData();
		$this->best_seller_products = BestSellerProducts::init();
		$this->settings             = MainSettings::init();
	}

	/**
	 * Best Sellers Background Task.
	 *
	 * @param mixed $key
	 * @param mixed $value
	 * @param array $data
	 * @return mixed
	 */
	protected function task( $key, $value, &$data ) {
		switch ( $value ) {
			case 'general_best_sellers':
				$this->best_seller_products->get_best_seller_in_general( true );
				break;
		}
		return false;
	}

	/**
	 * Auto Tasks.
	 *
	 * @return void
	 */
	public function auto() {
		$this->data( $this->best_sellers_data->get_data_for_cron() );
	}

	/**
	 * Run The background Process.
	 *
	 * @return void
	 */
	public function run_background_process() {
		$data = $this->best_sellers_data->get_data_for_cron();
		$this->run( $data );
	}

	/**
	 * Run The best seller Data update background process.
	 */
	public function run_best_seller_data_background( $settings, $old_setings, $tab, $is_saving, $settings_page ) {
		if ( 'general' !== $tab ) {
			return;
		}
		$this->run_background_process();
	}

	/**
	 * Run Update Best Sellers Background Data.
	 */
	public function run_update_best_sellers_data() {
		$this->run_background_process();
	}
}
