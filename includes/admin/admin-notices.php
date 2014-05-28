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
 * @global $ffw_boiler_settings Array of all the FFW_BOILER Options
 * @return void
 */
function ffw_boiler_admin_messages() {
    global $ffw_boiler_settings;

    settings_errors( 'ffw_boiler-notices' );
}
add_action( 'admin_notices', 'ffw_boiler_admin_messages' );


/**
 * Dismisses admin notices when Dismiss links are clicked
 *
 * @since 1.8
 * @return void
*/
function ffw_boiler_dismiss_notices() {

    $notice = isset( $_GET['ffw_boiler_notice'] ) ? $_GET['ffw_boiler_notice'] : false;

    if( ! $notice )
        return; // No notice, so get out of here

    update_user_meta( get_current_user_id(), '_ffw_boiler_' . $notice . '_dismissed', 1 );

    wp_redirect( remove_query_arg( array( 'ffw_boiler_action', 'ffw_boiler_notice' ) ) ); exit;

}
add_action( 'ffw_boiler_dismiss_notices', 'ffw_boiler_dismiss_notices' );
