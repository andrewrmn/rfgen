<?php
add_action(
	'wishlistmember_toggle_email_provider_infusionsoft', function( $state ) {
		if ( $state && ! $this->GetOption( 'infusionsoft_email_tags_migrate' ) ) {
			$ars = $this->GetOption('Autoresponders');
			$tags = array();
			foreach ( array( 'istags_add_app', 'istags_add_rem', 'istags_remove_app', 'istags_remove_rem', 'istags_cancelled_app', 'istags_cancelled_rem' ) as $option ) {
				$x = wlm_maybe_unserialize($this->GetOption( 'auto_' . $option ));
				if ( is_array( $x ) && $x ) {
					$tags[ $option ] = $x;
				}
				$this->SaveOption( 'infusionsoft_email_tags_migrate', 1 );
			}
			if(!is_array($ars['infusionsoft'])) {
				$ars['infusionsoft'] = array();
			}
			$ars['infusionsoft'] = array_merge($ars['infusionsoft'], $tags);
			$this->SaveOption('Autoresponders', $ars);
		}
	}, 10, 1
);
