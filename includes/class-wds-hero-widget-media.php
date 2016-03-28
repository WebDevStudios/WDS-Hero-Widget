<?php

if ( ! defined ( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'WDS_Hero_Widget_Media' ) ) :
	/**
	 * Adds option to use the media modal for an input.
	 *
	 * @since  1.1
	 */
	class WDS_Hero_Widget_Media {
		/**
		 * The input data.
		 *
		 * @since  1.1
		 * @var array
		 */
		private $input;

		/**
		 * The label of the input.
		 *
		 * @since  1.1
		 * @var string
		 */
		private $label;

		/**
		 * Construct.
		 *
		 * @since  1.1
		 * @param array $input The input data.
		 * @param string $label The label of the input.
		 */
		function __construct( $input, $label ) {
			$this->input = $input;
			$this->label = $label;

			require_once( trailingslashit( ABSPATH ) . 'wp-admin/includes/media.php' );
		}

		/**
		 * Add the link to the description.
		 *
		 * @since  1.1
		 * @return string The description with the link added.
		 */
		public function media_description() {
			$upload_link = esc_url( get_upload_iframe_src( 'image', md5( $this->input['description'] ) ) );

			return  sprintf( "<a href='%s' class='wds-hero-widget-media'>%s %s</a>", $upload_link, __( 'Choose', 'wds-hero-widget' ), esc_attr( $this->label ) );
		}
	} // WDS_Hero_Widget_Media
endif;
