<?php
/**
 * Register Settings
 *
 * @package     Plugin Framework Boiler
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2013, Bryan Monzon
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0
*/

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;


/**
 * Get an option
 *
 * Looks to see if the specified setting exists, returns default if not
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return mixed
 */
function boiler_get_option( $key = '', $default = false ) {
    global $boiler_settings;
    return isset( $boiler_settings[ $key ] ) ? $boiler_settings[ $key ] : $default;
}

/**
 * Get Settings
 *
 * Retrieves all plugin settings
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return array BOILER settings
 */
function boiler_get_settings() {

    $settings = get_option( 'boiler_settings' );
    if( empty( $settings ) ) {

        // Update old settings with new single option

        $general_settings = is_array( get_option( 'boiler_settings_general' ) )    ? get_option( 'boiler_settings_general' )      : array();


        $settings = array_merge( $general_settings );

        update_option( 'boiler_settings', $settings );
    }
    return apply_filters( 'boiler_get_settings', $settings );
}

/**
 * Add all settings sections and fields
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return void
*/
function boiler_register_settings() {

    if ( false == get_option( 'boiler_settings' ) ) {
        add_option( 'boiler_settings' );
    }

    foreach( boiler_get_registered_settings() as $tab => $settings ) {

        add_settings_section(
            'boiler_settings_' . $tab,
            __return_null(),
            '__return_false',
            'boiler_settings_' . $tab
        );

        foreach ( $settings as $option ) {
            add_settings_field(
                'boiler_settings[' . $option['id'] . ']',
                $option['name'],
                function_exists( 'boiler_' . $option['type'] . '_callback' ) ? 'boiler_' . $option['type'] . '_callback' : 'boiler_missing_callback',
                'boiler_settings_' . $tab,
                'boiler_settings_' . $tab,
                array(
                    'id'      => $option['id'],
                    'desc'    => ! empty( $option['desc'] ) ? $option['desc'] : '',
                    'name'    => $option['name'],
                    'section' => $tab,
                    'size'    => isset( $option['size'] ) ? $option['size'] : null,
                    'options' => isset( $option['options'] ) ? $option['options'] : '',
                    'std'     => isset( $option['std'] ) ? $option['std'] : ''
                )
            );
        }

    }

    // Creates our settings in the options table
    register_setting( 'boiler_settings', 'boiler_settings', 'boiler_settings_sanitize' );

}
add_action('admin_init', 'boiler_register_settings');

