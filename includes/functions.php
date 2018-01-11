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
 *
 * @since 1.0.0
 *
 * @return array
 */
function fx_primary_term_get_taxonomies() {
	$data = array(
		'post' => array( 'category' ),
	);
	// @todo: check if taxonomy hierarchical and validate each taxonomy/post type.
	return apply_filters( 'fx_primary_term_taxonomies', $data );
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
				'setPrimaryLabel' => esc_html__( 'Set Primary', 'fx-primary-term' ),
				'PrimaryLabel' => esc_html__( 'Primary', 'fx-primary-term' ),
			),
		) );
	}
} );

/**
 * Add Hidden Field To Set Primary Term
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
	<div class="fx-primary-term-fields" style="<?php echo defined( 'WP_DEBUG' ) && WP_DEBUG ? 'display:block;' : 'display:none !important;'; ?>">

		<?php foreach ( $taxonomies as $taxonomy ) : ?>
			<p>
				<label for="fx_primary_term_<?php echo esc_attr( $taxonomy ); ?>"><?php echo esc_html( $taxonomy ); ?></label>
				<input id="fx_primary_term_<?php echo esc_attr( $taxonomy ); ?>" type="number" class="fx_primary_term_field" name="fx_primary_term[<?php echo esc_attr( $taxonomy ); ?>]" value="<?php echo esc_attr( get_post_meta( $post->ID, "_yoast_wpseo_primary_{$taxonomy}", true ) ); ?>">
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

	$_taxonomies = fx_primary_term_get_taxonomies();
	$taxonomies = isset( $_taxonomies[ $post->post_type ] ) ? $_taxonomies[ $post->post_type ] : array();
	if ( ! $taxonomies || ! is_array( $_POST['fx_primary_term'] ) ) {
		return;
	}

	foreach ( $taxonomies as $taxonomy ) {
		if ( isset( $_POST['fx_primary_term'][ $taxonomy ] ) && $_POST['fx_primary_term'][ $taxonomy ] ) {
			// @todo: check taxonomy before saving.
			update_post_meta( $post_id, "_yoast_wpseo_primary_{$taxonomy}", absint( $_POST['fx_primary_term'][ $taxonomy ] ) );
		} else {
			delete_post_meta( $post_id, "_yoast_wpseo_primary_{$taxonomy}" );
		}
	}

}, 10, 2 );
