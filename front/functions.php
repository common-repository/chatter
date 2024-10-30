<?php
add_action( 'wp_footer', 'chatter_build_sidebar' );
function chatter_build_sidebar() {
	include 'view.php';
}

add_action( 'wp_ajax_refresh_chatter_messages', 'refresh_chatter_messages' );
function refresh_chatter_messages() {
	$response        = array(
		'html'    => '',
		'post_id' => '',
	);
	$post_id         = isset( $_POST['post_id'] ) ? sanitize_text_field( $_POST['post_id'] ) : '';
	$current_count   = isset( $_POST['current_count'] ) ? sanitize_text_field( $_POST['current_count'] ) : 0;
	$execute_refresh = false;

	if ( $post_id ) {
		if ( get_post_meta( $post_id, 'chatter_status', true ) != 'closed' ) {
			$comments_count = get_comments(
				array(
					'post_id' => $post_id,
					'count'   => true,
				)
			);
			if ( $current_count != $comments_count ) {
				$execute_refresh = true;
			}
		}
	}

	if ( $execute_refresh ) {
		$comments = get_comments(
			array(
				'post_id' => $post_id,
				'count'   => false,
				'order'   => 'ASC',
			)
		);
		if ( $comments ) {
			ob_start();
			foreach ( $comments as $comment ) {
				include CHATTER_PATH . 'inc/single-message.php';
			}
			$response['html']    = ob_get_clean();
			$response['post_id'] = $post_id;
		}
	}

	wp_send_json( $response );
}

add_action( 'wp_ajax_load_chatter_messages_by_user', 'load_chatter_messages_by_user' );
function load_chatter_messages_by_user() {
	$user_id  = isset( $_POST['user_id'] ) ? sanitize_text_field( $_POST['user_id'] ) : '';
	$post_id  = isset( $_POST['post_id'] ) ? sanitize_text_field( $_POST['post_id'] ) : '';
	$response = array(
		'html' => '',
	);

	$chatter_manager_user     = chatter_get_option( 'chatter_manager_user' );
	$chatter_show_user_avatar = chatter_get_option( 'chatter_show_user_avatar' );

	if ( $post_id ) {
		$post_item = get_post( $post_id );
		if ( $post_item ) {

			$comments = get_comments(
				array(
					'post_id' => $post_item->ID,
					'order'   => 'ASC',
				)
			);

			if ( $comments ) {
				ob_start();
				foreach ( $comments as $comment ) {
					include CHATTER_PATH . 'inc/single-message.php';
				}
				$response['html']    = ob_get_clean();
				$response['post_id'] = $post_id;
			}
		}

		if ( ( is_user_logged_in() && $chatter_manager_user && ( $chatter_manager_user == get_current_user_id() ) ) || current_user_can( 'administrator' ) ) {
			$response['edit_chat_admin_link'] = get_edit_post_link( $post_id );
		}
	}

	wp_send_json( $response );
}

add_action( 'wp_ajax_chatter_post_new_chat_message', 'chatter_post_new_chat_message' );
function chatter_post_new_chat_message() {

	$result    = array();
	$form      = isset( $_POST['form'] ) ? $_POST['form'] : '';
	$is_hebrew = ( isset( $_POST['is_hebrew'] ) && $_POST['is_hebrew'] ) ? true : false;
	$args      = array();
	parse_str( $form, $args );

	$nonce = isset( $args['nonce'] ) ? $args['nonce'] : '';
	if ( ! wp_verify_nonce( $nonce, 'new_chat_message_nnc' ) || ! current_user_can( 'subscriber' ) ) {
		wp_send_json(
			array(
				'post_id'         => 0,
				'comment_id'      => 0,
				'html'            => 'Security check failed',
				'trigger_refresh' => false,
			)
		);
	}

	if ( isset( $args['chatter_message_body'] ) && $args['chatter_message_body'] ) {
		$result = insert_new_chatter_message( $args, $is_hebrew );
	}

	wp_send_json( $result );
}

