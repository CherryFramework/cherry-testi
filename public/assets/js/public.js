/* global Swiper */
( function( $ ) {
	'use strict';

	$( function() {
		$( '.tm-testi-slider' ).each( initSlider );
	} );

	function initSlider() {

		var $container = $( this ),
			atts       = $container.data( 'atts' ),
			params     = {},
			swiper     = null,
			key;

		if ( undefined !== atts ) {

			params = {
				pagination: '#tm-testi-slider-pagination-' + atts.id,
				nextButton: '#tm-testi-slider-next-' + atts.id,
				prevButton: '#tm-testi-slider-prev-' + atts.id,
				paginationClickable: true,
				autoHeight: false,
				paginationBulletRender: imgPagination
			};

			// Parse params.
			for ( key in atts ) {
				params[ key ] = atts[ key ];
			}
		}

		swiper = new Swiper( $container, params );

	}

	function imgPagination( swiper, index, className ) {
		var avatars = swiper.paginationContainer.data( 'avatars' ),
			size    = swiper.paginationContainer.data( 'size' ),
			current = null;

		if ( avatars ) {
			current = avatars[ index ];
			return '<span class="' + className + ' img-pagination-item" style="background-image: url(\'' + current + '\'); width:' + size + 'px; height:' + size + 'px;"></span>';
		} else {
			return '<span class="' + className + '"></span>';
		}
	}

	function initElementorPlugin() {
		console.log( 123 );
		elementorFrontend.hooks.addAction( 'frontend/element_ready/tm_testimonials.default', function( $scope ) {
			console.log( $scope );
			$scope.find( '.tm-testi-slider' ).each( initSlider );
		} );

	}

	$( window ).on( 'elementor/frontend/init', initElementorPlugin );

} )( jQuery );
