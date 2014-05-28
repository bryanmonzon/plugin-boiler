<?php
/**
 * Post Type Functions
 *
 * @package     FFW_BOILER
 * @subpackage  Functions
 * @copyright   Copyright (c) 2013, Fifty and Fifty
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Registers and sets up the Downloads custom post type
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return void
 */
function setup_ffw_boiler_post_types() {
	global $ffw_boiler_settings;
	$archives = defined( 'FFW_BOILER_DISABLE_ARCHIVE' ) && FFW_BOILER_DISABLE_ARCHIVE ? false : true;

	//Check to see if anything is set in the settings area.
	if( !empty( $ffw_boiler_settings['boiler_slug'] ) ) {
	    $slug = defined( 'FFW_BOILER_SLUG' ) ? FFW_BOILER_SLUG : $ffw_boiler_settings['boiler_slug'];
	} else {
	    $slug = defined( 'FFW_BOILER_SLUG' ) ? FFW_BOILER_SLUG : 'boiler';
	}
	
	$rewrite  = defined( 'FFW_BOILER_DISABLE_REWRITE' ) && FFW_BOILER_DISABLE_REWRITE ? false : array('slug' => $slug, 'with_front' => false);

	$boiler_labels =  apply_filters( 'ffw_boiler_boiler_labels', array(
		'name' 				=> '%2$s',
		'singular_name' 	=> '%1$s',
		'add_new' 			=> __( 'Add New', 'FFW_boiler' ),
		'add_new_item' 		=> __( 'Add New %1$s', 'FFW_boiler' ),
		'edit_item' 		=> __( 'Edit %1$s', 'FFW_boiler' ),
		'new_item' 			=> __( 'New %1$s', 'FFW_boiler' ),
		'all_items' 		=> __( 'All %2$s', 'FFW_boiler' ),
		'view_item' 		=> __( 'View %1$s', 'FFW_boiler' ),
		'search_items' 		=> __( 'Search %2$s', 'FFW_boiler' ),
		'not_found' 		=> __( 'No %2$s found', 'FFW_boiler' ),
		'not_found_in_trash'=> __( 'No %2$s found in Trash', 'FFW_boiler' ),
		'parent_item_colon' => '',
		'menu_name' 		=> __( '%2$s', 'FFW_boiler' )
	) );

	foreach ( $boiler_labels as $key => $value ) {
	   $boiler_labels[ $key ] = sprintf( $value, ffw_boiler_get_label_singular(), ffw_boiler_get_label_plural() );
	}

	$boiler_args = array(
		'labels' 			=> $boiler_labels,
		'public' 			=> true,
		'publicly_queryable'=> true,
		'show_ui' 			=> true,
		'show_in_menu' 		=> true,
		'menu_icon'         => 'dashicons-businessman',
		'query_var' 		=> true,
		'rewrite' 			=> $rewrite,
		'map_meta_cap'      => true,
		'has_archive' 		=> $archives,
		'show_in_nav_menus'	=> true,
		'hierarchical' 		=> false,
		'supports' 			=> apply_filters( 'ffw_boiler_supports', array( 'title', 'editor', 'thumbnail', 'excerpt' ) ),
	);
	register_post_type( 'FFW_boiler', apply_filters( 'ffw_boiler_post_type_args', $boiler_args ) );
	
}
add_action( 'init', 'setup_ffw_boiler_post_types', 1 );

/**
 * Get Default Labels
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return array $defaults Default labels
 */
function ffw_boiler_get_default_labels() {
	global $ffw_boiler_settings;

	if( !empty( $ffw_boiler_settings['boiler_label_plural'] ) || !empty( $ffw_boiler_settings['boiler_label_singular'] ) ) {
	    $defaults = array(
	       'singular' => $ffw_boiler_settings['boiler_label_singular'],
	       'plural' => $ffw_boiler_settings['boiler_label_plural']
	    );
	 } else {
		$defaults = array(
		   'singular' => __( 'Boiler', 'FFW_boiler' ),
		   'plural' => __( 'Boiler', 'FFW_boiler')
		);
	}
	
	return apply_filters( 'ffw_boiler_default_name', $defaults );

}

/**
 * Get Singular Label
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return string $defaults['singular'] Singular label
 */
function ffw_boiler_get_label_singular( $lowercase = false ) {
	$defaults = ffw_boiler_get_default_labels();
	return ($lowercase) ? strtolower( $defaults['singular'] ) : $defaults['singular'];
}

