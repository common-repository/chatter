<?php
// Add the custom columns to the Chatter post type:
add_filter( 'manage_chatter_posts_columns', 'set_custom_edit_chatter_columns' );
function set_custom_edit_chatter_columns($columns) {
    $columns['from_user_id']          = __( 'From user', 'chatter' );
    $columns['to_user_id']            = __( 'To user', 'chatter' );
    $columns['chatter_status']        = __( 'Status', 'chatter' );
    return $columns;
}

// Add the data to the custom columns for the Chatter post type:
add_action( 'manage_chatter_posts_custom_column' , 'custom_chatter_column', 10, 2 );
function custom_chatter_column( $column, $post_id ) {
    switch ( $column ) {

        case 'from_user_id' :
            $from_user = get_user_by('ID', get_post_meta( $post_id, 'from_user_id', true ) );
            echo '<a href="'.get_edit_user_link(get_post_meta( $post_id, "from_user_id", true )).'">'.$from_user->user_email.'</a>';
            break;

        case 'to_user_id' :
            $to_user = get_user_by('ID', get_post_meta( $post_id, 'to_user_id', true ) );
            echo '<a href="'.get_edit_user_link(get_post_meta( $post_id, "to_user_id", true )).'">'.$to_user->user_email.'</a>';
            break;

        case 'chatter_status' :
            $chatter_status = get_post_meta( $post_id, 'chatter_status', true );
            if( $chatter_status == 'active' ){
                $color = 'green';
            } else {
                $color = 'red';
            }
            echo '<span style="font-weight:bold;color:'.$color.';">'.$chatter_status.'<span>';
            break;

    }
}

add_action( 'add_meta_boxes', 'chatter_register_meta_boxes' );
function chatter_register_meta_boxes() {
    add_meta_box( 'chatter-settings-box-id', __( 'Chatter settings', 'textdomain' ), 'chatter_settings_metabox_callback', 'chatter', 'side' );
}

function chatter_settings_metabox_callback( $post ) {
    $chatter_status = get_post_meta( $post->ID, 'chatter_status', true );
    $chatter_manager_notes = get_post_meta( $post->ID, 'chatter_manager_notes', true );
    ?>
    <label class="select-in">
        <span><?php _e('Status', 'chatter' ); ?></span>
        <select class="" name="chatter_status">
            <option value="active" <?php if( $chatter_status == 'active' ) : ?>selected<?php endif; ?>><?php _e('Active', 'chatter'); ?></option>
            <option value="closed" <?php if( $chatter_status == 'closed' ) : ?>selected<?php endif; ?>><?php _e('Closed', 'chatter'); ?></option>
        </select>
    </label>

    <label class="textarea-in">
        <span><?php _e('Chat manager notes', 'chatter' ); ?></span>
        <textarea name="chatter_manager_notes" style="width:100%; height:120px;"><?php echo $chatter_manager_notes; ?></textarea>
    </label>
<?php }

add_action( 'save_post', 'chatter_save_meta_box' );
function chatter_save_meta_box( $post_id ) {
    if( isset( $_POST['chatter_status'] ) && $_POST['chatter_status'] ) {
        update_post_meta( $post_id, 'chatter_status', sanitize_text_field( $_POST['chatter_status'] ) );
    }
    if( isset( $_POST['chatter_manager_notes'] ) && $_POST['chatter_manager_notes'] ) {
        update_post_meta( $post_id, 'chatter_manager_notes', sanitize_textarea_field( $_POST['chatter_manager_notes'] ) );
    }
}
