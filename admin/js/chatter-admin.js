jQuery(document).ready( function(){
    console.log('chatter admin script init');

    jQuery('#chatter_send_mail_to_chat_manager').on( 'change', function(){
        if( jQuery(this).is(':checked') ){
            console.log('checked');
            jQuery('div[data-rel="chatter_send_mail_to_chat_manager"]').show();
        } else {
            jQuery('div[data-rel="chatter_send_mail_to_chat_manager"]').hide();
        }
    });
});
