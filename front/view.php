<?php
if ( chatter_get_option( 'chatter_disabled' ) ) {
	return;
}
	global $post;
	$chatter_header_title          = chatter_get_option( 'chatter_header_title' );
	$chatter_show_user_avatar      = chatter_get_option( 'chatter_show_user_avatar' );
	$chatter_refresh               = chatter_get_option( 'chatter_refresh' );
	$chatter_manager_user          = chatter_get_option( 'chatter_manager_user' );
	$chatter_loggedin_only_message = chatter_get_option( 'chatter_loggedin_only_message' );

	$source_post_id = $post->ID;
if ( is_category() ) {
	$source_post_id = 'termid_' . get_queried_object_id();
}
?>

<div id="chatter-box" class="chatter-box-wrapper">

	<button type="button" class="toggle-chatter-box">
		<span>+</span>
	</button>

	<div class="chatter-box-inner" aria-hidden="false">

		<div class="chatter-box-header">
			<h3>
				<?php echo $chatter_header_title; ?>
				<?php if ( ( is_user_logged_in() && $chatter_manager_user && ( $chatter_manager_user == get_current_user_id() ) ) || current_user_can( 'administrator' ) ) : ?>
					<span></span>
				<?php endif; ?>
			</h3>
		</div>

		<div class="chatter-box-container">
			<div class="chatter-box-container-inner">

				<?php if ( is_user_logged_in() && $chatter_manager_user && ( $chatter_manager_user == get_current_user_id() ) ) : ?>
					<div class="chatter-manager-box">
						<div class="chatter-users-selection">
							<?php
								$chatter_prev_posts = get_chatter_posts_users();
							?>
							<select name="chatter-users-select">
								<option value=""><?php _e( 'Select chat', 'chatter' ); ?></option>
								<?php foreach ( $chatter_prev_posts as $item ) : ?>
									<option value="<?php echo $item['user_id']; ?>" data-postid="<?php echo $item['post_id']; ?>">
										<?php echo $item['option']; ?>
									</option>
								<?php endforeach; ?>
							</select>
						</div>
					</div>
				<?php endif; ?>

				<div class="chatter-box-messages
				<?php
				if ( is_user_logged_in() && $chatter_manager_user && ( $chatter_manager_user == get_current_user_id() ) ) :
					?>
					chatter-manager-box-on<?php endif; ?>">
				</div>

				<div class="chatter-box-submit-message">
					<?php if ( is_user_logged_in() ) : ?>
						<form method="post" id="post_new_chat_message">
							<textarea name="chatter_message_body" autocomplete="off" rows="8" cols="80" placeholder="<?php _e( 'Write your message here', 'chatter' ); ?>"></textarea>
							<input type="hidden" name="from_user_id" value="<?php echo get_current_user_id(); ?>" />
							<input type="hidden" name="to_user_id" value="<?php echo $chatter_manager_user; ?>" />
							<input type="hidden" name="nonce" value="<?php echo wp_create_nonce( 'new_chat_message_nnc' ); ?>" />
							<input type="hidden" name="chatter_post_id" value="" />
							<input type="hidden" name="source_post_id" value="<?php echo $source_post_id; ?>" />
							<p class="form-description"><?php _e( '* HTML is not allowed in messages', 'chatter' ); ?></p>
							<div class="submit-chatter-message-row">
								<button type="submit"><?php _e( 'Send', 'chatter' ); ?></button>
								<span class="keyboard-desc"><?php _e( 'Press CTRL+Enter for submit', 'chatter' ); ?></span>
							</div>
						</form>
					<?php else : ?>
						<p class="logged-in-only-submit-placeholder"><?php echo $chatter_loggedin_only_message; ?></p>
					<?php endif; ?>
				</div>

			</div>
		</div>

		<div class="chatter-box-footer">
			<p>
				<small>Accessibility first chatroom</small> <a href="https://accessibility-helper.co.il" target="_blank">Chatter by WAH</a>
			</p>
		</div>

	</div>

</div>
