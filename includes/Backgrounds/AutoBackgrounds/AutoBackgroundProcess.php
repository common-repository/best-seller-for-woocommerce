<?php
namespace GPLSCore\GPLS_PLUGIN_WOBTSLR\Backgrounds\AutoBackgrounds;

use GPLSCore\GPLS_PLUGIN_WOBTSLR\Backgrounds\Base\BackgroundProcess;

/**
 * Auto Background Process Handler class.
 */
abstract class AutoBackgroundProcess extends BackgroundProcess {

	/**
	 * Auto Process Interval.
	 *
	 * @var int
	 */
	protected $interval = \HOUR_IN_SECONDS * 24;

	/**
	 * Dispatch.
	 *
	 * @return array|\WP_Error
	 */
	public function dispatch( $run_now = false ) {
		$url  = add_query_arg( $this->get_query_args(), $this->get_query_url() );
		$args = $this->get_post_args( $run_now );
		return wp_remote_post( esc_url_raw( $url ), $args );
	}

	/**
	 * Handle cron healthcheck
	 *
	 * Restart the background process if not already running
	 * and data exists in the queue.
	 */
	public function handle_cron_healthcheck() {
		// 1) Process already running, abort.
		if ( $this->is_process_running() ) {
			// Background process already running.
			exit;
		}

		// 2) Set the auto process data, then save it.
		$this->_auto();

		// 3) Start handling the data.
		$this->handle();

		exit;
	}

	/**
	 * Setup the Auto process before handle.
	 *
	 * @return void
	 */
	private function _auto() {
		$this->auto();
		$this->save();
	}

	/**
	 * Setup Auto Tasks Cron.
	 *
	 * @return void
	 */
	protected static function auto_cron() {
		static::init();
		if ( ! wp_next_scheduled( self::get_hook_identifier() ) ) {
			wp_schedule_event( time(), self::get_interval_identifier(), self::get_hook_identifier() );
		}
	}

	/**
	 * Complete.
	 */
	protected function complete() {
		// Do nothing, It's auto background.
	}

	/**
	 * Setup Auto Cron.
	 *
	 * @return void
	 */
	public static function setup_auto_cron() {
		self::auto_cron();
	}

	/**
	 * Do nothing here for close process.
	 *
	 * @return void
	 */
	protected function cancel_process_clear() {
	}

	/**
	 * Auto Background Process Handle.
	 * Just set the Data.
     * use data() | push_to_queue()
	 *
	 * @return void
	 */
	abstract protected function auto();
}
