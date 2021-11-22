jQuery( function($){
    $( '#siteorigin-premium-notice .siteorigin-notice-dismiss' ).on( 'click', function( e ) {
        e.preventDefault();
        var $$ = $( this ).trigger( 'blur' );
        $.get( $$.attr('href') );

        $('#siteorigin-premium-notice').slideUp( function(){
            $(this).remove();
        } )
    } );
} );
