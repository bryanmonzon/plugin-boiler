<?php
/**
 * Admin Pages
 *
 * @package     Fifty Framework Boiler
 * @subpackage  Admin/Pages
 * @copyright   Copyright (c) 2013, Bryan Monzon
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       0.1
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;




/**
 * Creates the admin menu pages under Donately and assigns them their global variables
 *
 * @since  0.1
 * @author Bryan Monzon
 * @global  $boiler_settings_page
 * @return void
 */
function boiler_add_menu_page() {
    global $boiler_settings_page;

    $boiler_settings_page = add_submenu_page( 'edit.php?post_type=boiler', __( 'Settings', 'boiler' ), __( 'Settings', 'boiler'), 'edit_pages', 'boiler-settings', 'boiler_settings_page' );
    
}
add_action( 'admin_menu', 'boiler_add_menu_page', 11 );
