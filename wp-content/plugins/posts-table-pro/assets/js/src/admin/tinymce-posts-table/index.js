( function( window, document, undefined ) {
	"use strict";

	tinymce.PluginManager.add( 'poststablepro', function( editor, url ) {
		// Add posts table button to visual editor toolbar
		editor.addButton( 'poststablepro', {
			title: 'Insert Posts Table',
			cmd: 'insertPostsTable',
			icon: 'dashicon dashicons-editor-table'
		} );

		editor.addCommand( 'insertPostsTable', function() {
			editor.execCommand( 'mceInsertContent', false, '[posts_table]' );
			return;
		} );

	} );

} )( window, document );