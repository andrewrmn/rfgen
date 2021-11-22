/**
 * SiteOrigin specific animation code
 * Copyright SiteOrigin 2016
 */
window.SiteOriginPremium = window.SiteOriginPremium || {};

SiteOriginPremium.setupAnimations = function ( $ ) {
	
	var animationEnd = 'webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend';
	
	$( '[data-so-animation]' ).each( function () {
		var $$ = $( this );
		var animation = $$.data( 'so-animation' );
		
		// Set the animation duration
		var duration = parseFloat( animation.duration );
		if ( !isNaN( duration ) ) {
			$$.css( {
				'-webkit-animation-duration': duration + 's',
				'animation-duration': duration + 's',
			} );
		}
		
		var animateIn = function ( repeat ) {
			if ( animation.disableAnimationMobile && window.matchMedia( '(max-width: ' + animation.breakpoint + ')' ).matches ) {
				if ( animation.hide ) {
					$$.css( 'opacity', 1 );
				}
				$$.addClass( 'animated' );
				return;
			}

			var doAnimation = function () {
				if ( animation.hide ) {
					$$.css( 'opacity', 1 );
				}
				
				if ( repeat ) {
					$$
					.removeClass( 'animated ' + animation.animation )
					.addClass( 'animated ' + animation.animation );
				} else {
					$$.addClass( 'animated ' + animation.animation );
				}
				$$.one( animationEnd, function () {
					$$.removeClass( 'animated ' + animation.animation );
					if ( animation.finalState === 'hidden' ) {
						$$.css( 'opacity', 0 );
					} else if ( animation.finalState === 'removed' ) {
						$$.css( 'display', 'none' );
					}
				} )
			};
			
			var delay = parseFloat( animation.delay );
			if ( !isNaN( delay ) && delay > 0 ) {
				setTimeout( function () {
					doAnimation();
				}, delay * 1000 );
			} else {
				doAnimation();
			}
		};
		
		// Using 0 for debounce causes it to default to 100ms. :/
		var debounce = animation.debounce * 1000 || 1;
		// Only perform animation once for now. Will add option to repeat later.
		if ( animation.animation ) {
			switch ( animation.event ) {
				case 'enter':
					// We need a timeout to make sure the page is setup properly
					setTimeout( function () {
						var onScreen = new OnScreen( {
							tolerance: parseInt( animation.offset ),
							debounce: debounce,
						} );
						onScreen.on( 'enter', animation.selector, function () {
							animateIn( false );
							onScreen.off( 'enter', animation.selector );
						} );
					}, 150 );
					break;
				
				case 'in':
					setTimeout( function () {
						var onScreen = new OnScreen( {
							tolerance: parseInt( animation.offset ) + $$.outerHeight(),
							debounce: debounce,
						} );
						onScreen.on( 'enter', animation.selector, function () {
							animateIn( false );
							onScreen.off( 'enter', animation.selector );
						} );
					}, 150 );
					break;
				
				case 'hover':
					
					if ( animation.repeat ) {
						$$.on( 'mouseenter', function () {
							animateIn( true );
							$$.addClass( 'infinite' )
						} )
						.on( 'mouseleave', function () {
							$$.removeClass( 'infinite' )
						} );
					} else {
						$$.on( 'mouseenter', function () {
							animateIn( true );
						} );
					}
					break;
				
				case 'slide_display':
					$$.closest( '.sow-slider-image' ).on( 'sowSlideCycleAfter', function ( e ) {
						animateIn( true );
					} );

					// Don't hide animation if slide has slide out animation
					if ( animation.hide && ! animation.animation_type_slide_out ) {
						$$.closest( '.sow-slider-image' ).on( 'sowSlideCycleBefore', function ( e ) {
							$$.css( 'opacity', 0 );
						} );
					}

					setTimeout( function () {
						animateIn( true );
					}, 100 );
					break;
				
				case 'load':
					animateIn( false );
					break;
			}
		}

		if ( animation.animation_type_slide_out ) {
			$$.closest( '.sow-slider-images' ).on( 'cycle-before', function ( e ) {
				if ( animation.animation_type_slide_out ) {
					if ( $$.closest( '.sow-slider-image ' ).hasClass( 'cycle-slide-active' ) ) {
					$$
						.addClass( 'animated ' + animation.animation_type_slide_out )
						.one( animationEnd, function () {
							$$.removeClass( 'animated ' + animation.animation_type_slide_out );
						} )
					}
				}

				if ( animation.animation && animation.hide ) {
					$$.css( 'opacity', 0 );
				}
			} );

			if ( animation.animation && animation.hide ) {
				$$.closest( '.sow-slider-images' ).one( 'cycle-after', function ( e ) {
					$$.css( 'opacity', 1 );
				} );
			}
		}
	} );
};

jQuery( function ( $ ) {
	SiteOriginPremium.setupAnimations( $ );
	
	if ( window.sowb ) {
		$( window.sowb ).on( 'setup_widgets', function ( event, data ) {
			if ( data && data.preview ) {
				SiteOriginPremium.setupAnimations( $ );
			}
		} );
	}
} );

