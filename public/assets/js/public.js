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
						return imgPagination( this, index, className);
					},
				},
				autoHeight: false,
				autoplay: {
					delay: atts.autoplay || 5000
				},
				fadeEffect: {
					crossFade: true
				}
			};

			if ( 'fade' === atts.effect ) {
				params.fadeEffect = {
					crossFade: true
				}
			}

			params = $.extend( {}, atts, params );
		}

		console.log(params);

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

