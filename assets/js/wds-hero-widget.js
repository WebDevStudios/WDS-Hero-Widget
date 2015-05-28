( function( $ ) {

	// Anything with data attached to it, slick it!
	$( '.hero .sliders' ).each( function( i, v ) {
		var speed = $( this ).attr( 'data-slider-speed' );
		console.log( speed );
		$( this ).slick( {
			slidesToShow: 1,
			slidesToScroll: 1,
			autoplay: true,
			autoplaySpeed: speed * 1000,
		} );
	} );

} )( jQuery );
