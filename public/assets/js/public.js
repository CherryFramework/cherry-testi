/* global Swiper */
( function( $ ) {
	'use strict';

	$( function() {
		$( '.tm-testi-slider' ).each( initSlider );
	} );

	function initSlider() {

		var $container = $( this ),
			atts       = $container.data( 'atts' ),
			inited     = $container.data( 'init' ),
			params     = {},
			swiper     = null,
			key;

		if ( true === inited ) {
			return;
		}

		$container.data( 'init', true );

		if ( undefined !== atts ) {

			params = {
				navigation: {
					nextEl: '#tm-testi-slider-next-' + atts.id,
					prevEl: '#tm-testi-slider-prev-' + atts.id,
				},
				pagination: {
					el: '#tm-testi-slider-pagination-' + atts.id,
					clickable: true,
					renderBullet: function ( index, className ) {
						console.log(this.pagination.$el);
						return imgPagination( this, index, className);
						return '<span class="' + className + '"></span>';
					},
				},
				autoHeight: false,
			};

			// Parse params.
			for ( key in atts ) {
				params[ key ] = atts[ key ];
			}
		}

		swiper = new Swiper( $container, params );

	}

	function imgPagination( swiper, index, className ) {

		var paginInstance = $( swiper.pagination.$el[0] );

		if ( ! paginInstance ) {
			return '<span class="' + className + '"></span>';
		}

		var avatars = paginInstance.data( 'avatars' ),
			size    = paginInstance.data( 'size' ),
			current = null;

		if ( avatars ) {
			current = avatars[ index ];
			return '<span class="' + className + ' img-pagination-item" style="background-image: url(\'' + current + '\'); width:' + size + 'px; height:' + size + 'px;"></span>';
		} else {
			return '<span class="' + className + '"></span>';
		}
	}

	function initElementorPlugin() {
		elementorFrontend.hooks.addAction( 'frontend/element_ready/tm_testimonials.default', function( $scope ) {
			$scope.find( '.tm-testi-slider' ).each( initSlider );
		} );
	}

	$( window ).on( 'elementor/frontend/init', initElementorPlugin );

} )( jQuery );

