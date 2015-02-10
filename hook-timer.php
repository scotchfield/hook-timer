<?php
/**
 * Plugin Name: Hook Timer
 * Plugin URI: http://scotchfield.com/
 * Description: Track the time elapsed for each WordPress hook
 * Version: 0.1
 * Author: Scott Grant
 * Author URI: http://scotchfield.com/
 * License: GPL2
 */
class WP_HookTimer {

	private $time_obj;
	private $stack;

	/**
	 * The domain for localization.
	 */
	const DOMAIN = 'hooktimer';

	public static function getInstance() {
		static $instance = null;

		if ( null === $instance ) {
			$instance = new static();
		}

		return $instance;
	}

	/**
	 * Instantiate, if necessary, and add hooks.
	 */
	protected function __construct() {
		$this->time_obj = array();
		$this->stack = array();

		add_action( 'all', array( $this, 'time_start' ) );
		add_action( 'shutdown', array( $this, 'store' ) );

		add_action( 'admin_menu', array( $this, 'add_admin_menu' ) );
	}

	public function time_start() {
		$this->stack[] = microtime( true );

		// Props to Viper007Bond for this approach, who built 'whatissoslow.php'
		// https://gist.github.com/Viper007Bond/5192117
		add_filter( current_filter(), array( $this, 'time_end' ), 99999 );
	}

	public function time_end( $data ) {
		remove_filter( current_filter(), array( $this, 'time_end' ), 99999 );

		$start_time = array_pop( $this->stack );
		$end_time = microtime( true );

		$delta_time = $end_time - $start_time;
		array_push( $this->time_obj, array( $delta_time, $end_time, current_filter() ) );

		return $data;
	}

	public function store() {
		update_option( self::DOMAIN . '_times', $this->time_obj );
	}

	public function get_all_times() {
		return $this->time_obj;
	}

	public function clear_times() {
		$this->time_obj = array();
	}

	public function get_times_by_hook( $hook ) {
		$hook_obj = array();

		foreach ( $this->time_obj as $time ) {
			if ( $time[2] === $hook ) {
				$hook_obj[] = $time;
			}
		}

		return $hook_obj;
	}

	/**
	 * Add menu options to the dashboard, and meta boxes to the edit pages.
	 */
	public function add_admin_menu() {
		$page = add_options_page(
			esc_html__( 'Hook Timer', self::DOMAIN ),
			esc_html__( 'Hook Timer', self::DOMAIN ),
			'manage_options',
			self::DOMAIN,
			array( $this, 'plugin_settings_page' )
		);
	}

	public function plugin_settings_page() {
		$time_obj = get_option( self::DOMAIN . '_times', array() );
		rsort( $time_obj );

		echo( '<h1>Hook Timer</h1><h2>Total time spent during individual hooks on the <em>previous</em> page:</h2><ul>' );
		foreach ( $time_obj as $time ) {
			echo( '<li>' . $time[ 2 ] . ': ' . $time[ 0 ] . '</li>' );
		}
		echo( '</ul>' );
	}

}

$wp_hooktimer = WP_HookTimer::getInstance();
