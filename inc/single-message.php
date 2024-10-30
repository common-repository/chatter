<?php
    $chatter_show_user_avatar = chatter_get_option( 'chatter_show_user_avatar' );
    $user_info                = get_userdata( $comment->user_id );
    $is_hebrew                = get_comment_meta( $comment->comment_ID, 'is_hebrew', true );
    $from_user_id             = get_post_meta( $comment->comment_post_ID, 'from_user_id', true );
    $is_comment_author = '';
    if( $from_user_id && (int)$from_user_id == (int)$comment->user_id ){
        $is_comment_author = 'is_comment_author';
    }
?>

<div class="chatter-message <?php echo $is_comment_author; ?>" tabindex="0"
    data-comment_id="<?php echo $comment->comment_ID; ?>" data-post_id="<?php echo $comment->comment_post_ID; ?>">

    <div class="chatter-message-dropdown">
        <button type="button" class="chatter-dropdown-button" aria-haspopup="true" tabindex="0">
            <span></span><span></span><span></span>
        </button>
        <div class="chatter-dropdown-menu" aria-hidden="true" aria-expanded="false">
            <ul role="menu">
                <li role="menuitem" tabindex="0" class="chatter-copy-text">
                    <button type="button" class="chatter-copy-text-btn"><?php _e('Copy message text', 'chatter'); ?></button>
                </li>
            </ul>
        </div>
    </div>

    <?php if( $chatter_show_user_avatar ) :
        $avatar_url = get_avatar_url( $comment->user_id ); ?>
        <div class="chatter-user-avatar">
            <img width="50" height="50" src="<?php echo $avatar_url; ?>">
        </div>
    <?php endif; ?>

    <div class="chatter-message-author">
        <?php echo $user_info->display_name; ?> <span class="date-time">[<?php echo get_comment_date( 'd/m/Y - H:i:s', $comment->comment_ID ); ?>]</span>
    </div>

    <div class="chatter-message-content <?php echo $is_hebrew ? 'is-rtl' : ''; ?>">
        <?php echo esc_html($comment->comment_content); ?>
    </div>

</div>
