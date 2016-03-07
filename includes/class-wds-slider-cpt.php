<?php

// Exit if accessed directly
if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WDS_Slider_CPT' ) ) :
	/**
	 * Creates Slider Custom Post Types.
	 *
	 * @since  1.0
	 * @package  wds-hero-widget
	 */
	class WDS_Slider_CPT {
		/**
		 * Core access.
		 *
		 * This is where the base plugin is stored.
		 *
		 * @var object
		 */
		public $core;

		function __construct( $core ) {
			if ( ! apply_filters( 'disable_wds_slider_cpt', false ) ) {
				return; // Allows developers to disable Slider CPT.
			}

			// Assign core to this instance.
			$this->core = $core;

			// Add Custom Meta Boxes
			add_action( 'cmb2_init', array( $this, 'images_cmb2_init' ) );

			// Make our screen nice.
			add_filter( 'gettext', array( $this, 'screen_text' ), 20, 3 );
			add_action( 'admin_head-post.php', array( $this, 'hide_visibility_screen' ) );
			add_action( 'admin_head-post-new.php', array( $this, 'hide_visibility_screen' ) );
			add_filter( 'post_row_actions', array( $this, 'remove_quick_edit' ), 10, 2 );

			// Create custom post types.
			add_action( 'init', array( $this, 'register_cpt' ) );
		}

		/**
		 * Registers Hero Slider CPT.
		 *
		 * @return void
		 */
		public function register_cpt() {
			$labels = array(
				'name'               => _x( 'Hero Sliders', 'post type general name', 'wds-hero-widget' ),
				'singular_name'      => _x( 'Hero Slider', 'post type singular name', 'wds-hero-widget' ),
				'menu_name'          => _x( 'Hero Sliders', 'admin menu', 'wds-hero-widget' ),
				'name_admin_bar'     => _x( 'Hero Slider', 'add new on admin bar', 'wds-hero-widget' ),
				'add_new'            => _x( 'Add New', 'book', 'wds-hero-widget' ),
				'add_new_item'       => __( 'Add New Hero Slider', 'wds-hero-widget' ),
				'new_item'           => __( 'New Hero Slider', 'wds-hero-widget' ),
				'edit_item'          => __( 'Edit Hero Slider', 'wds-hero-widget' ),
				'view_item'          => __( 'View Hero Slider', 'wds-hero-widget' ),
				'all_items'          => __( 'All Hero Sliders', 'wds-hero-widget' ),
				'search_items'       => __( 'Search Hero Sliders', 'wds-hero-widget' ),
				'parent_item_colon'  => __( 'Parent Hero Sliders:', 'wds-hero-widget' ),
				'not_found'          => __( 'No Hero Slider found.', 'wds-hero-widget' ),
				'not_found_in_trash' => __( 'No Hero Slider found in Trash.', 'wds-hero-widget' )
			);

			$args = array(
				'labels'    => $labels,
				'public'    => false,
				'show_ui'   => true,
				'supports'  => array( 'title' ),
				'rewrite'   => false,
				'menu_icon' => 'dashicons-images-alt2',
			);

			register_post_type( 'wds-hero-slider', $args );
		}

		/**
		 * Add a way to add multiple images (or images) to CPT.
		 *
		 * @return void
		 */
		public function images_cmb2_init() {
			$slider_id = ( isset( $_GET['post'] ) ) ? $_GET['post'] : '0';

			$box = new_cmb2_box( array(
				'id'              => 'wds_hero_slider_metabox',
				'title'           => __( 'Hero Slider', 'wds-hero-widget' ),
				'object_types'    => array( 'wds-hero-slider', ), // Post type
				'context'         => 'normal',
				'priority'        => 'high',
				'show_names'      => true,
			) );

			$box->add_field( array(
				'name'            => __( 'Images', 'wds-hero-widget' ),
				'id'              => 'wds_hero_slider_images',
				'type'            => 'file_list',
				'desc'            => __( 'You do not need to create a slider if you intend to use a single image in your hero.', 'wds-hero-widget' ),
				'preview_size'         => array( 200, '200' ),
			) );

			$box->add_field( array(
				'name'            => __( 'Slider speed in seconds', 'wds-hero-widget' ),
				'id'              => 'wds_hero_slider_speed',
				'type'            => 'text',
				'sanitization_cb' => array( $this, 'sanitize_slider_speed' ),
				'desc'            => __( 'Each image will slide in every <code>&times</code> seconds.', 'wds-hero-widget' ),
				'after_row'     => $this->slider_instructions( $slider_id ),
			) );
		}

		/**
		 * Output's some basic instructions on how to get the slider on a page.
		 *
		 * @return string HTML passed back to CMB2
		 */
		function slider_instructions( $slider_id ) {
			if ( ! isset( $_GET['post'] ) ) {
				return; // Only show on saved posts.
			}

			ob_start();
			?>

			<div style="clear: both;">
				<p><?php echo sprintf( __( 'Add this as a %s or using a shortcode.', 'wds-hero-widget' ), '<a href="widgets.php">Widget</a>' ); ?></p>

				<h5><?php _e( 'Primary Hero', 'wds-hero-widget' ); ?></h5>
				<pre style="padding-left: 10px; white-space: pre-wrap;">[hero slider_id="<?php echo absint( $slider_id ); ?>" type="primary" class="my-class" heading="My Heading" sub_heading="My Sub Heading" button_text="My Button" button_link="#" image="my-image.png"]</pre>

				<h5><?php _e( 'Secondary Hero', 'wds-hero-widget' ); ?></h5>
				<pre style="padding-left: 10px; white-space: pre-wrap;">[hero slider_id="<?php echo absint( $slider_id ); ?>" type="secondary" class="my-class" video="" heading="My Heading" sub_heading="My Sub Heading" button_text="My Button" button_link="#" image="my-image.png"]</pre>

				<h5><?php _e( 'Parallaxed Horizontal Hero', 'wds-hero-widget' ); ?></h5>
				<pre style="padding-left: 10px; white-space: pre-wrap;">[hero slider_id="<?php echo absint( $slider_id ); ?>" type="secondary-paralaxed" class="my-class" heading="My Heading" sub_heading="My Sub Heading" button_text="My Button" button_link="#" image="my-image.png"]</pre>

				<p><em><?php _e( 'Note that the paralaxed version does not work with video. The layout of this option puts the button on the right, and the Heading on the left w/out the sub-heading.', 'wds-hero-widget' ); ?></em></p>

				<h5><?php _e( 'Adding a Video', 'wds-hero-widget' ); ?></h5>
				<pre style="padding-left: 10px; white-space: pre-wrap;">[hero slider_id="<?php echo absint( $slider_id ); ?>" type="primary" class="my-class" video="http://example.com/wp-content/uploads/2015/05/13/my-video.mp4" heading="My Heading" sub_heading="My Sub Heading" button_text="My Button" button_link="#"]</pre>

				<p><em><?php _e( 'The video is added using HTML5. Using a YouTube video is not supported.', 'wds-hero-widget' ); ?></em></p>

				<h5><?php _e( 'Adding an Overlay', 'wds-hero-widget' ); ?></h5>
				<pre style="padding-left: 10px; white-space: pre-wrap;">[hero slider_id="<?php echo absint( $slider_id ); ?>" type="primary" class="my-class" heading="My Heading" sub_heading="My Sub Heading" button_text="My Button" button_link="#" image="my-image.png" overlay="0.2" overlay_color="#000"]</pre>
			</div>

			<?php
			$html = ob_get_contents();
			ob_end_clean();
			return $html;
		}

		function sanitize_slider_speed( $value ) {
			return absint( $value );
		}

		/**
		 * Hides the publishing options since they aren't relevant here.
		 *
		 * @return void
		 */
		public function hide_visibility_screen() {
				if( 'wds-hero-slider' == get_post_type() ){
					echo '
						<!-- Hides the publishing options -->
						<style type="text/css">
							#misc-publishing-actions,
							#minor-publishing-actions {
								display:none;
							}
						</style>
					';
				}
		}

		/**
		 * Removes the Quick Edit from the bulk list options.
		 *
		 * @param  array $actions Default Actions
		 *
		 * @return array          Actions with any inline actions removed.
		 */
		public function remove_quick_edit( $actions ) {

			global $current_screen;

			if ( ! $current_screen ) {
				return;
			}

			// Only on this CPT
			if( $current_screen->post_type != 'wds-hero-slider' ) {
				return $actions;
			}

			// Remove any inline actions (Quick Edit).
			unset( $actions['inline hide-if-no-js'] );

			return $actions;
		}

		/**
		 * Changes default text to text that makes more sense for this plugin.
		 *
		 * @param  string $translated_text The un-translated text.
		 * @param  string $text            The original translated text.
		 * @param  string $domain          The text domain.
		 *
		 * @return string                  Modified text.
		 */
		public function screen_text( $translated_text, $text, $domain ) {
			if ( 'wds-hero-slider' == get_post_type() ) {
				switch ( $translated_text ) {
					case 'Publish' :
						$translated_text = __( 'Save', $domain );
						break;
					case 'Published' :
						$translated_text = __( 'Saved', $domain );
						break;
				}
			}
			return $translated_text;
		}
	}
endif;