/**
 * Get Plural Label
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return string $defaults['plural'] Plural label
 */
function ffw_boiler_get_label_plural( $lowercase = false ) {
	$defaults = ffw_boiler_get_default_labels();
	return ( $lowercase ) ? strtolower( $defaults['plural'] ) : $defaults['plural'];
}

/**
 * Change default "Enter title here" input
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param string $title Default title placeholder text
 * @return string $title New placeholder text
 */
function ffw_boiler_change_default_title( $title ) {
     $screen = get_current_screen();

     if  ( 'ffw_boiler' == $screen->post_type ) {
     	$label = ffw_boiler_get_label_singular();
        $title = sprintf( __( 'Enter %s title here', 'FFW_boiler' ), $label );
     }

     return $title;
}
add_filter( 'enter_title_here', 'ffw_boiler_change_default_title' );

/**
 * Registers the custom taxonomies for the downloads custom post type
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return void
*/
function ffw_boiler_setup_taxonomies() {

	$slug     = defined( 'FFW_BOILER_SLUG' ) ? FFW_BOILER_SLUG : 'boiler';

	/** Categories */
	$category_labels = array(
		'name' 				=> sprintf( _x( '%s Categories', 'taxonomy general name', 'FFW_boiler' ), ffw_boiler_get_label_singular() ),
		'singular_name' 	=> _x( 'Category', 'taxonomy singular name', 'FFW_boiler' ),
		'search_items' 		=> __( 'Search Categories', 'FFW_boiler'  ),
		'all_items' 		=> __( 'All Categories', 'FFW_boiler'  ),
		'parent_item' 		=> __( 'Parent Category', 'FFW_boiler'  ),
		'parent_item_colon' => __( 'Parent Category:', 'FFW_boiler'  ),
		'edit_item' 		=> __( 'Edit Category', 'FFW_boiler'  ),
		'update_item' 		=> __( 'Update Category', 'FFW_boiler'  ),
		'add_new_item' 		=> __( 'Add New Category', 'FFW_boiler'  ),
		'new_item_name' 	=> __( 'New Category Name', 'FFW_boiler'  ),
		'menu_name' 		=> __( 'Categories', 'FFW_boiler'  ),
	);

	$category_args = apply_filters( 'ffw_boiler_category_args', array(
			'hierarchical' 		=> true,
			'labels' 			=> apply_filters('ffw_boiler_category_labels', $category_labels),
			'show_ui' 			=> true,
			'query_var' 		=> 'boiler_category',
			'rewrite' 			=> array('slug' => $slug . '/category', 'with_front' => false, 'hierarchical' => true ),
			'capabilities'  	=> array( 'manage_terms','edit_terms', 'assign_terms', 'delete_terms' ),
			'show_admin_column'	=> true
		)
	);
	register_taxonomy( 'boiler_category', array('ffw_boiler'), $category_args );
	register_taxonomy_for_object_type( 'boiler_category', 'ffw_boiler' );

}
add_action( 'init', 'ffw_boiler_setup_taxonomies', 0 );



/**
 * Updated Messages
 *
 * Returns an array of with all updated messages.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $messages Post updated message
 * @return array $messages New post updated messages
 */
function ffw_boiler_updated_messages( $messages ) {
	global $post, $post_ID;

	$url1 = '<a href="' . get_permalink( $post_ID ) . '">';
	$url2 = ffw_boiler_get_label_singular();
	$url3 = '</a>';

	$messages['FFW_boiler'] = array(
		1 => sprintf( __( '%2$s updated. %1$sView %2$s%3$s.', 'FFW_boiler' ), $url1, $url2, $url3 ),
		4 => sprintf( __( '%2$s updated. %1$sView %2$s%3$s.', 'FFW_boiler' ), $url1, $url2, $url3 ),
		6 => sprintf( __( '%2$s published. %1$sView %2$s%3$s.', 'FFW_boiler' ), $url1, $url2, $url3 ),
		7 => sprintf( __( '%2$s saved. %1$sView %2$s%3$s.', 'FFW_boiler' ), $url1, $url2, $url3 ),
		8 => sprintf( __( '%2$s submitted. %1$sView %2$s%3$s.', 'FFW_boiler' ), $url1, $url2, $url3 )
	);

	return $messages;
}
add_filter( 'post_updated_messages', 'ffw_boiler_updated_messages' );
