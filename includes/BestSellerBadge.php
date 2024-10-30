<?php
namespace GPLSCore\GPLS_PLUGIN_WOBTSLR;

use GPLSCore\GPLS_PLUGIN_WOBTSLR\Utils\ImgUtilsTrait;
use GPLSCore\GPLS_PLUGIN_WOBTSLR\Settings\MainSettings;
use GPLSCore\GPLS_PLUGIN_WOBTSLR\BestSellerShortcodes;

/**
 * Best Seller Badge Class.
 */
class BestSellerBadge extends Base {

	use ImgUtilsTrait;

	/**
	 * Singular Instance.
	 *
	 * @var self
	 */
	private static $instance = null;

	/**
	 * Settings.
	 *
	 * @var MainSettings
	 */
	private $settings;

	/**
	 * Icon Badge URL.
	 *
	 * @var string
	 */
	private $icon_badge_url = null;

	/**
	 * Badges Attributes.
	 *
	 * @var array
	 */
	private $badges_attrs = array();

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
		$this->settings = MainSettings::init();

		$this->hooks();
		$this->setup();
	}

	/**
	 * HOoks.
	 *
	 * @return void
	 */
	private function hooks() {
		// Badge in Single and loop page.
		add_action( 'woocommerce_before_shop_loop_item', array( $this, 'loop_badge' ), 11 );
		add_action( 'woocommerce_before_shop_loop_item_title', array( $this, 'loop_badge' ), 1 );
		add_action( 'woocommerce_product_thumbnails', array( $this, 'single_badge' ), PHP_INT_MAX );
		// Badge Styles.
		add_action( 'wp_head', array( $this, 'badge_styles' ), PHP_INT_MAX );
		add_action( 'wp_enqueue_scripts', array( get_called_class(), 'front_assets' ) );
		add_action( 'woocommerce_single_product_summary', array( $this, 'clear_marked_badged_products_single' ), PHP_INT_MAX );
		add_action( 'woocommerce_after_shop_loop', array( $this, 'clear_marked_badged_products_loop' ), PHP_INT_MAX );
		add_filter( 'woocommerce_single_product_image_gallery_classes', array( $this, 'filter_woo_gallery_wrapper_classes' ), PHP_INT_MAX, 1 );
	}

	/**
	 * Setup.
	 *
	 * @return void
	 */
	private function setup() {
		$this->badges_attrs = array(
			'best_seller' => array(
				'classes'    => self::$plugin_info['classes_prefix'] . '-best-seller-badge-wrapper',
				'badge_side' => 'badge_side',
			),
		);
	}

	/**
	  * Add unique Woo Gallery Class.
	  *
	  * @param array $classes
	  * @return array
	  */
	  public function filter_woo_gallery_wrapper_classes( $classes ) {
		$classes[] = self::$plugin_info['classes_prefix'] . '-woo-product-gallery-wrapper';
		return $classes;
	}


	/**
	 * Frontend Assets.
	 *
	 * @return void
	 */
	public static function front_assets() {
		wp_enqueue_script( self::$plugin_info['name'] . '-dist-front-actions', self::$plugin_info['url'] . 'assets/dist/js/front/actions.min.js', array( 'jquery' ), self::$plugin_info['version'], true );
		$localize_data = array(
			'ajaxUrl'         => admin_url( 'admin-ajax.php' ),
			'nonce'           => wp_create_nonce( self::$plugin_info['name'] . '-nonce' ),
			'prefix'          => self::$plugin_info['name'],
			'prefix_under'    => self::$plugin_info['prefix_under'],
			'classes_general' => self::$plugin_info['classes_general'],
			'classes_prefix'  => self::$plugin_info['classes_prefix'],
			'related_plugins' => self::$plugin_info['related_plugins'],
		);
		$localize_data = apply_filters( self::$plugin_info['classes_prefix'] . '-front-localize-data', $localize_data );

		wp_localize_script(
			self::$plugin_info['name'] . '-dist-front-actions',
			self::$plugin_info['localize_var'],
			$localize_data
		);
	}

	/**
	 * Single Badge.
	 *
	 * @param string $img_html
	 * @return void
	 */
	public function single_badge() {
		global $product;
		if ( ! is_a( $product, '\WC_Product' ) ) {
			return;
		}
		if ( is_admin() ) {
			return;
		}
		if ( $this->is_widget() || $this->is_sidebar() ) {
			return;
		}
		if ( $this->already_badged( $product->get_id(), 'loop' ) ) {
			return;
		}
		$this->get_best_seller_badge( $product, 'single' );
		$this->mark_badged_product( $product->get_id(), 'single' );
	}

	/**
	 * Best Seller Badge Wrapper Start.
	 *
	 * @return mixed
	 */
	public function best_seller_badge_wrapper_start( $context = 'loop', $badge = 'best_seller', $return = true, $additional_classes = array(), $overwrite_classes = false ) {
		if ( self::pypass_badge_wrapper( $context ) ) {
			return '';
		}
		$badge_settings = $this->get_badge_settings();
		if ( $overwrite_classes ) {
			$classes = implode( ' ', $additional_classes );
		} else {
			$classes = $this->badges_attrs[ $badge ]['classes'] . ' small-badge best-seller-' . $context . '-badge-wrapper ' . $badge_settings[ $this->badges_attrs[ $badge ]['badge_side'] ] . ( ! empty( $additional_classes ) ? ( ' ' . implode( ' ', $additional_classes ) ) : '' );
		}
		if ( $return ) {
			ob_start();
		}
		?><div <?php self::base_attrs( '' ); ?> class="<?php echo esc_attr( $classes ); ?>" >
		<?php
		if ( $return ) {
			return ob_get_clean();
		}
	}

	/**
	 * Best Seller Badge Wrapper Start.
	 *
	 * @return mixed
	 */
	public function best_seller_badge_wrapper_end( $context = 'loop', $return = true ) {
		if ( self::pypass_badge_wrapper( $context ) ) {
			return '';
		}
		if ( $return ) {
			ob_start();
		}
		?>
		</div>
		<?php
		if ( $return ) {
			return ob_get_clean();
		}
	}

	/**
	 * Loop Badge
	 *
	 * @return void
	 */
	public function loop_badge() {
		global $product;
		if ( ! is_a( $product, '\WC_Product' ) ) {
			return;
		}
		if ( ! $this->is_badge_enabled() ) {
			return;
		}
		if ( self::is_bypass_badge() ) {
			return;
		}
		if ( ! BestSellerProduct::is_product_best_seller( $product->get_id() ) ) {
			return;
		}
		if ( $this->already_badged( $product->get_id(), 'loop' ) ) {
			return;
		}

		$this->get_best_seller_badge( $product, 'loop' );
		$this->mark_badged_product( $product->get_id(), 'loop' );
	}

	/**
	 * Clear Marked Badged Single Products.
	 *
	 * @return void
	 */
	public function clear_marked_badged_products_single() {
		unset( $GLOBALS[ self::$plugin_info['name'] . '-single-badged-products' ] );
	}
	/**
	 * Clear Marked Badged loop Products.
	 *
	 * @return void
	 */
	public function clear_marked_badged_products_loop() {
		unset( $GLOBALS[ self::$plugin_info['name'] . '-loop-badged-products' ] );
	}

	/**
	 * Already Badged Products.
	 *
	 * @param int $product_id
	 * @return boolean
	 */
	protected function already_badged( $product_id, $context = 'loop' ) {
		return ( ! empty( $GLOBALS[ self::$plugin_info['name'] . '-' . $context . '-badged-products' ] ) && in_array( $product_id, $GLOBALS[ self::$plugin_info['name'] . '-' . $context . '-badged-products' ] ) );
	}

	/**
	 * Mark a product as badged.
	 *
	 * @param int $product_id
	 * @return void
	 */
	protected function mark_badged_product( $product_id, $context = 'loop' ) {
		if ( ! isset( $GLOBALS[ self::$plugin_info['name'] . '-' . $context . '-badged-produts' ] ) ) {
			$GLOBALS[ self::$plugin_info['name'] . '-' . $context . '-badged-products' ] = array();
		}
		$GLOBALS[ self::$plugin_info['name'] . '-' . $context . '-badged-products' ][] = $product_id;
	}

	/**
	 * Check if the image is the main image thumbnail in single page.
	 *
	 * @param \WC_Product $product
	 * @param int         $attachment_id
	 *
	 * @return boolean
	 */
	private function is_single_page_main_image( $product, $attachment_id ) {
		if ( did_action( 'woocommerce_before_single_product' ) && ! did_action( 'woocommerce_after_single_product_summary' ) ) {
			$product_thumbnail_id = $product->get_image_id();
			return ( (int) $attachment_id === (int) $product_thumbnail_id );
		}
		return false;
	}

	/**
	 * Check if inside Cart table.
	 *
	 * @return boolean
	 */
	private function is_cart_table() {
		return did_action( 'woocommerce_before_cart_table' ) && ! did_action( 'woocommerce_after_cart_table' );
	}

	/**
	 * Check if inside Checkout Table.
	 *
	 * @return boolean
	 */
	private function is_checkout_table() {
		return did_action( 'woocommerce_review_order_before_cart_contents' ) && ! did_action( 'woocommerce_review_order_after_cart_contents' );
	}

	/**
	 * Is in email.
	 *
	 * @return boolean
	 */
	private function is_email() {
		return did_action( 'woocommerce_mail_content' ) && ! did_action( 'woocommerce_email_sent' );
	}

	/**
	 * Check if a product in widget.
	 *
	 * @return boolean
	 */
	private function is_widget() {
		return ( 'widget_block_content' === current_action() ) || ( did_action( 'woocommerce_widget_product_item_start' ) > did_action( 'woocommerce_widget_product_item_end' ) );
	}

	/**
	 * Check if in sidebar.
	 *
	 * @return boolean
	 */
	private function is_sidebar() {
		return ( did_action( 'dynamic_sidebar_before' ) > did_action( 'dynamic_sidebar_after' ) );
	}

	/**
	 * Check if bypass badge.
	 *
	 * @return boolean
	 */
	private static function is_bypass_badge() {
		return ! empty( $GLOBALS[ self::$plugin_info['name'] . '-bypass-best-seller-badge' ] );
	}

	/**
	 * Inside SHop Loop Item.
	 *
	 * @return boolean
	 */
	private function in_shop_loop_item() {
		return did_filter( 'woocommerce_product_loop_start' ) > did_filter( 'woocommerce_product_loop_end' );
	}

	/**
	 * Get Best Seller Badge for a product.
	 *
	 * @param \WC_Product|null $product
	 * @return mixed
	 */
	private function get_best_seller_badge( $product = null, $context = 'loop', $return = false, $img_classes = array() ) {
		if ( ! is_a( $product, '\WC_Product' ) ) {
			return;
		}
		if ( ! $this->is_badge_enabled() ) {
			return;
		}
		// Exception for General Best Sellers Shortcode.
		if ( ! BestSellerShortcodes::is_general_best_sellers_shortcode() && ! BestSellerProduct::is_product_best_seller( $product->get_id() ) ) {
			return;
		}
		if ( $return ) {
			ob_start();
		}
		$this->best_seller_badge_wrapper_start( $context, 'best_seller', false );
		$this->best_seller_badge( $product, false, $context, $img_classes );
		$this->best_seller_badge_wrapper_end( $context, false );
		if ( $return ) {
			return ob_get_clean();
		}
	}

	/**
	 * Best Seller Badge HTML.
	 *
	 * @param string $badge_url
	 * @param array  $badge_details
	 * @return mixed
	 */
	public function best_seller_badge( $product = null, $return = false, $context = 'loop', $img_classes = array() ) {
		$badge_type = $this->get_badge_settings( 'badge_type' );
		if ( $return ) {
			ob_start();
		}
		if ( 'text' === $badge_type ) {
			$this->text_badge( $product, $context, false, $img_classes );
		} elseif ( 'icon' === $badge_type ) {
			$this->icon_badge( $product, $context, false, $img_classes );
		}
		if ( $return ) {
			return ob_get_clean();
		}
	}

	/**
	 * Get Icon Badge.
	 *
	 * @return void
	 */
	public function icon_badge( $product = null, $context = 'loop', $with_link = false, $img_classes = array() ) {
		$badge_settings = $this->get_badge_settings();
		if ( ! is_null( $this->icon_badge_url ) ) {
			$badge_url = $this->icon_badge_url;
		} else {
			$badge_url            = $this->get_icon_badge_url( $badge_settings['badge_icon'] );
			$this->icon_badge_url = $badge_url;
		}
		if ( ! $badge_url ) {
			return;
		}
		?>
		<span class="<?php echo esc_attr( self::$plugin_info['classes_prefix'] . '-badge ' . self::$plugin_info['classes_prefix'] . '-' . $context . '-badge ' . self::$plugin_info['classes_prefix'] ); ?>-icon-badge" <?php echo ! empty( $img_classes ) ? ( esc_attr( 'data-classes' ) . '="' . esc_attr( implode( ' ', $img_classes ) ) . '"' ) : ''; ?> >
			<img class="<?php echo esc_attr( self::$plugin_info['classes_general'] . '-front-badge ' . self::$plugin_info['classes_prefix'] . '-icon-badge-img' ); ?>" src="<?php echo esc_url_raw( $badge_url ); ?>"  alt="<?php esc_html_e( 'best seller badge', 'gpls-wobtslr-woo-best-seller' ); ?>">
		</span>
		<?php
	}

	/**
	 * Get Icon Badge URL.
	 *
	 * @param int $attachment_id
	 * @return string|false
	 */
	private function get_icon_badge_url( $badge_icon ) {
		$icon_url = false;
		if ( is_numeric( $badge_icon ) ) {
			$icon_url = self::get_media_icon_badge_url( absint( $badge_icon ) );
		} else {
			$icon_url = self::$plugin_info['url'] . 'assets/images/icons/' . $badge_icon;
		}

		return $icon_url;
	}

	/**
	 * Get Media Icon Badge URL.
	 *
	 * @param int $attachment_id
	 * @return string|false
	 */
	public static function get_media_icon_badge_url( $attachment_id ) {
		$icon_url = wp_get_attachment_image_src( $attachment_id, array( 75, 75 ) );
		if ( ! $icon_url ) {
			$icon_url = wp_get_attachment_image_src( $attachment_id, 'thumbnail' );
			return is_array( $icon_url ) ? $icon_url[0] : false;
		}
		return $icon_url[0];
	}

	/**
	 * Get Text badge.
	 *
	 * @return void
	 */
	public function text_badge( $product = null, $context = 'loop', $with_link = false, $img_classes = array() ) {
		$badge_settings = $this->get_badge_settings();
		$this->simple_text_badge( $badge_settings, $context, $img_classes );
	}

		/**
	 * Text Badge without Link.
	 *
	 * @param array  $badge_settings
	 * @param string $base_styles
	 * @param string $text_styles
	 * @param string $context
	 * @return void
	 */
	private function simple_text_badge( $badge_settings, $context = 'loop', $img_classes = array() ) {
		?>
		<span class="<?php echo esc_attr( self::$plugin_info['classes_general'] . '-front-badge ' . self::$plugin_info['classes_prefix'] . '-badge ' . self::$plugin_info['classes_prefix'] . '-' . $context . '-badge ' . self::$plugin_info['classes_prefix'] . '-text-badge' ); ?>" <?php echo ! empty( $img_classes ) ? ( esc_attr( 'data-classes' ) . '="' . esc_attr( implode( ' ', $img_classes ) ) . '"' ) : ''; ?> >
			<span style="background-color:<?php echo esc_attr( $badge_settings['badge_bg'] ); ?>;" class="<?php echo esc_attr( self::$plugin_info['classes_prefix'] . '-text-badge-wrapper' ); ?>">
				<span class="<?php echo esc_attr( self::$plugin_info['classes_prefix'] . '-text-badge-title' ); ?>"><?php echo esc_html( sprintf( esc_html__( '%s', 'gpls-wobtslr-woo-best-seller' ), $badge_settings['badge_text'] ) ); ?></span>
			</span>
		</span>
		<?php
	}

	/**
	 * Badge Base Attributes.
	 *
	 * @return void
	 */
	private function base_attrs( $prefix = '' ) {
		$prefix          = ! empty( $prefix ) ? $prefix . '_' : $prefix;
		$badge_settings  = $this->get_badge_settings();
		$horz_margin_val = ! empty( $badge_settings[ $prefix . 'badge_horz_margin' ] ) ? (int) $badge_settings[ $prefix . 'badge_horz_margin' ] : 0;
		$vert_margin_val = ! empty( $badge_settings[ $prefix . 'badge_vert_margin' ] ) ? (int) $badge_settings[ $prefix . 'badge_vert_margin' ] : 0;
		$attrs           = array(
			'data-' . self::$plugin_info['prefix_under'] . '_position'        => $badge_settings[ $prefix . 'badge_side' ],
			'data-' . self::$plugin_info['prefix_under'] . '_horz_margin_val' => $horz_margin_val,
			'data-' . self::$plugin_info['prefix_under'] . '_vert_margin_val' => $vert_margin_val,
		);

		foreach ( $attrs as $key => $val ) {
			echo esc_attr( $key ) . '="' . esc_attr( $val ) . '" ';
		}
	}

	/**
	 * Get Icon Badge Styles.
	 *
	 * @return array
	 */
	private function get_icon_badge_styles() {
		$badge_settings = $this->get_badge_settings();
		$styles         = array(
			'margin'  => '0px !important',
			'opacity' => '1 !important',
		);
		if ( ! empty( $badge_settings['badge_width'] ) ) {
			$styles['width'] = $badge_settings['badge_width'] . 'px;';
		}
		if ( ! empty( $badge_settings['badge_height'] ) ) {
			$styles['height'] = $badge_settings['badge_height'] . 'px;';
		}
		return $styles;
	}

	/**
	 * Is Badge enabled.
	 *
	 * @return boolean
	 */
	public function is_badge_enabled() {
		return 'on' === $this->get_badge_settings( 'badge_global_enable' );
	}

	/**
	 * Get Badge Settings.
	 *
	 * @param string $key
	 * @return mixed
	 */
	public function get_badge_settings( $key = '' ) {
		$badge_settings = $this->settings->get_tab_settings( 'badge' );
		return ! empty( $key ) ? $badge_settings[ $key ] : $badge_settings;
	}

	/**
	 * Get Text Badge Styles.
	 *
	 * @return array
	 */
	private function get_text_badge_styles( $prefix = '' ) {
		$prefix         = ! empty( $prefix ) ? $prefix . '_' : $prefix;
		$badge_settings = $this->get_badge_settings();
		$styles         = array(
			'display'          => 'block',
			'line-height'      => 'normal',
			'margin'           => '0px',
			'font-style'       => $badge_settings[ $prefix . 'badge_font_style' ],
			'font-weight'      => $badge_settings[ $prefix . 'badge_font_weight' ],
			'font-size'        => $badge_settings[ $prefix . 'badge_fontsize' ] . 'px',
			'color'            => $badge_settings[ $prefix . 'badge_color' ],
		);
		return $styles;
	}

	/**
	 * Get Text Wrapper Badge Styles.
	 *
	 * @return array
	 */
	private function get_text_wrapper_badge_styles( $prefix = '' ) {
		$prefix         = ! empty( $prefix ) ? $prefix . '_' : $prefix;
		$badge_settings = $this->get_badge_settings();
		$styles         = array(
			'display'          => 'block',
			'position'         => 'relative',
			'padding'          => '2px 8px',
			'margin'           => '0px',
			'background-color' => $badge_settings[ $prefix . 'badge_bg' ],
		);
		return $styles;
	}

	/**
	 * Get Base Styles.
	 *
	 * @return array
	 */
	private function get_base_styles( $prefix = '' ) {
		$prefix = ! empty( $prefix ) ? $prefix . '_' : $prefix;
		$styles = array(
			'display'  => 'none',
			'position' => 'absolute',
			'z-index'  => '1000',
		);
		return $styles;
	}

	/**
	 * Badge Inline Styles.
	 *
	 * @return void
	 */
	public function badge_styles() {
		$badge_settings = $this->get_badge_settings();
		$styles         = array(
			self::$plugin_info['classes_prefix'] . '-single-badge' => array(
				'cursor'  => 'default',
				'display' => 'flex',
			),
			self::$plugin_info['classes_prefix'] . '-best-seller-badge-wrapper'     => $this->get_base_styles(),
			self::$plugin_info['classes_prefix'] . '-best-seller-badge-wrapper img' => array(
				'margin' => '0px !important',
			),
			self::$plugin_info['classes_prefix'] . '-category-badge' => array(
				'display'       => 'block',
				'margin-top'    => '5px',
				'margin-bottom' => '5px',
				'color'         => '#96588a',
			),
			self::$plugin_info['classes_prefix'] . '-category-badge:hover .' . self::$plugin_info['classes_prefix'] . '-text-badge-category' => array(
				'text-decoration' => 'underline',
				'color'           => $this->get_badge_settings( 'category_badge_bg' ),
			),
			self::$plugin_info['classes_prefix'] . '-category-badge .' . self::$plugin_info['classes_prefix'] . '-badge-title-wrapper' => array(
				'position'      => 'relative',
				'margin-right'  => '15px',
				'display'       => 'inline-block',
				'padding'       => '0px 8px',
				'border-radius' => '5px',
			),
			self::$plugin_info['classes_prefix'] . '-category-badge .' . self::$plugin_info['classes_prefix'] . '-badge-title' => array(
				'margin'                     => '0px !important',
				'position'                   => 'relative',
				'display'                    => 'block',
				'text-align'                 => 'left',
				'z-index'                    => 100,
				'width'                      => 'auto',
				'border-bottom-right-radius' => 0,
				'border-color'               => 'transparent',
			),
			self::$plugin_info['classes_prefix'] . '-category-badge .' . self::$plugin_info['classes_prefix'] . '-badge-notch' => array(
				'position'  => 'absolute',
				'right'     => '-10px',
				'bottom'    => 0,
				'clip-path' => 'polygon(0 0, 100% 0, 60% 48%, 100% 100%, 0 100%)',
				'width'     => '20px',
				'height'    => '100%',
			),
			self::$plugin_info['classes_prefix'] . '-badge' => array(
				'z-index'     => '100',
				'align-items' => 'center',
			),
			self::$plugin_info['classes_prefix'] . '-badge.with-link' => array(
				'cursor'  => 'pointer',
				'display' => 'flex',
			),
			self::$plugin_info['classes_prefix'] . '-best-seller-badge-wrapper.top_center .' . self::$plugin_info['classes_prefix'] . '-badge.with-link' => array(
				'flex-direction' => 'column',
			),
			self::$plugin_info['classes_prefix'] . '-best-seller-wrapper.bottom_center .' . self::$plugin_info['classes_prefix'] . '-badge.with-link' => array(
				'flex-direction' => 'column',
			),
			self::$plugin_info['classes_prefix'] . '-text-badge .' . self::$plugin_info['classes_prefix'] . '-text-badge-title'   => $this->get_text_badge_styles(),
			self::$plugin_info['classes_prefix'] . '-icon-badge .' . self::$plugin_info['classes_prefix'] . '-icon-badge-img'     => $this->get_icon_badge_styles(),
			self::$plugin_info['classes_prefix'] . '-text-badge .' . self::$plugin_info['classes_prefix'] . '-text-badge-wrapper' => $this->get_text_wrapper_badge_styles(),
			self::$plugin_info['classes_prefix'] . '-text-badge-wrapper::after'                                                   => array_merge(
				array(
					'position'   => 'absolute',
					'z-index'    => '-1',
					'height'     => ' 100%',
					'width'      => ' 100%',
					'background' => ' inherit',
					'top'        => ' 0px !important',
					'right'      => ' 0px !important',
					'bottom'     => ' 0px !important',
				),
				$this->text_badge_notch_styles()
			),
			self::$plugin_info['classes_prefix'] . '-text-badge .' . self::$plugin_info['classes_prefix'] . '-text-badge-notch' => array(
				'border-right' => '10px solid transparent',
				'border-top'   => '25px solid',
				'height'       => 0,
				'width'        => 0,
				'float'        => 'left',
			),
			self::$plugin_info['classes_prefix'] . '-badge .' . self::$plugin_info['classes_prefix'] . '-text-badge-category-name' => $this->badge_category_name_styles(),
			self::$plugin_info['classes_prefix'] . '-badge:hover .' . self::$plugin_info['classes_prefix'] . '-text-badge-category-name' => array(
				'display' => 'block !important',
			),
		);
		?>
		<style>
			<?php
			foreach ( $styles as $selector => $style_arr ) {
				echo esc_attr( '.' . $selector . '{' );
				foreach ( $style_arr as $style_key => $style_val ) {
					echo esc_attr( $style_key . ':' . $style_val . ';' );
				}
				echo esc_attr( '}' );
			}
			?>
			<?php do_action( self::$plugin_info['name'] . '-front-inline-styles' ); ?>
			<?php
				$badge_settings = $this->get_badge_settings();
				$rotate_style    = '';
				$position_styles = array(
					'top_left'      => array(
						'bottom' => 'unset !important',
						'right'  => 'unset !important',
						'left'   => $badge_settings['badge_horz_margin'] . 'px',
						'top'    => $badge_settings['badge_vert_margin'] . 'px',
					),
					'left_center'   => array(
						'bottom'    => 'unset !important',
						'right'     => 'unset !important',
						'left'      => $badge_settings['badge_horz_margin'] . 'px',
						'top'       => '50%',
						'transform' => 'translateY(-50%)',
					),
					'bottom_left'   => array(
						'top'    => 'unset !important',
						'right'  => 'unset !important',
						'left'   => $badge_settings['badge_horz_margin'] . 'px',
						'bottom' => $badge_settings['badge_vert_margin'] . 'px',
					),
					'top_right'     => array(
						'left'   => 'unset !important',
						'bottom' => 'unset !important',
						'right'  => $badge_settings['badge_horz_margin'] . 'px',
						'top'    => $badge_settings['badge_vert_margin'] . 'px',
					),
					'right_center'  => array(
						'left'      => 'unset !important',
						'bottom'    => 'unset !important',
						'right'     => $badge_settings['badge_horz_margin'] . 'px',
						'top'       => '50%',
						'transform' => 'translateY(-50%)',
					),
					'bottom_right'  => array(
						'left'   => 'unset !important',
						'top'    => 'unset !important',
						'right'  => $badge_settings['badge_horz_margin'] . 'px',
						'bottom' => $badge_settings['badge_vert_margin'] . 'px',
					),
					'top_center'    => array(
						'left'      => '50% !important',
						'right'     => 'unset !important',
						'bottom'    => 'unset !important',
						'top'       => $badge_settings['badge_vert_margin'] . 'px',
						'transform' => 'translateX(-50%)',
					),
					'bottom_center' => array(
						'left'      => '50% !important',
						'top'       => 'unset !important',
						'right'     => 'unset !important',
						'bottom'    => $badge_settings['badge_vert_margin'] . 'px',
						'transform' => 'translateX(-50%)',
					),
				);
				if ( ! empty( $badge_settings['badge_angle'] ) ) {
					$rotate_style = 'rotate(' . $badge_settings['badge_angle'] . 'deg)';

					echo esc_attr( '.' . self::$plugin_info['classes_prefix'] . '-best-seller-badge-wrapper img {' );
					echo esc_attr( 'transform:' . $rotate_style . ';' );
					echo esc_attr( '}' );
				}

				foreach ( $position_styles as $position_class => $position_styles ) {
					echo esc_attr( '.' . self::$plugin_info['classes_prefix'] . '-best-seller-badge-wrapper.' . $position_class . '{' );
					foreach ( $position_styles as $style_key => $style_value ) {
						echo esc_attr( $style_key ) . ':' . esc_attr( $style_value ) . ';';
					}
					echo esc_attr( '}' );
				}
				echo esc_attr( '.' . self::$plugin_info['classes_prefix'] . '-woo-product-gallery-wrapper' ); ?>{position:relative;}
				<?php echo esc_attr( '.' . self::$plugin_info['classes_prefix'] . '-text-badge-wrapper::after' ); ?>{ content: ''; }
		</style>
		<?php

	}


	/**
	 * Text Badge Notch Style based on badge Side.
	 *
	 * @return array
	 */
	private function text_badge_notch_styles() {
		$badge_side       = $this->get_badge_settings( 'badge_side' );
		$transform_origin = 'bottom right';
		$transform        = 'skew(-22deg)';
		if ( 'top_right' === $badge_side ) {
			$transform = 'skew(22deg)';
		} elseif ( 'bottom_left' === $badge_side ) {
			$transform        = 'skew(22deg)';
			$transform_origin = 'top left';
		} elseif ( 'bottom_right' === $badge_side ) {
			$transform        = 'skew(-22deg)';
			$transform_origin = 'top left';
		}
		$styles = array(
			'transform-origin' => $transform_origin,
			'transform'        => $transform,
		);
		if ( str_ends_with( $badge_side, 'center' ) ) {
			$styles['visibility'] = 'hidden';
		}
		return $styles;
	}
	/**
	 * Badge Category Name Styles.
	 *
	 * @return array
	 */
	private function badge_category_name_styles() {
		$badge_side = $this->get_badge_settings( 'badge_side' );
		return array(
			'display'         => 'none',
			'text-decoration' => 'underline',
			'margin-' . ( str_ends_with( $badge_side, 'right' ) ? 'right' : 'left' ) => '15px',
		);
	}
	/**
	 * Check if pypass badge wrapper.
	 *
	 * @return boolean
	 */
	private static function pypass_badge_wrapper( $context = 'loop' ) {
		if ( ! empty( $GLOBALS[ self::$plugin_info['name'] . '-pypass-badge-wrapper' ] ) ) {
			return true;
		}

		$pypass_themes_single = array( 'blocksy' );
		$pypass_themes_loop   = array( 'avada' );
		$stylesheet           = get_stylesheet();

		if ( 'loop' === $context && in_array( $stylesheet, $pypass_themes_loop ) ) {
			return true;
		}

		if ( 'single' === $context && in_array( $stylesheet, $pypass_themes_single ) ) {
			return true;
		}

		return false;
	}
}
