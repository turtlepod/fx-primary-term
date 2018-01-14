<?php
/**
 * f(x) Primary Term Functions
 *
 * @since 1.0.0
 */

if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Get all supported taxonomy in post types.
 * Currently only support hierarchical taxonomy.
 *
 * @since 1.0.0
 *
 * @return array
 */
function fx_primary_term_get_taxonomies() {
	$data = array(
		'post' => array( 'category' ),
	);

	$data = apply_filters( 'fx_primary_term_taxonomies', $data );
	return is_array( $data ) ? $data : array();
}

/**
 * Get Primary Term Post Meta Key
 * Where to store primary term ID, as default it use the same meta key as Yoast SEO.
 *
 * @since 1.0.0
 *
 * @param string $taxonomy Taxonomy name.
 * @return string
 */
function fx_primary_get_term_meta_key( $taxonomy ) {
	$key = apply_filters( 'fx_primary_term_meta_key', "_yoast_wpseo_primary_{$taxonomy}", $taxonomy );
	return sanitize_key( $key );
}

/**
 * Get Primary Term of A Post.
 * Will always return a term ID if a post/object at least have single taxonomy term.
 *
 * @since 1.0.0
 *
 * @param string      $taxonomy Taxonomy name. e.g "category".
 * @param int|WP_Post $post_id  Post ID. Optional, will use current post loop if not set.
 * @return int|null
 */
function fx_primary_term_get_primary_term( $taxonomy, $post_id = null ) {
	// Get post object.
	$post = get_post( $post_id );

	// Get list of supported taxonomies.
	$taxonomies = fx_primary_term_get_taxonomies();

	// Bail, if post not set, or not supported.
	if ( ! $post || ! isset( $taxonomies[ $post->post_type ] ) || ! is_array( $taxonomies[ $post->post_type ] ) || ! in_array( $taxonomy, $taxonomies[ $post->post_type ] ) ) {
		return null;
	}

	// Meta key.
	$meta_key = fx_primary_get_term_meta_key( $taxonomy );

	// Get meta stored in term ID.
	$primary_id = get_post_meta( $post->ID, $meta_key, true );

	// Get post terms.
	$post_terms = wp_get_post_terms( $post->ID, $taxonomy, array(
		'fields' => 'ids',
	) );
	if ( is_wp_error( $post_terms ) || ! $post_terms ) {
		return null;
	}

	// If ID valid, return it. If not, get the first one as primary.
	$primary_term = ( $primary_id && in_array( $primary_id, $post_terms ) ) ? $primary_id : current( $post_terms );

	return absint( apply_filters( 'fx_primary_term_primary_term', $primary_term, $post, $taxonomy ) );
}

/**
 * Enqueue Scripts to Post Edit Screen.
 *
 * @since 1.0.0
 *
 * @param string $hook_suffix Page context.
 */
add_action( 'admin_enqueue_scripts', function( $hook_suffix ) {
	global $post_type;
	$_taxonomies = fx_primary_term_get_taxonomies();
	$taxonomies = isset( $_taxonomies[ $post_type ] ) ? $_taxonomies[ $post_type ] : array();

	if ( 'post.php' === $hook_suffix && $taxonomies ) {
		wp_enqueue_style( 'fx-primary-term', FX_PRIMARY_TERM_URI . 'assets/fx-primary-term.css', array(), FX_PRIMARY_TERM_VERSION );
		wp_enqueue_script( 'fx-primary-term', FX_PRIMARY_TERM_URI . 'assets/fx-primary-term.js', array( 'jquery' ), FX_PRIMARY_TERM_VERSION );
		wp_localize_script( 'fx-primary-term', 'fxPrimaryTerm', array(
			'taxonomies' => $taxonomies,
			'i18n' => array(
				'setPrimaryLabel' => esc_html__( 'Set', 'fx-primary-term' ),
				'PrimaryLabel' => esc_html__( 'Primary', 'fx-primary-term' ),
			),
		) );
	}
} );

/**
 * Add Hidden Field To Set Primary Term
 * Display field for debug by defining FX_PRIMARY_TERM_DEBUG constant to true.
 *
 * @since 1.0.0
 *
 * @param WP_Post $post Post Object.
 */
add_action( 'edit_form_after_editor', function( $post ) {
	global $post_type, $typenow;
	$_taxonomies = fx_primary_term_get_taxonomies();
	$taxonomies = isset( $_taxonomies[ $post_type ] ) ? $_taxonomies[ $post_type ] : array();
	if ( 'post' !== $typenow || ! $taxonomies ) {
		return;
	}
	?>
	<div class="fx-primary-term-fields" style="<?php echo defined( 'FX_PRIMARY_TERM_DEBUG' ) && FX_PRIMARY_TERM_DEBUG ? 'display:block;' : 'display:none !important;'; ?>">

		<?php foreach ( $taxonomies as $taxonomy ) : ?>
			<p>
				<label for="fx_primary_term_<?php echo esc_attr( $taxonomy ); ?>"><?php echo esc_html( $taxonomy ); ?></label>
				<input id="fx_primary_term_<?php echo esc_attr( $taxonomy ); ?>" type="number" class="fx_primary_term_field" name="fx_primary_term[<?php echo esc_attr( $taxonomy ); ?>]" value="<?php echo esc_attr( fx_primary_term_get_primary_term( $taxonomy, $post->ID ) ); ?>">
			</p>
		<?php endforeach; ?>

		<?php wp_nonce_field( "fx-primary-term_{$post->ID}", '_fx_primary_term_nonce' ); ?>
	</div><!-- .fx-primary-term-fields -->
	<?php
} );

/**
 * Save Primary Term.
 *
 * @since 1.0.0
 *
 * @param int     $post_id Post ID.
 * @param WP_Post $post    Post.
 */
add_action( 'save_post', function( $post_id, $post ) {
	if ( ! isset( $_POST['_fx_primary_term_nonce'], $_POST['fx_primary_term'] ) || ! wp_verify_nonce( $_POST['_fx_primary_term_nonce'], "fx-primary-term_{$post_id}" ) ) {
		return;
	}

	// Get taxonomies.
	$_taxonomies = fx_primary_term_get_taxonomies();
	$taxonomies = is_array( $_taxonomies ) && isset( $_taxonomies[ $post->post_type ] ) ? $_taxonomies[ $post->post_type ] : array();
	if ( ! $taxonomies || ! is_array( $_POST['fx_primary_term'] ) ) {
		return;
	}

	foreach ( $taxonomies as $taxonomy ) {
		// Get primary term.
		$term_id = isset( $_POST['fx_primary_term'][ $taxonomy ] ) ? absint( $_POST['fx_primary_term'][ $taxonomy ] ) : false;

		if ( $term_id ) {

			// Get current post terms.
			$post_terms = wp_get_post_terms( $post_id, $taxonomy, array(
				'fields' => 'ids',
			) );

			// Check if input is valid term ID.
			if ( in_array( $term_id, $post_terms ) ) {
				update_post_meta( $post_id, fx_primary_get_term_meta_key( $taxonomy ), absint( $term_id ) );
			} else { // Not valid, delete it.
				delete_post_meta( $post_id, fx_primary_get_term_meta_key( $taxonomy ) );
			}
		} else { // No term, delete.
			delete_post_meta( $post_id, fx_primary_get_term_meta_key( $taxonomy ) );
		}
	}

}, 10, 2 );