/**
 * Retrieve the array of plugin settings
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return array
*/
function boiler_get_registered_settings() {

    $pages = get_pages();
    $pages_options = array( 0 => '' ); // Blank option
    if ( $pages ) {
        foreach ( $pages as $page ) {
            $pages_options[ $page->ID ] = $page->post_title;
        }
    }

    /**
     * 'Whitelisted' BOILER settings, filters are provided for each settings
     * section to allow extensions and other plugins to add their own settings
     */
    $boiler_settings = array(
        /** General Settings */
        'general' => apply_filters( 'boiler_settings_general',
            array(
                'basic_settings' => array(
                    'id' => 'basic_settings',
                    'name' => '<strong>' . __( 'Basic Settings', 'boiler' ) . '</strong>',
                    'desc' => '',
                    'type' => 'header'
                ),
                'boiler_slug' => array(
                    'id' => 'boiler_slug',
                    'name' => __( boiler_get_label_plural() . ' URL Slug', 'boiler' ),
                    'desc' => __( 'Enter the slug you would like to use for your ' . strtolower( boiler_get_label_plural() ) . '. (<em>You will need to <a href="' . admin_url( 'options-permalink.php' ) . '">refresh permalinks</a>, after saving changes</em>).'  , 'boiler' ),
                    'type' => 'text',
                    'size' => 'medium',
                    'std' => strtolower( boiler_get_label_plural() )
                ),
                'boiler_label_plural' => array(
                    'id' => 'boiler_label_plural',
                    'name' => __( boiler_get_label_plural() . ' Label Plural', 'boiler' ),
                    'desc' => __( 'Enter the label you would like to use for your ' . strtolower( boiler_get_label_plural() ) . '.', 'boiler' ),
                    'type' => 'text',
                    'size' => 'medium',
                    'std' => boiler_get_label_plural()
                ),
                'boiler_label_singular' => array(
                    'id' => 'boiler_label_singular',
                    'name' => __( boiler_get_label_singular() . ' Label Singular', 'boiler' ),
                    'desc' => __( 'Enter the label you would like to use for your ' . strtolower( boiler_get_label_singular() ) . '.', 'boiler' ),
                    'type' => 'text',
                    'size' => 'medium',
                    'std' => boiler_get_label_singular()
                ),
                'disable_archive' => array(
                    'id' => 'disable_archive',
                    'name' => __( 'Disable Archives Page', 'boiler' ),
                    'desc' => __( 'Check to disable archives page. (<em>You might need to <a href="' . admin_url( 'options-permalink.php' ) . '">refresh permalinks</a>, after saving changes</em>).', 'boiler' ),
                    'type' => 'checkbox',
                    'std' => ''
                ),
                'exclude_from_search' => array(
                    'id' => 'exclude_from_search',
                    'name' => __( 'Exclude from Search', 'boiler' ),
                    'desc' => __( 'Check to exclude from search. (<em>You might need to <a href="' . admin_url( 'options-permalink.php' ) . '">refresh permalinks</a>, after saving changes</em>)', 'boiler' ),
                    'type' => 'checkbox',
                    'std' => ''
                ),
            )
        ),
        
    );

    return $boiler_settings;
}

/**
 * Header Callback
 *
 * Renders the header.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @return void
 */
function boiler_header_callback( $args ) {
    $html = '<label for="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';
    echo $html;
}

/**
 * Checkbox Callback
 *
 * Renders checkboxes.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $boiler_settings Array of all the BOILER Options
 * @return void
 */
function boiler_checkbox_callback( $args ) {
    global $boiler_settings;

    $checked = isset($boiler_settings[$args['id']]) ? checked(1, $boiler_settings[$args['id']], false) : '';
    $html = '<input type="checkbox" id="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" name="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" value="1" ' . $checked . '/>';
    $html .= '<label for="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Multicheck Callback
 *
 * Renders multiple checkboxes.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $boiler_settings Array of all the BOILER Options
 * @return void
 */
function boiler_multicheck_callback( $args ) {
    global $boiler_settings;

    foreach( $args['options'] as $key => $option ):
        if( isset( $boiler_settings[$args['id']][$key] ) ) { $enabled = $option; } else { $enabled = NULL; }
        echo '<input name="boiler_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']" id="boiler_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']" type="checkbox" value="' . $option . '" ' . checked($option, $enabled, false) . '/>&nbsp;';
        echo '<label for="boiler_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
    endforeach;
    echo '<p class="description">' . $args['desc'] . '</p>';
}

/**
 * Radio Callback
 *
 * Renders radio boxes.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $boiler_settings Array of all the BOILER Options
 * @return void
 */
function boiler_radio_callback( $args ) {
    global $boiler_settings;

    foreach ( $args['options'] as $key => $option ) :
        $checked = false;

        if ( isset( $boiler_settings[ $args['id'] ] ) && $boiler_settings[ $args['id'] ] == $key )
            $checked = true;
        elseif( isset( $args['std'] ) && $args['std'] == $key && ! isset( $boiler_settings[ $args['id'] ] ) )
            $checked = true;

        echo '<input name="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']"" id="boiler_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked(true, $checked, false) . '/>&nbsp;';
        echo '<label for="boiler_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
    endforeach;

    echo '<p class="description">' . $args['desc'] . '</p>';
}



/**
 * Text Callback
 *
 * Renders text fields.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $boiler_settings Array of all the BOILER Options
 * @return void
 */