if ( ! function_exists( 'insert_new_chatter_message' ) ) {
	function insert_new_chatter_message( $args, $is_hebrew ) {

		$post_title               = 'Chatter from-' . $args['from_user_id'] . ' to-' . $args['to_user_id'];
		$chatter_show_user_avatar = chatter_get_option( 'chatter_show_user_avatar' );

		$comment_id      = '';
		$html            = '';
		$trigger_refresh = false;

		if ( isset( $args['chatter_post_id'] ) && $args['chatter_post_id'] ) {
			$post_id         = $args['chatter_post_id'];
			$trigger_refresh = true;
		} else {

			$chatter_post = array(
				'post_title'   => $post_title,
				'post_content' => '',
				'post_status'  => 'publish',
				'post_type'    => 'chatter',
				'post_author'  => $args['from_user_id'],
			);

			$post_id = wp_insert_post( $chatter_post );

			update_post_meta( $post_id, 'from_user_id', $args['from_user_id'] );
			update_post_meta( $post_id, 'to_user_id', $args['to_user_id'] );
			update_post_meta( $post_id, 'chatter_status', 'active' );

			// send mail to chat manager when new chat has been created
			$chatter_send_mail_to_chat_manager = chatter_get_option( 'chatter_send_mail_to_chat_manager' );
			if ( $chatter_send_mail_to_chat_manager ) {
				send_mail_to_chat_manager( $post_id, $args['to_user_id'] );
				do_action( 'after_send_mail_to_chat_manager', $post_id, $args['to_user_id'], $args['from_user_id'] );
			}

			$trigger_refresh = true;

		}

		if ( $post_id ) {

			$commentdata = array(
				'comment_approved' => true,
				'comment_date'     => date( 'Y-m-d H:i:s' ),
				'comment_date_gmt' => date( 'Y-m-d H:i:s' ),
				'comment_post_ID'  => $post_id,
				'user_id'          => $args['from_user_id'],
				'comment_content'  => $args['chatter_message_body'],
			);

			$comment_id = wp_insert_comment( $commentdata );
			if ( $is_hebrew ) {
				update_comment_meta( $comment_id, 'is_hebrew', true );
			} else {
				update_comment_meta( $comment_id, 'is_hebrew', false );
			}

			$user_info         = get_userdata( $args['from_user_id'] );
			$from_user_id      = get_post_meta( $post_id, 'from_user_id', true );
			$is_comment_author = '';
			if ( $from_user_id && (int) $from_user_id == (int) $args['from_user_id'] ) {
				$is_comment_author = 'is_comment_author';
			}

			ob_start();
			?>

				<div class="chatter-message <?php echo $is_comment_author; ?>" tabindex="0"
					data-comment_id="<?php echo $comment_id; ?>" data-post_id="<?php echo $post_id; ?>">

					<div class="chatter-message-dropdown">

						<button type="button" class="chatter-dropdown-button" aria-haspopup="true" tabindex="0">
							<span></span><span></span><span></span>
						</button>

						<div class="chatter-dropdown-menu" aria-hidden="true" aria-expanded="false">
							<ul role="menu">
								<li role="menuitem" tabindex="0" class="chatter-copy-text">
									<button type="button" class="chatter-copy-text-btn"><?php _e( 'Copy message text', 'chatter' ); ?></button>
								</li>
							</ul>
						</div>

					</div>

					<?php
					if ( $chatter_show_user_avatar ) :
						$avatar_url = get_avatar_url( $args['from_user_id'] );
						?>
						<div class="chatter-user-avatar">
							<img width="50" height="50" src="<?php echo $avatar_url; ?>">
						</div>
					<?php endif; ?>
					<div class="chatter-message-author">
						<?php echo $user_info->display_name; ?> <span class="date-time">[<?php echo date( 'd/m/Y - H:i:s' ); ?>]</span>
					</div>
					<div class="chatter-message-content <?php echo $is_hebrew ? 'is-rtl' : ''; ?>">
						<?php echo esc_html( $args['chatter_message_body'] ); ?>
					</div>

				</div>

			<?php
			$html = ob_get_clean();
		}

		return array(
			'post_id'         => $post_id,
			'comment_id'      => $comment_id,
			'html'            => $html,
			'trigger_refresh' => $trigger_refresh,
		);
	}
}

