<?php

/* Add shortcode for user expire date. */
add_shortcode( 'my_edd_expire_date', 'my_edd_stuff_expire_date_shortcode' );

/**
* Add shortcode for user expire date.
*
* @since 0.1.0
*/
function my_edd_stuff_expire_date_shortcode() {
	
	if( is_user_logged_in() ) {
	
		/* Get current user id. */
		$user_id = get_current_user_id();
		
		/* Get expire date. */
		$expire_date = get_user_meta( $user_id, 'expire_date', true );
		
		/* Return expire_date if there is one. */
		if ( !empty( $expire_date ) ) {
			return date_i18n( get_option( 'date_format' ), $expire_date );
		 }
		 else {
			return __( 'Unknown', 'my-edd-stuff' );
		 }
		
	}
}

?>