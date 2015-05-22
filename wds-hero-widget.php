<?php
/**
* Plugin Name: WDS Hero Widget
* Plugin URI:  http://webdevstudios.com
* Description: Allows you to add Hero images and videos using Widgets, Shortcodes and template tags!
* Version:     1.0.0
* Author:      WebDevStudios
* Author URI:  http://webdevstudios.com
* Donate link: http://webdevstudios.com
* License:     GPLv2
* Text Domain: wds-hero-widget
* Domain Path: /languages
*/

/**
 * Copyright (c) 2015 WebDevStudios (email : contact@webdevstudios.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

/**
 * Built using generator-plugin-wp
 */

/**
 * Main initiation class
 */
class WDS_Hero_Widget {

	public    $plugin_headers;
	public    $version;
	public    $text_domain;

	protected $url      = '';
	protected $path     = '';
	protected $basename = '';
	protected static $single_instance = null;

	/**
	 * Creates or returns an instance of this class.
	 * @since  0.1.0
	 * @return WDS_Hero_Widget A single instance of this class.
	 */
	public static function get_instance() {
		if ( null === self::$single_instance ) {
			self::$single_instance = new self();
		}

		return self::$single_instance;
	}

	/**
	 * Sets up our plugin
	 * @since  1.0.0
	 */
	protected function __construct() {
		$this->basename = plugin_basename( __FILE__ );
		$this->url      = plugin_dir_url( __FILE__ );
		$this->path     = plugin_dir_path( __FILE__ );

		$this->plugin_classes();
		$this->hooks();

		$this->set_plugin_info();
		$this->version = $this->get_plugin_info( 'Version' );
		$this->text_domain = $this->get_plugin_info( 'Text Domain' );
	}


	/**
	 * Set the plugin header info.
	 *
	 * @return void
	 */
	function set_plugin_info() {
		$this->plugin_headers = get_file_data(
			trailingslashit( dirname( __FILE__ ) ) . '../ryde-wp-ant-dashboard.php', array(
				'Plugin Name' => 'Plugin Name',
				'Plugin URI' => 'Plugin URI',
				'Version' => 'Version',
				'Description' => 'Description',
				'Author' => 'Author',
				'Author URI' => 'Author URI',
				'Text Domain' => 'Text Domain',
				'Domain Path' => 'Domain Path',
			),
		'plugin' );
	}

	/**
	 * Get a particular header value.
	 *
	 * @param  string $key The value of the plugin header.
	 *
	 * @return void
	 */
	function get_plugin_info( $key ) {
		return trim( $this->plugin_headers[ $key ] );
	}

	/**
	 * Attach other plugin classes to the base plugin class.
	 * @since 1.0.0
	 */
	function plugin_classes() {
		// Attach other plugin classes to the base plugin class.
		// $this->admin = new WDSHW_Admin( $this );
	}

	/**
	 * Add hooks and filters
	 * @since 1.0.0
	 */
	public function hooks() {
		register_activation_hook( __FILE__, array( $this, '_activate' ) );
		register_deactivation_hook( __FILE__, array( $this, '_deactivate' ) );

		add_action( 'init', array( $this, 'init' ) );
	}

	/**
	 * Activate the plugin
	 * @since  1.0.0
	 */
	function _activate() {
		// Make sure any rewrite functionality has been loaded
		flush_rewrite_rules();
	}

	/**
	 * Deactivate the plugin
	 * Uninstall routines should be in uninstall.php
	 * @since  1.0.0
	 */
	function _deactivate() {}

	/**
	 * When debugging, disabled cache.
	 *
	 * @return string Timestamp when debugging, actual version when not.
	 */
	protected function script_version() {
		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			return time();
		} else {
			return $this->version;
		}
	}

	/**
	 * Init hooks
	 * @since  1.0.0
	 * @return null
	 */
	public function init() {
		if ( $this->check_requirements() ) {
			load_plugin_textdomain( 'wds-hero-widget', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}
	}

	/**
	 * Check that all plugin requirements are met
	 * @since  1.0.0
	 * @return boolean
	 */
	public static function meets_requirements() {
		// Do checks for required classes / functions
		// function_exists('') & class_exists('')

		// We have met all requirements
		return true;
	}

	/**
	 * Check if the plugin meets requirements and
	 * disable it if they are not present.
	 * @since  1.0.0
	 * @return boolean result of meets_requirements
	 */
	public function check_requirements() {
		if ( ! $this->meets_requirements() ) {
			// Display our error
			echo '<div id="message" class="error">';
				echo '<p>' . sprintf( __( 'WDS Hero Widget is missing requirements and has been <a href="%s">deactivated</a>. Please make sure all requirements are available.', 'wds-hero-widget' ), admin_url( 'plugins.php' ) ) . '</p>';
			echo '</div>';
			// Deactivate our plugin
			deactivate_plugins( $this->basename );

			return false;
		}

		return true;
	}

	/**
	 * Magic getter for our object.
	 *
	 * @since  1.0.0
	 * @param string $field
	 * @throws Exception Throws an exception if the field is invalid.
	 * @return mixed
	 */
	public function __get( $field ) {
		switch ( $field ) {
			case 'version':
				return self::$this->version;
			case 'basename':
			case 'url':
			case 'path':
				return $this->$field;
			default:
				throw new Exception( 'Invalid '. __CLASS__ .' property: ' . $field );
		}
	}

	/**
	 * Include a file from the includes directory
	 * @since  1.0.0
	 * @param  string $filename Name of the file to be included
	 */
	public static function include_file( $filename ) {
		$file = self::dir( 'includes/'. $filename .'.php' );
		if ( file_exists( $file ) ) {
			return include_once( $file );
		}
	}
}

/**
 * Grab the WDS_Hero_Widget object and return it.
 * Wrapper for WDS_Hero_Widget::get_instance()
 */
function wds_hero_widget() {
	return WDS_Hero_Widget::get_instance();
}

// Kick it off
wds_hero_widget();
