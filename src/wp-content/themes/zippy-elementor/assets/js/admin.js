(function($) {
    'use strict';

    $(function () {

        if(typeof pagenow !== "undefined" && pagenow === "widgets"){
            $( '.widget-liquid-right' ).append( bakerfresh_admin_vars.widget_info );

            var $create_box = $( '#la_pb_widget_area_create' ),
                $widget_name_input = $create_box.find( '#la_pb_new_widget_area_name' ),
                $la_pb_sidebars = $( 'div[id^=bakerfresh_widget_area_]' );

            $create_box.find( '.la_pb_create_widget_area' ).on('click', function( event ) {
                var $this_el = $(this);

                event.preventDefault();

                if ( $widget_name_input.val() === '' ) return;

                $.ajax( {
                    type: "POST",
                    url: bakerfresh_admin_vars.ajaxurl,
                    data:
                        {
                            action : 'bakerfresh_core_action',
                            router : 'add_sidebar',
                            admin_load_nonce : bakerfresh_admin_vars.admin_load_nonce,
                            widget_area_name : $widget_name_input.val()
                        },
                    success: function( data ){
                        if(data.success){
                            $this_el.closest( '#la_pb_widget_area_create' ).find( '.la_pb_widget_area_result' ).hide().html( data.data.message ).slideToggle();
                        }
                    }
                } );
            } );

            $la_pb_sidebars.each( function() {
                if ( $(this).is( '#la_pb_widget_area_create' ) || $(this).closest( '.inactive-sidebar' ).length ) return true;

                $(this).closest('.widgets-holder-wrap').find('.sidebar-name h2, .sidebar-name h3').before( '<a href="#" class="la_pb_widget_area_remove">' + bakerfresh_admin_vars.delete_string + '</a>' );

                $( '.la_pb_widget_area_remove' ).on('click' ,function( event ) {
                    var $this_el = $(this);

                    event.preventDefault();

                    if(confirm(bakerfresh_admin_vars.confirm_delete_string)){
                        $.ajax( {
                            type: "POST",
                            url: bakerfresh_admin_vars.ajaxurl,
                            data:
                                {
                                    action : 'bakerfresh_core_action',
                                    router : 'remove_sidebar',
                                    admin_load_nonce : bakerfresh_admin_vars.admin_load_nonce,
                                    widget_area_name : $this_el.closest( '.widgets-holder-wrap' ).find( 'div[id^=bakerfresh_widget_area_]' ).attr( 'id' )
                                },
                            success: function( data ){
                                if(data.success){
                                    $( '#' + data.data.sidebar_id ).closest( '.widgets-holder-wrap' ).remove();
                                }
                            }
                        } );
                    }

                    return false;
                } );
            } );
        }
    })

})(jQuery);