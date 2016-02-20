jQuery(document).ready(function($){
	$("a[data-upvote-id]").click(function(){
		var id = $(this).attr('data-upvote-id');
		$.ajax( {
				url: WP_API_Settings.root + 'goodmorning-news/1.0/upvote/' + id,
				method: 'GET',
				beforeSend: function ( xhr ) {
						xhr.setRequestHeader( 'X-WP-Nonce', WP_API_Settings.nonce );
				}
			} ).done( function ( response ) {
				console.log( response );
			} );
	});

	$("a[data-downvote-id]").click(function(){
		var id = $(this).attr('data-downvote-id');
		$.ajax( {
				url: WP_API_Settings.root + 'goodmorning-news/1.0/downvote/' + id,
				method: 'GET',
				beforeSend: function ( xhr ) {
						xhr.setRequestHeader( 'X-WP-Nonce', WP_API_Settings.nonce );
				}
		} ).done( function ( response ) {
			console.log( response );
		} );
	});
});