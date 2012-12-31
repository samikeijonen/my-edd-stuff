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
	$payment_data = get_post_meta( $payment_id, '_edd_payment_meta', true );
	$user_info = maybe_unserialize( $payment_data['user_info'] );
	$user_id = $user_info['id'];

	/* Get cart items. */
	$cart_items = maybe_unserialize( $payment_data['cart_details'] );
	
	foreach( $cart_items as $key => $cart_item ) {
		
		/* Retrieve the ID of the download. */
		$id = $cart_item['id'];
		
		/* Retrieve the price_id. */
		$price_options = $cart_items[$key]['item_number']['options'];
																															
		if( isset( $price_options['price_id'] ) ) {
			
			/* Get price id. This is 0, 1, 2 etc. Use this. */
			$price_id = $price_options['price_id'];
			
			/* Get price name. This is Basic, Standard or Developer. This is just if needed. Use price_id. */
			$price_name = edd_get_price_option_name( $id, $price_options['price_id'] );
		
		}
	
	}
	
	/* Support time is based on price_id. */
	if ( $price_id == 0 ) {
		$support_time = '+6 month';
	}
	else if ( $price_id == 1 ) {
		$support_time = '+1 year';
	}
	else if ( $price_id == 2 ) {
		$support_time = '+2 year';
	}
	else {
		$support_time = '+2 year';
	}
	
	/* Current date. */
	$current_date = date( 'Y-m-d' );
		
	/* Get expire date. */
	$expire_date = get_user_meta( $user_id, 'expire_date', true );
	
	/* If expire_date is not set ( this means new user), add current_date + support_time. Else there is current_date already. */
	if ( !isset( $expire_date ) ) {
		$expire_date = strtotime ( $support_time , strtotime ( $current_date ) ) ;
	}
	else {
		
		/* if expire_date < current_date, add current_date + support_time. */
		if ( $expire_date < strtotime ( $current_date ) ) {
			$expire_date = strtotime ( $support_time , strtotime ( $current_date ) );
		}
		else {
			
			/* Get future_date so that we can check should we do anything to current expire_date. Else add current_date + support_time. */
			$future_date = strtotime ( $support_time , strtotime ( $current_date ) );
			
			/* If future_date < expire_date, don't add anything. */
			if ( $future_date < $expire_date ) {
				$expire_date = $expire_date;
			}
			else {
				$expire_date = strtotime ( $support_time , strtotime ( $current_date ) );
			}
			
		}
		
	}
	
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