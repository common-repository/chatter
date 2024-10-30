<?php
/**
 * Plugin Name:       Chatter
 * Plugin URI:        https://accessibility-helper.co.il/chatter-wp-chat-plugin/
 * Description:       Chatter - Chat plugin. Support your customers with the live chat
 * Version:           1.0.1
 * Requires at least: 5.0
 * Requires PHP:      7.4
 * Author:            Alex Volkov & WAH Team
 * Author URI:        https://volkov.co.il
 * License:           GPL v2 or later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       chatter
 * Domain Path:       /languages
 */

define( 'CHATTER_VERSION', '1.0.1' );
define( 'CHATTER_PATH', plugin_dir_path( __FILE__ ) );

add_action( 'wp_enqueue_scripts', 'chatter_add_stylesheets' );
function chatter_add_stylesheets() {
	wp_enqueue_style( 'chatter-theme', plugin_dir_url( __FILE__ ) . '/front/css/chatter-theme.css', array(), CHATTER_VERSION, 'all' );
	wp_register_script( 'chatter-script', plugin_dir_url( __FILE__ ) . '/front/js/chatter-script.js', array( 'jquery' ), CHATTER_VERSION, true );
	$data = array(
		'plugin_name'        => 'Chatter',
		'plugin_author'      => 'Alex Volkov',
		'plugin_author_url'  => 'http://volkov.co.il',
		'plugin_version'     => CHATTER_VERSION,
		'ajax_url'           => admin_url( 'admin-ajax.php' ),
		'chatter_refresh'    => (int) chatter_get_option( 'chatter_refresh' ),
		'start_minimized'    => chatter_get_option( 'chatter_minimized' ),
		'is_chatter_manager' => ( is_user_logged_in() && chatter_get_option( 'chatter_manager_user' ) == get_current_user_id() ) ? true : false,
	);
	wp_localize_script( 'chatter-script', 'chatterObject', $data );
	wp_enqueue_script( 'chatter-script' );
}

add_action( 'admin_enqueue_scripts', 'chatter_enqueue_admin_script' );
function chatter_enqueue_admin_script( $hook ) {
	if ( 'settings_page_chatter' == $hook || 'post.php' == $hook ) {
		wp_enqueue_style( 'chatter-admin', plugin_dir_url( __FILE__ ) . '/admin/css/chatter-admin.css', array(), CHATTER_VERSION, 'all' );
		wp_enqueue_script( 'chatter-admin', plugin_dir_url( __FILE__ ) . '/admin/js/chatter-admin.js', array(), CHATTER_VERSION );
	}
}

// Option page
add_action( 'admin_menu', 'chatter_register_options_page' );
function chatter_register_options_page() {
	add_options_page( 'Chatter settings', 'Chatter settings', 'manage_options', 'chatter', 'chatter_options_page' );
}

function chatter_options_page() {
	include 'admin/option-page.php';
}
require 'inc/register-post-type.php';
require 'front/functions.php';

if ( is_admin() ) {
	include 'admin/functions.php';
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'chatter_add_plugin_page_settings_link' );
function chatter_add_plugin_page_settings_link( $links ) {
	$links[] = '<a href="' . admin_url( 'options-general.php?page=chatter' ) . '">' . __( 'Chatter Settings', 'chatter' ) . '</a>';
	return $links;
}

function chatter_admin_notice() {
	?>
	<div class="chatter-admin-notice">
		Wellcome to Chatter - WordPress Chat plugin with accessibility features built-in. <a href="https://accessibility-helper.co.il/?refs=Chatter" target="_blank">by WAH Team</a>
	</div>
	<?php
}

function chatter_get_option( $option ) {
	return get_option( $option );
}

function chatter_update_option( $option, $value ) {
	update_option( $option, $value );
}
