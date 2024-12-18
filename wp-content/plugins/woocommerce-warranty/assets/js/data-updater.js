jQuery( document ).ready( function( $ ) {
	var progressbar     = $( '#progressbar' ),
	    progressLabel   = $( '.progress-label' ),
	    total_processed = 0,
	    total_items     = 0,
	    session         = '',
	    log             = $( '#log' );

	progressbar.progressbar( {
		value: false,
		change: function() {
			progressLabel.text( progressbar.progressbar( 'value' ) + '%' );
		}, complete: function() {
			progressLabel.text( 'Update complete!' );
			$( 'body' ).trigger( 'progress-complete' );
		},
	} );

	// initialize the import
	( function() {
		var body = $( 'body' );

		// attach the event listeners
		body.on( 'update_init_completed', update_data );
		body.on( 'update_completed', update_completed );

		const params = $.extend( wcWarrantyUpdaterPage.ajax_params || {}, {
			'cmd': 'start', 
			'action': wcWarrantyUpdaterPage.ajax_endpoint,
			'woo_nonce': wcWarrantyUpdaterPage.ajax_nonce,
		} );

		$( '#total-items-label' ).html( 'Scanning data. This may take a few minutes.' );

		$.post( ajaxurl, params, function( resp ) {
			if ( !resp ) {
				alert( 'There was an error executing the request. Please try again later.' );
			} else {
				session = resp.update_session;
				total_items = resp.total_items;

				$( '#total-items-label' )
					.html( 'Total ' + wcWarrantyUpdaterPage.entity_label_plural + ': ' + total_items );

				update_progressbar( 0 );

				$( 'body' ).trigger( 'update_init_completed' );

			}

		} );
	} )();

	function update_data() {
		const params = $.extend( wcWarrantyUpdaterPage.ajax_params || {}, {
			'action': wcWarrantyUpdaterPage.ajax_endpoint,
			'woo_nonce': wcWarrantyUpdaterPage.ajax_nonce,
			'cmd': 'update',
			'update_session': session,
		} );

		xhr = $.post( ajaxurl, params, function( resp ) {
			if ( resp.error ) {
				$( '#log' )
					.append( '<p class="failure"><span class="dashicons dashicons-no"></span> Error: ' + resp.error + '</p>' );
			} else {
				if ( 'partial' === resp.status ) {
					log_import_data( resp.update_data );

					// update the progress bar and execute again
					var num_processed = resp.update_data.length;

					total_processed = total_processed + num_processed;
					var progress_value = ( total_processed / total_items ) * 100;
					update_progressbar( progress_value );

					update_data();
				} else if ( 'completed' === resp.status ) {
					log_import_data( resp.update_data );

					$( 'body' ).trigger( 'update_completed' );
				}
			}

		} );

	}

	function update_completed() {
		updating_complete();
	}

	function update_progressbar( value ) {
		progressbar.progressbar( 'value', Math.ceil( value ) );
	}

	function log_import_data( data ) {
		for ( var x = 0; x < data.length; x ++ ) {
			var row;
			var id = data[x].id;

			if ( 'success' === data[x].status ) {
				row = '<p class="success"><span class="dashicons dashicons-yes"></span> ' + ( data[x].status_text || wcWarrantyUpdaterPage.entity_label_singular + ' #' + id + ' ' + wcWarrantyUpdaterPage.action_label ) + '</p>';
			} else {
				row = '<p class="failure"><span class="dashicons dashicons-no"></span> ' + ( data[x].status_text || wcWarrantyUpdaterPage.entity_label_singular + ' #' + id + ' - ' + data[x].reason ) + '</p>';
			}

			log.append( row );

			var height = log[0].scrollHeight;
			log.scrollTop( height );

		}
	}

	function updating_complete() {
		update_progressbar( 100 );
		if ( 0 === log.find( 'a.return_link' ).length ) {
			log.append( '<div class="updated"><p>All done! <a href="#" class="return_link">Go back</a></p></div>' );
			var height = log[0].scrollHeight;
			log.scrollTop( height );
			$( '.return_link' ).attr( 'href', wcWarrantyUpdaterPage.return_url );
		}
	}
} );
