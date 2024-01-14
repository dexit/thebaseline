/*
 * Master Log Tab
 */
jQuery( function ( $ ) {
    'use strict' ;
    var MasterLogScript = {
        init : function () {
            this.show_or_hide_for_user_type() ;
            $( document ).on( 'change' , '.rs_export_import_masterlog_option' , this.show_or_hide_for_user_type ) ;
            $( document ).on( 'click' , '#rs_export_master_log_csv' , this.export_log_as_csv ) ;
        } ,
        show_or_hide_for_user_type : function () {
            if ( ( $( 'input[name="rs_export_import_masterlog_option"]:checked' ).val() ) === '2' ) {
                $( '#rs_export_masterlog_users_list' ).parent().parent().show() ;
            } else {
                $( '#rs_export_masterlog_users_list' ).parent().parent().hide() ;
            }
        } ,
        export_log_as_csv : function () {
            var $block = $( this ).closest( '.rs_modulecheck_wrapper' ) ;
            MasterLogScript.block( $block ) ;
            var data = ( {
                action : 'export_log' ,
                usertype : $( "input:radio[name=rs_export_import_masterlog_option]:checked" ).val() ,
                selecteduser : $( "#rs_export_masterlog_users_list" ).val() ,
                sumo_security : fp_masterlog_params.fp_export_log ,
            } ) ;
            $.post( fp_masterlog_params.ajaxurl , data , function ( response ) {
                if ( true === response.success ) {
                    window.location.href = response.data.redirect_url ;
                } else {
                    window.alert( response.data.error ) ;
                }
                MasterLogScript.unblock( $block ) ;
            } ) ;
        } ,
        block : function ( id ) {
            $( id ).block( {
                message : null ,
                overlayCSS : {
                    background : '#fff' ,
                    opacity : 0.6
                }
            } ) ;
        } ,
        unblock : function ( id ) {
            $( id ).unblock() ;
        } ,
    } ;
    MasterLogScript.init() ;
} ) ;