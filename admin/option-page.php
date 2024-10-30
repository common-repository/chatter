<?php
    $chat_manager_users = get_users( [ 'role__in' => [ 'administrator', 'editor' ] ] );
    $chatter_hidden     = isset($_POST['chatter_hidden']) ? sanitize_text_field( $_POST['chatter_hidden'] ) : '';

    if( $chatter_hidden == 'Y' && !empty( $chatter_hidden ) ) {

        $chatter_header_title = isset( $_POST['chatter_header_title'] ) ? sanitize_text_field( $_POST['chatter_header_title'] ) : '';
        chatter_update_option( 'chatter_header_title', $chatter_header_title );

        $chatter_show_user_avatar = isset( $_POST['chatter_show_user_avatar'] ) ? true : false;
        chatter_update_option( 'chatter_show_user_avatar', $chatter_header_title );

        $chatter_refresh = isset( $_POST['chatter_refresh'] ) ? (int)$_POST['chatter_refresh'] : 10;
        chatter_update_option( 'chatter_refresh', $chatter_refresh );

        $chatter_manager_user = isset( $_POST['chatter_manager_user'] ) ? sanitize_text_field( $_POST['chatter_manager_user'] ) : '';
        chatter_update_option( 'chatter_manager_user', $chatter_manager_user );

        $chatter_loggedin_only_message = isset( $_POST['chatter_loggedin_only_message'] ) ? sanitize_text_field($_POST['chatter_loggedin_only_message']) : __('Please, login first', 'chatter');
        chatter_update_option( 'chatter_loggedin_only_message', $chatter_loggedin_only_message );

        $chatter_minimized = isset( $_POST['chatter_minimized'] ) ? true : false;
        chatter_update_option( 'chatter_minimized', $chatter_minimized );

        $chatter_disabled = isset( $_POST['chatter_disabled'] ) ? true : false;
        chatter_update_option( 'chatter_disabled', $chatter_disabled );

        $chatter_send_mail_to_chat_manager = isset( $_POST['chatter_send_mail_to_chat_manager'] ) ? true : false;
        chatter_update_option( 'chatter_send_mail_to_chat_manager', $chatter_send_mail_to_chat_manager );

        $chatter_manager_mail_body = ( isset( $_POST['chatter_manager_mail_body'] ) && $_POST['chatter_manager_mail_body'] ) ? wp_kses( $_POST['chatter_manager_mail_body'] ) : '';
        chatter_update_option( 'chatter_manager_mail_body', $chatter_manager_mail_body );

        $chatter_manager_mail_from = ( isset( $_POST['chatter_manager_mail_from'] ) && $_POST['chatter_manager_mail_from'] ) ? sanitize_email( $_POST['chatter_manager_mail_from'] ) : '';
        chatter_update_option( 'chatter_manager_mail_from', $chatter_manager_mail_from );

        $chatter_copy_message_text_label = isset( $_POST['chatter_copy_message_text_label'] ) ? sanitize_text_field( $_POST['chatter_copy_message_text_label'] ) : __('Copy message text', 'chatter');
        chatter_update_option( 'chatter_copy_message_text_label', $chatter_copy_message_text_label );

    } else {

        $chatter_header_title              = chatter_get_option( 'chatter_header_title' );
        $chatter_show_user_avatar          = chatter_get_option( 'chatter_show_user_avatar' );
        $chatter_refresh                   = chatter_get_option( 'chatter_refresh' );
        $chatter_manager_user              = chatter_get_option( 'chatter_manager_user' );
        $chatter_loggedin_only_message     = chatter_get_option( 'chatter_loggedin_only_message' );
        $chatter_minimized                 = chatter_get_option( 'chatter_minimized' );
        $chatter_disabled                  = chatter_get_option( 'chatter_disabled' );
        $chatter_send_mail_to_chat_manager = chatter_get_option( 'chatter_send_mail_to_chat_manager' );
        $chatter_manager_mail_body         = chatter_get_option( 'chatter_manager_mail_body' );
        $chatter_manager_mail_from         = chatter_get_option( 'chatter_manager_mail_from' );
        $chatter_copy_message_text_label   = chatter_get_option( 'chatter_copy_message_text_label' );

    }
?>

