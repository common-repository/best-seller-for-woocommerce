<?php
namespace GPLSCore\GPLS_PLUGIN_WOBTSLR;

use GPLSCore\GPLS_PLUGIN_WOBTSLR\Data\BestSellersData;
use GPLSCore\GPLS_PLUGIN_WOBTSLR\Settings\MainSettings;

/**
 * Best Seller Product Class.
 */
class BestSellerProduct extends Base {

	/**
	 * Singular Instance.
	 *
	 * @var self
	 */
	private static $instance = null;


	/**
	 * Best Seller Data.
	 *
	 * @var BestSellerData
	 */
	private static $best_seller_data;

	/**
	 * Settings.
	 *
	 * @var MainSettings
	 */
	private static $settings;

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
		self::$best_seller_data = new BestSellersData();
		self::$settings         = MainSettings::init();
		$this->hooks();
	}

	/**
	 * HOoks.
	 *
	 * @return void
	 */
	private function hooks() {
		add_action( 'woocommerce_product_options_general_product_data', array( $this, 'best_sellers_settings' ) );
		add_action( 'woocommerce_update_product', array( $this, 'update_settings' ), 100, 1 );
	}

	/**
	 * Best Seller Setting per product.
	 *
	 * @return void
	 */
	public function best_sellers_settings() {
		?>
		<div class="options_group">
			<?php
			woocommerce_wp_checkbox(
				array(
					'id'                => self::$plugin_info['name'] . '-show-best-seller-badge',
					'label'             => esc_html__( 'BestSeller [GrandPlugins]', 'best-seller-for-woocommerce' ),
					'description'       => esc_html__( 'Show best seller badge', 'best-seller-for-woocommerce' ) . self::$core->pro_btn( '', 'Pro', '', 'border-radius:6px;padding:5px 10px;display:inline-flex !important;', true ),
					'custom_attributes' => array(
						'disabled' => 'disabled',
					),
				)
			);
			?>
		</div>
		<style>
			.gpls-permium-btn-wave { display: inline-flex !important;}
		</style>
		<?php
	}

	public function update_settings( $product_id ) {
		if ( ! empty( $_POST[ self::$plugin_info['name'] . '-show-best-seller-badge' ] ) ) {
			update_post_meta( $product_id, self::$plugin_info['name'] . '-show-best-seller-badge', 'yes' );
		} else {
			update_post_meta( $product_id, self::$plugin_info['name'] . '-show-best-seller-badge', 'no' );
		}
	}

	/**
	 * Show Best Seller Badge for a product.
	 *
	 * @param int product_id
	 * @return boolean
	 */
	public static function show_best_seller_badge( $product_id ) {
		return ( 'yes' === get_post_meta( $product_id, self::$plugin_info['name'] . '-show-best-seller-badge', true ) );
	}

	/**
	 * Check if the product is a best seller.
	 *
	 * @param int|\WC_Product $product
	 * @return boolean
	 */
	public static function is_product_best_seller( $product ) {
		if ( is_admin() ) {
			return false;
		}
		$product_id = is_int( $product ) ? $product : $product->get_id();

		// Check General Best Sellers.
		if ( self::is_product_best_seller_in_general( $product_id, true ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Get Product Total Sales.
	 *
	 * @param int $product_id
	 * @return int
	 */
	public static function get_product_total_sales( $product_id ) {
		return absint( get_post_meta( $product_id, 'total_sales', true ) );
	}

	/**
	 * Check if a product exists in general best seller products.
	 *
	 * @param int $product_id
	 * @return boolean
	 */
	public static function is_product_best_seller_in_general( $product_id, $for_badge = false ) {
		$general_best_sellers = self::$best_seller_data->get_data( 'general_best_sellers' );
		if ( $for_badge ) {
			$max_best_seller_badge_rank = self::$settings->get_settings( 'max_best_seller' );
			return isset( $general_best_sellers[ $product_id ] ) && ( $max_best_seller_badge_rank >= $general_best_sellers[ $product_id ]['rank'] );
		}
		return isset( $general_best_sellers[ $product_id ] );
	}

}
