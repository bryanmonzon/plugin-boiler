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
 * @since  1.0
 * @global  $ffw_boiler_settings_page
  * @return void
 */
function ffw_boiler_add_menu_page() {
    global $ffw_boiler_settings_page;

    $ffw_boiler_settings_page = add_submenu_page( 'edit.php?post_type=ffw_boiler', __( 'Settings', 'ffw_boiler' ), __( 'Settings', 'ffw_boiler'), 'edit_pages', 'boiler-settings', 'ffw_boiler_settings_page' );
    
}
add_action( 'admin_menu', 'ffw_boiler_add_menu_page', 11 );
