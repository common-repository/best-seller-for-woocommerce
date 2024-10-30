<?php
namespace GPLSCore\GPLS_PLUGIN_WOBTSLR\Settings\SettingsFields;

use GPLSCore\GPLS_PLUGIN_WOBTSLR\Settings\SettingsFields\FieldBase;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Color Field.
 */
class ColorField extends TextField {

	/**
	 * Get Text Field HTML.
	 *
	 * @param boolean $return;
	 *
	 * @return mixed
	 */
	public function get_field_html( $return = false ) {
		if ( $return ) {
			ob_start();
		}
		?>
		<input style="cursor:pointer;" type="color" <?php $this->field_id(); ?> <?php $this->field_classes(); ?> <?php $this->field_name( $this->field ); ?> value="<?php echo esc_attr( isset( $this->field['value'] ) ? $this->field['value'] : '' ); ?>" <?php $this->custom_attributes_html( $this->field ); ?> >
		<?php
		if ( $return ) {
			return ob_get_clean();
		}
	}

	/**
	 * Sanitize Field.
	 *
	 * @param string $value
	 * @return string
	 */
	public function sanitize_field( $value ) {
		return sanitize_hex_color( $value );
	}
}