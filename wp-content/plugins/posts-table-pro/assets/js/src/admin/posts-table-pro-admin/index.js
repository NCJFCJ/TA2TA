( function( $, window, document, undefined ) {
    "use strict";

    let toggleChildSettings = function( $parent, $children ) {
        let showChildren = false,
            toggleValue = $parent.data( 'toggleValue' );

        if ( 'radio' === $parent.attr( 'type' ) ) {
            showChildren = $parent.prop( 'checked' ) && toggleValue == $parent.val();
        } else if ( 'checkbox' === $parent.attr( 'type' ) ) {
            if ( typeof toggleValue === 'undefined' || 1 == toggleValue ) {
                showChildren = $parent.prop( 'checked' );
            } else {
                showChildren = !$parent.prop( 'checked' );
            }
        } else {
            showChildren = ( toggleValue == $parent.val() );
        }

        $children.toggle( showChildren );
    };

    $( function() {
        $( '.color-picker' ).wpColorPicker();

        $( '.form-table .toggle' ).each( function() {
            let $parent = $( this ),
                $children = $parent.closest( '.form-table' ).find( '.' + $parent.data( 'toggleClass' ) );

            toggleChildSettings( $parent, $children );

            $parent.on( 'change', function() {
                toggleChildSettings( $parent, $children );
            } );
        } );

    } );

} )( jQuery, window, document );

