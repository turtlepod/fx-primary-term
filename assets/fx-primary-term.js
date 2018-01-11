/**
 * Primary Term
 *
 * @since 1.0.0
 */
(function( window, undefined ){

	window.wp = window.wp || {};
	var document = window.document;
	var $ = window.jQuery;

	/**
	 * Bind items to to the DOM.
	 *
	 * @since 1.0.0
	 */
	$( function() {

		/**
		 * Loop each term and display primary info.
		 *
		 * @since 1.0.0
		 *
		 * @param tax Taxonomy
		 */
		var ProcessPrimaryTermList = function( tax ) {
			var metaBox = $( '#' + tax + 'div' );
			var list = $( '#' + tax + 'div ul.' + tax + 'checklist' );
			var primary_field = $( '#fx_primary_term_' + tax );
			var primary_id = primary_field.val();

			list.find( 'li' ).each( function( index ) {
				var input = $( this ).find( 'input[type="checkbox"]' );
				var term_id = input.val();

				// Remove all label.
				$( this ).find( '.fx-primary-term-primary,.fx-primary-term-set-primary' ).remove();

				// Rebuild label.
				if ( term_id === primary_id ) {
					$( this ).append( '<span class="fx-primary-term-primary">' + fxPrimaryTerm.i18n.PrimaryLabel + '</span>' );
				} else {
					$( this ).append( '<span class="fx-primary-term-set-primary" data-term_id="' + term_id + '" data-taxonomy="' + tax + '">' + fxPrimaryTerm.i18n.setPrimaryLabel + '</span>' );
				}
			} );
		};

		// Process all supported taxonomies on initial load.
		$.each( fxPrimaryTerm.taxonomies, function( index, tax ) {
			ProcessPrimaryTermList( tax );
		} );

		// Set primary.
		$( document ).on( 'click', '.fx-primary-term-set-primary', function(e) {
			var tax = $( this ).data( 'taxonomy' );
			var term_id = $( this ).data( 'term_id' );
			$( '#fx_primary_term_' + tax ).val( term_id );
			ProcessPrimaryTermList( tax );
		} );

	});

})( window );
