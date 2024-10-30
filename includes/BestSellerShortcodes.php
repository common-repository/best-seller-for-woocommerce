<?php
namespace GPLSCore\GPLS_PLUGIN_WOBTSLR;

use GPLSCore\GPLS_PLUGIN_WOBTSLR\BestSellerQueries;
use GPLSCore\GPLS_PLUGIN_WOBTSLR\Data\BestSellersData;
use GPLSCore\GPLS_PLUGIN_WOBTSLR\Settings\MainSettings;

/**
 * Best Seller Shortcodes Class.
 */
class BestSellerShortcodes extends Base {

    /**
	 * Singular Instance.
	 *
	 * @var self
	 */
	private static $instance = null;

	/**
	 * Best Seller data.
	 *
	 * @var BestSellersData
	 */
	private $best_seller_data;

	/**
	 * Best Seller Badge
	 *
	 * @var BestSellerBadge
	 */
	private $best_seller_badge;

	/**
	 * Settings.
	 *
	 * @var MainSettings
	 */
	private $settings;

	/**
	 * Title Tags.
	 *
	 * @var array
	 */
	private $title_tags = array( 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'p', 'span' );

	/**
	 * Query Results Tracker.
	 *
	 * @var null|object
	 */
	private $query_results_tracker = null;

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
		$this->hooks();

		$this->settings          = MainSettings::init();
		$this->best_seller_badge = BestSellerBadge::init();
		$this->best_seller_data  = new BestSellersData();
	}

	/**
	 * HOoks.
	 *
	 * @return void
	 */
	private function hooks() {
		add_action( 'init', array( $this, 'best_sellers_shortcodes' ) );
	}

	/**
	 * Register Shortcodes.
	 *
	 * @return void
	 */
	public function best_sellers_shortcodes() {
		add_shortcode( str_replace( '-', '_', self::$plugin_info['classes_prefix'] ) . '_best_sellers', array( $this, 'best_sellers_shortcode' ) );
    }

    /**
	 * General Best Sellers Shortcode.
	 *
	 * @param array $attrs
	 * @return string
	 */
	public function best_sellers_shortcode( $attrs ) {
		return $this->best_sellers( $attrs );
	}

	/**
	 * General Best Sellers.
	 *
	 * @param array $attrs
	 * @return string
	 */
	public function best_sellers( $attrs, $is_slider = false, $carousel_args = array() ) {
		$attrs = (array) $attrs;
		$attrs = array_map( 'sanitize_text_field', $attrs );
		$title = $attrs['title'] ?? null;

		if ( is_null( $title ) ) {
			$title = esc_html__( 'Best Sellers', 'best-seller-for-woocommerce' );
		}

		$general_best_sellers = $this->best_seller_data->get_data( 'general_best_sellers', true );
		if ( empty( $general_best_sellers ) ) {
			return '';
		}

		$query_args = apply_filters(
			self::$plugin_info['name'] . '-best-sellers-shortcode',
			array(
				'ids'      => implode( ',', array_keys( $general_best_sellers ) ),
				'paginate' => false,
			)
		);
		return $this->core_shortcode( $query_args, $attrs, $title, $carousel_args, 'general' );
	}

    /**
	 * Core Shortcode Handle.
	 *
	 * @param array $args
	 * @return string
	 */
	private function core_shortcode( $args, $attrs, $title = '', $carousel_args = array(), $shortcode_type = 'general' ) {
		$result = '';
		$args   = array_merge(
			array(
				'limit'       => 30,
				'post_status' => 'publish',
				'post_type'   => 'product',
				'order'       => 'DESC',
				'orderby'     => 'post__in',
			),
			$args
		);

		if ( ! empty( $carousel_args ) ) {
			$args['columns'] = 1;
		}

		$this->setup_globals( $attrs, $carousel_args, $shortcode_type );

		$shortcode         = new \WC_Shortcode_Products( $args );
		$shortcode_content = $shortcode->get_content();

		ob_start();
		$this->shortcode_wrapper_start( $attrs, $title, $carousel_args );
		$result .= ob_get_clean();

		$result .= $shortcode_content;

		ob_start();
		$this->shortcode_wrapper_end( $attrs );
		$result .= ob_get_clean();

		$this->query_results_tracker = null;

		return $result;
	}

    /**
	 * Shortcode Wrapper Start.
	 *
	 * @param array    $attrs
	 * @param string   $title
	 * @param \WP_Term $cat
	 * @return void
	 */
	private function shortcode_wrapper_start( $attrs, $title = '', $carousel_args = array() ) {
		$title_tag = $attrs['title_tag'] ?? 'h3';

		if ( empty( $title_tag ) || ! in_array( $title_tag, $this->title_tags ) ) {
			$title_tag = 'h3';
		}

		?><div class="<?php echo esc_attr( self::$plugin_info['classes_prefix'] . '-in-category-shortcode' ); ?>" >
		<?php
		if ( ! empty( $title ) ) {
			?>
			<<?php echo esc_attr( $title_tag ); ?> class="<?php echo esc_attr( self::$plugin_info['classes_prefix'] . '-shortcode-title' ); ?>">
				<?php
				/* translators: %s Best Sellers Products Shortcode Title. */
				echo esc_html( sprintf( esc_html__( '%s', 'best-seller-for-woocommerce' ), $title ) );
				?>
			</<?php echo esc_attr( $title_tag ); ?>>
			<?php
		}
	}

    /**
	 * Shortcode Wrapper End.
	 *
	 * @param array $attrs
	 * @return void
	 */
	private function shortcode_wrapper_end( $attrs ) {
		?></div>
		<?php
		unset( $GLOBALS[ self::$plugin_info['name'] . '-track-query-results' ] );
		unset( $GLOBALS[ self::$plugin_info['name'] . '-bypass-best-seller-badge' ] );
		unset( $GLOBALS[ self::$plugin_info['name'] . '-best-sellers-shortcode-type' ] );
	}

    /**
	 * Setup Shortcode Globals.
	 *
	 * @param array $attrs
	 * @param array $carousel_args
	 * @param string $shortcode_type
	 *
	 * @return void
	 */
	private function setup_globals( $attrs, $carousel_args, $shortcode_type ) {
		// Shortcode Trigger.
		$GLOBALS[ self::$plugin_info['name'] . '-best-sellers-shortcode' ] = true;

		$GLOBALS[ self::$plugin_info['name'] . '-best-sellers-shortcode-type' ] = $shortcode_type;

		// Best Seller Badge.
		$show_badge = ! empty( $attrs['show_badge'] ) ? $attrs['show_badge'] : 'yes';
		$show_badge = ( 'yes' === $show_badge ) ? true : false;
		if ( ! $show_badge ) {
			$GLOBALS[ self::$plugin_info['name'] . '-bypass-best-seller-badge' ] = true;
		}

		// Track Query Results.
		$GLOBALS[ self::$plugin_info['name'] . '-track-query-results' ] = true;
	}

    /**
	 * Check if General Best Sellers Shortcode.
	 *
	 * @return boolean
	 */
	public static function is_general_best_sellers_shortcode() {
		return ( ! empty( $GLOBALS[ self::$plugin_info['name'] . '-best-sellers-shortcode-type' ] ) && ( 'general' === sanitize_text_field( wp_unslash( $GLOBALS[ self::$plugin_info['name'] . '-best-sellers-shortcode-type' ] ) ) ) );
	}
}
