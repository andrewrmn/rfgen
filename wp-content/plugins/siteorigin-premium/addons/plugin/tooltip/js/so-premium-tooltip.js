/* globals jQuery, soPremiumTooltipOptions, SiteOriginPremium */

window.SiteOriginPremium = window.SiteOriginPremium || {};

SiteOriginPremium.setupTooltip = function( $ ) {
	$( '.so-widget-sow-image, .so-widget-sow-image-grid, .so-widget-sow-simple-masonry, .so-widget-sow-icon, .so-widget-sow-price-table, .so-widget-sow-features' )
	.each( function( index, element ) {
		var $wrapper = $( element );
		if ( !$wrapper.data( 'tooltip-enabled' ) ) {
			return;
		}
		var theme = $wrapper.data( 'tooltip-theme' );

		var initializeTooltip = function( $item ) {
			var tooltipText = $item.attr( 'title' );

			if ( tooltipText ) {
				// This prevents the default browser tooltip from being displayed without removing the title attribute.
				$( this.image ).css( 'pointerEvents', 'none' );
				var $tooltip = $( '<div class="so-premium-tooltip">' + tooltipText + '<div class="callout"></div></div>' );
				$tooltip.css( 'visibility', 'hidden' );
				$tooltip.addClass( 'theme-' + theme );
				var isIcon = $item.is( 'span' );
				var isPriceTable = $item.hasClass( 'ow-pt-feature' );
				var isFeatures = $item.hasClass( 'sow-icon-container' );
				if ( isFeatures ) {
					$item.removeAttr( 'title' );
					// Due to how icon is sized, we need to set the item container to the parent to avoid sizing issues.
					$item = $item.find( 'div, span' );
				}
				if ( isIcon || isPriceTable ) {
					var $itemContainer = $item;
				} else {
					var $itemContainer = $item.parent().closest( ':not( a )' );
				}

				var isMasonryImage = $itemContainer.is( '.sow-masonry-grid-item' );
				if ( isMasonryImage || isFeatures || isIcon ) {
					$itemContainer.parent().append( $tooltip );
				} else {
					$itemContainer.append( $tooltip );
				}

				var tooltipRelativeOffset = { top: 0, left: 0 };
				var $callout = $tooltip.find( '.callout' );

				var updateTooltipPosition = function( event = false ) {
					var itemPosition = isMasonryImage || isFeatures ? $itemContainer.position() : $item.position(),
						tooltipArrowPos = $tooltip.data( 'width' ) / 2,
						leftPosition = itemPosition.left + tooltipRelativeOffset.left - tooltipArrowPos,
						toolTipExceedsPageWidth = $( 'body' ).innerWidth() - tooltipArrowPos;

					if ( event && event.pageX >= toolTipExceedsPageWidth ) {
						var $offsetTarget = isMasonryImage || isFeatures ? $itemContainer.parent() : $( event.currentTarget );
						leftPosition = toolTipExceedsPageWidth - tooltipArrowPos - $offsetTarget.offset().left;
					}
	
					$tooltip.css( {
						top: itemPosition.top + tooltipRelativeOffset.top - $tooltip.outerHeight() + 'px',
						left: leftPosition + 'px',
					} );
				};

				var showTooltip = function() {
					updateTooltipPosition();
					$tooltip.fadeIn( 100 );
				};
				if ( soPremiumTooltipOptions.position === 'follow_cursor' ) {
					$tooltip.css( 'pointer-events', 'none' );
				}

				var showTimeoutId;
				$tooltip.hide();
				$tooltip.css( 'visibility', 'visible' );
				$itemContainer.on( soPremiumTooltipOptions.show_trigger, function( event ) {
					var tooltipElement = $tooltip.get( 0 );
					// Make sure the show action isn't triggered when mouse moves from the image to the tooltip or back.
					if ( ! isPriceTable ) {
						// Make sure the show action isn't triggered when mouse moves from the image to the tooltip or back.
						if ( $tooltip.is( ':visible' ) || event.target === tooltipElement || event.relatedTarget === tooltipElement || event.relatedTarget === $item.get( 0 ) ) {
							return false;
						}
					}

					$item.removeAttr( 'title' );
					var $sizingElement = isMasonryImage ? $itemContainer : $item;
					$callout.removeClass( 'bottom' ).addClass( 'top' );
					$callout.css( 'pointer-events', 'none' );
					var calloutOffset = $callout.outerHeight() * 0.5;
					switch ( soPremiumTooltipOptions.position ) {
						case 'follow_cursor':
							if ( ! $tooltip.data( 'width' ) ) {
								$tooltip.data( 'width', $tooltip.outerWidth() + 1 );
								$tooltip.css( 'width', $tooltip.data( 'width' ) + 1 + 'px' );
							}

							$itemContainer.on( 'mousemove', function( event ) {
								// For cases where the image overflows it's container, e.g. masonry items, we need to subtract the overflow.
								var itemHeight = $item.outerHeight();
								var itemContainerHeight = $itemContainer.outerHeight();
								var itemOverflowY = ( itemHeight > itemContainerHeight ) ? ( itemHeight - itemContainerHeight ) * 0.5 : 0;
								var itemWidth = $item.outerWidth();
								var itemContainerWidth = $itemContainer.outerWidth();
								var itemOverflowX = ( itemWidth > itemContainerWidth ) ? ( itemWidth - itemContainerWidth ) * 0.5 : 0;

								if ( isMasonryImage ) {
									tooltipRelativeOffset.top = event.offsetY - calloutOffset - itemOverflowY;
									tooltipRelativeOffset.left = event.offsetX - itemOverflowX;
								} else {
									tooltipRelativeOffset.top = event.pageY - $(event.currentTarget).offset().top - calloutOffset - itemOverflowY;
									tooltipRelativeOffset.left = event.pageX - $( event.currentTarget ).offset().left - itemOverflowX;
									if ( isFeatures ) {
										// Features has margin that needs to be included in the offset to prevent misalignment.
										tooltipRelativeOffset.left += parseInt( $itemContainer.css( 'margin-left' ) );
									}
								}

								updateTooltipPosition( event );
							} );
							break;
						case 'center':
							tooltipRelativeOffset.top = ( $sizingElement.outerHeight() * 0.5 ) - calloutOffset;
							tooltipRelativeOffset.left = $sizingElement.outerWidth() * 0.5;
							break;
						case 'top':
							tooltipRelativeOffset.top = 0 - calloutOffset;
							tooltipRelativeOffset.left = $sizingElement.outerWidth() * 0.5;
							break;
						case 'bottom':
							$callout.removeClass( 'top' ).addClass( 'bottom' );
							tooltipRelativeOffset.top = $sizingElement.outerHeight() + $tooltip.outerHeight() + calloutOffset;
							tooltipRelativeOffset.left = $sizingElement.outerWidth() * 0.5;
							break;
					}
					if ( soPremiumTooltipOptions.show_trigger === 'mouseover' &&
						soPremiumTooltipOptions.show_delay && soPremiumTooltipOptions.show_delay > 0 ) {
						if ( showTimeoutId ) {
							clearTimeout( showTimeoutId );
						}
						showTimeoutId = setTimeout( function() {
							showTimeoutId = null;
							showTooltip();
						}, soPremiumTooltipOptions.show_delay );
					} else {
						showTooltip();
					}

					if ( soPremiumTooltipOptions.hide_trigger === 'click' ) {
						var hideTooltip = function() {
							$tooltip.fadeOut( 100 );
							$item.attr( 'title', tooltipText );
							$( window ).off( 'click', hideTooltip );
						};
						setTimeout( function() {
							$( window ).on( 'click', hideTooltip );
						}, 100 );
					}
				} );
				
				if ( soPremiumTooltipOptions.hide_trigger === 'mouseout' ) {
					$itemContainer.on( 'mouseout', function( event ) {
						if ( showTimeoutId ) {
							clearTimeout( showTimeoutId );
						}
						// Make sure the hide action isn't triggered when mouse moves from the image to the tooltip.
						if ( event.relatedTarget !== $tooltip.get( 0 ) &&
							!$.contains( $itemContainer.get( 0 ), event.relatedTarget ) ) {
							$tooltip.fadeOut( 100 );
							$item.attr( 'title', tooltipText );
							$itemContainer.off( 'mousemove', updateTooltipPosition );
							$item.attr( 'title', tooltipText );
						}
					} );
					if ( isMasonryImage ) {
						$tooltip.on( 'mouseout', function( event ) {
							if ( event.relatedTarget !== $item.get( 0 ) || event.relatedTarget !== $item.get( 0 ) ) {
								$tooltip.fadeOut( 100 );
								$item.attr( 'title', tooltipText );
								$itemContainer.off( 'mousemove', updateTooltipPosition );
								$item.attr( 'title', tooltipText );
							}
						} );
					}
				}
			}
		};

		if ( $wrapper.hasClass( 'so-widget-sow-icon' ) ) {
			initializeTooltip( $wrapper.find( 'span' ) );
		} else if  ( $wrapper.hasClass( 'so-widget-sow-features' ) ) {
			$wrapper.find( '.sow-icon-container' ).each( function( index, feature ) {
				initializeTooltip( $( feature ) );
			} )
		} else if ( $wrapper.hasClass( 'so-widget-sow-price-table' ) ) {
			$wrapper.find( '.ow-pt-feature' ).each( function( index, feature ) {
				initializeTooltip( $( feature ) );
			} )
		} else {
			$wrapper.find( 'img' ).each( function( index, image ) {
				function initOrListForLoad( image ) {
					var $image = $( image );
					if ( image.complete ) {
						initializeTooltip( $image );
					} else {
						$image
							.on( 'load', function() {
								initializeTooltip( $image );
							} )
							.on( 'error', function() {
								console.log( 'Could not setup tooltip. Image loading failed.' );
							} );
					}
				}

				if ( image.classList.contains( 'jetpack-lazy-image' ) ) {
					$( image ).on( 'jetpack-lazy-loaded-image', function( event ) {
						initOrListForLoad( event.target );
					} );
				} else {
					initOrListForLoad( image );
				}

			} );
		}
	} );
};

jQuery( function( $ ) {
	SiteOriginPremium.setupTooltip( $ );
	
	if ( window.sowb ) {
		$( window.sowb ).on( 'setup_widgets', function() {
			SiteOriginPremium.setupTooltip( $ );
		} );
	}
} );
