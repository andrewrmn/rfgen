<?php

$ifs_class_name = '\WishListMember\Autoresponders\Infusionsoft';

// tags
add_action( 'wishlistmember_user_registered', array( $ifs_class_name, 'NewUserTagsHookQueue' ), 99, 2 );
add_action( 'wishlistmember_add_user_levels', array( $ifs_class_name, 'AddUserTagsHookQueue' ), 10, 3 );

add_action( 'wishlistmember_confirm_user_levels', array( $ifs_class_name, 'ConfirmApproveLevelsTagsHook' ), 99, 2 );
add_action( 'wishlistmember_approve_user_levels', array( $ifs_class_name, 'ConfirmApproveLevelsTagsHook' ), 99, 2 );

add_action( 'wishlistmember_pre_remove_user_levels', array( $ifs_class_name, 'RemoveUserTagsHookQueue' ), 99, 2 );
add_action( 'wishlistmember_cancel_user_levels', array( $ifs_class_name, 'CancelUserTagsHookQueue' ), 99, 2 );
add_action( 'wishlistmember_uncancel_user_levels', array( $ifs_class_name, 'UnCancelUserTagsHookQueue' ), 99, 2 );
add_action( 'delete_user', array( $ifs_class_name, 'DeleteUserHookQueue' ), 9, 1 );

// we only process the following methods if they're not handled by the Infusionsofy payment integration
global $WishListMemberInstance;
if ( !$WishListMemberInstance->payment_integration_is_active('infusionsoft') || !$WishListMemberInstance->GetOption( 'ismachine' ) || !$WishListMemberInstance->GetOption( 'isapikey' ) ) {
	add_action( 'edit_user_profile', array( $ifs_class_name, 'ProfileForm' ) );
	add_action( 'show_user_profile', array( $ifs_class_name, 'ProfileForm' ) );
	add_action( 'profile_update', array( $ifs_class_name, 'UpdateProfile' ), 9, 2 );

	add_filter( 'wishlist_member_user_custom_fields', array( $ifs_class_name, 'add_ifs_field' ), 99, 2 );
	add_filter( 'wishlistmember_post_update_user', array( $ifs_class_name, 'save_ifs_field' ), 99, 1 );
}

add_action( 'wishlistmember_api_queue', array( $ifs_class_name, 'ifarProcessQueue' ) );

// legacy
add_action( 'wishlistmember3_autoresponder_subscribe', array( $ifs_class_name, 'subscribe' ), 10, 2 );
add_action( 'wishlistmember3_autoresponder_unsubscribe', array( $ifs_class_name, 'unsubscribe' ), 10, 2 );
