/* globals jQuery, lightbox, SiteOriginPremium */

window.SiteOriginPremium = window.SiteOriginPremium || {};

SiteOriginPremium.setupLightbox = function ( $ ) {
	$( 'a[data-lightbox]' ).on( 'click', function () {
		// Set options just before lightbox is opened to ensure instance specific settings are applied.
		var instanceOptions = $( this ).data( 'lightboxOptions' );
		lightbox.option( instanceOptions );
		var $overlay = $( '#lightboxOverlay' );
		$overlay.css( 'background-color', instanceOptions.overlayColor );
		$overlay.css( 'opacity', instanceOptions.overlayOpacity );
	} );
};


jQuery( function( $ ) {
	SiteOriginPremium.setupLightbox( $ );

	if ( window.sowb ) {
		$( window.sowb ).on( 'setup_widgets', function() {
			SiteOriginPremium.setupLightbox( $ );
		} );
	}
} );
