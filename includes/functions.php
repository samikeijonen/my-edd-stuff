<?php

/* Set licence key to last 20 years. */
add_filter( 'edd_sl_license_exp_length', 'my_edd_stuff_license_length', 10, 4 );

/* Redirecting to Checkout when Adding an Item to the Cart. */
//add_action( 'edd_add_to_cart', 'my_edd_stuff_redirect_to_cart_on_add', 999 );

/* Show how many license activations you have used in [download_history] table. */
add_action( 'edd_download_history_header_end', 'my_edd_stuff_downloads_license_limit_th', 11 );
add_action( 'edd_download_history_row_end', 'my_edd_stuff_downloads_license_limit_td', 11, 2 );

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
 * Show license key limit in [download_history] shortcode. 
 *
 * @since 0.1.0
 */
function my_edd_stuff_downloads_license_limit_th() {

	echo '<th class="my-edd-stuff-download-limit">' . __( 'Limit', 'my-edd-stuff' ) . '</th>';
	
}

/**
 * Show license key limit in [download_history] shortcode.
 *
 * @since 0.1.0
 */
function my_edd_stuff_downloads_license_limit_td( $puchase_id, $download_id ){
	
	/* Get license limit. */
	$license_limit = get_post_meta( $download_id, '_edd_sl_limit', true );
	
	/* If license limit is infinite (0), set it as infinite sign. */
	if ( 0 == $license_limit )
		$license_limit = "&#8734;";
	
	/* Get site count. How many sites have been activated with this key. */
	$site_count = my_edd_stuff_get_license_count( $download_id );
	
	/* If there is no site count, set it to 0. */
	if ( empty( $site_count ) )
		$site_count = 0;
	
	/* Echo site count anf license limit to [download_history] shortcode. */
	echo '<td class="my-edd-stuff-download-limit">'. $site_count . '/' . $license_limit .'</td>';
	
}

/**
 * Get site count.
 *
 * @since 0.1.0
 */
function my_edd_stuff_get_license_count( $download_id ) {

	//$licensing = new EDD_Software_Licensing();
	$licensing = $GLOBALS['EDD_Software_Licenseing'];
	
	/* Get license. */
	$license = $licensing->get_license_by_download( $download_id );
	
	/* Get license ID if there is one. */
	if ( $license )
		$license_id = $license->ID;
	
	/* Get site count by license ID if there is one. */
	if ( isset( $license_id ) )
		return absint( get_post_meta( $license_id, '_edd_sl_site_count', true ) );

}

?>