<?php 
/**
 * Plugin Name: Plugin Framework Boiler
 * Plugin URI: http://bryanmonzon.com/
 * Description: Build boiler pages for your site
 * Version: 1.0
 * Author: Bryan Monzon
 * Author URI: http://bryanmonzon.com
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'BOILER' ) ) :


/**
 * Main BOILER Class
 *
 * @since 1.0 */
final class BOILER {

  /**
   * @var BOILER Instance
   * @since 1.0
   */
  private static $instance;


  /**
   * BOILER Instance / Constructor
   *
   * Insures only one instance of BOILER exists in memory at any one
   * time & prevents needing to define globals all over the place. 
   * Inspired by and credit to BOILER.
   *
   * @since 1.0
   * @static
   * @uses BOILER::setup_globals() Setup the globals needed
   * @uses BOILER::includes() Include the required files
   * @uses BOILER::setup_actions() Setup the hooks and actions
   * @see BOILER()
   * @return void
   */
  public static function instance() {
    if ( ! isset( self::$instance ) && ! ( self::$instance instanceof BOILER ) ) {
      self::$instance = new BOILER;
      self::$instance->setup_constants();
      self::$instance->includes();
      // self::$instance->load_textdomain();
      // use @examples from public vars defined above upon implementation
    }
    return self::$instance;
  }



  /**
   * Setup plugin constants
   * @access private
   * @since 1.0 
   * @return void
   */
  private function setup_constants() {
    // Plugin version
    if ( ! defined( 'BOILER_VERSION' ) )
      define( 'BOILER_VERSION', '0.1' );

    // Plugin Folder Path
    if ( ! defined( 'BOILER_PLUGIN_DIR' ) )
      define( 'BOILER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

    // Plugin Folder URL
    if ( ! defined( 'BOILER_PLUGIN_URL' ) )
      define( 'BOILER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

    // Plugin Root File
    if ( ! defined( 'BOILER_PLUGIN_FILE' ) )
      define( 'BOILER_PLUGIN_FILE', __FILE__ );

    if ( ! defined( 'BOILER_DEBUG' ) )
      define ( 'BOILER_DEBUG', true );
  }



  /**
   * Include required files
   * @access private
   * @since 1.0
   * @return void
   */
  private function includes() {
    global $boiler_settings, $wp_version;

    require_once BOILER_PLUGIN_DIR . '/includes/admin/settings/register-settings.php';
    $boiler_settings = boiler_get_settings();

    // Required Plugin Files
    require_once BOILER_PLUGIN_DIR . '/includes/functions.php';
    require_once BOILER_PLUGIN_DIR . '/includes/posttypes.php';
    require_once BOILER_PLUGIN_DIR . '/includes/scripts.php';
    require_once BOILER_PLUGIN_DIR . '/includes/shortcodes.php';

    if( is_admin() ){
        //Admin Required Plugin Files
        require_once BOILER_PLUGIN_DIR . '/includes/admin/admin-pages.php';
        require_once BOILER_PLUGIN_DIR . '/includes/admin/admin-notices.php';
        require_once BOILER_PLUGIN_DIR . '/includes/admin/settings/display-settings.php';

    }

    require_once BOILER_PLUGIN_DIR . '/includes/install.php';


  }

} /* end BOILER class */
endif; // End if class_exists check


/**
 * Main function for returning BOILER Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $sqcash = BOILER(); ?>
 *
 * @since 1.0
 * @return object The one true BOILER Instance
 */
function BOILER() {
  return BOILER::instance();
}


/**
 * Initiate
 * Run the BOILER() function, which runs the instance of the BOILER class.
 */
BOILER();



/**
 * Debugging
 * @since 1.0
 */
if ( BOILER_DEBUG ) {
  ini_set('display_errors','On');
  error_reporting(E_ALL);
}


