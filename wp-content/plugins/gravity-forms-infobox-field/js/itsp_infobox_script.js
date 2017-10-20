jQuery( function ( $ ) {
	$( '[class*=gfield_infobox_more_info_]' ).on( 'click', function(e) {
		$( this ).next().toggleClass( 'gfield_infobox_more_display' );
	});
});