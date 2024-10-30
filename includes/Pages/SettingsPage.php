<?php
namespace GPLSCore\GPLS_PLUGIN_WOBTSLR\Pages;

use GPLSCore\GPLS_PLUGIN_WOBTSLR\BestSellerBadge;
use GPLSCore\GPLS_PLUGIN_WOBTSLR\Pages\PagesBase\AdminPage;
use GPLSCore\GPLS_PLUGIN_WOBTSLR\Settings\MainSettings;
use GPLSCore\GPLS_PLUGIN_WOBTSLR\Utils\NoticeUtilsTrait;
use GPLSCore\GPLS_PLUGIN_WOBTSLR\Backgrounds\AutoBackgrounds\BestSellersBackground;

/**
 * Settings Page Class.
 */
final class SettingsPage extends AdminPage {
	use NoticeUtilsTrait;

	/**
	 * Singleton Instance.
	 *
	 * @var self
	 */
	protected static $instance = null;

	/**
	 * Best Seller Badge.
	 *
	 * @var BestSellerBadge
	 */
	protected $best_seller_badge;

	/**
	 * Best Seller Background.
	 *
	 * @var BestSellersBackground
	 */
	protected $best_seller_background;

	/**
	 * Page Hooks.
	 *
	 * @return void
	 */
	protected function hooks() {
		add_action( 'plugin_action_links_' . self::$plugin_info['basename'], array( $this, 'settings_link' ), 5, 1 );
		add_action( self::$plugin_info['name'] . '-admin-page-assets', array( $this, 'custom_assets' ), PHP_INT_MAX, 1 );
		add_action( self::$plugin_info['name'] . '-' . $this->page_props['menu_slug'] . '-template-footer', array( $this, 'page_footer' ) );
		add_action( 'woocommerce_after_settings_' . $this->page_props['menu_slug'], array( $this, 'settings_page_footer' ) );
	}

	/**
	 * Settings Link.
	 *
	 * @param array $links Plugin Row Links.
	 * @return array
	 */
	public function settings_link( $links ) {
		$links[] = '<a href="' . esc_url( admin_url( 'admin.php?page=wc-settings&tab=' . self::$plugin_info['name'] . '-settings' ) ) . '">' . esc_html__( 'Settings', 'best-seller-for-woocommerce' ) . '</a>';
		return $links;
	}

	/**
	 * Prepare Page.
	 *
	 * @return void
	 */
	protected function prepare() {
		$this->page_props = array(
			'page_title'     => esc_html__( 'Best Seller', 'best-seller-for-woocommerce' ),
			'menu_title'     => esc_html__( 'Best Seller [GrandPlugins]', 'best-seller-for-woocommerce' ),
			'menu_slug'      => self::$plugin_info['name'] . '-settings',
			'is_woocommerce' => true,
			'tab_key'        => 'sub_tab',
		);

		$this->tabs = array(
			'general'    => array(
				'title'             => esc_html__( 'General', 'best-seller-for-woocommerce' ),
				'default'           => true,
				'settings'          => true,
				'woo_hide_save_btn' => true,
				'hide_title'        => true,
			),
			'badge'      => array(
				'title'             => esc_html__( 'Badge', 'best-seller-for-woocommerce' ),
				'template'          => 'main-settings-badge-template.php',
				'hide_title'        => true,
			),
			'shortcodes' => array(
				'title'             => esc_html__( 'Shortcodes', 'best-seller-for-woocommerce' ),
				'woo_hide_save_btn' => true,
				'template'          => 'main-settings-shortcodes-template.php',
				'hide_title'        => true,
			),
		);

		$this->settings               = MainSettings::init();
		$this->best_seller_badge      = BestSellerBadge::init();
		$this->best_seller_background = BestSellersBackground::init();
	}

