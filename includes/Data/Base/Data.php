<?php
namespace GPLSCore\GPLS_PLUGIN_WOBTSLR\Data\Base;

use GPLSCore\GPLS_PLUGIN_WOBTSLR\Base;

/**
 * Data Base Class.
 */
abstract class Data extends Base {

	/**
	 * Default Data.
	 *
	 * @var mixed
	 */
	protected $default_data = array();

	/**
	 * Data.
	 *
	 * @var mixed
	 */
	protected $data = null;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->set_default_data();
	}

	/**
	 * Get Data.
	 *
	 * @return mixed
	 */
	public function get_data( $key = null, $hard = false ) {
		if ( $hard || is_null( $this->data ) ) {
			$this->data = $this->fetch_data();
		}

		if ( is_array( $this->data ) ) {
			$this->data = array_replace_recursive( $this->default_data, $this->data );
		}

		if ( is_null( $this->data ) || ( false === $this->data ) ) {
			$this->data = $this->default_data;
		}

		if ( is_null( $key ) ) {
			return $this->data;
		}

		if ( ! is_null( $key ) && ! isset( $this->data[ $key ] ) ) {
			return false;
		}

		return $this->data[ $key ];
	}

	/**
	 * Update Data.
	 *
	 * @param mixed $data Data to update.
	 * @return true|\WP_Error
	 */
	public function update_data( $data ) {
		$result = $this->_update_data( $data );
		if ( true === $result ) {
			$this->data = $data;
			return true;
		}
		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return new \WP_Error(
			self::$plugin_info['name'] . '-update-data-failed',
			esc_html__( 'Data update failed' )
		);
	}

	/**
	 * Get Default Data.
	 *
	 * @return array
	 */
	public function get_default_data() {
		return $this->default_data;
	}

	/**
	 * Set Default Data.
	 *
	 * @return void
	 */
	abstract protected function set_default_data();

	/**
	 * Update Data.
	 *
	 * @param mixed $data Data to update.
	 * @return boolean
	 */
	abstract protected function _update_data( $data );

	/**
	 * Fetch Data.
	 *
	 * @return array|false
	 */
	abstract protected function fetch_data();
}
