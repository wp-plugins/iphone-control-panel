<?php
/* 
Plugin Name: iPhone Control Panel
Plugin URI: http://www.adrian3.com/
Version: v0.2
Author: <a href="http://adrian3.com/">Adrian3</a>
Description: The iPhone Control Panel plugin makes it easy to customize how iPhone and iPod touch users see your website. You can add a custom icon to the iPhone's home screen, create custom css that affects only iPhones, resize the viewport, or redirect iPhones to a different url.

*/


//Original Framework http://theundersigned.net/2006/06/wordpress-how-to-theme-options/ 
//Updated and added additional options by Jeremy Clark http://clarktech.no-ip.com/
//Hacked and Frankensteined into a plugin by Adrian Hanft http://adrian3.com/

$themename_icp = "iPhone Control Panel";
$shortname_icp = "iphone_control_panel";
$options_icp = array (
    array(  "name" => "Bookmark icon*",
            "id" => $shortname_icp."_icon",
            "type" => "select",
            "std" => "icon.png",
            "options" => array("icon.png", 
			"alarm.png", 
			"bomb.png", 
			"butterfly.png", 
			"cards.png", 
			"chess.png", 
			"coffee1.png", 
			"coffee2.png", 
			"gamer.png", 
			"golf1.png", 
			"golf2.png", 
			"headphones.png", 
			"home.png", 
			"ladybug.png", 
			"life_ring.png",
			"life_vest.png", 
			"lightbulb.png", 
			"lightning.png", 
			"mac.png", 
			"magic.png", 
			"news.png", 
			"notepad.png", 
			"notepad2.png", 
			"painter.png", 
			"piano.png", 
			"planet.png", 
			"rss.png", 
			"snake.png", 
			"tron.png")),

    array(  "name" => "CSS*",
				            "id" => $shortname_icp."_css",
				            "std" => "/* enter your styles here */",
				            "type" => "textarea"),

	array(  "name" => "Viewport*",
		            "id" => $shortname_icp."_viewport",
		            "std" => "320",
		            "type" => "text"),

    array(  "name" => "Redirect*",
            "id" => $shortname_icp."_redirect",
            "std" => "",
            "type" => "text"),

  
);

function mytheme_add_iphone_panel() {

    global $themename_icp, $shortname_icp, $options_icp;

    if ( $_GET['page'] == basename(__FILE__) ) {
    
        if ( 'save' == $_REQUEST['action'] ) {

                foreach ($options_icp as $value) {
                    update_option( $value['id'], $_REQUEST[ $value['id'] ] ); }

                foreach ($options_icp as $value) {
                    if( isset( $_REQUEST[ $value['id'] ] ) ) { update_option( $value['id'], $_REQUEST[ $value['id'] ]  ); } else { delete_option( $value['id'] ); } }

                header("Location: options-general.php?page=iphone_control_panel.php&saved=true");
                die;

        } else if( 'reset' == $_REQUEST['action'] ) {

            foreach ($options_icp as $value) {
                delete_option( $value['id'] ); 
                update_option( $value['id'], $value['std'] );}

            header("Location: options-general.php?page=iphone_control_panel.php&reset=true");
            die;

        }
    }

    add_options_page($themename_icp." Options", "iPhone Control Panel", 'edit_themes', basename(__FILE__), 'mytheme_iphone_admin');

}


function add_iphone_panel() {
	global $options_icp;
	foreach ($options_icp as $value) {
	    if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } 
	      else { $$value['id'] = get_settings( $value['id'] ); } 
	}
	echo '<!-- viewport -->
		<meta name="viewport" content="width=';
		echo $iphone_control_panel_viewport;
		echo '" />

	<!-- iphone css -->
<link media="only screen and (max-device-width: 480px)" href="';

echo get_bloginfo('wpurl'); 
echo '/wp-content/plugins/iphone-control-panel/css/iphone_css.php';

