<?php

/* Add custom user meta when purchasing something on the site: expire_date --> current date +1 year. */
add_action( 'edd_update_payment_status', 'my_edd_stuff_add_expire_date', 10, 3 );

/* Add new user column: Expire Date. */
add_filter( 'manage_users_columns', 'my_edd_stuff_expire_date_column' );

/* Adds Expire Date. */
add_action( 'manage_users_custom_column', 'my_edd_stuff_expire_date_data', 10, 3 );

/**
 *  Add or update custom user meta when purchasing something on the site: expire_date --> current date +1 year.
 *
 * @since       0.1.0
*/
function my_edd_stuff_add_expire_date( $payment_id, $new_status, $old_status ) {

	if( $new_status != 'publish' && $new_status != 'complete' )
		return;

	/* Get current user id. */
	$payment_data 	= get_post_meta( $payment_id, '_edd_payment_meta', true );
	$user_info 	= maybe_unserialize( $payment_data['user_info'] );
	$user_id = $user_info['id'];

	/* Current date. */
	$current_date = date( 'Y-m-d' );
	
	/* Add one year. */
	$expire_date = strtotime ( '+1 year' , strtotime ( $current_date ) ) ;
	
	/* Update user meta. */
	update_user_meta( $user_id, 'expire_date', $expire_date );

}

/**
 * Add new user column: Expire Date.
 *
 * @since       0.1.0
 */
function my_edd_stuff_expire_date_column( $columns ) {

	$columns['expire_date'] = __( 'Expire Date', 'my-edd-stuff' );

	return $columns;

}

/**
 * Adds Expire Date to column.
 *
 * @since       0.1.0
 */
function my_edd_stuff_expire_date_data( $value, $column_name, $user_id ) {

	if( 'expire_date' == $column_name ) {
		
		/* Get expire date. */
		$expire_date = get_user_meta( $user_id, 'expire_date', true );
		
		/* Return expire_date if there is one. */
		if ( !empty( $expire_date ) ) {
			return date_i18n( get_option( 'date_format' ), $expire_date );
		 }
	
	} 

}

?>