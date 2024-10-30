<?php
namespace GPLSCore\GPLS_PLUGIN_WOBTSLR\Data;

use GPLSCore\GPLS_PLUGIN_WOBTSLR\BestSellerQueries;
use GPLSCore\GPLS_PLUGIN_WOBTSLR\Data\Base\OptionsData;
use GPLSCore\GPLS_PLUGIN_WOBTSLR\Settings\MainSettings;

/**
 * Best Sellers Data.
 */
class BestSellersData extends OptionsData {

	/**
	 * Options Autoload.
	 *
	 * @var boolean
	 */
	protected $autoload = true;

	/**
	 * Set Options Key.
	 *
	 * @return void
	 */
	protected function set_options_key() {
		$this->options_key = self::$plugin_info['classes_prefix'] . '-best-seller-data';
	}

	/**
	 * Set Default Best Seller Data.
	 *
	 * @return void
	 */
	protected function set_default_data() {
		$this->default_data = array(
			'general_best_sellers'     => array(),
			'best_sellers_categories'  => array(),
			'best_sellers_by_category' => array(),
		);
	}

	/**
	 * Get Data to fetch for Cron.
	 *
	 * @return array
	 */
	public function get_data_for_cron() {
		$catgs = get_terms(
			'product_cat',
			array(
				'orderby'    => 'count',
				'hide_empty' => true,
				'fields'     => 'ids',
			)
		);

		$catgs = array_map(
			function( $cat_id ) {
				return 'cat_' . $cat_id;
			},
			$catgs
		);

		$data = array_merge( array( 'general_best_sellers', 'best_sellers_categories' ), $catgs );
		return $data;
	}
}
