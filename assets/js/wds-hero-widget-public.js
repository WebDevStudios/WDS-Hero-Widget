( function( $ ) {
	/**
	 * Fronted JS.
	 *
	 * @return {Object}
	 */
	$.fn.WDS_Hero_Widget_Public = function() {
		var $target = this; // Store the target element in this variable (jQuery stuff).

		/**
		 * App.
		 *
		 * @since  1.1
		 * @type {Object}
		 */
		var app = {
			/**
			 * Run the Construct every time we create a new instance.
			 *
			 * @since  1.1
			 * @return {Object} This object.
			 */
			__construct: function() {
				$( document ).ready( app.sliders ); // Init the sliders on the page, if any.

				return app; // Return to jQuery for chaining.
			},

			/**
			 * Init any sliders.
			 *
			 * @since  1.1
			 * @return {object} App.
			 */
			sliders: function() {
				$( '.hero .sliders' ).each( function( i, v ) {
					var speed = $( this ).attr( 'data-slider-speed' );

					$( this ).slick( {
						slidesToShow:    1,
						slidesToScroll:  1,
						autoplay:        true,
						autoplaySpeed:   speed * 1000,
					} );
				} );

				return app;
			},
		};

		app.__construct(); // Always run these things when the Application is created.
		return app; // return the Application so we can access other properties.
	};
} ( jQuery ) );

// Kick it off.
jQuery().WDS_Hero_Widget_Public();