function boiler_text_callback( $args ) {
    global $boiler_settings;

    if ( isset( $boiler_settings[ $args['id'] ] ) )
        $value = $boiler_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="text" class="' . $size . '-text" id="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" name="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
    $html .= '<label for="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}


/**
 * BOILER Hidden Text Field Callback
 *
 * Renders text fields (Hidden, for necessary values in boiler_settings in the wp_options table)
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $boiler_settings Array of all the BOILER Options
 * @return void
 * @todo refactor it is not needed entirely
 */
function boiler_hidden_callback( $args ) {
    global $boiler_settings;

    $hidden = isset($args['hidden']) ? $args['hidden'] : false;

    if ( isset( $boiler_settings[ $args['id'] ] ) )
        $value = $boiler_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="hidden" class="' . $size . '-text" id="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" name="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
    $html .= '<label for="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['std'] . '</label>';

    echo $html;
}




/**
 * Textarea Callback
 *
 * Renders textarea fields.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $boiler_settings Array of all the BOILER Options
 * @return void
 */
function boiler_textarea_callback( $args ) {
    global $boiler_settings;

    if ( isset( $boiler_settings[ $args['id'] ] ) )
        $value = $boiler_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<textarea class="large-text" cols="50" rows="5" id="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" name="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
    $html .= '<label for="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Password Callback
 *
 * Renders password fields.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $boiler_settings Array of all the BOILER Options
 * @return void
 */
function boiler_password_callback( $args ) {
    global $boiler_settings;

    if ( isset( $boiler_settings[ $args['id'] ] ) )
        $value = $boiler_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="password" class="' . $size . '-text" id="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" name="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( $value ) . '"/>';
    $html .= '<label for="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Missing Callback
 *
 * If a function is missing for settings callbacks alert the user.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @return void
 */
function boiler_missing_callback($args) {
    printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'boiler' ), $args['id'] );
}

/**
 * Select Callback
 *
 * Renders select fields.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $boiler_settings Array of all the BOILER Options
 * @return void
 */
function boiler_select_callback($args) {
    global $boiler_settings;

    if ( isset( $boiler_settings[ $args['id'] ] ) )
        $value = $boiler_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $html = '<select id="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" name="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']"/>';

    foreach ( $args['options'] as $option => $name ) :
        $selected = selected( $option, $value, false );
        $html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
    endforeach;

    $html .= '</select>';
    $html .= '<label for="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Color select Callback
 *
 * Renders color select fields.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $boiler_settings Array of all the BOILER Options
 * @return void
 */
function boiler_color_select_callback( $args ) {
    global $boiler_settings;

    if ( isset( $boiler_settings[ $args['id'] ] ) )
        $value = $boiler_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $html = '<select id="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" name="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']"/>';

    foreach ( $args['options'] as $option => $color ) :
        $selected = selected( $option, $value, false );
        $html .= '<option value="' . $option . '" ' . $selected . '>' . $color['label'] . '</option>';
    endforeach;

    $html .= '</select>';
    $html .= '<label for="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Rich Editor Callback
 *
 * Renders rich editor fields.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $boiler_settings Array of all the BOILER Options
 * @global $wp_version WordPress Version
 */
function boiler_rich_editor_callback( $args ) {
    global $boiler_settings, $wp_version;

    if ( isset( $boiler_settings[ $args['id'] ] ) )
        $value = $boiler_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    if ( $wp_version >= 3.3 && function_exists( 'wp_editor' ) ) {
        $html = wp_editor( stripslashes( $value ), 'boiler_settings_' . $args['section'] . '[' . $args['id'] . ']', array( 'textarea_name' => 'boiler_settings_' . $args['section'] . '[' . $args['id'] . ']' ) );
    } else {
        $html = '<textarea class="large-text" rows="10" id="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" name="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
    }

    $html .= '<br/><label for="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}

/**
 * Upload Callback
 *
 * Renders upload fields.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $boiler_settings Array of all the BOILER Options
 * @return void
 */
function boiler_upload_callback( $args ) {
    global $boiler_settings;

    if ( isset( $boiler_settings[ $args['id'] ] ) )
        $value = $boiler_settings[$args['id']];
    else
        $value = isset($args['std']) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="text" class="' . $size . '-text boiler_upload_field" id="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" name="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
    $html .= '<span>&nbsp;<input type="button" class="boiler_settings_upload_button button-secondary" value="' . __( 'Upload File', 'boiler' ) . '"/></span>';
    $html .= '<label for="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}


/**
 * Color picker Callback
 *
 * Renders color picker fields.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $boiler_settings Array of all the BOILER Options
 * @return void
 */
function boiler_color_callback( $args ) {
    global $boiler_settings;

    if ( isset( $boiler_settings[ $args['id'] ] ) )
        $value = $boiler_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $default = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="text" class="boiler-color-picker" id="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" name="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( $value ) . '" data-default-color="' . esc_attr( $default ) . '" />';
    $html .= '<label for="boiler_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}



/**
 * Hook Callback
 *
 * Adds a do_action() hook in place of the field
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @return void
 */
function boiler_hook_callback( $args ) {
    do_action( 'boiler_' . $args['id'] );


    
}

/**
 * Settings Sanitization
 *
 * Adds a settings error (for the updated message)
 * At some point this will validate input
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $input The value inputted in the field
 * @return string $input Sanitizied value
 */
function boiler_settings_sanitize( $input = array() ) {

    global $boiler_settings;

    parse_str( $_POST['_wp_http_referer'], $referrer );

    $output    = array();
    $settings  = boiler_get_registered_settings();
    $tab       = isset( $referrer['tab'] ) ? $referrer['tab'] : 'general';
    $post_data = isset( $_POST[ 'boiler_settings_' . $tab ] ) ? $_POST[ 'boiler_settings_' . $tab ] : array();

    $input = apply_filters( 'boiler_settings_' . $tab . '_sanitize', $post_data );

    // Loop through each setting being saved and pass it through a sanitization filter
    foreach( $input as $key => $value ) {

        // Get the setting type (checkbox, select, etc)
        $type = isset( $settings[ $key ][ 'type' ] ) ? $settings[ $key ][ 'type' ] : false;

        if( $type ) {
            // Field type specific filter
            $output[ $key ] = apply_filters( 'boiler_settings_sanitize_' . $type, $value, $key );
        }

        // General filter
        $output[ $key ] = apply_filters( 'boiler_settings_sanitize', $value, $key );
    }


    // Loop through the whitelist and unset any that are empty for the tab being saved
    if( ! empty( $settings[ $tab ] ) ) {
        foreach( $settings[ $tab ] as $key => $value ) {

            // settings used to have numeric keys, now they have keys that match the option ID. This ensures both methods work
            if( is_numeric( $key ) ) {
                $key = $value['id'];
            }

            if( empty( $_POST[ 'boiler_settings_' . $tab ][ $key ] ) ) {
                unset( $boiler_settings[ $key ] );
            }

        }
    }

    // Merge our new settings with the existing
    $output = array_merge( $boiler_settings, $output );

    // @TODO: Get Notices Working in the backend.
    add_settings_error( 'boiler-notices', '', __( 'Settings Updated', 'boiler' ), 'updated' );

    return $output;

}

/**
 * Sanitize text fields
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $input The field value
 * @return string $input Sanitizied value
 */
function boiler_sanitize_text_field( $input ) {
    return trim( $input );
}
add_filter( 'boiler_settings_sanitize_text', 'boiler_sanitize_text_field' );

/**
 * Retrieve settings tabs
 * @since  0.1
 * @author Bryan Monzon
 * @param array $input The field value
 * @return string $input Sanitizied value
 */
function boiler_get_settings_tabs() {

    $settings = boiler_get_registered_settings();

    $tabs            = array();
    $tabs['general'] = __( 'General', 'boiler' );

    return apply_filters( 'boiler_settings_tabs', $tabs );
}