	/**
	 * Set Assets.
	 *
	 * @return void
	 */
	protected function set_assets() {
		$this->assets[] = array(
			'type'      => 'js',
			'handle'    => self::$plugin_info['name'] . '-settings-page-actions',
			'url'       => self::$plugin_info['url'] . 'assets/dist/js/admin/settings.min.js',
			'localized' => array(
				'name' => str_replace( '-', '_', self::$plugin_info['name'] ),
				'data' => array(
					'prefix'         => self::$plugin_info['classes_prefix'],
					'badge_settings' => $this->settings->get_tab_settings( 'badge' ),
				),
			),
			'dependency' => array(
				self::$plugin_info['name'] . '-bootstrap-js',
			),
		);
	}

	/**
	 * Custom CSS.
	 *
	 * @param self $page
	 * @return void
	 */
	public function custom_assets( $page ) {
		if ( 'badge' === $page->get_current_tab() ) {
			wp_enqueue_media();
			self::notice_assets();
		}

		wp_deregister_style( 'porto_admin' );
		wp_dequeue_style( 'porto_admin' );
	}

	/**
	 * Get Best Seller badge.
	 *
	 * @return BestSellerBadge
	 */
	public function badge() {
		return $this->best_seller_badge;
	}

	/**
	 * Text Badge.
	 *
	 * @return void
	 */
	public function text_badge() {
		$this->best_seller_badge->text_badge();
	}

	/**
	 * Badge Styles.
	 *
	 * @return void
	 */
	public function badge_styles() {
		$this->best_seller_badge->badge_styles();
	}

	/**
	 * Page Footer.
	 *
	 * @return void
	 */
	public function page_footer() {
		self::$core->default_footer_section();
	}

	/**
	 * Text Badge Styles in Settings.
	 *
	 * @return void
	 */
	public function text_badge_settings_styles() {
		?>
		<style type="text/css" >
		.<?php echo esc_attr( self::$plugin_info['classes_prefix'] . '-text-badge' ); ?> .<?php echo esc_attr( self::$plugin_info['classes_prefix'] . '-top_left' ); ?>::after {
			transform-origin: bottom right !important;
			transform: skew( -22deg ) !important;
			visibility: visible;
		}
		.<?php echo esc_attr( self::$plugin_info['classes_prefix'] . '-text-badge' ); ?> .<?php echo esc_attr( self::$plugin_info['classes_prefix'] . '-top_right' ); ?>::after {
			transform-origin: bottom right !important;
			transform: skew( 22deg ) !important;
			visibility: visible;
		}
		.<?php echo esc_attr( self::$plugin_info['classes_prefix'] . '-text-badge' ); ?> .<?php echo esc_attr( self::$plugin_info['classes_prefix'] . '-bottom_left' ); ?>::after {
			transform-origin: top left !important;
			transform: skew( 22deg ) !important;
			visibility: visible;
		}
		.<?php echo esc_attr( self::$plugin_info['classes_prefix'] . '-text-badge' ); ?> .<?php echo esc_attr( self::$plugin_info['classes_prefix'] . '-bottom_right' ); ?>::after {
			transform-origin: top right !important;
			transform: skew( -22deg ) !important;
			visibility: visible;
		}
		.<?php echo esc_attr( self::$plugin_info['classes_prefix'] . '-text-badge' ); ?> .<?php echo esc_attr( self::$plugin_info['classes_prefix'] . '-top_center' ); ?>::after,
		.<?php echo esc_attr( self::$plugin_info['classes_prefix'] . '-text-badge' ); ?> .<?php echo esc_attr( self::$plugin_info['classes_prefix'] . '-bottom_center' ); ?>::after
		{
			visibility: hidden;
		}
		</style>
		<?php
	}

	/**
	 * Settings Page Footer.
	 *
	 * @return void
	 */
	public function settings_page_footer() {
		?>
		<div class="mt-5">
			<div class="mt-5 pt-5">
				<div class="mt-5 pt-5">
				<?php self::$core->plugins_sidebar(); ?>
				</div>
			</div>
		</div>
		<?php
	}
}
