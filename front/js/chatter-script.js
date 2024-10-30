jQuery(document).on('click', '.chatter-dropdown-button', function(e){
    e.preventDefault();
    var parent_item   = jQuery(this).parents('.chatter-message-dropdown');
    var dropdown_menu = parent_item.find('.chatter-dropdown-menu');

    if( dropdown_menu.attr('aria-hidden') == "true" ){
        dropdown_menu.attr('aria-hidden', 'false');
    } else {
        dropdown_menu.attr('aria-hidden', 'true');
    }

    if( dropdown_menu.attr('aria-expanded') == "true" ){
        dropdown_menu.attr('aria-expanded', 'false');
    } else {
        dropdown_menu.attr('aria-expanded', 'true');
    }
});

jQuery(document).on('click', '.chatter-copy-text-btn', function(e){
    e.preventDefault();
    var _this = jQuery(this);
    var parent_item = _this.parents('.chatter-message');
    var message_txt = parent_item.find('.chatter-message-content').text();
    chatterCopyToClipboard(message_txt);

    setTimeout( function(){
        _this.addClass('copied');
        setTimeout( function(){
            _this.removeClass('copied');
        }, 800 );
    }, 200 );
});

jQuery(document).ready( function(){

    if( typeof chatterObject !='undefined' ){
        if( chatterObject.start_minimized ){
            toggle_chatter_box();
        }
    }

    jQuery('textarea[name="chatter_message_body"]').keydown(function (e) {
        if (e.ctrlKey && e.keyCode == 13) {
            // Ctrl-Enter pressed
            jQuery('#post_new_chat_message').submit();
        }
    });

    load_chatter_messages();

    jQuery('.preventDefault').on('click', function(e){
        e.preventDefault();
    });

    jQuery('.toggle-chatter-box').on('click', function(e){
        e.preventDefault();
        toggle_chatter_box();
    });

    jQuery('#post_new_chat_message').on('submit', function(e){
        e.preventDefault();
        var _this = jQuery(this);
        _this.find('button[type="submit"]').prop('disabled', true );
        var chatter_message_body = jQuery('textarea[name="chatter_message_body"]');
        if( chatter_message_body.val() ){
            chatter_message_body.removeClass('chatter-input-error');
            var form = jQuery(this).serialize();
            // hebrew support
            var txt       = chatter_message_body.val();
            var position  = txt.search(/[\u0590-\u05FF]/);
            var is_hebrew = 0;
            if( position == 0){
                is_hebrew = 1;
            }
            chatter_post_new_chat_message( form, is_hebrew );
        } else {
            chatter_message_body.addClass('chatter-input-error');
        }

    });

    if( jQuery('select[name="chatter-users-select"]').length ){
        jQuery('select[name="chatter-users-select"]').on('change', function(e){
            e.preventDefault();
            var post_id = jQuery(this).find('option:selected').attr('data-postid') ? jQuery(this).find('option:selected').attr('data-postid') : '';
            var user_id = jQuery(this).val() ? jQuery(this).val() : '';
            jQuery('.chatter-box-messages').addClass('loading');
            load_chatter_messages_by_user( user_id, post_id );
        });
    }

    if( typeof chatterObject !='undefined' ){
        setInterval(function() {
            refresh_chatter_messages();
        }, parseInt(chatterObject.chatter_refresh)*1000 );
    }

});

function load_chatter_messages_by_user( user_id, post_id ){
    jQuery.ajax({
        type     : "post",
        dataType : "json",
        url      : chatterObject.ajax_url,
        data     : {
            action  : "load_chatter_messages_by_user",
            user_id : user_id,
            post_id : post_id
        },
        success  : function(response) {
            jQuery('.chatter-box-messages').removeClass('loading');
            jQuery('.chatter-box-messages').html('');
            if( response.post_id ){
                jQuery('#post_new_chat_message input[name="chatter_post_id"]').val( response.post_id );
            }
            if( response.edit_chat_admin_link ){
                var edit_link     = response.edit_chat_admin_link.replace("amp;", "");
                var edit_link_tpl = '<small><a href="'+edit_link+'" class="edit_chat_admin_link" target="_blank">(edit chat)</a></small>';
                jQuery('#chatter-box .chatter-box-header h3 span').html(edit_link_tpl);
            } else {
                jQuery('#chatter-box .chatter-box-header h3 span').html('');
            }
            append_message_row_to_chatter( response.html );
            scroll_chatter_down();
        }
    });
}

function append_message_row_to_chatter( html ){
    if( jQuery('.chatter-box-messages').length ){
        jQuery('.chatter-box-messages').append(html);
    }
}

