<?php

/* Set licence key to last 20 years. */
add_filter( 'edd_sl_license_exp_length', 'my_edd_stuff_license_length', 10, 4 );

/* Redirecting to Checkout when Adding an Item to the Cart. */
//add_action( 'edd_add_to_cart', 'my_edd_stuff_redirect_to_cart_on_add', 999 );

/**
 * Set licence key to last 10 years.
 *
 * @since       0.1.0
*/
function my_edd_stuff_license_length( $length, $payment_id, $download_id, $license_id ) {
	return '+20 year'; // set length to 20 years from creation date
}

/**
 * Redirecting to Checkout when Adding an Item to the Cart.
 *
 * @since       0.1.0
*/
function my_edd_stuff_redirect_to_cart_on_add( $data ) {
	global $edd_options;
 
	$redirect_url = get_permalink( $edd_options['purchase_page'] );
 
	if ( edd_get_current_page_url() != $redirect_url ) {
		wp_redirect( $redirect_url, 303 ); 
		exit;
	}
	
}

?>