<?php
/**
 * Metabox Functions
 *
 * @package     Boiler
 * @subpackage  Admin/Classes
 * @copyright   Copyright (c) 2013, Bryan Monzon
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;



/**
 * Register all the meta boxes for the Download custom post type
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return void
 */
function boiler_add_meta_box() {

    $post_types = apply_filters( 'boiler_metabox_post_types' , array( 'boiler' ) );

    foreach ( $post_types as $post_type ) {

        /** Class Configuration */
        add_meta_box( 'classinfo', sprintf( __( '%1$s Disable', 'boiler' ), boiler_get_label_singular(), boiler_get_label_plural() ),  'boiler_render_meta_box', $post_type, 'side', 'core' );

        
    }
}
add_action( 'add_meta_boxes', 'boiler_add_meta_box' );


/**
 * Sabe post meta when the save_post action is called
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param int $post_id Download (Post) ID
 * @global array $post All the data of the the current post
 * @return void
 */
function boiler_meta_box_save( $post_id) {
    global $post, $boiler_settings;

    if ( ! isset( $_POST['boiler_meta_box_nonce'] ) || ! wp_verify_nonce( $_POST['boiler_meta_box_nonce'], basename( __FILE__ ) ) )
        return $post_id;

    if ( ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) || ( defined( 'DOING_AJAX') && DOING_AJAX ) || isset( $_REQUEST['bulk_edit'] ) )
        return $post_id;

    if ( isset( $post->post_type ) && $post->post_type == 'revision' )
        return $post_id;




    // The default fields that get saved
    $fields = apply_filters( 'boiler_metabox_fields_save', array(
            'boiler_disable_link_to',


        )
    );


    foreach ( $fields as $field ) {
        if ( ! empty( $_POST[ $field ] ) ) {
            $new = apply_filters( 'etm_metabox_save_' . $field, $_POST[ $field ] );
            update_post_meta( $post_id, $field, $new );
        } else {
            delete_post_meta( $post_id, $field );
        }
    }
}
add_action( 'save_post', 'boiler_meta_box_save' );





/** Class Configuration *****************************************************************/

/**
 * Class Metabox
 *
 * Extensions (as well as the core plugin) can add items to the main download
 * configuration metabox via the `boiler_meta_box_fields` action.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return void
 */
function boiler_render_meta_box() {
    global $post, $boiler_settings;

    do_action( 'boiler_meta_box_fields', $post->ID );
    wp_nonce_field( basename( __FILE__ ), 'boiler_meta_box_nonce' );
}



/**
 * Render the fields
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param  [type] $post [description]
 * @return [type]       [description]
 */
function boiler_render_fields( $post )
{
    global $post, $boiler_settings; 

    /*$postmeta_check = get_post_meta($post->ID);
    echo '<pre>';
    var_dump($postmeta_check);
    echo '</pre>';*/
    $diable_link_to = get_post_meta( $post->ID, 'boiler_disable_link_to', true);
    ?>
    
    <div id="boiler_disable_link_to">
        <p>
            <label for="boiler_disable_link_to">
                <input type="checkbox" name="boiler_disable_link_to" value="1"<?php checked(1, $diable_link_to ); ?> >
                Disable link to profile page
            </label>
        </p>
    </div>
    
    <?php

}
add_action( 'boiler_meta_box_fields', 'boiler_render_fields', 10 );

