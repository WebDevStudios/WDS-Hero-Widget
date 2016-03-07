/**
 * Init Hero sliders.
 *
 * This just takes all the sliders places on the page
 * and initiates Slick JS to animate and slide them.
 *
 * @param  {Object} $ jQuery
 */
( function( $ ) {
	// Anything with data attached to it, slick it!
	$( '.hero .sliders' ).each( function( i, v ) {
		var speed = $( this ).attr( 'data-slider-speed' );

		$( this ).slick( {
			slidesToShow:    1,
			slidesToScroll:  1,
			autoplay:        true,
			autoplaySpeed:   speed * 1000,
		} );
	} );
} )( jQuery );
