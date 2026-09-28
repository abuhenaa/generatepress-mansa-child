(function (window, document, $) {
	'use strict';

	var frame;

	function createGalleryItem( id, url ) {
		var removeLabel = ( window.mansaChildI18n && window.mansaChildI18n.removeImage ) ? window.mansaChildI18n.removeImage : 'Remove image';
		return (
			'<div class="mansa-meta-gallery__item" data-id="' + id + '">' +
				'<img src="' + url + '" data-id="' + id + '" />' +
				'<button type="button" class="mansa-meta-gallery__remove" title="' + removeLabel + '">&times;</button>' +
			'</div>'
		);
	}

	function updateGalleryPreview( ids ) {
		var $preview = $( '#mansa-product-gallery-preview' );
		$preview.empty();

		ids.forEach( function ( id ) {
			var attachment = wp.media.attachment( id );
			if ( attachment && attachment.attributes && attachment.attributes.url ) {
				var url = ( attachment.attributes.sizes && attachment.attributes.sizes.thumbnail ) ? attachment.attributes.sizes.thumbnail.url : attachment.attributes.url;
				$preview.append( createGalleryItem( id, url ) );
			} else if ( attachment ) {
				attachment.fetch().done( function () {
					var url = ( attachment.attributes.sizes && attachment.attributes.sizes.thumbnail ) ? attachment.attributes.sizes.thumbnail.url : attachment.attributes.url;
					if ( url && !$preview.find( '.mansa-meta-gallery__item[data-id="' + id + '"]' ).length ) {
						$preview.append( createGalleryItem( id, url ) );
					}
				} );
			}
		} );
	}

	function parseGalleryField() {
		var val = $( '#mansa-product-gallery' ).val();
		return val ? val.split( ',' ).map( function (id) { return parseInt( id, 10 ); } ).filter( Boolean ) : [];
	}

	function updateGalleryField( ids ) {
		$( '#mansa-product-gallery' ).val( ids.join( ',' ) );
		updateGalleryPreview( ids );
	}

	function createBuyLinkRow( label, url ) {
		var index = $( '#mansa-buy-links .mansa-buy-link-row' ).length;
		var $row = $(
			'<div class="mansa-buy-link-row">' +
				'<input type="text" name="mansa_buy_links[' + index + '][label]" value="' + ( label || '' ) + '" placeholder="' + window.mansaChildI18n.label + '" />' +
				'<input type="url" name="mansa_buy_links[' + index + '][url]" value="' + ( url || '' ) + '" placeholder="' + window.mansaChildI18n.url + '" />' +
				'<button type="button" class="button mansa-buy-link-remove">&times;</button>' +
			'</div>'
		);
		return $row;
	}

	$( document ).ready( function () {
		$( '#mansa-product-gallery-button' ).on( 'click', function ( e ) {
			e.preventDefault();

			var initialSelection = parseGalleryField();

			if ( frame ) {
				frame.open();
				return;
			}

			frame = wp.media({
				title: window.mansaChildI18n.selectImages,
				button: { text: window.mansaChildI18n.useSelected },
				multiple: true,
				library: { type: 'image' }
			});

			frame.on( 'open', function () {
				var selection = frame.state().get( 'selection' );
				initialSelection.forEach( function ( id ) {
					var attachment = wp.media.attachment( id );
					attachment.fetch();
					selection.add( attachment );
				} );
			} );

			frame.on( 'select', function () {
				var selection = frame.state().get( 'selection' );
				var ids = [];
				selection.each( function ( attachment ) {
					ids.push( attachment.id );
				} );
				updateGalleryField( ids );
			} );

			frame.open();
		} );

		// Remove individual gallery image
		$( document ).on( 'click', '.mansa-meta-gallery__remove', function ( e ) {
			e.preventDefault();
			var $item = $( this ).closest( '.mansa-meta-gallery__item' );
			var idToRemove = parseInt( $item.data( 'id' ), 10 );
			var currentIds = parseGalleryField();
			var newIds = currentIds.filter( function ( id ) {
				return id !== idToRemove;
			} );
			$( '#mansa-product-gallery' ).val( newIds.join( ',' ) );
			$item.remove();
		} );

		$( document ).on( 'click', '.mansa-buy-link-remove', function ( e ) {
			e.preventDefault();
			$( this ).closest( '.mansa-buy-link-row' ).remove();
		} );

		$( '#mansa-add-buy-link' ).on( 'click', function ( e ) {
			e.preventDefault();
			var $row = createBuyLinkRow( '', '' );
			$( '#mansa-buy-links' ).append( $row );
		} );
	} );
})( window, document, jQuery );
