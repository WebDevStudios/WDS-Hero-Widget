<?php
/*
Plugin Name: WDS Hero Widget
Plugin URI:  http://webdevstudios.com
Description: Allows you to add Hero images and videos using Widgets, Shortcodes and template tags!
Version:     1.0.0
Author:      WebDevStudios
Author URI:  http://webdevstudios.com
Donate link: http://webdevstudios.com
License:     GPLv2
Text Domain: wds-hero-widget
Domain Path: /languages
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

// Exit if accessed directly
if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}

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

		// Includes more files.
		$this->includes();

		// Enqueue styles
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Enqueue JS scripts.
	 *
	 * @return void
	 */
	public function enqueue_scripts() {
	}

	/**
	 * Enqueue public facing styles.
	 *
	 * @return void
	 */
	public function enqueue_styles() {
			wp_enqueue_style( 'wds-hero-widget', plugins_url( 'assets/css/wds-hero-widget.css', __FILE__ ), array(), $this->script_version(), 'screen' );
	}

	/**
	 * Shows a Hero.
	 *
	 * Can be used as a template tag or added via a shortcode.
	 *
	 * Template Tag Example:
	 *
	 *     <?php wds_hero( array(
	 *         'type'                  => 'primary',
	 *         'class'                 => 'my-class',
	 *         'image'                 => 'https://mysite.com/my-background.png', // Background image
	 *         'video'                 => 'https://mysite.com/my-video.mp4', // Background video
	 *         'echo'                  => true, // Should always be set to true when using a template tag
	 *         'heading'               => __( 'My Heading', 'text-domain' ),
	 *         'sub_heading'           => __( 'My Sub heading', 'text-domain' ),
	 *         'button_text'           => __( 'My Button', 'text-domain' ),
	 *         'button_link'           => 'http://webdevstudios.com',
	 *         'custom_content_action' => 'my_content_action_filter',
	 *         'overlay'               => 0.2, // Sets an overlay to 0.2 (20%)
	 *         'overlay_color'         => '#000', // Black
	 *     ) ); ?>
	 *
	 * Shortcode Example:
	 *
	 *     [hero type="primary" class="my-class" video="http://mysite.com/my-video.mp4" heading="My Heading" sub_heading="My Sub Heading" button_text="My Button" button_link="http://webdevstudios.com" custom_content_action="my_content_action_filter" image="http://mysite.com/my-image.png" overlay="0.2" overlay_color="#fff"]
	 *         Additional Content
	 *     [/hero]
	 *
	 * Argument Defaults:
	 *
	 *     'type'                  => 'primary',
	 *     'class'                 => '',
	 *     'video'                 => false,
	 *     'image'                 => false,
	 *     'echo'                  => false,
	 *     'heading'               => false,
	 *     'sub_heading'           => false,
	 *     'button_text'           => false,
	 *     'button_link'           => false,
	 *     'custom_content_action' => false,
	 *     'overlay'               => false,
	 *     'overlay_color'         => '#000',
	 *
	 * Adding your own content using an action is simple. If you're using the
	 * example template tag or shortcode from above:
	 *
	 *     <?php
	 *
	 *     function my_content() {
	 *         echo __( "My own content", 'text-domain' );
	 *     }
	 *
	 *     // In order for the action to work, you must define custom_content_action
	 *     add_action( 'my_content_action_filter' , 'my_content' );
	 *
	 *     ?>
	 *
	 * @param  array $args Arguments to apply to hero.
	 *
	 * @return void
	 */
	function wds_hero( $args = array(), $content = false ) {

		// HTML arguments.
		$args_setup = array(

			// A primary or secondary
			'type' => ( isset( $args['type'] ) )  ? $args['type'] : 'primary',

			// Extra classes
			'class'=> ( isset( $args['class'] ) )  ? $args['class'] : '',

			// Add a playable video (YouTube) to the hero.
			'video'=> ( isset( $args['video'] ) )  ? $args['video'] : false,

			// Want us to echo it out (on by default for shortcodes).
			'echo' => ( isset( $args['echo'] ) )   ? $args['echo'] : false,

			// Add an action filter so additional content can be added.
			'custom_content_action' => ( isset( $args['custom_content_action'] ) ) ? $args['custom_content_action'] : false,

			// Ability to set element background image.
			'image' => ( isset( $args['image'] ) ) ? $args['image'] : false,

			// Overlay
			'overlay' => ( isset( $args['overlay'] ) ) ? (float) $args['overlay']  : false,

			// Overlay
			'overlay_color' => ( isset( $args['overlay_color'] ) ) ? $args['overlay_color']  : '#000',

		);

		// Arguments for content.
		$args_content = array(
			'heading'      => ( isset( $args['heading'] ) )      ? $args['heading']     : false,
			'sub_heading'  => ( isset( $args['sub_heading'] ) )  ? $args['sub_heading'] : false,
			'button_text'  => ( isset( $args['button_text'] ) )  ? $args['button_text'] : false,
			'button_link'  => ( isset( $args['button_link'] ) )  ? $args['button_link'] : false,
		);

		$args = array_merge( $args_setup, $args_content );

		ob_start();
		?>

		<div class="hero <?php echo esc_attr( $args['type'] ); ?>-hero <?php echo esc_attr( $args['class'] ); ?>">
			<div class="content-wrapper">
				<span class="content-headings">
					<?php if ( $args['heading'] && 'primary' == $args['type'] ) : ?>
						<h1><?php echo wp_kses( $args['heading'], wp_kses_allowed_html( 'post' ) ); ?></h1>
					<?php else: ?>
						<h2><?php echo wp_kses( $args['heading'], wp_kses_allowed_html( 'post' ) ); ?></h2>
					<?php endif; ?>

					<?php if ( $args['sub_heading'] ) : ?>
						<p><?php echo wp_kses( $args['sub_heading'], wp_kses_allowed_html( 'post' ) ); ?></p>
					<?php endif; ?>
				</span>
				<span class="content-button">
					<?php if ( $args['button_text'] ) : ?>
						<?php if( $args['button_link'] ): ?><a href="<?php echo esc_url( $args['button_link'] ); ?>"><?php endif; ?><button><?php echo wp_kses( $args['button_text'], wp_kses_allowed_html( 'post' ) ); ?></button><?php if( $args['button_link'] ): ?></a><?php endif; ?>
					<?php endif; ?>
				</span>
				<span class="content-custom-content">
					<?php if ( $args['custom_content_action'] ) : ?>

						<!-- Content added via wds_hero_inner_html action -->
						<?php do_action( $args['custom_content_action'] ); ?>

					<?php endif; ?>
					<?php if ( $content ) : ?>
						<!-- Content passed via shortcode -->
						<?php echo $content; ?>
					<?php endif; ?>
				</span>
			</div>
			<?php if ( $args['image'] ): ?>
				<div class="image"  style="<?php $this->wds_hero_bg_image( $args['image'] ) ?>"></div>
			<?php endif; ?>

			<?php if ( $args['video'] ): ?>
				<!-- To be used as the background if a video is supplied -->
				<video loop autoplay muted>
					<source src="<?php echo $args['video'] ?>" type="video/<?php echo pathinfo( basename( $args['video'] ), PATHINFO_EXTENSION ); ?>" />
				</video>
			<?php endif; ?>

			<?php if ( $args['overlay'] ): ?>
				<div class="overlay" style="<?php $this->wds_hero_overlay( array(
					'overlay' => $args['overlay'],
					'overlay_color' => $args['overlay_color']
				) ); ?>"> </div>
			<?php endif; ?>
		</div>

		<?php
		$html = ob_get_contents();
		ob_end_clean();

		// Return/echo the HTML
		if ( $args['echo'] ) {
			echo $html;
		} else {
			return $html;
		}
	}

	/**
	 * Add inline CSS for for background image.
	 *
	 * @param  string $background_url URL of the background image.
	 *
	 * @return string                 CSS background-image with size set to cover.
	 */
	function wds_hero_bg_image( $background_url ) {
		if ( ! $background_url ) {
			return;
		}

		echo "background-image: url($background_url);";
	}

	/**
	 * Sets the inline CSS for opacity.
	 *
	 * @param  string $opacity URL of the background image.
	 *
	 * @return string                 CSS opacity set to $opacity
	 */
	function wds_hero_overlay( $args ) {
		if ( ! $args['overlay'] ) {
			return;
		}

		$css = "opacity: " . $args['overlay'] . ";";

		// Overlay color
		if ( $args['overlay_color'] ) {
			$css .= " background-color: " . $args['overlay_color'] . ";";
		}

		echo $css;
	}

	/**
	 * Include a file from the includes directory
	 * @since  1.0
	 * @param  string $filename Name of the file to be included
	 */
	public static function includes( $filename = false ) {
		if ( $filename ) {
			$file = self::dir( './includes/'. $filename .'.php' );
			if ( file_exists( $file ) ) {
				return include_once( $file );
			}
		}
		foreach ( new DirectoryIterator( trailingslashit( dirname( __FILE__ ) ) . 'includes' ) as $fileInfo ) {
			if( ! $fileInfo->isDot() ) {
				require_once trailingslashit( $fileInfo->getPath() ) . $fileInfo->getFilename();
			}
		}
	}

	/**
	 * Set the plugin header info.
	 *
	 * @return void
	 */
	function set_plugin_info() {
		$this->plugin_headers = get_file_data(
			__FILE__, array(
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

		// Widget
		add_action( 'widgets_init', array( $this, 'wds_hero_widget_widget' ) );

		// Template tag shortcode
		add_shortcode( 'hero', array( $this, 'wds_hero' ) );
	}

	/**
	 * Register this widget with WordPress.
	 */
	function wds_hero_widget_widget() {
		register_widget( 'WDS_Hero_Widget_Widget' );
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
