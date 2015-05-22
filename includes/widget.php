<?php

// Exit if accessed directly
if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}

class WDS_Hero_Widget_Widget extends WP_Widget {

	/**
	 * Unique identifier for this widget.
	 *
	 * Will also serve as the widget class.
	 *
	 * @var string
	 */
	protected $widget_slug = 'wds-hero-widget';

	/**
	 * Widget name displayed in Widgets dashboard.
	 * Set in __construct since __() shouldn't take a variable.
	 *
	 * @var string
	 */
	protected $widget_name = '';

	/**
	 * Default widget title displayed in Widgets dashboard.
	 * Set in __construct since __() shouldn't take a variable.
	 *
	 * @var string
	 */
	protected $default_widget_title = '';

	/**
	 * Shortcode name for this widget
	 *
	 * @var string
	 */
	protected $shortcode = 'wds_hero';

	/**
	 * All the text inputs used in the Widget
	 *
	 * @var array
	 */
	protected $text_inputs;

	/**
	 * All the types of Hero's
	 *
	 * @var array
	 */
	protected $types;

	/**
	 * The plugin.
	 *
	 * @var object
	 */
	public $plugin;

	/**
	 * Contruct widget.
	 */
	public function __construct( $widget_name = false ) {

		// The plugin.
		$this->plugin = wds_hero_widget();

		// For backwards compatibility.
		if ( ! $widget_name ) {
			$this->widget_name          = esc_html__( 'Hero', 'wds-hero-widget' );
			$this->default_widget_title = esc_html__( 'Hero', 'wds-hero-widget' );
		} else {
			$this->widget_name          = esc_html__( $widget_name, 'wds-hero-widget' );
			$this->default_widget_title = esc_html__( $widget_name, 'wds-hero-widget' );
		}

		parent::__construct(
			$this->widget_slug,
			$this->widget_name,
			array(
				'classname'   => $this->widget_slug,
				'description' => esc_html__( 'Hero widget.', 'wds-hero-widget' ),
			)
		);

		add_action( 'save_post',    array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
		add_shortcode( $this->shortcode, array( $this, 'get_widget' ) );

		// Types of Heros
		$this->types = array(
			'primary',
			'secondary',
			'secondary-paralaxed'
		);

		// Text inputs for Hero Widgets.
		$this->text_inputs = array(
			array(
				'label' => __( 'Classes', 'wds-hero-widget' ),
				'slug'  => 'class',
				'description' => __( 'Any additional classes you would like to add to this Hero.', 'wds-hero-widget' ),
				'placeholder' => 'my-class my-class-2',
				'default' => '',
			),
			array(
				'label' => __( 'Background Image URL', 'wds-hero-widget' ),
				'slug'  => 'image',
				'description' => false,
				'placeholder' => 'http://example.com/background-image.png',
				'default' => '',
			),
			array(
				'label' => __( 'Background Video URL', 'wds-hero-widget' ),
				'slug'  => 'video',
				'description' => __( 'Must be a .mp4 video.', 'wds-hero-widget' ),
				'placeholder' => 'http://example.com/background-video.mp4',
				'default' => '',
			),
			array(
				'label' => __( 'Primary Heading', 'wds-hero-widget' ),
				'slug'  => 'heading',
				'description' => false,
				'placeholder' => __( 'My Heading', 'wds-hero-widget' ),
				'default' => '',
			),
			array(
				'label' => __( 'Sub Heading', 'wds-hero-widget' ),
				'slug'  => 'sub_heading',
				'description' => __( 'Displayed directly below Primary Heading.', 'wds-hero-widget' ),
				'placeholder' => __( 'Sub Heading', 'wds-hero-widget' ),
				'default' => '',
			),
			array(
				'label' => __( 'Button Text', 'wds-hero-widget' ),
				'slug'  => 'button_text',
				'description' => __( 'Displayed below headings.', 'wds-hero-widget' ),
				'placeholder' => __( 'Button Text', 'wds-hero-widget' ),
				'default' => '',
			),
			array(
				'label' => __( 'Button URL', 'wds-hero-widget' ),
				'slug'  => 'button_link',
				'description' => __( 'What URL should the button open?', 'wds-hero-widget' ),
				'placeholder' => 'http://example.com/another/page',
				'default' => '',
			),
			array(
				'label' => __( 'Overlay Transparency', 'wds-hero-widget' ),
				'slug'  => 'overlay',
				'description' => __( 'Will automatically calculate CSS safe value.', 'wds-hero-widget' ),
				'placeholder' => '0',
				'default' => '',
			),
			array(
				'label' => __( 'Overlay Color', 'wds-hero-widget' ),
				'slug'  => 'overlay_color',
				'description' => __( 'The color of the overlay. Include # or use colors like "red"', 'wds-hero-widget' ),
				'placeholder' => '#000',
				'default' => '#000',
			),
		);
	}

	/**
	 * Delete this widget's cache.
	 *
	 * Note: Could also delete any transients
	 * delete_transient( 'some-transient-generated-by-this-widget' );
	 */
	public function flush_widget_cache() {
		wp_cache_delete( $this->widget_slug, 'widget' );
	}

	/**
	 * Front-end display of widget.
	 *
	 * @param  array  $args      The widget arguments set up when a sidebar is registered.
	 * @param  array  $instance  The widget settings as set by user.
	 */
	public function widget( $args, $instance ) {

		$args = array(
			'before_widget' => $args['before_widget'],
			'after_widget'  => $args['after_widget'],
			'before_title'  => $args['before_title'],
			'after_title'   => $args['after_title'],
		);

		$inputs = $this->get_all_the_input_slugs();

		foreach ( $inputs as $input_slug ) {
			if ( isset( $instance[ $input_slug ] ) ) {
				$args[ $input_slug ] = $instance[ $input_slug ];
			}
		}

		echo $this->get_widget( $args );
	}

	/**
	 * Return the widget/shortcode output
	 *
	 * @param  array  $atts Array of widget/shortcode attributes/args
	 * @return string       Widget output
	 */
	public function get_widget( $atts ) {
		$widget = '';

		// Before widget hook
		$widget .= $atts['before_widget'];

			$widget .= $this->plugin->wds_hero( array(
				'type'                  => ( isset( $atts['type'] ) && ! empty( $atts['type'] ) ) ? $atts['type'] : 'primary',
				'class'                 => ( isset( $atts['class'] ) && ! empty( $atts['class'] ) ) ? $atts['class'] : false,
				'image'                 => ( isset( $atts['image'] ) && ! empty( $atts['image'] ) ) ? $atts['image'] : false,
				'video'                 => ( isset( $atts['video'] ) && ! empty( $atts['video'] ) ) ? $atts['video'] : false,

				'echo'                  => false,

				'heading'               => ( isset( $atts['heading'] ) && ! empty( $atts['heading'] ) ) ? $atts['heading'] : false,
				'sub_heading'           => ( isset( $atts['sub_heading'] ) && ! empty( $atts['sub_heading'] ) ) ? $atts['sub_heading'] : false,
				'button_text'           => ( isset( $atts['button_text'] ) && ! empty( $atts['button_text'] ) ) ? $atts['button_text'] : false,
				'button_link'           => ( isset( $atts['button_link'] ) && ! empty( $atts['button_link'] ) ) ? $atts['button_link'] : false,

				'custom_content_action' => false,

				'overlay'               => ( isset( $atts['overlay'] ) && ! empty( $atts['overlay'] ) ) ? $atts['overlay'] : false,
				'overlay_color'         => ( isset( $atts['overlay_color'] ) && ! empty( $atts['overlay_color'] ) ) ? $atts['overlay_color'] : '#000',
			) );

		// After widget hook
		$widget .= $atts['after_widget'];

		return $widget;
	}

	/**
	 * Condenses all the inputs into a simple array of slugs.
	 *
	 * @return array Simplified inputs
	 */
	function get_all_the_input_slugs() {
		$inputs[] = 'type'; // Type inputs (not a basic text input)
		foreach ( $this->text_inputs as $input ) {
			$inputs[] = $input['slug'];
		}
		return $inputs;
	}

	/**
	 * Update form values as they are saved.
	 *
	 * @param  array  $new_instance  New settings for this instance as input by the user.
	 * @param  array  $old_instance  Old settings for this instance.
	 * @return array  Settings to save or bool false to cancel saving.
	 */
	public function update( $new_instance, $old_instance ) {

		// Previously saved values
		$instance = $old_instance;

		// Get all the inputs
		$inputs = $this->get_all_the_input_slugs();

		// Sanitizations
		add_filter( 'wds_widget_update_video',           array( $this, 'sanitize_url' ) );
		add_filter( 'wds_widget_update_button_link',     array( $this, 'sanitize_url' ) );
		add_filter( 'wds_widget_update_image',           array( $this, 'sanitize_url' ) );
		add_filter( 'wds_widget_update_overlay',         array( $this, 'sanitize_overlay' ) );

		// Save the inputs and add filter for sanitizing.
		foreach ( $inputs as $slug ) {
			$instance[ $slug ] = apply_filters( "wds_widget_update_$slug", $new_instance[ $slug ] );
		}

		// Flush cache
		$this->flush_widget_cache();

		return $instance;
	}

	/**
	 * Makes sure we set a float for transparency.
	 *
	 * @param  string $overlay The string set by the user
	 *
	 * @return float           The decimal value it should use instead.
	 */
	function sanitize_overlay( $overlay ) {
		if ( absint( $overlay ) ) {
			return $overlay / 100;
		} else if ( is_float( $overlay ) ) {
			return $overlay;
		} else {
			return (float) $overlay;
		}
	}

	/**
	 * Make sure we're storing a sanitized URL in the DB.
	 *
	 * @param  string $url The URL set by the user.
	 *
	 * @return string      Sanitized URL going to the DB.
	 */
	function sanitize_url( $url ) {
		return esc_url( $url );
	}

	/**
	 * Back-end widget form with defaults.
	 *
	 * @param  array  $instance  Current settings.
	 */
	public function form( $instance ) {

		// Condense Meta & Defaults
		$widget_meta = array();
		foreach ( $this->text_inputs as $input ) {
			$widget_meta[ $input['slug'] ] = ( isset( $input[ 'default' ] ) ? $input[ 'default' ] : '' );
		}
		$instance = wp_parse_args( (array) $instance, array_merge( $widget_meta, array(

			// Type isn't added via the basic text inputs because it is a <select>.
			'type'  => 'primary', // Default hero type.
		) ) );

		?>

			<!-- Type input (select) -->
			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>">
					<?php esc_html_e( 'Hero Type', 'wds-hero-widget' ); ?>
				</label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'type' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'type' ) ); ?>">
					<?php foreach( $this->types as $type ) : ?>
						<option value="<?php echo $type; ?>" <?php echo ( $type == $instance[ 'type' ] ) ? 'selected="selected"' : ''; ?>><?php echo ucwords( str_replace( '-', ' ', $type ) ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>

			<!-- Text inputs -->
			<?php foreach( $this->text_inputs as $input ): ?>

				<p>
					<label for="<?php echo esc_attr( $this->get_field_id( $input['slug'] ) ); ?>">
						<?php echo esc_html( $input['label'] ) . ': '; ?>
					</label>
					<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $input['slug'] ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $input['slug'] ) ); ?>" value="<?php echo esc_attr( $instance[ $input['slug'] ] ); ?>" placeholder="<?php echo esc_attr( $input['placeholder'] ); ?>" /><br />
					<?php if ( $input['description'] ): ?>
						<small class="description"><?php echo esc_html( $input['description' ] ); ?></small>
					<?php endif; ?>
				</p>

			<?php endforeach; ?>

		<?php
	}
}