<div class="wrap chatter-admin-wrap">

    <?php chatter_admin_notice(); ?>

    <h1>Chatter [free version] - Wordpress chat with accessibility features built in!</h1>

    <hr>

    <form method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">

        <input type="hidden" name="chatter_hidden" value="Y">

        <h2><?php _e('Chatter general settings', 'chatter' ); ?></h2>

        <?php if( $chat_manager_users ) : ?>
            <div class="form-row">
                <div class="input-wrapper">
                    <label for="chatter_manager_user">
                        <span><?php _e('Select chat manager user', 'chatter'); ?></span>
                    </label>
                    <select name="chatter_manager_user" id="chatter_manager_user">
                        <option value=""><?php _e('Select chat manager', 'chatter'); ?></option>
                        <?php foreach( $chat_manager_users as $manager ) : ?>
                            <option value="<?php echo $manager->ID; ?>" <?php echo selected( $manager->ID, $chatter_manager_user ); ?>>
                                <?php echo $manager->user_nicename; ?> - <?php echo $manager->user_email; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        <?php endif; ?>

        <div class="form-row">
            <div class="input-wrapper">
                <label for="chatter_refresh">
                    <span><?php _e('Refresh chat every (seconds)', 'chatter'); ?></span>
                </label>
                <input type="number" name="chatter_refresh" id="chatter_refresh" value="<?php echo $chatter_refresh; ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="input-wrapper">
                <label for="chatter_disabled">
                    <span><?php _e('Disable Chatter?', 'chatter'); ?></span>
                </label>
                <input type="checkbox" name="chatter_disabled" id="chatter_disabled" <?php if( $chatter_disabled ) : ?>checked<?php endif; ?>>
            </div>
        </div>

        <div class="form-row">
            <div class="input-wrapper">
                <label for="chatter_show_user_avatar">
                    <span><?php _e('Display user avatar', 'chatter'); ?></span>
                </label>
                <input type="checkbox" name="chatter_show_user_avatar" id="chatter_show_user_avatar" <?php if( $chatter_show_user_avatar ): ?>checked<?php endif; ?>>
            </div>
        </div>

        <div class="form-row">
            <div class="input-wrapper">
                <label for="chatter_minimized">
                    <span><?php _e('Load Chatter as closed box', 'chatter'); ?></span>
                </label>
                <input type="checkbox" name="chatter_minimized" id="chatter_minimized" <?php if( $chatter_minimized ): ?>checked<?php endif; ?>>
            </div>
        </div>

        <div class="form-row">
            <div class="input-wrapper">
                <label for="chatter_send_mail_to_chat_manager">
                    <span><?php _e('Send email notification to the chat manager when new chat has been created', 'chatter'); ?></span>
                </label>
                <input type="checkbox" name="chatter_send_mail_to_chat_manager" id="chatter_send_mail_to_chat_manager" <?php if( $chatter_send_mail_to_chat_manager ): ?>checked<?php endif; ?>>
            </div>
        </div>

        <div class="form-row" data-rel="chatter_send_mail_to_chat_manager"
            <?php if( !$chatter_send_mail_to_chat_manager ): ?>style="display:none;"<?php endif; ?>>
            <div class="input-wrapper">
                <label for="chatter_manager_mail_body">
                    <span><?php _e('Email body', 'chatter'); ?></span>
                </label>
                <?php
                    $settings = array(
                        'teeny'         => true,
                        'tinymce'       => true,
                        'textarea_rows' => 10,
                        'tabindex'      => 1,
                        'media_buttons' => false
                    );
                    wp_editor( $chatter_manager_mail_body, 'chatter_manager_mail_body', $settings);
                ?>
            </div>
            <p class="field-description">To display the manager name inside the email body please put <code>%%manager%%</code> tag.</p>
        </div>

        <div class="form-row" data-rel="chatter_send_mail_to_chat_manager"
            <?php if( !$chatter_send_mail_to_chat_manager ): ?>style="display:none;"<?php endif; ?>>
            <div class="input-wrapper">
                <label for="chatter_manager_mail_from">
                    <span><?php _e('Email from', 'chatter'); ?></span>
                </label>
                <input type="email" name="chatter_manager_mail_from" id="chatter_manager_mail_from" value="<?php echo $chatter_manager_mail_from; ?>">
            </div>
        </div>

        <hr>
        <h2><?php _e('Labels (translation)', 'chatter' ); ?></h2>

        <div class="form-row">
            <div class="input-wrapper">
                <label for="chatter_header_title">
                    <span><?php _e('Chatter header title', 'chatter'); ?></span>
                </label>
                <input type="text" name="chatter_header_title" id="chatter_header_title" value="<?php echo $chatter_header_title; ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="input-wrapper">
                <label for="chatter_loggedin_only_message">
                    <span><?php _e('Please, login first message', 'chatter'); ?></span>
                </label>
                <input type="text" name="chatter_loggedin_only_message" id="chatter_loggedin_only_message" value="<?php echo $chatter_loggedin_only_message; ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="input-wrapper">
                <label for="chatter_copy_message_text_label">
                    <span><?php _e('Copy message text label', 'chatter'); ?></span>
                </label>
                <input type="text" name="chatter_copy_message_text_label" id="chatter_copy_message_text_label" value="<?php echo $chatter_copy_message_text_label; ?>">
            </div>
        </div>

        <?php submit_button(); ?>
    </form>

</div>
