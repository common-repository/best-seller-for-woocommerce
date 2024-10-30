<?php
namespace GPLSCore\GPLS_PLUGIN_WOBTSLR\Backgrounds\Base;

use GPLSCore\GPLS_PLUGIN_WOBTSLR\Base;

/**
 * Base Async Rqeuest Class.
 */
abstract class AsyncRequest extends Base {

	/**
	 * Start time of current process.
	 *
	 * @var int
	 */
	protected $start_time = 0;

	/**
	 * Data
	 *
	 * @var array
	 */
	protected $data = array();

	/**
	 * Push to queue
	 *
	 * @param mixed $data Data.
	 *
	 * @return $this
	 */
	public function push_to_queue( $data ) {
		$this->data[] = $data;

		return $this;
	}

	/**
	 * Set data used during the request
	 *
	 * @param array $data Data.
	 *
	 * @return self
	 */
	public function data( $data ) {
		$this->data = $data;

		return $this;
	}

	/**
	 * Check if data is empty.
	 *
	 * @return boolean
	 */
	public function is_data_empty() {
		return empty( $this->data );
	}

	/**
	 * Initiate new async request
	 */
	protected function __construct() {
		add_action( 'wp_ajax_' . self::get_identifier(), array( $this, 'maybe_handle' ) );
		add_action( 'wp_ajax_nopriv_' . self::get_identifier(), array( $this, 'maybe_handle' ) );

		$this->setup();
		$this->hooks();

		if ( method_exists( $this, 'background_hooks' ) ) {
			$this->background_hooks();
		}
	}

	/**
	 * Setup Background Process args.
	 *
	 * @return void
	 */
	abstract protected function setup();

	/**
	 * Set Background Hooks.
	 *
	 * @return void
	 */
	abstract protected function hooks();

	/**
	 * Initialize Page.
	 *
	 */
	public static function init() {
		if ( is_null( static::$instance ) ) {
			static::$instance = new static();
		}
		return static::$instance;
	}

	/**
	 * Maybe handle
	 *
	 * Check for correct nonce and pass to handler.
	 */
	public function maybe_handle() {
		// Close the request.
		session_write_close();

		check_ajax_referer( self::get_identifier(), 'nonce' );

		$this->handle();

		wp_die();
	}

	/**
	 * Dispatch the async request
	 *
	 * @return array|\WP_Error
	 */
	public function dispatch( $run_now = false ) {
		$url  = add_query_arg( $this->get_query_args(), $this->get_query_url() );
		$args = $this->get_post_args( $run_now );

		return wp_remote_post( esc_url_raw( $url ), $args );
	}

	/**
	 * Get query args
	 *
	 * @return array
	 */
	protected function get_query_args() {
		if ( property_exists( $this, 'query_args' ) ) {
			return $this->query_args;
		}

		$args = array(
			'action' => self::get_identifier(),
			'nonce'  => wp_create_nonce( self::get_identifier() ),
		);

		/**
		 * Filters the post arguments used during an async request.
		 *
		 * @param array $url
		 */
		return apply_filters( self::get_identifier() . '_query_args', $args );
	}

	/**
	 * Get post args
	 *
	 * @return array
	 */
	protected function get_post_args( $run_now = false ) {
		if ( property_exists( $this, 'post_args' ) ) {
			return $this->post_args;
		}

		$args = array(
			'timeout'   => 0.01,
			'blocking'  => false,
			'run_now'   => $run_now,
			// 'body'      => $this->data,
			'cookies'   => $_COOKIE,
			'sslverify' => apply_filters( 'https_local_ssl_verify', false ),
		);

		if ( property_exists( $this, 'post_args' ) ) {
			$args = array_merge( $args, $this->post_args );
		}

		return $args;
	}

	/**
	 * Get query URL
	 *
	 * @return string
	 */
	protected function get_query_url() {
		if ( property_exists( $this, 'query_url' ) ) {
			return $this->query_url;
		}

		$url = admin_url( 'admin-ajax.php' );

		/**
		 * Filters the post arguments used during an async request.
		 *
		 * @param string $url
		 */
		return apply_filters( self::get_identifier() . '_query_url', $url );
	}

