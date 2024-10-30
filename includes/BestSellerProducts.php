<?php
namespace GPLSCore\GPLS_PLUGIN_WOBTSLR;

use GPLSCore\GPLS_PLUGIN_WOBTSLR\BestSellerQueries;
use GPLSCore\GPLS_PLUGIN_WOBTSLR\Data\BestSellersData;
use GPLSCore\GPLS_PLUGIN_WOBTSLR\Settings\MainSettings;

/**
 * Best Seller Products Class.
 */
class BestSellerProducts extends Base {

	/**
	 * Singular Instance.
	 *
	 * @var self
	 */
	private static $instance = null;

	/**
	 * Best Seller Settings.
	 *
	 * @var MainSettings
	 */
	private $settings;

	/**
	 * Best Seller Data.
	 *
	 * @var BestSellersData
	 */
	private $best_seller_data;

	/**
	 * Init.
	 *
	 * @return self
	 */
	public static function init() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 */
	private function __construct() {
		$this->settings         = MainSettings::init();
		$this->best_seller_data = new BestSellersData();
		$this->hooks();
	}

	/**
	 * HOoks.
	 *
	 * @return void
	 */
	private function hooks() {
	}

	/**
	 * General Best Seller Products.
	 *
	 * @param boolean $update_data
	 * @return array
	 */
	public function get_best_seller_in_general( $update_data = true ) {
		$rank_limit           = absint( $this->settings->get_settings( 'max_best_sellers' ) );
		$best_seller_products = BestSellerQueries::get_ranked_best_sellers( $rank_limit );

		if ( $update_data ) {
			$best_seller_data                         = $this->best_seller_data->get_data( null, true );
			$best_seller_data['general_best_sellers'] = $best_seller_products;
			$this->best_seller_data->update_data( $best_seller_data );
		}

		return $best_seller_products;
	}

	/**
	 * Calculate Best Sellers Data.
	 *
	 * @return void
	 */
	public function update_best_seller_data() {
		$best_seller_data = array(
			'general_best_sellers'     => $this->get_best_seller_in_general( false ),
			'best_sellers_by_category' => array(),
			'best_sellers_categories'  => array(),
		);
		$this->best_seller_data->update_data( $best_seller_data );
	}

	/**
	 * Is Best Seller Products Listing.
	 *
	 * @return boolean
	 */
	public static function is_best_sellers_listing() {
		if ( empty( $_GET[ self::$plugin_info['classes_prefix'] . '-best-sellers' ] ) ) {
			return false;
		}

		return true;
	}
}
