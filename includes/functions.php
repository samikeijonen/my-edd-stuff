<?php

/* Set licence key to last 20 years. */
add_filter( 'edd_sl_license_exp_length', 'my_edd_stuff_license_length', 10, 4 );

/* Redirecting to Checkout when Adding an Item to the Cart. */
//add_action( 'edd_add_to_cart', 'my_edd_stuff_redirect_to_cart_on_add', 999 );

/* Show how many license activations you have used in [purchase_history] table. */
add_action( 'edd_purchase_history_header_after', 'my_edd_stuff_downloads_license_limit_th', 11 );
add_action( 'edd_purchase_history_row_end', 'my_edd_stuff_downloads_license_limit_td', 11, 2 );

/**
 * Set licence key to last 20 years.
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

/**
 * Show license key limit in [purchase_history] shortcode.
 *
 * @since 0.1.1
 */
function my_edd_stuff_downloads_license_limit_th() {

	echo '<th class="my-edd-stuff-site-count">' . __( 'Site Count', 'my-edd-stuff' ) . '</th>';
	
}

/**
 * Show license key limit in [purchase_history] shortcode.
 *
 * @since 0.1.1
 */
function my_edd_stuff_downloads_license_limit_td( $payment_id, $purchase_data ) {
	
	/* Get license limit. */
	//$license_limit = get_post_meta( $download_id, '_edd_sl_limit', true );
	$license_limit = my_edd_stuff_get_license_limit( $payment_id, $purchase_data );
	
	/* If license limit is infinite (0), set it as infinite sign. */
	if ( 0 == $license_limit )
		$license_limit = "&#8734;";
	
	/* Get site count. How many sites have been activated with this key. */
	$site_count = my_edd_stuff_get_license_count( $payment_id, $purchase_data );
	
	/* If there is no site count, set it to 0. */
	if ( empty( $site_count ) )
		$site_count = 0;
	
	/* Echo site count and license limit to [download_history] shortcode. */
	echo '<td class="my-edd-stuff-site-count">'. $site_count . '/' . $license_limit .'</td>';
	
}

/**
 * Get site count.
 *
 * @since 0.1.0
 */
function my_edd_stuff_get_license_count( $payment_id, $purchase_data) {

	$licensing = edd_software_licensing();
	$downloads = edd_get_payment_meta_downloads( $payment_id );
	
	/* Get license. */
	foreach( $downloads as $download ) {
	$license = $licensing->get_license_by_purchase( $payment_id, $download['id'] );
		if( $license ) {
			return absint( get_post_meta( $license->ID, '_edd_sl_site_count', true ) );
		}
	}

}

/**
 * Get license limit. This is based on price id in variable pricing. This is 0, 1, 2 etc.
 *
 * @since 0.1.0
 */
function my_edd_stuff_get_license_limit( $payment_id, $purchase_data ) {

	global $edd_receipt_args;
	
	/* Get purchase details. */
	$meta = edd_get_payment_meta( $payment_id );
	$cart = edd_get_payment_meta_cart_details( $payment_id, true );
	
	$price_id = false;
	
	/* Get price id from variable pricing. This is 0, 1, 2. */
	foreach( $cart as $key => $item ) {
		$price_id = edd_get_cart_item_price_id( $item );
	}
	
	/* Decide what are license limits. For now it's 1, 4 and unlimited. */
	if( $price_id !== false ) {

		switch( $price_id ) {

			case 0:
				$license_limit = 1; // single site license
				break;
			case 1:
				$license_limit = 4; // up to 4 sites
				break;
			case 2:
				$license_limit = 0; // unlimited
				break;
		}
	
	}
	
	/* Return license limit. */
	return $license_limit;

}

?>