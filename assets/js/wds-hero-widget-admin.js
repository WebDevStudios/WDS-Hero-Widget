( function( $ ) {
	/**
	 * WP Admin JS.
	 *
	 * @since  1.1
	 * @return {Object}
	 */
	$.fn.WDS_Hero_Widget_Admin = function() {
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
				$( document ).ready( app.media.__construct );

				return app; // Return to jQuery for chaining.
			},

			/**
			 * Handle Media Uploader.
			 *
			 * Will setup and handle links that should populate
			 * via the media uploader.
			 *
			 * @since  1.1
			 * @type {Object}
			 */
			media: {
				/**
				 * WP frame object.
				 *
				 * @since  1.1
				 * @type {Boolean}
				 */
				frame: false,

				/**
				 * Widget UI elements
				 *
				 * @since  1.1
				 * @type {Object}
				 */
				ui: {
					$links: $( '.wds-hero-widget-media' ),
				},

				/**
				 * The current Link.
				 *
				 * @since  1.1
				 * @type {Boolean}
				 */
				$currentLink: false,

				/**
				 * Construct.
				 *
				 * @since  1.1
				 * @return {Object} App
				 */
				__construct: function() {
					app.media.ui.$links.on( 'click', app.media.modal );

					return app;
				},

				/**
				 * Open the Media Uploader modal.
				 *
				 * @since  1.1
				 * @param  {Object} e Event
				 * @return {Object}   App
				 */
				modal: function( e ) {
					e.preventDefault();

					// The current link being processed.
					app.media.$currentLink = $( this );

					// Just open the current frame if we created on.
					if ( app.media.frame ) {
						app.media.frame.open();
						return;
					}

					// Open the modal.
					app.media.frame = wp.media( {
						title: WDS_Hero_Widget_l10n.media.title,

						button: {
							text: WDS_Hero_Widget_l10n.media.button,
						},

						multiple: false,
					} ).open().on( 'select', app.media.select );

					return app;
				},

				/**
				 * Select a file.
				 *
				 * @since  1.1
				 * @return {Object) App.
				 */
				select: function() {
					// Get media attachment details from the frame state
					var attachment = app.media.frame.state().get( 'selection' ).first().toJSON();

					// Where do we put it?
					var inputID = $( app.media.$currentLink ).parent().attr( 'data-input-id' );

					// Put it... :D
					$( '#' + inputID ).val( attachment.url );

					return app;
				},
			},
		};

		app.__construct(); // Always run these things when the Application is created.
		return app; // return the Application so we can access other properties.
	};
} ( jQuery ) );

// Kick it off.
jQuery().WDS_Hero_Widget_Admin();
