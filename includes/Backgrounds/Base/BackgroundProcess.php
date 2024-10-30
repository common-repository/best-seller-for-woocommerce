<?php
namespace GPLSCore\GPLS_PLUGIN_WOBTSLR\Backgrounds\Base;

use GPLSCore\GPLS_PLUGIN_WOBTSLR\Base;

/**
 * Abstract Background Process Handler class.
 */
abstract class BackgroundProcess extends AsyncRequest {

	/**
	 * Main Hooks.
	 *
	 * @return void
	 */
	protected function hooks() {
		add_filter( 'cron_schedules', array( $this, 'schedule_cron_healthcheck' ) );
		add_action( self::get_hook_identifier(), array( $this, 'handle_cron_healthcheck' ) );
	}

	/**
	 * Dispatch
	 *
	 * @access public
	 * @return array|\WP_Error
	 */
	public function dispatch( $run_now = false ) {
		// Schedule the cron healthcheck.
		$this->schedule_event();

		$url  = add_query_arg( $this->get_query_args(), $this->get_query_url() );
		$args = $this->get_post_args( $run_now );
		return wp_remote_post( esc_url_raw( $url ), $args );
	}

	/**
	 * Save queue
	 *
	 * @return $this
	 */
	public function save() {
		if ( ! empty( $this->data ) ) {
			update_option( self::get_key(), $this->data );
		}
		$this->data = array();
		return $this;
	}

	/**
	 * Update queue
	 *
	 * @param array $data Data.
	 *
	 * @return $this
	 */
	public function update( $data ) {
		if ( ! empty( $data ) ) {
			update_option( self::get_key(), $data );
		} else {
			self::delete();
		}

		return $this;
	}

	/**
	 * Delete queue
	 *
	 * @return void
	 */
	protected static function delete() {
		delete_option( self::get_key() );
	}

	/**
	 * Generate data options key
	 *
	 * @return string
	 */
	protected static function get_key() {
		return self::get_identifier() . '_data_key';
	}

	/**
	 * Maybe process queue
	 *
	 * Checks whether data exists within the queue and that
	 * the process is not already running.
	 */
	public function maybe_handle() {
		// Close the request.
		session_write_close();

		if ( $this->is_process_running() ) {
			// Background process already running.
			if ( $this->is_run_now_request() ) {
				$this->set_run_again();
			}
			wp_die();
		}

		if ( $this->is_queue_empty() ) {
			// No data to process.
			wp_die();
		}

		check_ajax_referer( self::get_identifier(), 'nonce' );

		$this->handle();

		wp_die();
	}

	/**
	 * Is queue empty
	 *
	 * @return bool
	 */
	protected function is_queue_empty() {
		return false === get_option( self::get_key() );
	}

	/**
	 * Get Data
	 *
	 * @return array
	 */
	protected function get_data() {
		return get_option( self::get_key() );
	}

	/**
	 * Handle
	 *
	 * Pass each queue item to the task handler, while remaining
	 * within server memory and time limit constraints.
	 */
	protected function handle() {
		$this->lock_process();
		self::clear_run_again();

		$data = $this->get_data();

		foreach ( $data as $key => $value ) {
			$task = $this->task( $key, $value, $data );

			if ( false !== $task ) {
				$data[ $key ] = $task;
			} else {
				unset( $data[ $key ] );
			}

			if ( $this->time_exceeded() || $this->memory_exceeded() ) {
				// Data limits reached.
				break;
			}
		}

		// Unlock The process.
		$this->unlock_process();

		// Update Data.
		$this->update( $data );

		// Start a new Request if still data or a ( run now ) request was on hold.
		if ( ! empty( $data ) || $this->should_run_again() ) {
			$this->dispatch();
		} else {
			$this->complete();
		}

		wp_die();
	}


	/**
	 * Complete.
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		// Unschedule the cron healthcheck.
		$this->clear_scheduled_event();
	}

	/**
	 * Schedule cron healthcheck
	 *
	 * @access public
	 *
	 * @param mixed $schedules Schedules.
	 *
	 * @return mixed
	 */
	public function schedule_cron_healthcheck( $schedules ) {
		$interval = property_exists( $this, 'interval' ) ? $this->interval : MINUTE_IN_SECONDS * 24;

		// Adds every 5 minutes to the existing schedules.
		$schedules[ self::get_interval_identifier() ] = array(
			'interval' => $interval,
			'display'  => sprintf( esc_html__( 'Every %d Minutes' ), $interval ),
		);

		return $schedules;
	}

	/**
	 * Handle cron healthcheck
	 *
	 * Restart the background process if not already running
	 * and data exists in the queue.
	 */
	public function handle_cron_healthcheck() {
		if ( $this->is_process_running() ) {
			// Background process already running.
			exit;
		}

		if ( $this->is_queue_empty() ) {
			// No data to process.
			$this->clear_scheduled_event();
			exit;
		}

		$this->handle();

		exit;
	}

	/**
	 * Schedule event
	 */
	protected function schedule_event() {
		if ( ! wp_next_scheduled( self::get_hook_identifier() ) ) {
			wp_schedule_event( time(), self::get_interval_identifier(), self::get_hook_identifier() );
		}
	}

	/**
	 * Clear scheduled event
	 */
	protected function clear_scheduled_event() {
		$timestamp = wp_next_scheduled( self::get_hook_identifier() );

		if ( $timestamp ) {
			wp_unschedule_event( $timestamp, self::get_hook_identifier() );
		}
	}

	/**
	 * Cancel Process
	 *
	 * Stop processing queue items, clear cronjob and delete Delete.
	 */
	public function cancel_process( $clear_event = true ) {
		if ( ! $this->is_queue_empty() ) {
			self::delete();
		}

		if ( $clear_event ) {
			$this->cancel_process_clear();
		}
	}

	/**
	 * Clear event for process cancel.
	 *
	 * @return void
	 */
	protected function cancel_process_clear() {
		self::clear_cron();
	}

	/**
	 * Clear Cron.
	 *
	 * @return void
	 */
	public static function clear_cron() {
		self::clear_run_again();
		self::delete();
		wp_clear_scheduled_hook( self::get_hook_identifier() );
	}

	/**
	 * Run Background Process now.
	 *
	 * @return void
	 */
	public function run( $data ) {
		$this->data( $data );
		$this->save();
		$this->dispatch( true );
	}

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @return mixed
	 */
	abstract protected function task( $key, $value, &$data );
}
