<?php
/**
 * Deprecated Functions
 *
 * @since 1.0.0
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Get Primary Term Post Meta Key
 * Where to store primary term ID, as default it use the same meta key as Yoast SEO.
 *
 * @since 1.0.0
 * @deprecated 1.0.1
 *
 * @param string $taxonomy Taxonomy name.
 * @return string
 */
function fx_primary_get_term_meta_key( $taxonomy ) {
	_deprecated_function( __FUNCTION__, '1.0.1', 'fx_primary_term_get_post_meta_key()' );
	return fx_primary_term_get_post_meta_key( $taxonomy );
}
