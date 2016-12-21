/* global Swiper */
( function( $ ) {
	'use strict';

	$(function() {
		$( '.tm-testi-slider' ).each( function() {
			var $container = $( this ),
				atts = $container.data( 'atts' ),
				params = {
					pagination: '#tm-testi-slider-pagination-' + atts.id,
					nextButton: '#tm-testi-slider-next-' + atts.id,
					prevButton: '#tm-testi-slider-prev-' + atts.id,
					paginationClickable: true,
					autoHeight: false
				},
				swiper = null,
				key;

			if ( 'undefined' !== atts ) {

				// Parse params.
				for ( key in atts ) {
					params[ key ] = atts[ key ];
				}
			}

			swiper = new Swiper( $container, params );
		});
	});

} )( jQuery );
