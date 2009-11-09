<?php
/* 
Plugin Name: iPhone Control Panel
Plugin URI: http://www.adrian3.com/
Version: v0.6
Author: <a href="http://adrian3.com/">Adrian3</a>
Description: The iPhone Control Panel plugin makes it easy to customize how iPhone and iPod touch users see your website. You can add a custom icon to the iPhone's home screen, create custom css that affects only iPhones, resize the viewport, or redirect iPhones to a different url.

*/


/*  Copyright 2009  

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/**
* Guess the wp-content and plugin urls/paths
*/
// Pre-2.6 compatibility
if ( ! defined( 'WP_CONTENT_URL' ) )
      define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
      define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
      define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
      define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );


if (!class_exists('iphone_cp_ad3')) {
    class iphone_cp_ad3 {
        //This is where the class variables go, don't forget to use @var to tell what they're for
        /**
        * @var string The options string name for this plugin
        */
        var $optionsName = 'iphone_cp_ad3_options';
        
        /**
        * @var string $localizationDomain Domain used for localization
        */
        var $localizationDomain = "iphone_cp_ad3";
        
        /**
        * @var string $pluginurl The path to this plugin
        */ 
        var $thispluginurl = '';
        /**
        * @var string $pluginurlpath The path to this plugin
        */
        var $thispluginpath = '';
            
        /**
        * @var array $options Stores the options for this plugin
        */
        var $options = array();
        
        //Class Functions
        /**
        * PHP 4 Compatible Constructor
        */
        function iphone_cp_ad3(){$this->__construct();}
        
        /**
        * PHP 5 Constructor
        */        
        function __construct(){
            //Language Setup
            $locale = get_locale();
            $mo = dirname(__FILE__) . "/languages/" . $this->localizationDomain . "-".$locale.".mo";
            load_textdomain($this->localizationDomain, $mo);

            //"Constants" setup
            $this->thispluginurl = PLUGIN_URL . '/' . dirname(plugin_basename(__FILE__)).'/';
            $this->thispluginpath = PLUGIN_PATH . '/' . dirname(plugin_basename(__FILE__)).'/';
            
            //Initialize the options
            //This is REQUIRED to initialize the options when the plugin is loaded!
            $this->getOptions();
            
            //Actions        
            add_action("admin_menu", array(&$this,"admin_menu_link"));

            
            //Widget Registration Actions
            add_action('plugins_loaded', array(&$this,'register_widgets'));
            



            add_action("wp_head", array(&$this,"add_iphone_control_panel"));
            add_action("wp_head", array(&$this,"iphone_redirection"));
            add_action("wp_head", array(&$this,"iphone_css"));
		
            /*            add_action('wp_print_scripts', array(&$this, 'add_js'));
            */
            
            //Filters
            /*
            add_filter('the_content', array(&$this, 'filter_content'), 0);
            */
        }




function add_iphone_control_panel() {
echo '<!-- viewport -->
<meta name="viewport" content="width=';
echo $this->options['iphone_cp_ad3_viewport']; 
		echo '" />
<!-- apple touch icon -->
<link rel="apple-touch-icon" href="';
echo get_bloginfo('wpurl');
echo '/wp-content/plugins/iphone-control-panel/icons/';
echo $this->options['iphone_cp_ad3_icon']; 
echo '"/>';	
}     



function iphone_css() {
         if ($this->options['iphone_cp_ad3_css_on_off'] == 'on') {
	echo '<!-- iphone css -->
<style type="text/css">
<!--
@media only screen and (max-device-width: 480px) {';
echo $this->options['iphone_cp_ad3_css']; 	
echo '  }
}
-->
</style>'
; }}



function iphone_redirection() {
         if ($this->options['redirect_on_off'] == 'on') {
	echo '
<script language=javascript>
if((navigator.userAgent.match(/iPhone/i)) || (navigator.userAgent.match(/iPod/i))) { location.href=\'';
echo $this->options['iphone_cp_ad3_redirect_url']; 	
echo '\'} </script>
'
; }}

      

      
        /**
        * Retrieves the plugin options from the database.
        * @return array
        */
        function getOptions() {

            //Don't forget to set up the default options
            if (!$theOptions = get_option($this->optionsName)) {
                $theOptions = array(
	'iphone_cp_ad3_viewport'=>'320',
	'redirect_on_off'=>'off',
	'iphone_cp_ad3_icon'=>'home.png',
	'iphone_cp_ad3_css_on_off'=>'off'
	);
                update_option($this->optionsName, $theOptions);
            }
            $this->options = $theOptions;
            
            //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!f
            //There is no return here, because you should use the $this->options variable!!!
            //!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
        }
        /**
        * Saves the admin options to the database.
        */
        function saveAdminOptions(){
            return update_option($this->optionsName, $this->options);
        }
        
        /**
        * @desc Adds the options subpanel
        */
        function admin_menu_link() {
            //If you change this from add_options_page, MAKE SURE you change the filter_plugin_actions function (below) to
            //reflect the page filename (ie - options-general.php) of the page your plugin is under!
            add_options_page('iPhone Control Panel', 'iPhone Control Panel', 10, basename(__FILE__), array(&$this,'admin_options_page'));
            add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), array(&$this, 'filter_plugin_actions'), 10, 2 );
        }
        
        /**
        * @desc Adds the Settings link to the plugin activate/deactivate page
        */
        function filter_plugin_actions($links, $file) {
           //If your plugin is under a different top-level menu than Settiongs (IE - you changed the function above to something other than add_options_page)
           //Then you're going to want to change options-general.php below to the name of your top-level page
           $settings_link = '<a href="options-general.php?page=' . basename(__FILE__) . '">' . __('Settings') . '</a>';
           array_unshift( $links, $settings_link ); // before other links

           return $links;
        }
        
        /**
        * Adds settings/options page
        */
        function admin_options_page() { 
            if($_POST['iphone_cp_ad3_save']){
                if (! wp_verify_nonce($_POST['_wpnonce'], 'iphone_cp_ad3-update-options') ) die('Whoops! There was a problem with the data you posted. Please go back and try again.'); 
                $this->options['iphone_cp_ad3_viewport'] = $_POST['iphone_cp_ad3_viewport'];
                $this->options['iphone_cp_ad3_css'] = $_POST['iphone_cp_ad3_css'];
                $this->options['iphone_cp_ad3_icon'] = $_POST['iphone_cp_ad3_icon'];
                $this->options['redirect_on_off'] = $_POST['redirect_on_off'];
                $this->options['iphone_cp_ad3_redirect_url'] = $_POST['iphone_cp_ad3_redirect_url'];
                $this->options['iphone_cp_ad3_css_on_off'] = $_POST['iphone_cp_ad3_css_on_off'];
                                        
                $this->saveAdminOptions();
                
                echo '<div class="updated"><p>Success! Your changes were sucessfully saved!</p></div>';
            }
?>                                   
                <div class="wrap">
                <h2>iPhone Control Panel</h2>
                <table width="759" border="0" cellspacing="0" cellpadding="0">
                  <tr>
                    <td colspan="3" valign="top"><p>The iPhone Control Panel allows you to control how iPhones and iPod Touches will "see" your site. You can choose an icon that will be used when users add a bookmark to their home screen. You can create custom css that only affects touch devices. You can control the viewport, and you can even redirect iPhones and iPod touches to a different url.</p></td>
                  </tr>
                  <tr>
                    <td width="371" valign="top">
  <form method="post" id="iphone_cp_ad3_options">
                <?php wp_nonce_field('iphone_cp_ad3-update-options'); ?>
                  
				  
				  
				  
			<?php _e('Bookmark Icon*:', $this->localizationDomain); ?>
            
                                          <p>
                                            <select name="iphone_cp_ad3_icon" id="iphone_cp_ad3_icon">
                                              <option selected="selected"><?php echo $this->options['iphone_cp_ad3_icon'] ;?></option>
                                              <option>icon.png</option>
                                              <option>alarm.png</option>
                                              <option>bomb.png</option>
                                              <option>cards.png</option>
                                              <option>chess.png</option>
                                              <option>coffee1.png</option>
                                              <option>coffee2.png</option>
                                              <option>gamer.png</option>
                                              <option>golf1.png</option>
                                              <option>golf2.png</option>
                                              <option>headphones.png</option>
                                              <option>home.png</option>
                                              <option>ladybug.png</option>
                                              <option>life_ring.png</option>
                                              <option>life_vest.png</option>
                                              <option>lightbulb.png</option>
                                              <option>lightning.png</option>
                                              <option>mac.png</option>
                                              <option>magic.png</option>
                                              <option>news.png</option>
                                              <option>notepad.png</option>
                                              <option>notepad2.png</option>
                                              <option>painter.png</option>
                                              <option>piano.png</option>
                                              <option>planet.png</option>
                                              <option>rss.png</option>
                                              <option>snake.png</option>
                                              <option>tron.png</option>
                                            </select>
                                          </p>
<hr />
                                          <p>
                                            <?php _e('Custom CSS On/Off*:', $this->localizationDomain); ?>
                                            <select name="iphone_cp_ad3_css_on_off" id="iphone_cp_ad3_css_on_off">
                                              <option selected="selected"><?php echo $this->options['iphone_cp_ad3_css_on_off'] ;?></option>
                                              <option>on</option>
                                              <option>off</option>
                                            </select>
                          </p>
<hr />
                                          <p>
                                            <?php _e('Custom CSS*:', $this->localizationDomain); ?>
                                            <textarea name="iphone_cp_ad3_css" cols="45" rows="10" id="iphone_cp_ad3_css"><?php echo $this->options['iphone_cp_ad3_css'] ;?></textarea>
        </p>

<hr />
                                          <p>
  <?php _e('Viewport*', $this->localizationDomain); ?>
  <input name="iphone_cp_ad3_viewport" type="text" id="iphone_cp_ad3_viewport" value="<?php echo $this->options['iphone_cp_ad3_viewport'] ;?>"/>
                          </p>
<hr />
                                          <p>
                                            <?php _e('Redirect On/Off*:', $this->localizationDomain); ?>
                                            <select name="redirect_on_off" id="redirect_on_off">
                                              <option selected="selected"><?php echo $this->options['redirect_on_off'] ;?></option>
                                              <option>on</option>
                                              <option>off</option>
                                            </select>
                          </p>

<hr />
                                          <p>
  <?php _e('Redirect URL*', $this->localizationDomain); ?>
  <input name="iphone_cp_ad3_redirect_url" type="text" id="iphone_cp_ad3_redirect_url" value="<?php echo $this->options['iphone_cp_ad3_redirect_url'] ;?>"/>
                                          </p>

<hr />
                                          <p>
                                            <input type="submit" name="iphone_cp_ad3_save" value="Save" />
        </p></form></td>
                    <td width="29">&nbsp;</td>
                    <td width="359"><table border="0" width="453" cellspacing="0" cellpadding="0" bgcolor="#000000">
                      <tr>
                        <td colspan="3"><img src="<?php	echo get_bloginfo('wpurl'); ?>/wp-content/plugins/iphone-control-panel/images/iphone_top.jpg" width="453" height="423" /></td>
                      </tr>
                      <tr>
                        <td width="240"><img src="<?php	echo get_bloginfo('wpurl'); ?>/wp-content/plugins/iphone-control-panel/images/iphone_left.jpg" width="240" height="57" /></td>
                        <td width="57" height="57"><img src="<?php echo get_bloginfo('wpurl'); ?>/wp-content/plugins/iphone-control-panel/icons/<?php 
echo $this->options['iphone_cp_ad3_icon'];?>" width="57" height="57" /></td>
                        <td width="156"><img src="<?php	echo get_bloginfo('wpurl'); ?>/wp-content/plugins/iphone-control-panel/images/iphone_right.jpg" width="156" height="57" /></td>
                      </tr>
                      <tr>
                        <td colspan="3"><img src="<?php	echo get_bloginfo('wpurl'); ?>/wp-content/plugins/iphone-control-panel/images/iphone_bottom.jpg" width="453" height="334" /></td>
                      </tr>
                    </table></td>
                  </tr>
                  <tr>
                    <td valign="top">&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                  </tr>
                  <tr>
                    <td colspan="3" valign="top"><h4>*Bookmark icon</h4>
                      <p>The bookmark icon dropdown menu above lets you choose the icon that appears on a users home screen when they bookmark your site. When you click "save changes" you will be able to see your new icon on the phone on the right.</p>
                      <p>If you want to us a custom icon of your own design, simply replace the "icon.png" in this plugin's folder with your own icon. The size of the icons are 57x57 pixels.</p>
                      <h4>*CSS</h4>
                      <p>You can enter custom css that will only be seen by iPhone and iPod Touches. Keep in mind that the phone will also see your other stylesheets, so you need to redefine your styles here in order to "overwrite" them.</p>
                      <h4>*Viewport</h4>
                      <p>Think of the viewport as the size of the window that is seen on the iPhone's screen. The width of the screen of the touch devices is 320 pixels when held vertically and 480 pixels when held horizontally. The optimum viewport for your site will vary so experiment with it until you get it right.</p>
                      <h4>*Redirect On/Off</h4>
                      <p>Select whether or not you want to redirect iphone/ipod touch users to a different url.</p>
                      <h4>*Redirect URL</h4>
                      <p>If you would like to redirect iPhone and iPod Touch devices to another url, enter it in this box. Other browsers are not affected by this redirect, just touch devices. This will add a redirect code to every page of your site, so use it carefully. Make sure to include the "http://" and don't forget to set the "on/off" dropdown to on.'</p>
                      <h4>Credits</h4>
                      <p>The iPhone Control Panel was created by Adrian Hanft. If you have feedback or bugs to report please <a href="http://adrian3.com/contact-adrian/" title="contact me">contact me</a> or leave a comment on <a href="http://adrian3.com/2008/10/iphone-control…rdpress-plugin/" title="this plugin's homepage">this plugin's homepage</a>. This plugin is free for you to use, but a link or mention on your blog would be greatly appreciated. Thanks, and I hope you enjoy using the iPhone Control Panel plugin!</p>
                    <p>(The icons used in this plugin come from <a href="http://www.everaldo.com/crystal/" title="Crystal">Crystal</a>, a great open source collection of icons.)</p></td>
                  </tr>
                </table>
<p>&nbsp;</p></td>
<td rowspan="5">&nbsp;</td>
                        </tr>

                        <tr valign="top"> 
                          <th scope="row">&nbsp;</th> 
                            <td>&nbsp;</td>
                        </tr>     

                        <tr valign="top"> 
                            <th scope="row">&nbsp;</th> 
                            <td>&nbsp;</td>
                        </tr>

                        <tr valign="top"> 
                            <th scope="row">&nbsp;</th> 
                            			<td>&nbsp;</td>
               			</tr>

                        <tr valign="top"> 
                            <th scope="row">&nbsp;</th> 
                            <td>&nbsp;</td>
                        </tr>




                        <tr valign="top"> 


                            <th colspan=3><h4>&nbsp;</h4>
</th>
                        </tr>
                    </table>

<?php
        }
     
        
  } //End Class
} //End if class exists statement

//instantiate the class
if (class_exists('iphone_cp_ad3')) {
    $iphone_cp_ad3_var = new iphone_cp_ad3();
}

?>