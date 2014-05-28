<?php 
/**
 * Plugin Name: Fifty Framework Boiler
 * Plugin URI: http://fiftyandfifty.org
 * Description: Build boiler pages for your site
 * Version: 1.0
 * Author: Fifty and Fifty
 * Author URI: http://labs.fiftyandfifty.org
 */


// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'FFW_BOILER' ) ) :


/**
 * Main FFW_BOILER Class
 *
 * @since 1.0 */
final class FFW_BOILER {

  /**
   * @var FFW_BOILER Instance
   * @since 1.0
   */
  private static $instance;


  /**
   * FFW_BOILER Instance / Constructor
   *
   * Insures only one instance of FFW_BOILER exists in memory at any one
   * time & prevents needing to define globals all over the place. 
   * Inspired by and credit to FFW_BOILER.
   *
   * @since 1.0
   * @static
   * @uses FFW_BOILER::setup_globals() Setup the globals needed
   * @uses FFW_BOILER::includes() Include the required files
   * @uses FFW_BOILER::setup_actions() Setup the hooks and actions
   * @see FFW_BOILER()
   * @return void
   */
  public static function instance() {
    if ( ! isset( self::$instance ) && ! ( self::$instance instanceof FFW_BOILER ) ) {
      self::$instance = new FFW_BOILER;
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
    if ( ! defined( 'FFW_BOILER_VERSION' ) )
      define( 'FFW_BOILER_VERSION', '1.0' );

    // Plugin Folder Path
    if ( ! defined( 'FFW_BOILER_PLUGIN_DIR' ) )
      define( 'FFW_BOILER_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );

    // Plugin Folder URL
    if ( ! defined( 'FFW_BOILER_PLUGIN_URL' ) )
      define( 'FFW_BOILER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

    // Plugin Root File
    if ( ! defined( 'FFW_BOILER_PLUGIN_FILE' ) )
      define( 'FFW_BOILER_PLUGIN_FILE', __FILE__ );

    if ( ! defined( 'FFW_BOILER_DEBUG' ) )
      define ( 'FFW_BOILER_DEBUG', true );
  }



  /**
   * Include required files
   * @access private
   * @since 1.0
   * @return void
   */
  private function includes() {
    global $ffw_boiler_settings, $wp_version;

    require_once FFW_BOILER_PLUGIN_DIR . '/includes/admin/settings/register-settings.php';
    $ffw_boiler_settings = ffw_boiler_get_settings();

    // Required Plugin Files
    require_once FFW_BOILER_PLUGIN_DIR . '/includes/functions.php';
    require_once FFW_BOILER_PLUGIN_DIR . '/includes/posttypes.php';
    require_once FFW_BOILER_PLUGIN_DIR . '/includes/scripts.php';
    require_once FFW_BOILER_PLUGIN_DIR . '/includes/shortcodes.php';

    if( is_admin() ){
        //Admin Required Plugin Files
        require_once FFW_BOILER_PLUGIN_DIR . '/includes/admin/admin-pages.php';
        require_once FFW_BOILER_PLUGIN_DIR . '/includes/admin/admin-notices.php';
        require_once FFW_BOILER_PLUGIN_DIR . '/includes/admin/settings/display-settings.php';

    }


  }

} /* end FFW_BOILER class */
endif; // End if class_exists check


/**
 * Main function for returning FFW_BOILER Instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without needing
 * to declare the global.
 *
 * Example: <?php $sqcash = FFW_BOILER(); ?>
 *
 * @since 1.0
 * @return object The one true FFW_BOILER Instance
 */
function FFW_BOILER() {
  return FFW_BOILER::instance();
}


/**
 * Initiate
 * Run the FFW_BOILER() function, which runs the instance of the FFW_BOILER class.
 */
FFW_BOILER();



/**
 * Debugging
 * @since 1.0
 */
if ( FFW_BOILER_DEBUG ) {
  ini_set('display_errors','On');
  error_reporting(E_ALL);
}


