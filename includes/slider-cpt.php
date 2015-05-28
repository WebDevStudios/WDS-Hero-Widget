<?php

class WDS_Slider_CPT {
	public $core;

	function __construct( $core ) {

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
			'name'               => _x( 'Hero Sliders', 'post type general name', $this->core->text_domain ),
			'singular_name'      => _x( 'Hero Slider', 'post type singular name', $this->core->text_domain ),
			'menu_name'          => _x( 'Hero Sliders', 'admin menu', $this->core->text_domain ),
			'name_admin_bar'     => _x( 'Hero Slider', 'add new on admin bar', $this->core->text_domain ),
			'add_new'            => _x( 'Add New', 'book', $this->core->text_domain ),
			'add_new_item'       => __( 'Add New Hero Slider', $this->core->text_domain ),
			'new_item'           => __( 'New Hero Slider', $this->core->text_domain ),
			'edit_item'          => __( 'Edit Hero Slider', $this->core->text_domain ),
			'view_item'          => __( 'View Hero Slider', $this->core->text_domain ),
			'all_items'          => __( 'All Hero Sliders', $this->core->text_domain ),
			'search_items'       => __( 'Search Hero Sliders', $this->core->text_domain ),
			'parent_item_colon'  => __( 'Parent Hero Sliders:', $this->core->text_domain ),
			'not_found'          => __( 'No Hero Slider found.', $this->core->text_domain ),
			'not_found_in_trash' => __( 'No Hero Slider found in Trash.', $this->core->text_domain )
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
		$box = new_cmb2_box( array(
			'id'              => 'wds_hero_slider_metabox',
			'title'           => __( 'Hero Slider', $this->core->text_domain ),
			'object_types'    => array( 'wds-hero-slider', ), // Post type
			'context'         => 'normal',
			'priority'        => 'high',
			'show_names'      => true,
		) );

		$box->add_field( array(
			'name'            => __( 'Images', $this->core->text_domain ),
			'id'              => 'wds_hero_slider_images',
			'type'            => 'file_list',
			'preview_size'         => array( 200, '200' ),
		) );

		$box->add_field( array(
			'name'            => __( 'Slider Speed in Seconds', $this->core->text_domain ),
			'id'              => 'wds_hero_slider_speed',
			'type'            => 'text',
			'sanitization_cb' => array( $this, 'sanitize_slider_speed' ),
			'desc'            => __( 'Each image will slide in every <code>&times</code> seconds.', $this->core->text_domain ),
		) );
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
