<?php
namespace GPLSCore\GPLS_PLUGIN_WOBTSLR\Settings;

use GPLSCore\GPLS_PLUGIN_WOBTSLR\BestSellerBadge;
use GPLSCore\GPLS_PLUGIN_WOBTSLR\Settings\SettingsBase\Settings;
use function GPLSCore\GPLS_PLUGIN_WOBTSLR\Settings\Fields\setup_main_settings_fields;

/**
 * Main Settings CLass.
 */
final class MainSettings extends Settings {

	/**
	 * Singleton Instance.
	 *
	 * @var self
	 */
	protected static $instance = null;

	/**
	 * Prepare Settings.
	 *
	 * @return void
	 */
	protected function prepare() {
		  $this->id      = self::$plugin_info['name'] . '-best-seller-main-settings';
		  $this->tab_key = 'sub_tab';
		  $this->fields  = setup_main_settings_fields( self::$core, self::$plugin_info );
	}

	/**
	 * Settings Hooks.
	 *
	 * @return void
	 */
	protected function hooks() {
		add_action( $this->id . '-settings-field-html-badge_icon', array( $this, 'badge_icon_input' ), 100, 1 );
	}

	/**
	 * Badge icon Input.
	 *
	 * @param array $field
	 * @return void
	 */
	public function badge_icon_input( $field ) {
		$badge_settings = self::get_tab_settings( 'badge' );
		?>
		<div class="badge-icons">
			<div class="badge-icons-toggler">
				<button class="badge-icons-toggle button-primary"><?php esc_html_e( 'Available Icons', 'best-seller-for-woocommerce' ); ?></button>
				<input type="hidden" id="wp-media-icon" name="<?php echo esc_attr( $this->id . '[badge_icon]' ); ?>" value="<?php echo esc_attr( $badge_settings['badge_icon'] ); ?>">

			</div>
			<div class="badge-icons-wrapper row collapse <?php echo esc_attr( 'icon' === $badge_settings['badge_type'] ? 'show' : '' ); ?> mt-3">
				<div class="default-icon bg-muted p-3 mb-3">
					<h6><?php esc_html_e( 'Default icons', 'best-seller-for-woocommerce' ); ?></h6>
					<div class="default-icons row">
						<?php foreach ( $this->get_badges() as $badge ) : ?>
						<div class="badge-icon-element col border shadow-sm px-3 py-1">
							<input name="badge-icon-preview" <?php echo esc_attr( $badge['name'] === $badge_settings['badge_icon'] ? 'checked' : '' ); ?> type="radio" value="<?php echo esc_attr( $badge['name'] ); ?>" class="edit edit-badge-icon-radio d-block mx-auto my-3">
							<img width="75" height="75"  class="d-block mx-auto pb-2" src="<?php echo esc_url( $badge['url'] ); ?>" alt="best-seller-badge-icon">
						</div>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="media-icon-wrapper bg-muted p-3 d-flex flex-column">
					<h6 class="text-start"><?php esc_html_e( 'Media icon', 'best-seller-for-woocommerce' ); ?></h6>
					<button class="media-browse button button-primary" data-media-url="#wp-media-icon"><?php esc_html_e( 'Media' ); ?></button>
					<input name="badge-icon-preview" <?php echo esc_attr( is_numeric( $badge_settings['badge_icon'] ) ? 'checked' : '' ); ?> type="radio" value="" class="edit badge-media-radio edit-badge-icon-radio <?php echo esc_attr( is_numeric( $badge_settings['badge_icon'] ) ? 'd-block' : 'd-none' ); ?> my-3">
					<img class="media-icon <?php echo esc_attr( is_numeric( $badge_settings['badge_icon'] ) ? '' : 'hidden' ); ?>" class="d-block mx-auto pb-2" src="<?php echo ( is_numeric( $badge_settings['badge_icon'] ) ? esc_url_raw( BestSellerBadge::get_media_icon_badge_url( $badge_settings['badge_icon'] ) ) : '#' ); ?>" alt="best-seller-badge-icon">
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Get Available Badges.
	 *
	 * @return array
	 */
	public function get_badges() {
		require_once \ABSPATH . 'wp-admin/includes/file.php';
		$badges        = array();
		$badges_folder = self::$plugin_info['path'] . 'assets/images/icons/';
		$badges_url    = self::$plugin_info['url'] . 'assets/images/icons/';
		$badges_files  = list_files( $badges_folder, 1, array( 'badge-preview.jpg' ) );
		foreach ( $badges_files as $badge ) {
			$badge_name = wp_basename( $badge );
			$badge_ext  = pathinfo( $badge, PATHINFO_EXTENSION );
			$badges[]   = array(
				'name' => $badge_name,
				'ext'  => $badge_ext,
				'url'  => $badges_url . $badge_name,
				'path' => $badge,
			);
		}
		return $badges;
	}

}
