<?php

/**
 * Plugin Name: Qonvo Webchat
 * Plugin URI: http://www.qonvo.com
 * Description: Enable Qonvo WebChat on your Wordpress site.
 * Version: 1.0.1
 * Author: Qonvo
 * Author URI: http://qonvo.com
 * license: GPL2
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// Qonvo Settings
function qonvo_settings_init()
{
    // register a new setting for "qonvo" page
    register_setting('qonvo', 'qonvo_setting_customer_id');

    // register a new section in the "qonvo" page
    add_settings_section(
        'qonvo_settings_section',
        'Settings',
        'qonvo_settings_section_cb',
        'qonvo'
    );

    // register a new field in the "qonvo_settings_section" section, inside the "qonvo" page
    add_settings_field(
        'qonvo_settings_field',
        'Qonvo Customer ID',
        'qonvo_settings_field_cb',
        'qonvo',
        'qonvo_settings_section'
    );
}

function qonvo_settings_section_cb() {
  echo '<p>Set your Qonvo Web Chat client settings here. Your widget will not be displayed on your page without a customer ID.</p>';
}

function qonvo_settings_field_cb()
{
    // get the value of the setting we've registered with register_setting()
    $setting = get_option('qonvo_setting_customer_id');
    // output the field
    ?>
    <input type="text" name="qonvo_setting_customer_id" value="<?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?>">
    <?php
}

add_action('admin_init', 'qonvo_settings_init');

function qonvo_settings_page() {
  add_menu_page(
    'Qonvo Web Chat',
    'Qonvo Settings',
    'manage_options',
    'qonvo',
    'qonvo_settings_page_html'
  );
}

add_action( 'admin_menu', 'qonvo_settings_page' );

function qonvo_settings_page_html() {
  // check user capabilities
 if ( ! current_user_can( 'manage_options' ) ) {
 return;
 }

 // add error/update messages

 // check if the user have submitted the settings
 // wordpress will add the "settings-updated" $_GET parameter to the url
 if ( isset( $_GET['settings-updated'] ) ) {
 // add settings saved message with the class of "updated"
 add_settings_error( 'qonvo_messages', 'qonvo_message', __( 'Settings Saved', 'qonvo' ), 'updated' );
 }

 // show error/update messages
 settings_errors( 'qonvo_messages' );
 ?>
 <div class="wrap">
 <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>
 <form action="options.php" method="post">
 <?php
 // output security fields for the registered setting "qonvo"
 settings_fields( 'qonvo' );
 // output setting sections and their fields
 // (sections are registered for "qonvo", each field is registered to a specific section)
 do_settings_sections( 'qonvo' );
 // output save settings button
 submit_button( 'Save Settings' );
 ?>
 </form>
 </div>
 <?php
}

// Add Qonvo widget to header of page
add_action( 'wp_head', 'qonvo_scripts' );
function qonvo_scripts() {
  $setting = get_option('qonvo_setting_customer_id');

  if ( esc_attr( $setting ) ) {
    ?>
    <script type="text/javascript">
      if ( !window.qonvo ) { (function(d,e,s,p){
          var f = d.createElement(e);
          f.async = true, f.src = s, f.onload = f.onreadystatechange = function() {
              if( window.qonvo && qonvo.loaded ) return;
              if (d.readyState == "loading") { d.addEventListener('DOMContentLoaded', function() { qonvo.init(p); }) } else { qonvo.init(p); };
          };
          var ref = d.getElementsByTagName(e)[0]; ref.parentNode.insertBefore(f, ref);
      })(document, 'script', 'https://www.qonvo.com/qonvo_plugin.js', { customerId: "<?php echo isset( $setting ) ? esc_attr( $setting ) : ''; ?>" }); };
    </script>
    <?php
  }


}

?>
