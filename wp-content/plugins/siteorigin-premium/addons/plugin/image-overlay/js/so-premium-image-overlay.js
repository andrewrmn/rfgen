/* globals jQuery, SiteOriginPremium */

window.SiteOriginPremium = window.SiteOriginPremium || {};

SiteOriginPremium.ImageOverlay = function ( image, options, index ) {
	var $ = jQuery;

	this.image = image;
	this.options = options;

	if ( typeof OnScreen == 'function' ) {
		this.onScreen = new OnScreen( {
			debounce: 0,
		} );
	}

	this.init = function () {
		if ( ! this.imageContainer ) {
			this.imageContainer = $( image ).parent().closest( 'div' );
			this.createChildren();
			this.addEventListeners();
		}

		setTimeout( this.layoutChildren.bind( this ), 150 );
	};

	this.createChildren = function () {
		this.overlayContainer = $('<div class="so-premium-image-overlay-container"></div>');
		this.overlay = $('<div class="so-premium-image-overlay"></div>');

		this.overlayBackground = $('<div class="so-premium-image-overlay-background"></div>');
		this.overlayBackground.css({
			backgroundColor: options.overlay_color,
			opacity: options.overlay_opacity,
		});

		this.overlay.append(this.overlayBackground);


		if ( typeof this.options.overlay_text == 'undefined' ) {
			var overlay_text = this.image.title;
		} else if ( typeof this.options.overlay_text == "object" ) {
			// Check if the current image has unique overlay text
			var overlay_text = typeof this.options.overlay_text[ index ] == 'undefined' ? this.image.title : this.options.overlay_text[ index ];
		} else {
			var overlay_text = this.options.overlay_text;
		}

		this.overlayText = $('<div class="so-premium-image-overlay-text">' + overlay_text + '</div>');
		// This prevents the default browser tooltip from being displayed without removing the title attribute.
		$(this.image).css('pointerEvents', 'none');
		var overlayTextCss = {
			margin: '0',
			padding: this.options.text_padding,
		};

		if (options.hasOwnProperty('font') && options.font) {
			overlayTextCss.fontFamily = options.font.family;
			if (options.font.hasOwnProperty('weight')) {
				overlayTextCss.fontWeight = options.font.weight;
			}
			if (options.font.hasOwnProperty('url')) {
				$('head').append('<link rel="stylesheet" media="all" href="' + options.font.url + '"/>');
			}
		}

		if (options.hasOwnProperty('text_size')) {
			overlayTextCss.fontSize = options.text_size;
		}

		if (options.hasOwnProperty('text_color')) {
			overlayTextCss.color = options.text_color;
		}

		overlayTextCss.textAlign = options.text_align;

		this.overlayText.css(overlayTextCss);

		this.overlay.append(this.overlayText);

		this.overlay.css('opacity', 0);
		this.overlayContainer.append(this.overlay);

		if ( options.widget_id ) {
			this.overlayContainer.addClass( 'so-premium-' + options.widget_id );
		}

		this.overlayContainer.css({
			overflow: 'hidden',
		});

		if ( this.imageContainer.find( '> a' ).length ) {
			this.imageContainer.find( '> a' ).append( this.overlayContainer );
		} else {
			this.imageContainer.append( this.overlayContainer );
		}
	};

	this.layoutChildren = function () {
		var $image = $(this.image);
		var imgLayout = this.imageContainer.position();
		imgLayout.width = Math.min(this.imageContainer.width(), $image.width());
		imgLayout.height = Math.min(this.imageContainer.height(), $image.height());
		var overlayContainerCss = {
			top: Math.max(0, $image.position().top) + 'px',
			left: Math.max(0, $image.position().left) + 'px',
			width: imgLayout.width + 'px',
			height: imgLayout.height + 'px',
			maxWidth: this.imageContainer.css('maxWidth'),
			maxHeight: this.imageContainer.css('maxHeight'),
		};

		this.overlayContainer.css(overlayContainerCss);
		this.isDesktop = window.matchMedia( '(min-width: ' + this.options.responsive_breakpoint + ')' ).matches;

		var overlayCss = {
			top: 0,
			left: 0,
			width: imgLayout.width,
			height: imgLayout.height,
			maxWidth: $image.css('maxWidth'),
			maxHeight: $image.css('maxHeight'),
		};

		var overlaySize = this.options.overlay_size;
		switch (this.options.overlay_position) {
			case 'top':
				overlayCss.height = imgLayout.height * overlaySize;
				break;
			case 'right':
				overlayCss.width = imgLayout.width * overlaySize;
				overlayCss.left = imgLayout.width - overlayCss.width;
				break;
			case 'bottom':
				overlayCss.height = imgLayout.height * overlaySize;
				overlayCss.top = imgLayout.height - overlayCss.height;
				break;
			case 'left':
				overlayCss.width = imgLayout.width * overlaySize;
				break;
		}

		// Give overlayCss units of measurements.
		// This can't be done earlier due to the numbers being used in calculations.
		overlayCss.top += 'px';
		overlayCss.width += 'px';
		overlayCss.height += 'px';
		overlayCss.left += 'px';
		this.overlay.css(overlayCss);

		var overlayHeight = this.overlay.outerHeight();
		var textHeight = this.overlayText.outerHeight();
		var overlayTextCss = {};

		switch (this.options.text_position) {
			case 'top':
				overlayTextCss.top = 0;
				break;
			case 'middle':
				overlayTextCss.top = (overlayHeight - textHeight) * 0.5 + 'px';
				break;
			case 'bottom':
				overlayTextCss.top = overlayHeight - textHeight + 'px';
				break;
		}

		this.overlayText.css(overlayTextCss);

		// If the current device is always set to show, and not animate onscren, always display it.
		if ( 
			! this.options.overlay_animation_onscreen &&
			(
				( this.isDesktop && this.options.desktop_show_trigger === 'always' ) ||
				( ! this.isDesktop && this.options.touch_show_trigger === 'always' )
			)
		){
			this.overlay.css('opacity', 1);
			this.overlayText.css( 'opacity', 1 );
		} else {
			if ( this.options.overlay_animation && this.options.overlay_animation !== 'none' ) {
				this.animationTimeline = anime.timeline( { autoplay: false } );
				this.animationTimeline.add(
					this.getAnimationOptions(
						this.overlay.get(0),
						this.options.overlay_animation,
						{position: this.options.overlay_position}
					)
				);
			}

			// Handle text animations on desktop.
			if ( ! this.isDesktop ) {
				if ( this.options.text_animation && this.options.text_animation !== 'none' ) {
					if ( ! this.animationTimeline ) {
						this.animationTimeline = anime.timeline( { autoplay: false } );
					}
					this.animationTimeline.add(
						this.getAnimationOptions(
							this.overlayText.get(0),
							this.options.text_animation
						)
					);
				}
			} else {
				this.overlayText.css( 'opacity', 1 );
			}

			if (
				this.options.overlay_animation_onscreen &&
				(
					( this.isDesktop && this.options.desktop_show_trigger === 'always' ) ||
					( ! this.isDesktop && this.options.touch_show_trigger === 'always' )
				)
			) {
				setTimeout( function () {
					this.onScreen.on( 'enter', '.so-premium-' + options.widget_id, function () {
						this.toggleOverlay();
						this.onScreen.off( 'enter', '.so-premium-' + options.widget_id );
					}.bind( this ) );
				}.bind( this ), 150 );
			}
		}

		// Handle hover/touch overlay display.
		if ( this.isDesktop ) {
			if ( this.options.desktop_show_trigger === 'hover' ) {
				this.imageContainer.on( 'mouseenter', function () {
					if ( this.isDesktop ) {
						this.showOverlay();
					}
				}.bind( this ) );

				this.imageContainer.on( 'mouseleave', function () {
					this.hideOverlay();
				}.bind( this ) );
			}
		} else {
			if ( this.options.touch_show_trigger === 'touch' ) {
				this.imageContainer.on( 'touchend', function () {
					this.toggleOverlay();
				}.bind( this ) );
			}
		}
	};

	this.getAnimationOptions = function (target, animationType, additionalOptions) {
		var animationOptions = {
			targets: target,
			changeBegin: function () {
				this.overlayContainer.css('overflow', 'hidden');
			}.bind(this),
		};
		var $target = $(target);
		switch (animationType) {
			case 'fade':
				animationOptions.easing = 'linear';
				animationOptions.duration = 250;
				animationOptions.opacity = [0, 1];
				break;
			case 'slide':
				animationOptions.easing = 'easeOutExpo';
				animationOptions.duration = 500;
				$target.css('opacity', 1);
				var pos = additionalOptions.hasOwnProperty('position') ? additionalOptions.position : 'top';
				switch (pos) {
					case 'top':
						animationOptions.translateY = [-$target.outerHeight(), 0]; // [ from, to ]
						break;
					case 'right':
						animationOptions.translateX = [$target.parent().outerWidth(), 0];
						break;
					case 'bottom':
						animationOptions.translateY = [$target.parent().outerHeight(), 0];
						break;
					case 'left':
						animationOptions.translateX = [-$target.outerWidth(), 0];
						break;
				}
				break;
			case 'slide_left':
				animationOptions.easing = 'easeOutExpo';
				animationOptions.duration = 500;
				animationOptions.opacity = [0, 1];
				animationOptions.translateX = [20, 0];
				break;
			case 'slide_right':
				animationOptions.easing = 'easeOutExpo';
				animationOptions.duration = 500;
				animationOptions.opacity = [0, 1];
				animationOptions.translateX = [-20, 0];
				break;
			case 'slide_up':
				animationOptions.easing = 'easeOutExpo';
				animationOptions.duration = 500;
				animationOptions.opacity = [0, 1];
				animationOptions.translateY = [10, 0];
				break;
			case 'slide_down':
				animationOptions.easing = 'easeOutExpo';
				animationOptions.duration = 500;
				animationOptions.opacity = [0, 1];
				animationOptions.translateY = [-10, 0];
				break;
			case 'drop':
				animationOptions.easing = 'linear';
				animationOptions.duration = 250;
				animationOptions.opacity = [0, 1];
				animationOptions.scale = [1.1, 1];
				animationOptions.changeBegin = function () {
					this.overlayContainer.css('overflow', 'visible');
				}.bind(this);
				break;
		}

		return animationOptions;
	};

	this.addEventListeners = function () {
		if (this.imageContainer.is('.sow-masonry-grid-item,.sow-image-grid-image')) {
			this.imageContainer
				.closest('.sow-masonry-grid,.sow-image-grid-wrapper')
				.on('layoutComplete', this.layoutChildren.bind(this));
		}
		$(window).on('resize', this.layoutChildren.bind(this));
	};

	this.showOverlay = function () {
		if (this.showingOverlay) {
			return;
		}
		this.showingOverlay = true;
		if (this.animationTimeline) {
			if (this.animationTimeline.direction === 'reverse') {
				this.animationTimeline.reverse();
			}
			if (!this.animationTimeline.began || this.animationTimeline.completed) {
				this.animationTimeline.play();
			}
		} else {
			this.overlay.css('opacity', 1);
		}
	};

	this.hideOverlay = function () {
		if (!this.showingOverlay) {
			return;
		}
		this.showingOverlay = false;
		if ( this.animationTimeline ) {
			if ( this.animationTimeline.direction === 'normal' ) {
				this.animationTimeline.reverse();
			}
			if ( this.animationTimeline.completed ) {
				// This is a hacky workaround for an issue (See https://github.com/juliangarnier/anime/issues/512 )
				// where the animation's direction is ignored when play is called from the completed state.
				this.animationTimeline.completed = false;
				this.animationTimeline.play();
			}
		} else {
			this.overlay.css( 'opacity', 0 );
		}
	};

	this.toggleOverlay = function () {
		if (this.animationTimeline) {
			if (this.animationTimeline.began && this.animationTimeline.direction === 'normal') {
				this.hideOverlay();
			} else {
				this.showOverlay();
			}
		} else {
			var newOpacity = this.overlay.css('opacity') === 1 ? 0 : 1;
			this.overlay.css('opacity', newOpacity);
		}
	};
	
	if ( image.complete ) {
		this.init();
	} else {
		image.addEventListener( 'load', function () {
			this.init();
		}.bind( this ) );
		image.addEventListener( 'error', function () {
			console.log( 'Could not setup Image Overlay. Image loading failed.' );
		} );
	}
};

SiteOriginPremium.createImageOverlays = function ( $ ) {
	$( '.so-widget-sow-image, .so-widget-sow-image-grid, .so-widget-sow-simple-masonry' )
	.each( function ( index, element ) {
		var $wrapper = $( element );
		if ( !$wrapper.data( 'overlay-enabled' ) ) {
			return;
		}
		var settings = $wrapper.data( 'overlay-settings' );
		
		$( element ).find( 'img' )
		.each( function ( index, image ) {
			if (image.title) {
				if (image.classList.contains('jetpack-lazy-image')) {
					$(image).on('jetpack-lazy-loaded-image', function (event) {
						// Use `event.target` and not `image` to ensure the cloned image is used.
						new SiteOriginPremium.ImageOverlay( event.target, settings, index );
					});
				} else {
					new SiteOriginPremium.ImageOverlay( image, settings, index );
				}
			}
		} );
	} );
};

jQuery( function( $ ){
	SiteOriginPremium.createImageOverlays( $ );
	
	if ( window.sowb ) {
		$( window.sowb ).on( 'setup_widgets', function() {
			SiteOriginPremium.createImageOverlays( $ );
		} );
	}
} );
