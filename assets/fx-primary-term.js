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
		fxPrimaryTerm.ProcessPrimaryTermList = function( tax ) {
			var metaBox = $( '#' + tax + 'div' );

			// Check if list exists and supported.
			if ( 'undefined' === metaBox || $.inArray( tax, fxPrimaryTerm.taxonomies ) < 0 ) {
				return;
			}

			var list = $( '#' + tax + 'div ul#' + tax + 'checklist, #' + tax + 'div ul#' + tax + 'checklist-pop' );
			var primary_field = $( '#fx_primary_term_' + tax );
			var primary_id = primary_field.val();

			// Mark this list using class.
			list.addClass( 'fx-primary-term-taxonomy-list' );

			// Process the list.
			list.find( 'li' ).each( function( index ) {
				var input = $( this ).find( 'input[type="checkbox"]' );
				var term_id = input.val();

				// Add class.
				$( this ).addClass( 'fx-primary-term-taxonomy-list-item' );

				// Add checked data.
				if ( input.is( ':checked' ) ) {
					$( this ).attr( 'data-checked', '1' );
				} else {
					$( this ).attr( 'data-checked', '0' );
				}

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
			fxPrimaryTerm.ProcessPrimaryTermList( tax );
		} );

		/**
		 * Process list after adding new term.
		 * @link https://github.com/WordPress/WordPress/blob/4.9.1/wp-includes/js/wp-lists.js#L375
		 */
		$( document ).on( 'wpListAddEnd', function( settings, list ) {
			fxPrimaryTerm.ProcessPrimaryTermList( list.what );
		} );

		// Set primary.
		$( document ).on( 'click', '.fx-primary-term-set-primary', function(e) {
			var tax = $( this ).data( 'taxonomy' );
			var term_id = $( this ).data( 'term_id' );
			$( '#fx_primary_term_' + tax ).val( term_id );
			fxPrimaryTerm.ProcessPrimaryTermList( tax );
		} );

		// Switch "All" and "Most Used" Tabs.
		$( document ).on( 'click', '.category-tabs a', function(e) {
			var tax = $( this ).parents( 'ul' ).attr( 'id' ).replace( '-tabs', '' );
			fxPrimaryTerm.ProcessPrimaryTermList( tax );
		} );

		// Checked/Unchecked Class.
		$( document ).on( 'change', '.fx-primary-term-taxonomy-list input[type="checkbox"]', function(e) {
			if ( $( this ).is( ':checked' ) ) {
				$( this ).parents( 'li.fx-primary-term-taxonomy-list-item' ).attr( 'data-checked', '1' );
			} else {
				$( this ).parents( 'li.fx-primary-term-taxonomy-list-item' ).attr( 'data-checked', '0' );
			}
		} );

	});

})( window );
