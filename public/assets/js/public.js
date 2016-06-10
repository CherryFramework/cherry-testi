(function( $ ) {
	"use strict";

	$(function() {
		$( '.tm-testi-shortcode--slider' ).each( function() {
			var $container = $( this ),
				atts = $container.data( 'atts' ),
				params = {
					pagination: '.tm-testi__pagination',
					nextButton: '.tm-testi__button-next',
					prevButton: '.tm-testi__button-prev',
					paginationClickable: true,
					autoHeight: false,
					onInit: function(){
						$( '.tm-testi__button-next' ).css({ 'display': 'block' });
						$( '.tm-testi__button-prev' ).css({ 'display': 'block' });
					}
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

})( jQuery );