	/**
	 * Time exceeded.
	 *
	 * Ensures the Data never exceeds a sensible time limit.
	 * A timeout limit of 30s is common on shared hosting.
	 *
	 * @return bool
	 */
	protected function time_exceeded() {
		$finish = $this->start_time + apply_filters( self::get_identifier() . '_default_time_limit', 20 ); // 20 seconds
		$return = false;

		if ( time() >= $finish ) {
			$return = true;
		}

		return apply_filters( self::get_identifier() . '_time_exceeded', $return );
	}

	/**
	 * Memory exceeded
	 *
	 * Ensures the Data process never exceeds 90%
	 * of the maximum WordPress memory.
	 *
	 * @return bool
	 */
	protected function memory_exceeded() {
		$memory_limit   = $this->get_memory_limit() * 0.9; // 90% of max memory
		$current_memory = memory_get_usage( true );
		$return         = false;

		if ( $current_memory >= $memory_limit ) {
			$return = true;
		}

		return apply_filters( self::get_identifier() . '_memory_exceeded', $return );
	}

	/**
	 * Get memory limit
	 *
	 * @return int
	 */
	protected function get_memory_limit() {
		if ( function_exists( 'ini_get' ) ) {
			$memory_limit = ini_get( 'memory_limit' );
		} else {
			// Sensible default.
			$memory_limit = '128M';
		}

		if ( ! $memory_limit || - 1 === intval( $memory_limit ) ) {
			// Unlimited, set to 32GB.
			$memory_limit = '32000M';
		}

		return wp_convert_hr_to_bytes( $memory_limit );
	}

	/**
	 * Is process running
	 *
	 * Check whether the current process is already running
	 * in a background process.
	 */
	protected function is_process_running() {
		if ( get_transient( self::get_identifier() . '_process_lock' ) ) {
			// Process already running.
			return true;
		}

		return false;
	}

	/**
	 * Lock process
	 *
	 * Lock the process so that multiple instances can't run simultaneously.
	 * Override if applicable, but the duration should be greater than that
	 * defined in the time_exceeded() method.
	 */
	protected function lock_process() {
		$this->start_time = time(); // Set start time of current process.
		$lock_duration    = ( property_exists( $this, 'queue_lock_time' ) ) ? $this->queue_lock_time : 60; // 1 minute
		set_transient( self::get_identifier() . '_process_lock', microtime(), $lock_duration );
	}

	/**
	 * Unlock process
	 *
	 * Unlock the process so that other instances can spawn.
	 *
	 * @return $this
	 */
	protected function unlock_process() {
		delete_transient( self::get_identifier() . '_process_lock' );
		return $this;
	}

	/**
	 * Check if run now Request.
	 *
	 * @return boolean
	 */
	protected function is_run_now_request() {
		return ! empty( $_REQUEST['run_now'] );
	}

	/**
	 * Should run the process again?
	 *
	 * @return boolean
	 */
	protected function should_run_again() {
		return get_option( self::get_identifier() . '_run_again', false );
	}

	/**
	 * Set flag to run the process again.
	 *
	 * @return void
	 */
	protected function set_run_again() {
		update_option( self::get_identifier() . '_run_again', true );
	}

	/**
	 * Clear the run process again flag.
	 *
	 * @return void
	 */
	protected static function clear_run_again() {
		delete_option( self::get_identifier() . '_run_again' );
	}

	/**
	 * Handle
	 *
	 * Override this method to perform any actions required
	 * during the async request.
	 */
	abstract protected function handle();

	/**
	 * Get Background Process Identifier.
	 *
	 * @return string
	 */
	protected static function get_identifier() {
		return static::get_id();
	}

	/**
	 * Get Background Hook Identifier.
	 *
	 * @return string
	 */
	protected static function get_hook_identifier() {
		return static::get_id() . '_cron';

	}

	/**
	 * Get Background Process Interval Identifier.
	 *
	 * @return string
	 */
	protected static function get_interval_identifier() {
		return static::get_id() . '_interval_cron';
	}

	/**
	 * Force ID for each Async background Request.
	 *
	 * @return string
	 */
	abstract protected static function get_id();
}
