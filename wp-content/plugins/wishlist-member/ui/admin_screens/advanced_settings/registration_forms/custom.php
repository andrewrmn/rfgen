<style type="text/css">
	.regform-edit-accordion-placeholder {
		display: block;
		height: 60px;
		background: white;
		border: 1px dotted black;
		margin: 5px 0;
	}
	#regform-edit-accordion .regform-edit-accordion-placeholder:first-child {
		margin-top: 0;
	}
	.ui-draggable-dragging {
		width: 100%;
	}
	.ui-draggable-dragging i {
		display: none;
	}
	.chosen-fields .ui-draggable-dragging {
		width: calc(100% - 45px) !important;
		list-style: none;
		background: #DBE4EE;
		border: 1px dotted black;
		padding: 15px;
		border-radius: 3px;
	}
	.inputh {
		display: none;
	}
	.field_hidden .inputh {
		display: inline-block;
	}

	.reg-cookie-timeout .input-group {
		width: 150px;
	}

	#custom-registration-forms-list tbody:not(:empty) ~ tfoot {
		display: none;
	}
</style>
<?php
	$registration_forms = $this->GetCustomRegForms();
	$wpm_levels = $this->GetOption( 'wpm_levels' );
	$used_forms = [];
	foreach ( $wpm_levels as $level ) {
		if ( ! empty( $level['custom_reg_form'] ) && ! empty( $level['enable_custom_reg_form'] ) ) {
			if ( empty( $used_forms[ $level['custom_reg_form'] ] ) ) {
				$used_forms[ $level['custom_reg_form'] ] = array();
			}
			$used_forms[ $level['custom_reg_form'] ][] = $level['name'];
		}
	}
	$countries = include wishlistmember_instance()->pluginDir3 . '/helpers/countries.php';
	printf( "<script type='text/javascript'>\nwpm_regforms = %s\nwpm_regform_default = %s\nwpm_levels = %s\nwpm_used_forms = %s\nwpm_countries = %s\n</script>\n", preg_replace( '/ style=\\\\".+?"/', '', json_encode( $registration_forms ) ), json_encode( $this->get_legacy_registration_form( $the_formid, '', true ) ), json_encode( $wpm_levels ), json_encode( $used_forms ), json_encode( $countries ) );

	include 'custom/list.php';
	include 'custom/edit.php';