echo '" type="text/css" rel="stylesheet" />

	<!-- apple touch icon -->
	<link rel="apple-touch-icon" href="';



	echo get_bloginfo('wpurl');
	echo '/wp-content/plugins/iphone-control-panel/icons/';
	echo $iphone_control_panel_icon;
	echo '"/>

	<!-- redirect iphones script -->
		<script language=javascript>
		<!--
		if((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i)))
		{
		location.href=\'';
	echo $iphone_control_panel_redirect;
	echo '\
		}
		-->
	</script>
	
	';
	
	
}
function mytheme_iphone_admin() {

    global $themename_icp, $shortname_icp, $options_icp;

    if ( $_REQUEST['saved'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename_icp.' settings saved.</strong></p></div>';
    if ( $_REQUEST['reset'] ) echo '<div id="message" class="updated fade"><p><strong>'.$themename_icp.' settings reset.</strong></p></div>';
    
?>
<div class="wrap">
<h2><?php echo $themename_icp; ?></h2>
<p>The iPhone Control Panel allows you to control how iPhones and iPod Touches will "see" your site. You can choose an icon that will be used when users add a bookmark to their home screen. You can create custom css that only affects touch devices. You can control the viewport, and you can even redirect iPhones and iPod touches to a different url.</p>


<form method="post">
<table width="873" border="0" cellspacing="0" cellpadding="0">
  <tr>
        <td width="420" valign="top">
	<table class="optiontable">

	<?php foreach ($options_icp as $value) { 

	if ($value['type'] == "text") { ?>

	<tr valign="top"> 
	    <th width="17" scope="row"><?php echo $value['name']; ?></th>
	    <td width="365">
	    <input name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php if ( get_settings( $value['id'] ) != "") { echo get_settings( $value['id'] ); } else { echo $value['std']; } ?>" size="40" />    </td>
      </tr>
	<tr><td colspan=2><hr /></td>
	  </tr>
	<?php } elseif ($value['type'] == "select") { ?>

	    <tr valign="top"> 
	        <th scope="top"><?php echo $value['name']; ?></th>
	        <td>
	            <select name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>">
	                <?php foreach ($value['options'] as $option) { ?>
	                <option<?php if ( get_settings( $value['id'] ) == $option) { echo ' selected="selected"'; }?>><?php echo $option; ?></option>
	                <?php } ?>
	            </select>        </td>
        </tr>
	<tr><td colspan=2><hr /></td>
	  </tr>
	<?php } elseif ($value['type'] == "radio") { ?>

	    <tr valign="top"> 
	        <th scope="top"><?php echo $value['name']; ?></th>
	        <td>
	                <?php foreach ($value['options'] as $option) { ?>
	      <?php echo $option; ?><input name="<?php echo $value['id']; ?>" type="<?php echo $value['type']; ?>" value="<?php echo $option; ?>" <?php if ( get_settings( $value['id'] ) == $option) { echo 'checked'; } ?>/> &nbsp;&nbsp;&nbsp;
	<?php } ?>        </td>
        </tr>
	<tr><td colspan=2><hr /></td>
	  </tr>
	<?php } elseif ($value['type'] == "textarea") { ?>

	    <tr valign="top"> 
	        <th scope="top"><?php echo $value['name']; ?></th>
	        <td>
	            <textarea name="<?php echo $value['id']; ?>" id="<?php echo $value['id']; ?>" cols="40" rows="16"/><?php if ( get_settings( $value['id'] ) != "") { echo get_settings( $value['id'] ); } else { echo $value['std']; } ?>
	</textarea>        </td>
        </tr>
	<tr><td colspan=2><hr />
		</td>
	  </tr>
	<?php } ?>
	<?php 
	}
	?>
	</table>

	    <p class="submit">
	<input name="save" type="submit" value="Save changes" />    
	<input type="hidden" name="action" value="save" />
	</p>
	</form>
	<form method="post">
	<p class="submit">
	<input name="reset" type="submit" value="Reset" />
	<input type="hidden" name="action" value="reset" />
	</p>
	</form>	
<h4>*Bookmark icon</h4>
<p>The bookmark icon dropdown menu above lets you choose the icon that appears on a users home screen when they bookmark your site. When you click "save changes" you will be able to see your new icon on the phone on the right.</p>
	
<p>If you want to us a custom icon of your own design, simply replace the "icon.png" in this plugin's folder with your own icon. The size of the icons are 57x57 pixels.</p>

<h4>*CSS</h4>
<p>You can enter custom css that will only be seen by iPhone and iPod Touches. Keep in mind that the phone will also see your other stylesheets, so you need to redefine your styles here in order to "overwrite" them. Also, there is a known bug that messes up single (') and double (") quotes so avoid them if at all possible until the problem is resolved.</p>

<h4>*Viewport</h4>
<p>Think of the viewport as the size of the window that is seen on the iPhone's screen. The width of the screen of the touch devices is 320 pixels when held vertically and 480 pixels when held horizontally. The optimum viewport for your site will vary so experiment with it until you get it right.</p>

<h4>*Redirect</h4>
<p>If you would like to redirect iPhone and iPod Touch devices to another url, enter it in this box. Other browsers are not affected by this redirect, just touch devices. This will add a redirect code to every page of your site, so use it carefully. Leave this box blank and no redirect will occur.</p>

<h4>Credits</h4>
<p>The iPhone Control Panel was created by Adrian Hanft. If you have feedback or bugs to report please <a href="http://adrian3.com/contact-adrian/" title="contact me">contact me</a> or leave a comment on <a href="http://adrian3.com/2008/10/iphone-control…rdpress-plugin/" title="this plugin's homepage">this plugin's homepage</a>. This plugin is free for you to use, but a link or mention on your blog would be greatly appreciated. Thanks, and I hope you enjoy using the iPhone Control Panel plugin!</p>
<p>(The icons used in this plugin come from <a href="http://www.everaldo.com/crystal/" title="Crystal">Crystal</a>, a great open source collection of icons.)</p>

    </td>
        <td width="453" valign="top"><table border="0" cellspacing="0" cellpadding="0" bgcolor="#000000">
  <tr>
    <td colspan="3"><img src="<?php	echo get_bloginfo('wpurl'); ?>/wp-content/plugins/iphone-control-panel/images/iphone_top.jpg" width="453" height="423" /></td>
  </tr>
  <tr>
    <td width="240"><img src="<?php	echo get_bloginfo('wpurl'); ?>/wp-content/plugins/iphone-control-panel/images/iphone_left.jpg" width="240" height="57" /></td>
    <td width="57" height="57"><img src="<?php echo get_bloginfo('wpurl'); ?>/wp-content/plugins/iphone-control-panel/icons/<?php 
	global $options_icp;
	foreach ($options_icp as $value) {
	    if (get_settings( $value['id'] ) === FALSE) { $$value['id'] = $value['std']; } 
	      else { $$value['id'] = get_settings( $value['id'] ); } 
	}
echo $iphone_control_panel_icon; ?>" width="57" height="57" /></td>
    <td width="156"><img src="<?php	echo get_bloginfo('wpurl'); ?>/wp-content/plugins/iphone-control-panel/images/iphone_right.jpg" width="156" height="57" /></td>
  </tr>
  <tr>
    <td colspan="3"><img src="<?php	echo get_bloginfo('wpurl'); ?>/wp-content/plugins/iphone-control-panel/images/iphone_bottom.jpg" width="453" height="334" /></td>
  </tr>
</table>
</td>
    </tr>
  </table>


<?php
}
add_action('admin_menu', 'mytheme_add_iphone_panel');
add_action('wp_head', 'add_iphone_panel'); ?>
