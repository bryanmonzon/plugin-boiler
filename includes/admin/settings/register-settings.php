<?php
/**
 * Register Settings
 *
 * @package     Fifty Framework Boiler
 * @subpackage  Admin/Settings
 * @copyright   Copyright (c) 2013, Fifty & Fifty
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
function ffw_boiler_get_option( $key = '', $default = false ) {
    global $ffw_boiler_settings;
    return isset( $ffw_boiler_settings[ $key ] ) ? $ffw_boiler_settings[ $key ] : $default;
}

/**
 * Get Settings
 *
 * Retrieves all plugin settings
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return array FFW_BOILER settings
 */
function ffw_boiler_get_settings() {

    $settings = get_option( 'ffw_boiler_settings' );
    if( empty( $settings ) ) {

        // Update old settings with new single option

        $general_settings = is_array( get_option( 'ffw_boiler_settings_general' ) )    ? get_option( 'ffw_boiler_settings_general' )      : array();


        $settings = array_merge( $general_settings );

        update_option( 'ffw_boiler_settings', $settings );
    }
    return apply_filters( 'ffw_boiler_get_settings', $settings );
}

/**
 * Add all settings sections and fields
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return void
*/
function ffw_boiler_register_settings() {

    if ( false == get_option( 'ffw_boiler_settings' ) ) {
        add_option( 'ffw_boiler_settings' );
    }

    foreach( ffw_boiler_get_registered_settings() as $tab => $settings ) {

        add_settings_section(
            'ffw_boiler_settings_' . $tab,
            __return_null(),
            '__return_false',
            'ffw_boiler_settings_' . $tab
        );

        foreach ( $settings as $option ) {
            add_settings_field(
                'ffw_boiler_settings[' . $option['id'] . ']',
                $option['name'],
                function_exists( 'ffw_boiler_' . $option['type'] . '_callback' ) ? 'ffw_boiler_' . $option['type'] . '_callback' : 'ffw_boiler_missing_callback',
                'ffw_boiler_settings_' . $tab,
                'ffw_boiler_settings_' . $tab,
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
    register_setting( 'ffw_boiler_settings', 'ffw_boiler_settings', 'ffw_boiler_settings_sanitize' );

}
add_action('admin_init', 'ffw_boiler_register_settings');

/**
 * Retrieve the array of plugin settings
 *
 * @since  0.1
 * @author Bryan Monzon
 * @return array
*/
function ffw_boiler_get_registered_settings() {

    $pages = get_pages();
    $pages_options = array( 0 => '' ); // Blank option
    if ( $pages ) {
        foreach ( $pages as $page ) {
            $pages_options[ $page->ID ] = $page->post_title;
        }
    }

    /**
     * 'Whitelisted' FFW_BOILER settings, filters are provided for each settings
     * section to allow extensions and other plugins to add their own settings
     */
    $ffw_boiler_settings = array(
        /** General Settings */
        'general' => apply_filters( 'ffw_boiler_settings_general',
            array(
                'basic_settings' => array(
                    'id' => 'basic_settings',
                    'name' => '<strong>' . __( 'Basic Settings', 'ffw_boiler' ) . '</strong>',
                    'desc' => '',
                    'type' => 'header'
                ),
                'boiler_slug' => array(
                    'id' => 'boiler_slug',
                    'name' => __( ffw_boiler_get_label_plural() . ' URL Slug', 'ffw_boiler' ),
                    'desc' => __( 'Enter the slug you would like to use for your ' . strtolower( ffw_boiler_get_label_plural() ) . '.'  , 'ffw_boiler' ),
                    'type' => 'text',
                    'size' => 'medium',
                    'std' => strtolower( ffw_boiler_get_label_plural() )
                ),
                'boiler_label_plural' => array(
                    'id' => 'boiler_label_plural',
                    'name' => __( ffw_boiler_get_label_plural() . ' Label Plural', 'ffw_boiler' ),
                    'desc' => __( 'Enter the label you would like to use for your ' . strtolower( ffw_boiler_get_label_plural() ) . '.', 'ffw_boiler' ),
                    'type' => 'text',
                    'size' => 'medium',
                    'std' => ffw_boiler_get_label_plural()
                ),
                'boiler_label_singular' => array(
                    'id' => 'boiler_label_singular',
                    'name' => __( ffw_boiler_get_label_singular() . ' Label Singular', 'ffw_boiler' ),
                    'desc' => __( 'Enter the label you would like to use for your ' . strtolower( ffw_boiler_get_label_singular() ) . '.', 'ffw_boiler' ),
                    'type' => 'text',
                    'size' => 'medium',
                    'std' => ffw_boiler_get_label_singular()
                ),
            )
        ),
        
    );

    return $ffw_boiler_settings;
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
function ffw_boiler_header_callback( $args ) {
    $html = '<label for="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';
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
 * @global $ffw_boiler_settings Array of all the FFW_BOILER Options
 * @return void
 */
function ffw_boiler_checkbox_callback( $args ) {
    global $ffw_boiler_settings;

    $checked = isset($ffw_boiler_settings[$args['id']]) ? checked(1, $ffw_boiler_settings[$args['id']], false) : '';
    $html = '<input type="checkbox" id="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" value="1" ' . $checked . '/>';
    $html .= '<label for="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

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
 * @global $ffw_boiler_settings Array of all the FFW_BOILER Options
 * @return void
 */
function ffw_boiler_multicheck_callback( $args ) {
    global $ffw_boiler_settings;

    foreach( $args['options'] as $key => $option ):
        if( isset( $ffw_boiler_settings[$args['id']][$key] ) ) { $enabled = $option; } else { $enabled = NULL; }
        echo '<input name="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']" id="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']" type="checkbox" value="' . $option . '" ' . checked($option, $enabled, false) . '/>&nbsp;';
        echo '<label for="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
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
 * @global $ffw_boiler_settings Array of all the FFW_BOILER Options
 * @return void
 */
function ffw_boiler_radio_callback( $args ) {
    global $ffw_boiler_settings;

    foreach ( $args['options'] as $key => $option ) :
        $checked = false;

        if ( isset( $ffw_boiler_settings[ $args['id'] ] ) && $ffw_boiler_settings[ $args['id'] ] == $key )
            $checked = true;
        elseif( isset( $args['std'] ) && $args['std'] == $key && ! isset( $ffw_boiler_settings[ $args['id'] ] ) )
            $checked = true;

        echo '<input name="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']"" id="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']" type="radio" value="' . $key . '" ' . checked(true, $checked, false) . '/>&nbsp;';
        echo '<label for="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . '][' . $key . ']">' . $option . '</label><br/>';
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
 * @global $ffw_boiler_settings Array of all the FFW_BOILER Options
 * @return void
 */
function ffw_boiler_text_callback( $args ) {
    global $ffw_boiler_settings;

    if ( isset( $ffw_boiler_settings[ $args['id'] ] ) )
        $value = $ffw_boiler_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="text" class="' . $size . '-text" id="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
    $html .= '<label for="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

    echo $html;
}


/**
 * FFW_BOILER Hidden Text Field Callback
 *
 * Renders text fields (Hidden, for necessary values in ffw_boiler_settings in the wp_options table)
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $ffw_boiler_settings Array of all the FFW_BOILER Options
 * @return void
 * @todo refactor it is not needed entirely
 */
function ffw_boiler_hidden_callback( $args ) {
    global $ffw_boiler_settings;

    $hidden = isset($args['hidden']) ? $args['hidden'] : false;

    if ( isset( $ffw_boiler_settings[ $args['id'] ] ) )
        $value = $ffw_boiler_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="hidden" class="' . $size . '-text" id="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
    $html .= '<label for="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['std'] . '</label>';

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
 * @global $ffw_boiler_settings Array of all the FFW_BOILER Options
 * @return void
 */
function ffw_boiler_textarea_callback( $args ) {
    global $ffw_boiler_settings;

    if ( isset( $ffw_boiler_settings[ $args['id'] ] ) )
        $value = $ffw_boiler_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<textarea class="large-text" cols="50" rows="5" id="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
    $html .= '<label for="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

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
 * @global $ffw_boiler_settings Array of all the FFW_BOILER Options
 * @return void
 */
function ffw_boiler_password_callback( $args ) {
    global $ffw_boiler_settings;

    if ( isset( $ffw_boiler_settings[ $args['id'] ] ) )
        $value = $ffw_boiler_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="password" class="' . $size . '-text" id="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( $value ) . '"/>';
    $html .= '<label for="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

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
function ffw_boiler_missing_callback($args) {
    printf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'ffw_boiler' ), $args['id'] );
}

/**
 * Select Callback
 *
 * Renders select fields.
 *
 * @since  0.1
 * @author Bryan Monzon
 * @param array $args Arguments passed by the setting
 * @global $ffw_boiler_settings Array of all the FFW_BOILER Options
 * @return void
 */
function ffw_boiler_select_callback($args) {
    global $ffw_boiler_settings;

    if ( isset( $ffw_boiler_settings[ $args['id'] ] ) )
        $value = $ffw_boiler_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $html = '<select id="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']"/>';

    foreach ( $args['options'] as $option => $name ) :
        $selected = selected( $option, $value, false );
        $html .= '<option value="' . $option . '" ' . $selected . '>' . $name . '</option>';
    endforeach;

    $html .= '</select>';
    $html .= '<label for="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

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
 * @global $ffw_boiler_settings Array of all the FFW_BOILER Options
 * @return void
 */
function ffw_boiler_color_select_callback( $args ) {
    global $ffw_boiler_settings;

    if ( isset( $ffw_boiler_settings[ $args['id'] ] ) )
        $value = $ffw_boiler_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $html = '<select id="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']"/>';

    foreach ( $args['options'] as $option => $color ) :
        $selected = selected( $option, $value, false );
        $html .= '<option value="' . $option . '" ' . $selected . '>' . $color['label'] . '</option>';
    endforeach;

    $html .= '</select>';
    $html .= '<label for="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

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
 * @global $ffw_boiler_settings Array of all the FFW_BOILER Options
 * @global $wp_version WordPress Version
 */
function ffw_boiler_rich_editor_callback( $args ) {
    global $ffw_boiler_settings, $wp_version;

    if ( isset( $ffw_boiler_settings[ $args['id'] ] ) )
        $value = $ffw_boiler_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    if ( $wp_version >= 3.3 && function_exists( 'wp_editor' ) ) {
        $html = wp_editor( stripslashes( $value ), 'ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']', array( 'textarea_name' => 'ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']' ) );
    } else {
        $html = '<textarea class="large-text" rows="10" id="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']">' . esc_textarea( stripslashes( $value ) ) . '</textarea>';
    }

    $html .= '<br/><label for="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

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
 * @global $ffw_boiler_settings Array of all the FFW_BOILER Options
 * @return void
 */
function ffw_boiler_upload_callback( $args ) {
    global $ffw_boiler_settings;

    if ( isset( $ffw_boiler_settings[ $args['id'] ] ) )
        $value = $ffw_boiler_settings[$args['id']];
    else
        $value = isset($args['std']) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="text" class="' . $size . '-text ffw_boiler_upload_field" id="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( stripslashes( $value ) ) . '"/>';
    $html .= '<span>&nbsp;<input type="button" class="ffw_boiler_settings_upload_button button-secondary" value="' . __( 'Upload File', 'ffw_boiler' ) . '"/></span>';
    $html .= '<label for="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

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
 * @global $ffw_boiler_settings Array of all the FFW_BOILER Options
 * @return void
 */
function ffw_boiler_color_callback( $args ) {
    global $ffw_boiler_settings;

    if ( isset( $ffw_boiler_settings[ $args['id'] ] ) )
        $value = $ffw_boiler_settings[ $args['id'] ];
    else
        $value = isset( $args['std'] ) ? $args['std'] : '';

    $default = isset( $args['std'] ) ? $args['std'] : '';

    $size = ( isset( $args['size'] ) && ! is_null( $args['size'] ) ) ? $args['size'] : 'regular';
    $html = '<input type="text" class="ffw_boiler-color-picker" id="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" name="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']" value="' . esc_attr( $value ) . '" data-default-color="' . esc_attr( $default ) . '" />';
    $html .= '<label for="ffw_boiler_settings_' . $args['section'] . '[' . $args['id'] . ']"> '  . $args['desc'] . '</label>';

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
function ffw_boiler_hook_callback( $args ) {
    do_action( 'ffw_boiler_' . $args['id'] );


    
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
function ffw_boiler_settings_sanitize( $input = array() ) {

    global $ffw_boiler_settings;

    parse_str( $_POST['_wp_http_referer'], $referrer );

    $output    = array();
    $settings  = ffw_boiler_get_registered_settings();
    $tab       = isset( $referrer['tab'] ) ? $referrer['tab'] : 'general';
    $post_data = isset( $_POST[ 'ffw_boiler_settings_' . $tab ] ) ? $_POST[ 'ffw_boiler_settings_' . $tab ] : array();

    $input = apply_filters( 'ffw_boiler_settings_' . $tab . '_sanitize', $post_data );

    // Loop through each setting being saved and pass it through a sanitization filter
    foreach( $input as $key => $value ) {

        // Get the setting type (checkbox, select, etc)
        $type = isset( $settings[ $key ][ 'type' ] ) ? $settings[ $key ][ 'type' ] : false;

        if( $type ) {
            // Field type specific filter
            $output[ $key ] = apply_filters( 'ffw_boiler_settings_sanitize_' . $type, $value, $key );
        }

        // General filter
        $output[ $key ] = apply_filters( 'ffw_boiler_settings_sanitize', $value, $key );
    }


    // Loop through the whitelist and unset any that are empty for the tab being saved
    if( ! empty( $settings[ $tab ] ) ) {
        foreach( $settings[ $tab ] as $key => $value ) {

            // settings used to have numeric keys, now they have keys that match the option ID. This ensures both methods work
            if( is_numeric( $key ) ) {
                $key = $value['id'];
            }

            if( empty( $_POST[ 'ffw_boiler_settings_' . $tab ][ $key ] ) ) {
                unset( $ffw_boiler_settings[ $key ] );
            }

        }
    }

    // Merge our new settings with the existing
    $output = array_merge( $ffw_boiler_settings, $output );

    // @TODO: Get Notices Working in the backend.
    add_settings_error( 'ffw_boiler-notices', '', __( 'Settings Updated', 'ffw_boiler' ), 'updated' );

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
function ffw_boiler_sanitize_text_field( $input ) {
    return trim( $input );
}
add_filter( 'ffw_boiler_settings_sanitize_text', 'ffw_boiler_sanitize_text_field' );

/**
 * Retrieve settings tabs
 * @since  0.1
 * @author Bryan Monzon
 * @param array $input The field value
 * @return string $input Sanitizied value
 */
function ffw_boiler_get_settings_tabs() {

    $settings = ffw_boiler_get_registered_settings();

    $tabs            = array();
    $tabs['general'] = __( 'General', 'ffw_boiler' );

    return apply_filters( 'ffw_boiler_settings_tabs', $tabs );
}
