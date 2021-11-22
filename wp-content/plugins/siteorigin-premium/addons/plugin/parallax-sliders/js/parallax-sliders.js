( function( $ ) {
	$( document ).ready( function() {
		if (
			typeof parallaxStyles != 'undefined' &&
			(
				! parallaxStyles['disable-parallax-mobile'] ||
				! window.matchMedia( '(max-width: ' + parallaxStyles['mobile-breakpoint'] + ')' ).matches
			)
		) {
			new simpleParallax( document.querySelectorAll( '[data-siteorigin-parallax], .sow-slider-image-parallax .sow-slider-background-image' ), {
				delay: parallaxStyles['delay'],
				scale: parallaxStyles['scale'] < 1.1 ? 1.1 : parallaxStyles['scale'],
			} );
		}
	} );
} )( jQuery );
