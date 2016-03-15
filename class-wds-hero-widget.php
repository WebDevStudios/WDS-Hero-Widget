<?php

// Exit if accessed directly
if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WDS_Hero_Widget' ) ) :
	/**
	 * WDS Hero Widget Base Class.
	 *
	 * @since  1.0.0
	 * @package  wds-hero-widget
	 */
	class WDS_Hero_Widget {
		/**
		 * Plugin Header Information.
		 *
		 * @since  1.0.0
		 * @var array
		 */
		public $plugin_headers;

		/**
		 * Slider CPT Class.
		 *
		 * @since  1.0.0
		 * @var object WDS_Slider_CPT
		 */
		public $slider_cpt;

		/**
		 * Single instance plugin.
		 *
		 * @since  1.0.0
		 * @var null
		 */
		protected static $single_instance = null;

		/**
		 * Creates or returns an instance of this class.
		 *
		 * @since  1.0.0
		 *
		 * @return WDS_Hero_Widget A single instance of this class.
		 */
		public static function get_instance() {
			if ( null === self::$single_instance ) {
				self::$single_instance = new self();
			}

			return self::$single_instance;
		}

		/**
		 * Sets up our plugin.
		 *
		 * @since  1.0.0
		 */
		protected function __construct() {
			// Includes more files.
			$this->includes();
			$this->set_plugin_info();

			// Slider CPT.
			$this->slider_cpt = new WDS_Slider_CPT();

			// Activate
			register_activation_hook( __FILE__, array( $this, '_activate' ) );

			// Enqueue styles
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

			// s
			add_action( 'init', array( $this, 'load_text_domain' ) );

			// Widget
			if ( ! defined( 'DISABLE_WDS_HERO_WIDGET' ) || false === DISABLE_WDS_HERO_WIDGET  ) {
				add_action( 'widgets_init', array( $this, 'wds_hero_widget_widget' ) );
			}

			// Template tag shortcode
			add_shortcode( 'hero', array( $this, 'wds_hero' ) );
		}

		/**
		 * Enqueue JS scripts.
		 *
		 * @since  1.0.0
		 */
		public function enqueue_scripts() {
			// Slick animations.
			wp_enqueue_script( 'slick-js', plugins_url( 'assets/bower/slick.js/slick/slick.js', __FILE__ ), array( 'jquery' ), $this->script_version(), true );

			// Setup slick on sliders.
			wp_enqueue_script( 'wds-hero-widget', plugins_url( 'assets/js/wds-hero-widget.js', __FILE__ ), array( 'jquery', 'slick-js' ), $this->script_version(), true );
		}

		/**
		 * Enqueue public facing styles.\
		 *
		 * @since  1.0.0
		 */
		public function enqueue_styles() {
			wp_enqueue_style( 'wds-hero-widget', plugins_url( 'assets/css/wds-hero-widget.css', __FILE__ ), array(), $this->script_version(), 'screen' );

			// Slick animations.
			wp_enqueue_style( 'slick-css', plugins_url( 'assets/bower/slick.js/slick/slick.css', __FILE__ ), array(), $this->script_version() );
			wp_enqueue_style( 'slick-css-theme', plugins_url( 'assets/bower/slick.js/slick/slick-theme.css', __FILE__ ), array( 'slick-css' ), $this->script_version() );
		}

		/**
		 * Get the details of a logo (attachment).
		 *
		 * @since  1.0.0
		 *
		 * @param  int $attachment_id   The ID of the attachment.
		 *
		 * @return array                Details for the attachment/logo.
		 */
		function get_attachment_details( $attachment_id, $size = 'large' ) {
			// Get the desired attachment src for the size we want.
			$details['src'] = wp_get_attachment_image_src( $attachment_id, $size );

			$details['src'] = ( isset( $details['src'][0] ) ) ? $details['src'][0] : $details['src'];

			// Meta alt tag.
			$details['alt'] = get_post_meta( $attachment_id, '_wp_attachment_image_alt', true );

			// We want to get the description (have to hack post_content for that).
			$attachment = get_post( $attachment_id );
			$details['url'] = $attachment->post_content;

			return $details;
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
		 *         'id'                    => 'id-name',
		 *         'class'                 => 'my-class',
		 *         'image'                 => 'https://mysite.com/my-background.png', // Background image
		 *         'video'                 => 'https://mysite.com/my-video.mp4', // Background video
		 *         'echo'                  => true, // Should always be set to true when using a template tag
		 *         'heading'               => __( 'My Heading', 'text-domain' ),
		 *         'sub_heading'           => __( 'My Sub heading', 'text-domain' ),
		 *         'button_text'           => __( 'My Button', 'text-domain' ),
		 *         'button_link'           => 'http://webdevstudios.com',
		 *         'custom_content_action' => 'hero_action',
		 *         'overlay'               => 0.2, // Sets an overlay to 0.2 (20%)
		 *         'overlay_color'         => '#000', // Black
		 *         'slider_id'             => false, // The slider post id of the CPT "Hero Sliders."
		 *     ) ); ?>
		 *
		 * Shortcode Example:
		 *
		 *     [hero type="primary" id="my-id" class="my-class" video="http://mysite.com/my-video.mp4" heading="My Heading" sub_heading="My Sub Heading" button_text="My Button" button_link="http://webdevstudios.com" custom_content_action="hero_action" image="http://mysite.com/my-image.png" overlay="0.2" overlay_color="#fff" slider_id="2"]
		 *         Additional Content
		 *     [/hero]
		 *
		 * Argument Defaults:
		 *
		 *     'type'                  => 'primary',
		 *     'id'                    => '',
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
		 *     'slider_id'             => false,
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
		 *     add_action( 'hero_action' , 'my_content' );
		 *
		 *     ?>
		 *
		 * @param  array $args Arguments to apply to hero.
		 *
		 */
		function wds_hero( $args = array(), $content = false ) {
			$defaults = array(
				'button_link'           => false,
				'button_text'           => false,
				'id'                    => '',
				'class'                 => '',
				'content'               => false,
				'custom_content_action' => false,
				'echo'                  => false,
				'heading'               => false,
				'image'                 => false,
				'overlay'               => false,
				'overlay_color'         => '#000',
				'slider_id'             => false,
				'sub_heading'           => false,
				'type'                  => 'primary',
				'video'                 => false,
			);

			$args = wp_parse_args( $args, $defaults );

			ob_start();

			?>

			<div id="<?php echo esc_attr( $args['id'] ); ?>" class="hero <?php echo esc_attr( $args['type'] ); ?>-hero <?php echo esc_attr( $args['class'] ); ?>">

				<?php if ( $args['slider_id'] ) :

					// Get the images stored for the slider id set/passed.
					$images = get_post_meta( $args['slider_id'], 'wds_hero_slider_images', true );

					$speed_meta = get_post_meta( $args['slider_id'], 'wds_hero_slider_speed', true );
					$speed = $speed_meta ? absint( $speed_meta ) : 5000;
				?>
					<div class="sliders" data-slider-speed="<?php echo esc_attr( $speed ); ?>">
						<?php if ( isset( $images ) && is_array( $images ) ) : ?>
							<?php foreach ( $images as $attachment_id => $src ) :

								// Attachment details.
								$details = $this->get_attachment_details( $attachment_id, 'large' );
								$src = $details['src']; // A better URL
								$alt = $details['alt'];
							?>

								<div class="slider" style="background-image: url(<?php echo $src; ?>);">
									<img src="<?php echo $src; ?>" alt="<?php echo $alt; ?>" />
								</div>

							<?php endforeach; ?>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<div class="content-wrapper">
					<span class="content-headings">
						<?php if ( $args['heading'] && 'primary' == $args['type'] ) : ?>
							<h1><?php echo wp_kses_post( $args['heading'] ); ?></h1>
						<?php else: ?>
							<h2><?php echo wp_kses_post( $args['heading'] ); ?></h2>
						<?php endif; ?>

						<?php if ( $args['sub_heading'] ) : ?>
							<p><?php echo wp_kses_post( $args['sub_heading'] ); ?></p>
						<?php endif; ?>
					</span>

					<span class="content-button">
						<?php if ( $args['button_text'] ) : ?>
							<?php if ( $args['button_link'] ) : ?><a href="<?php echo esc_url( $args['button_link'] ); ?>"><?php endif; ?><button><?php echo esc_html( $args['button_text'] ); ?></button><?php if ( $args['button_link'] ) : ?></a><?php endif; ?>
						<?php endif; ?>
					</span>

					<span class="content-custom-content">
						<?php if ( $args['custom_content_action'] ) : ?>

							<!-- Content added via wds_hero_inner_html action -->
							<?php do_action( $args['custom_content_action'] ); ?>

						<?php endif; ?>

						<?php if ( $args['content'] ) : ?>
							<!-- Content passed via shortcode content="" -->
							<?php echo wp_kses_post( $args['content'] ); ?>
						<?php endif; ?>

						<!-- Shortcode Content -->
						<?php if ( $content ) : ?>
							<?php echo wp_kses_post( $content ); ?>
						<?php endif; ?>
					</span>
				</div>

				<?php if ( $args['image'] ): ?>
					<div class="image"  style="<?php $this->wds_hero_bg_image( $args['image'] ) ?>"></div>
				<?php endif; ?>

				<?php if ( $args['video'] ): ?>
					<!-- To be used as the background if a video is supplied -->
					<video loop autoplay muted>
						<source src="<?php echo esc_url( $args['video'] ); ?>" type="video/<?php echo pathinfo( basename( $args['video'] ), PATHINFO_EXTENSION ); ?>" />
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

			echo 'background-image: url(' . esc_url( $background_url ) . ');';
		}

		/**
		 * Sets the inline CSS for opacity.
		 *
		 * @since 1.0.0
		 *
		 * @param  string $opacity URL of the background image.
		 *
		 * @return string                 CSS opacity set to $opacity
		 */
		function wds_hero_overlay( $args ) {
			if ( ! $args['overlay'] ) {
				return;
			}

			$css = "opacity: " . esc_attr( $args['overlay'] ) . ";";

			// Overlay color
			if ( $args['overlay_color'] ) {
				$css .= " background-color: " . esc_attr( $args['overlay_color'] ) . ";";
			}

			echo $css;
		}

		/**
		 * Include a file from the includes directory
		 *
		 * @since  1.0.0
		 */
		public function includes() {
			$files = array(
				'includes/class-wds-slider-cpt.php',
				'includes/class-wds-hero-widget.php',
				'includes/template-tags.php',
			);

			foreach ( $files as $file ) {
				require_once( $file );
			}

			// CMB2
			if ( ! class_exists( 'CMB2' ) ) {
				require_once( 'includes/cmb2/init.php' );
			}
		}

		/**
		 * Set the plugin header info.
		 *
		 * @since  1.0.0
		 */
		function set_plugin_info() {
			$headers_file = trailingslashit( dirname( __FILE__ ) ) . 'wds-hero-widget.php';
			$this->plugin_headers = get_file_data(
				$headers_file, array(
					'Plugin Name' => 'Plugin Name',
					'Plugin URI'  => 'Plugin URI',
					'Version'     => 'Version',
					'Description' => 'Description',
					'Author'      => 'Author',
					'Author URI'  => 'Author URI',
					'Text Domain' => 'Text Domain',
					'Domain Path' => 'Domain Path',
				),
			'plugin' );
		}

		/**
		 * Get a particular header value.
		 *
		 * @since  1.0.0
		 *
		 * @param  string $key The value of the plugin header.
		 */
		function get_plugin_info( $key ) {
			return trim( $this->plugin_headers[ $key ] );
		}

		/**
		 * Register this widget with WordPress.
		 *
		 * @since  1.0.0
		 */
		function wds_hero_widget_widget() {
			register_widget( 'WDS_Hero_Widget_Widget' );
		}

		/**
		 * Activate the plugin.
		 *
		 * @since  1.0.0
		 */
		function _activate() {
			// Make sure any rewrite functionality has been loaded
			flush_rewrite_rules();
		}

		/**
		 * When debugging, disabled cache.
		 *
		 * @since  1.0.0
		 *
		 * @return string Timestamp when debugging, actual version when not.
		 */
		protected function script_version() {
			if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
				return time();
			} else {
				return $this->get_plugin_info( 'Version' );
			}
		}

		/**
		 * Load Plugin Language files.
		 *
		 * @since 1.1 Rewritten to just load the text domain language files.
		 */
		public function load_text_domain() {
			load_plugin_textdomain( 'wds-hero-widget', false, dirname( __FILE__ ) . '../languages/' );
		}
	} // WDS_Hero_Widget Class.

	/**
	 * Access WDS_Hero_Widget.
	 *
	 * @since  1.1
	 *
	 * @return object WDS_Hero_Widget.
	 */
	function wds_hero_widget() {
		return WDS_Hero_Widget::get_instance();
	}

	// Kick off!
	wds_hero_widget();
endif;
