<?php
/**
 * Admin Notices
 *
 * @package     FFW Boiler
 * @subpackage  Admin/Notices
 * @copyright   Copyright (c) 2013, FIfty & Fifty
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Admin Messages
 *
 * @since  0.1
 * @author Bryan Monzon
 * @global $boiler_settings Array of all the BOILER Options
 * @return void
 */
function boiler_admin_messages() {
    global $boiler_settings;

    settings_errors( 'boiler-notices' );
}
add_action( 'admin_notices', 'boiler_admin_messages' );


/**
 * Dismisses admin notices when Dismiss links are clicked
 *
 * @since 1.8
 * @return void
*/
function boiler_dismiss_notices() {

    $notice = isset( $_GET['boiler_notice'] ) ? $_GET['boiler_notice'] : false;

    if( ! $notice )
        return; // No notice, so get out of here

    update_user_meta( get_current_user_id(), '_boiler_' . $notice . '_dismissed', 1 );

    wp_redirect( remove_query_arg( array( 'boiler_action', 'boiler_notice' ) ) ); exit;

}
add_action( 'boiler_dismiss_notices', 'boiler_dismiss_notices' );
