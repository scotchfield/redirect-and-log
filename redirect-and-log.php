<?php
/**
 * Plugin Name: Redirect and Log
 * Plugin URI: http://scootah.com/
 * Description: Set up simple WordPress redirects and log visits to the database
 * Version: 1.0
 * Author: Scott Grant
 * Author URI: http://scootah.com/
 */
class WP_RedirectAndLog {

	/**
	 * Store reference to singleton object.
	 */
	private static $instance = null;

	/**
	 * The domain for localization.
	 */
	const DOMAIN = 'wp-redirectandlog';

	/**
	 * Instantiate, if necessary, and add hooks.
	 */
	public function __construct() {
		if ( isset( self::$instance ) ) {
			wp_die( esc_html__(
				'WP_RedirectAndLog is already instantiated!',
				self::DOMAIN ) );
		}

		self::$instance = $this;

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post_meta' ) );

		add_action( 'template_redirect', array( $this, 'do_redirect' ) );
	}

	/**
	 * Initialize meta box.
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'redirect-and-log-url',
			'Redirect and Log URL',
			array( $this, 'generate_meta_box' ),
			'',
			'normal'
		);
	}

	/**
	 * Show HTML for the zone details stored in post meta.
	 */
	public function generate_meta_box( $post ) {
		$post_id = intval( $post->ID );
		$post_url = get_post_meta( $post_id, 'redirect_and_log_url', true );

		echo( '<p>Redirect URL: <input type="text" name="redirect_and_log_url" value="' .
			$post_url . '"></p>' );
	}

	/**
	 * Extract the updates from $_POST and save in post meta.
	 */
	public function save_post_meta( $post_id ) {
		if ( isset( $_POST[ 'redirect_and_log_url' ] ) ) {
			update_post_meta( $post_id, 'redirect_and_log_url',
				$_POST[ 'redirect_and_log_url' ] );
		}
	}

	public function do_redirect() {
		$post_id = get_the_ID();

		if ( $post_id !== false ) {
			$post_url = get_post_meta( $post_id, 'redirect_and_log_url', true );
			if ( strlen( $post_url ) > 0 ) {
				wp_redirect( $post_url );
				exit();
			}
		}
	}

	/**
	 *
	 */
	public function plugin_settings_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'You do not have sufficient permissions to access this page.', self::DOMAIN ) );
		}

		echo( '<h1>Redirect and Log</h1>' );
	}

}

$wp_redirectandlog = new WP_RedirectAndLog();
