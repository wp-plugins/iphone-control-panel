<?php
require_once( dirname(__FILE__) . '../../../../../wp-config.php');
require_once( dirname(__FILE__) . '../../iphone_control_panel.php');
header("Content-type: text/css");
 

global $options_icp;
foreach ($options_icp as $value) {
    if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } 
      else { $$value['id'] = get_settings( $value['id'] ); } 
} ?>
/*  
This css will only be seen by iPhones and iPod touches. The CSS in this page comes from the iPhone Control Panel Wordpress plugin.
*/

<?php echo $iphone_control_panel_css ?>