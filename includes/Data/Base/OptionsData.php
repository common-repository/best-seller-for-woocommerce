<?php
namespace GPLSCore\GPLS_PLUGIN_WOBTSLR\Data\Base;

/**
 * Data in Options Table Class.
 */
abstract class OptionsData extends Data {

	/**
	 * Options Key.
	 *
	 * @var string
	 */
	protected $options_key;

	/**
	 * Constructor.
	 *
	 */
	public function __construct() {
		parent::__construct();
		$this->setup();
	}

	/**
	 * Setup.
	 *
	 * @return void
	 */
	protected function setup() {
		$this->set_options_key();
	}

	/**
	 * Update Data.
	 *
	 * @param array $data
	 * @return boolean|\WP_Error
	 */
	protected function _update_data( $data ) {
		return update_option( $this->options_key, $data, property_exists( $this, 'autoload' ) ? $this->autoload : false );
	}

	/**
	 * Fetch Data.
	 *
	 * @return array
	 */
	protected function fetch_data() {
		return get_option( $this->options_key, array() );
	}

	/**
	 * Set Options Key.
	 *
	 * @return void
	 */
	abstract protected function set_options_key();
}
