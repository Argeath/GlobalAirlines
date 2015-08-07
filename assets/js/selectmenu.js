(function( $, undefined ) {

// We use this in a few select menu methods, so no need to'
// define it for every instance.
    var iconClass = "flag flag-";

    $.widget( "ab.selectmenu", $.ui.selectmenu, {

        // Adding the new item icons flag.
        options: {
            itemIcons: false
        },

        // Constructor.  First calls the real constructor.
        _create: function() {

            this._super();

            if ( !this.options.itemIcons ) {
                return;
            }

            // Select menu uses a menu widget internally,
            // and needs this class in order to work with
            // icons.
            this.menu.addClass( "ui-menu-icons" );

            var $element = this.element,
                $button  = this.button,
                iconData = $element.find( "option:selected" )
                    .data( "icon" );

            // Adds the icon for the selected option, using
            // the icon data attribute.
            $( "<span/>" ).addClass( iconClass + iconData )
                .css( "left", "0.4em" )
                .appendTo( $button );

            // Adjust the text of the selected option, making
            // room for the icon.
            $button.find( ".ui-selectmenu-text" )
                .css( "padding-left", "24px" );

        },

        // Renders the icon for each item in the drop-down.
        _renderItem: function( ul, item ) {

            // This is the rendered li element before we attach
            // an icon to it.
            var result = this._super( ul, item );

            if ( !this.options.itemIcons ) {
                return result;
            }

            var iconData  = item.element.data( "icon" ),
                $itemText = result.find( "a" )
                    .css( "padding-left", "2em" );

            // Adds the icon to the rendered item.
            $( "<span/>" ).addClass( iconClass + iconData )
                .appendTo( $itemText );

            return result;

        },

        // Updates the selected item icon.
        _select: function( item, event ) {

            this._super( item, event );

            if ( !this.options.itemIcons ) {
                return;
            }

            var iconData = item.element.data( "icon" ),
                $icon = this.button.find( "span.ui-icon:last" );

            // Remove any old icon classes and add the newly
            // selected one.
            $icon.removeClass()
                .addClass( iconClass + iconData );

        }

    });

})( jQuery );