function clear_chatter_message_input(){
    jQuery('textarea[name="chatter_message_body"]').val('');
}

function chatter_post_new_chat_message( form, is_hebrew ){
    if( typeof is_hebrew == 'undefined' ){
        is_hebrew = false;
    }
    jQuery.ajax({
        type     : "post",
        dataType : "json",
        url      : chatterObject.ajax_url,
        data     : {
            action    : "chatter_post_new_chat_message",
            form      : form,
            is_hebrew : is_hebrew
        },
        success  : function(response) {
            if( response.html ){
                append_message_row_to_chatter( response.html );
                clear_chatter_message_input();
                scroll_chatter_down();
                if( response.post_id ){
                    jQuery('#post_new_chat_message input[name="chatter_post_id"]').val( response.post_id );
                }
                jQuery('#post_new_chat_message').find('button[type="submit"]').prop('disabled', false );
            }
        }
    });
}

function scroll_chatter_down(){
    jQuery('.chatter-box-messages').scrollTop( jQuery('.chatter-box-messages')[0].scrollHeight );
}

function refresh_chatter_messages(){
    jQuery.ajax({
        type     : "post",
        dataType : "json",
        url      : chatterObject.ajax_url,
        data     : {
            action        : "refresh_chatter_messages",
            post_id       : jQuery('#post_new_chat_message input[name="chatter_post_id"]').val(),
            current_count : jQuery('.chatter-box-container-inner .chatter-box-messages .chatter-message').length
        },
        success  : function(response) {

            if( chatterObject.is_chatter_manager ){

                if( jQuery('select[name="chatter-users-select"] option:selected').attr('data-postid') == response.post_id ){
                    if( response.html ){
                        jQuery('.chatter-box-messages').html( response.html );
                        // scroll chatterbox down
                        setTimeout( function(){
                            scroll_chatter_down();
                        });
                    }
                }

            } else {

                if( response.html ){
                    jQuery('.chatter-box-messages').html( response.html );
                    // scroll chatterbox down
                    setTimeout( function(){
                        scroll_chatter_down();
                    });
                }
            }

            if( response.post_id ){
                jQuery('#post_new_chat_message input[name="chatter_post_id"]').val( response.post_id );
            }
        }
    });
}

function load_chatter_messages(){
    jQuery.ajax({
        type     : "post",
        dataType : "json",
        url      : chatterObject.ajax_url,
        data     : {
            action : "load_chatter_messages"
        },
        success  : function(response) {
            if( response.html ){
                jQuery('.chatter-box-messages').html( response.html );
            }
            if( response.post_id ){
                jQuery('#post_new_chat_message input[name="chatter_post_id"]').val( response.post_id );
            } else {
                jQuery('#post_new_chat_message input[name="chatter_post_id"]').val( '' );
            }
        }
    });
}

function toggle_chatter_box(){
    var chatter_box_status = jQuery('.chatter-box-inner').attr('aria-hidden');
    var chatter_box_height = jQuery('.chatter-box-inner').height();

    if( chatter_box_status == 'false' ){
        jQuery('.chatter-box-inner').attr('aria-hidden', 'true');
        jQuery('#chatter-box').css('bottom', - chatter_box_height ).addClass('chatter-hidden');
        jQuery('.toggle-chatter-box span').html('+');
    } else {
        jQuery('.chatter-box-inner').attr('aria-hidden', 'false');
        jQuery('.toggle-chatter-box span').html('-');
        jQuery('#chatter-box').css('bottom', 5 ).removeClass('chatter-hidden');
        scroll_chatter_down();
    }
}

function chatterCopyToClipboard(str) {
    const el = document.createElement('textarea');  // Create a <textarea> element
    el.value = str;                                 // Set its value to the string that you want copied
    el.setAttribute('readonly', '');                // Make it readonly to be tamper-proof
    el.style.position = 'absolute';
    el.style.left = '-9999px';                      // Move outside the screen to make it invisible
    document.body.appendChild(el);                  // Append the <textarea> element to the HTML document
    const selected =
    document.getSelection().rangeCount > 0        // Check if there is any content selected previously
      ? document.getSelection().getRangeAt(0)     // Store selection if found
      : false;                                    // Mark as false to know no selection existed before
    el.select();                                    // Select the <textarea> content
    document.execCommand('copy');                   // Copy - only works as a result of a user action (e.g. click events)
    document.body.removeChild(el);                  // Remove the <textarea> element
    if (selected) {                                 // If a selection existed before copying
        document.getSelection().removeAllRanges();    // Unselect everything on the HTML document
        document.getSelection().addRange(selected);   // Restore the original selection
    }
};
