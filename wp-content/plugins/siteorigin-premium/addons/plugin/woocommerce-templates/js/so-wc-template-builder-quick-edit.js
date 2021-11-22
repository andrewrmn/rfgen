jQuery( function( $ ) {
	// Load stored product data.
	$( '.editinline' ).on( 'click', function() {
		var $product = $( this ).closest( 'tr' );
		$( '.so_wc_template_post_id option:selected' ).attr( 'selected', false );
		$( '.so_wc_template_post_id option[value="' + $product.find( '.so_wc_template_post_id_current' ).text() + '"]' ).attr( 'selected', 'selected' );
	} );
} );