if ( ! function_exists( 'get_chatter_posts_users' ) ) {
	function get_chatter_posts_users() {
		global $post;
		$args      = array(
			'post_type'      => 'chatter',
			'posts_per_page' => -1,
			'post_status'    => 'publish',
			'meta_query'     => array(
				array(
					'key'     => 'chatter_status',
					'value'   => 'closed',
					'compare' => '!=',
				),
			),
		);
		$users     = new WP_Query( $args );
		$users_ids = array();

		if ( $users->have_posts() ) {
			while ( $users->have_posts() ) :
				$users->the_post();
				if ( get_post_meta( $post->ID, 'from_user_id', true ) ) {
					$user                              = get_user_by( 'ID', get_post_meta( $post->ID, 'from_user_id', true ) );
					$users_ids[ $post->ID ]['user_id'] = get_post_meta( $post->ID, 'from_user_id', true );
					$users_ids[ $post->ID ]['post_id'] = $post->ID;
					$users_ids[ $post->ID ]['option']  = $user->display_name . ' - ' . $user->user_email;
				}
			endwhile;
			wp_reset_query();
		}

		return $users_ids;
	}
}


add_action( 'wp_ajax_load_chatter_messages', 'load_chatter_messages' );
function load_chatter_messages() {
	$chatter_manager_user = chatter_get_option( 'chatter_manager_user' );

	$response = array(
		'html'    => 'Start talking =)',
		'post_id' => '',
	);

	if ( is_user_logged_in() && $chatter_manager_user && ( $chatter_manager_user == get_current_user_id() ) ) {

		$chatter_post = false;

	} else {

		$args         = array(
			'post_type'      => 'chatter',
			'posts_per_page' => 1,
			'meta_query'     => array(
				'relation' => 'AND',
				array(
					'key'     => 'chatter_status',
					'value'   => 'active',
					'compare' => '=',
				),
				array(
					'key'     => 'from_user_id',
					'value'   => get_current_user_id(),
					'type'    => 'numeric',
					'compare' => '=',
				),
			),
		);
		$chatter_post = new WP_Query( $args );

	}

	if ( $chatter_post && $chatter_post->have_posts() ) {

		$post_object = reset( $chatter_post->posts );
		$post_id     = $post_object->ID;

		if ( $post_id ) {

			$comments = get_comments(
				array(
					'post_id' => $post_id,
					'order'   => 'ASC',
				)
			);

			if ( $comments ) {
				ob_start();
				foreach ( $comments as $comment ) {
					include CHATTER_PATH . 'inc/single-message.php';
				}
				$response['html']    = ob_get_clean();
				$response['post_id'] = $post_id;
			}
		}
	}

	wp_send_json( $response );
}

if ( ! function_exists( 'send_mail_to_chat_manager' ) ) {

	function send_mail_to_chat_manager( $post_id, $to_user_id ) {

		if ( $post_id && $to_user_id ) {

			$chatter_post = get_post( $post_id );
			$to_user      = get_user_by( 'ID', $to_user_id );
			$subject      = __( 'New Chatter has been submitted', 'chatter' );

			$chatter_manager_mail_from = chatter_get_option( 'chatter_manager_mail_from' );
			$chatter_manager_mail_body = chatter_get_option( 'chatter_manager_mail_body' );

			$chatter_manager_mail_body = str_replace( '%%manager%%', $to_user->display_name, $chatter_manager_mail_body );

			$message  = '<html><body>';
			$message .= wpautop( $chatter_manager_mail_body );
			$message .= '</body></html>';

			$headers  = 'From: Chatter notifications <' . strip_tags( $chatter_manager_mail_from ) . ">\r\n";
			$headers .= "MIME-Version: 1.0\r\n";
			$headers .= "Content-Type: text/html; charset=UTF-8\r\n";

			wp_mail( $to_user->user_email, $subject, $message, $headers );
		}
	}

}
