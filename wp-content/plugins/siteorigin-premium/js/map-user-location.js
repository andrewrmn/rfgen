
( function( $ ) {
	$( window.sowb ).on( 'maps_loaded autocomplete_loaded', function( e ) {
		var processUserLocation = function( position ) {
			window.sowb.SiteOriginGoogleMapInstances.forEach( function( map ) {
				var $mapElement = $( map.getDiv() );
				if ( $mapElement.data( 'options' )['center_user_location'] ) {
					var userLocation = new google.maps.LatLng( position.coords.latitude, position.coords.longitude );
					map.setCenter( userLocation );
					if ( map.centerMarker ) {
						map.centerMarker.setPosition( userLocation );
					}

					var autoCompleteField = $mapElement.prev();
					if ( autoCompleteField && typeof google.maps.places !== 'undefined') {
						var geocoder = new google.maps.Geocoder();

						var latlng = {
							lat: position.coords.latitude,
							lng: position.coords.longitude,
						}
						geocoder.geocode({ location: latlng }, ( results, status ) => {
							if ( status === 'OK' && results[0] ) {
								autoCompleteField.val( results[0].formatted_address );
							}
						} );
					}
				}
			} );
		};

		var handleUserLocationErr = function( error ) {
			console.log( error );
		};

		if ( navigator.geolocation ) {
			navigator.geolocation.getCurrentPosition(
				processUserLocation,
				handleUserLocationErr,
				{
					enableHighAccuracy: true,
					timeout: 20000,
					maximumAge: 0, // Prevent caching
				}
			);
		}
	} );
} )( jQuery );
