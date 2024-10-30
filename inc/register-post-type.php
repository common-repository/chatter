<?php
if ( ! function_exists('chatter_post_type') ) {

// Register Custom Post Type
function chatter_post_type() {

	$labels = array(
		'name'                  => _x( 'Chatter', 'Post Type General Name', 'chatter' ),
		'singular_name'         => _x( 'Chatter', 'Post Type Singular Name', 'chatter' ),
		'menu_name'             => __( 'Chatter', 'chatter' ),
		'name_admin_bar'        => __( 'Chatter', 'chatter' ),
		'archives'              => __( 'Item Archives', 'chatter' ),
		'attributes'            => __( 'Item Attributes', 'chatter' ),
		'parent_item_colon'     => __( 'Parent Item:', 'chatter' ),
		'all_items'             => __( 'All Items', 'chatter' ),
		'add_new_item'          => __( 'Add New Item', 'chatter' ),
		'add_new'               => __( 'Add New', 'chatter' ),
		'new_item'              => __( 'New Item', 'chatter' ),
		'edit_item'             => __( 'Edit Item', 'chatter' ),
		'update_item'           => __( 'Update Item', 'chatter' ),
		'view_item'             => __( 'View Item', 'chatter' ),
		'view_items'            => __( 'View Items', 'chatter' ),
		'search_items'          => __( 'Search Item', 'chatter' ),
		'not_found'             => __( 'Not found', 'chatter' ),
		'not_found_in_trash'    => __( 'Not found in Trash', 'chatter' ),
		'featured_image'        => __( 'Featured Image', 'chatter' ),
		'set_featured_image'    => __( 'Set featured image', 'chatter' ),
		'remove_featured_image' => __( 'Remove featured image', 'chatter' ),
		'use_featured_image'    => __( 'Use as featured image', 'chatter' ),
		'insert_into_item'      => __( 'Insert into item', 'chatter' ),
		'uploaded_to_this_item' => __( 'Uploaded to this item', 'chatter' ),
		'items_list'            => __( 'Items list', 'chatter' ),
		'items_list_navigation' => __( 'Items list navigation', 'chatter' ),
		'filter_items_list'     => __( 'Filter items list', 'chatter' ),
	);
	$args = array(
		'label'                 => __( 'Chatter', 'chatter' ),
		'description'           => __( 'Chatter', 'chatter' ),
		'labels'                => $labels,
		'supports'              => array( 'title', 'comments' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 5,
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => true,
		'can_export'            => true,
		'has_archive'           => true,
		'exclude_from_search'   => false,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
	);
	register_post_type( 'chatter', $args );

}
add_action( 'init', 'chatter_post_type', 0 );

}
