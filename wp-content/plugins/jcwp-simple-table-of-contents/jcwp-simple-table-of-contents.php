<?php
  /*
    Plugin Name: jcwp simple table of contents
    Plugin URI: http://jaspreetchahal.org/wordpress-simple-table-of-contents-plugin
    Description: This plugin gives options to display "Table of contents" container on your wordpress post or page
    Author: Jaspreet Chahal
    Version: 1.01
    Author URI: http://jaspreetchahal.org
    License: GPLv2 or later
    */

    /*
    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.
    */
    
    // if not an admin just block access
    if(preg_match('/admin\.php/',$_SERVER['REQUEST_URI']) && is_admin() == false) {
        return false;
    }
    register_activation_hook(__FILE__,'jcorgtoc_activate');
    function jcorgtoc_activate() {
            add_option('jcorgtoc_active','1');
            add_option('jcorgtoc_duration',1000);
            add_option('jcorgtoc_title',"Table of contents");
            add_option('jcorgtoc_easingType',"easeInOutQuad");
            add_option('jcorgtoc_textlength',50);
            add_option('jcorgtoc_height',"300");
            add_option('jcorgtoc_scroll','Yes');
            add_option('jcorgtoc_scroll_element','html,body');
            add_option('jcorgtoc_position',"bottomleft");
            add_option('jcorgtoc_minimize','No');
            add_option('jcorgtoc_linkback','No');
            add_option('jcorgtoc_parenttagclass','div.entry-content');
    }
    
    add_action("admin_menu","jcorgtoc_menu");
    function jcorgtoc_menu() {
        add_options_page('JCWP Table Of Contents', 'JCWP Table Of Contents', 'manage_options', 'jcorgtoc-plugin', 'jcorgtoc_plugin_options');
    }
    add_action('admin_init','jcorgtoc_regsettings');
    function jcorgtoc_regsettings() {        
        register_setting("jcorgtoc-setting","jcorgtoc_active");
        register_setting("jcorgtoc-setting","jcorgtoc_parenttagclass");  
        register_setting("jcorgtoc-setting","jcorgtoc_duration");
        register_setting("jcorgtoc-setting","jcorgtoc_title");
        register_setting("jcorgtoc-setting","jcorgtoc_easingType");     
        register_setting("jcorgtoc-setting","jcorgtoc_textlength");
        register_setting("jcorgtoc-setting","jcorgtoc_height");     
        register_setting("jcorgtoc-setting","jcorgtoc_scroll");     
        register_setting("jcorgtoc-setting","jcorgtoc_scroll_element");     
        register_setting("jcorgtoc-setting","jcorgtoc_position");     
        register_setting("jcorgtoc-setting","jcorgtoc_minimize");      
        register_setting("jcorgtoc-setting","jcorgtoc_linkback");        
        wp_enqueue_script('jquery');
        wp_enqueue_script('jqueryui');
    }
    
    
    add_action('wp_head','jcorgtoc_init');
    function jcorgtoc_init() {        
        
        wp_enqueue_script('jquery');
        wp_enqueue_script('jquery-ui-core');    
        wp_enqueue_script('jcorgtoc_script',plugins_url("jquery.jscrollpane.min.js",__FILE__), array('jquery', 'jquery-ui-core', 'jquery-effects-core'),'1.0');
        wp_enqueue_script('jcorgtoc_mwscript',plugins_url("jquery.mousewheel.js",__FILE__), array('jquery'),'1.0');
        wp_enqueue_script('jcorgtoc_tocscript',plugins_url("jcwptoc.js",__FILE__), array('jquery', 'jquery-ui-core'),'1.0');
        wp_enqueue_style('jcorgtoc_jsspstyl',plugins_url("jquery.jscrollpane.css",__FILE__));
        wp_enqueue_style('jcorgtoc_jsspstyltoc',plugins_url("jcwptoc.css",__FILE__));
    }
    add_action('wp_footer','jcorgtoc_inclscript',20);
    function jcorgtoc_inclscript() {
        if(get_option('jcorgtoc_active') == "1" && (is_single() || is_page())) {
        ?> 
         <script>                                
                               
         jQuery(document).ready(function(){
                      jQuery('.entry-content').jcSimpleTOC({
                          position:"<?php echo strlen(trim(get_option("jcorgtoc_position")))>0?trim(get_option("jcorgtoc_position")):'right'?>",
                          scrollSpeed:<?php echo strlen(trim(get_option("jcorgtoc_duration")))>0?trim(get_option("jcorgtoc_duration")):'1000'?>,
                          tocTitle:"<?php echo strlen(trim(get_option("jcorgtoc_title")))>0?trim(get_option("jcorgtoc_title")):'Table of contents'?>",
                          scrollingElement:"<?php echo strlen(trim(get_option("jcorgtoc_scroll_element")))>0?trim(get_option("jcorgtoc_scroll_element")):'html,body'?>",
                          itemTextLength:<?php echo strlen(trim(get_option("jcorgtoc_textlength")))>0?trim(get_option("jcorgtoc_textlength")):'40'?>,
                          tocDefaultHeight:<?php echo strlen(trim(get_option("jcorgtoc_height")))>0?trim(get_option("jcorgtoc_height")):'300'?>,
                          useFancyScroll:<?php echo (trim(get_option("jcorgtoc_scroll")) == "Yes")?'true':'false'?>,
                          autoMinimize:<?php echo (trim(get_option("jcorgtoc_minimize")) == "Yes")?'true':'false'?>,
                          tagsForTOC:'h1,h2,h3,h4',
                          mainTag:"<?php echo strlen(trim(get_option("jcorgtoc_parenttagclass")))>0?trim(get_option("jcorgtoc_parenttagclass")):'div.entry-content'?>",
                          easingType:"<?php echo strlen(trim(get_option("jcorgtoc_easingType")))>0?trim(get_option("jcorgtoc_easingType")):'linear'?>",
                          imgPath:'<?php echo plugin_dir_url(__FILE__)?>'
                      });      
         });
         </script>
         
        <?php
        if(get_option('jcorgtoc_linkback') =="Yes") {
            echo '<a style="font-size:0em !important;color:transparent !important" href="http://jaspreetchahal.org">Scroll to top is powered by http://jaspreetchahal.org</a>';
        }
        }
    }
    
    function jcorgtoc_plugin_options() {
        jcorgtocDonationDetail();
           
        ?> 
        <style type="text/css">
        .jcorgbsuccess, .jcorgberror {   border: 1px solid #ccc; margin:0px; padding:15px 10px 15px 50px; font-size:12px;}
        .jcorgbsuccess {color: #FFF;background: green; border: 1px solid  #FEE7D8;}
        .jcorgberror {color: #B70000;border: 1px solid  #FEE7D8;}
        .jcorgb-errors-title {font-size:12px;color:black;font-weight:bold;}
        .jcorgb-errors { border: #FFD7C4 1px solid;padding:5px; background: #FFF1EA;}
        .jcorgb-errors ul {list-style:none; color:black; font-size:12px;margin-left:10px;}
        .jcorgb-errors ul li {list-style:circle;line-height:150%;/*background: url(/images/icons/star_red.png) no-repeat left;*/font-size:11px;margin-left:10px; margin-top:5px;font-weight:normal;padding-left:15px}
        td {font-weight: normal;}
        </style><br>
        <div class="wrap" style="float: left;" >
            <?php             
            
            screen_icon('tools');?>
            <h2><a href="http://jaspreetchahal.org">JaspreetChahal's</a> Table of contents settings</h2>
            <?php 
                $errors = get_settings_errors("",true);
                $errmsgs = array();
                $msgs = "";
                if(count($errors) >0)
                foreach ($errors as $error) {
                    if($error["type"] == "error")
                        $errmsgs[] = $error["message"];
                    else if($error["type"] == "updated")
                        $msgs = $error["message"];
                }

                echo jcorgtocMakeErrorsHtml($errmsgs,'warning1');
                if(strlen($msgs) > 0) {
                    echo "<div class='jcorgbsuccess' style='width:90%'>$msgs</div>";
                }

            ?><br><br>
            <form action="options.php" method="post" id="jcorgbotinfo_settings_form">
            <?php settings_fields("jcorgtoc-setting");?>
            <table class="widefat" style="width: 700px;" cellpadding="7">
                <tr valign="top">
                    <th scope="row">Enabled</th>
                    <td><input type="radio" name="jcorgtoc_active" <?php if(get_option('jcorgtoc_active') == "1"|| get_option('jcorgtoc_active') == "") echo "checked='checked'";?>
                            value="1" 
                            /> Yes
                            <input type="radio" name="jcorgtoc_active" <?php if(get_option('jcorgtoc_active') == "0" ) echo "checked='checked'";?>
                            value="0" 
                            /> No 
                    </td>
                </tr>        
                <tr valign="top">
                    <th width="25%" scope="row">Article parent tag and class</th>
                    <td><input type="test" name="jcorgtoc_parenttagclass"
                            value="<?php echo get_option('jcorgtoc_parenttagclass'); ?>"  style="padding:5px" size="40"/> <br>Generally this value is always "div.entry-content", you don't need to change it. Its there just if your case id different for some reasons</td>
                </tr>
                 <tr valign="top">
                    <th width="25%" scope="row">Scroll Speeed</th>
                    <td><input type="number" name="jcorgtoc_duration"
                            value="<?php echo get_option('jcorgtoc_duration'); ?>"  style="padding:5px" size="40"/> <br>Must be a numeric value</td>
                </tr>        
                <tr valign="top">
                    <th scope="row">Text length per item</th>
                    <td><input type="number" name="jcorgtoc_textlength"
                            value="<?php echo get_option('jcorgtoc_textlength'); ?>"  style="padding:5px" size="40"/> (numberic value e.g. 40)</td>
                </tr>
                 <tr valign="top">
                    <th scope="row">Fancy Scroll?</th>
                    <td><input type="radio" name="jcorgtoc_scroll" <?php if(get_option('jcorgtoc_scroll') == "Yes"|| get_option('jcorgtoc_scroll') == "") echo "checked='checked'";?>
                            value="Yes" 
                            /> Yes
                            <input type="radio" name="jcorgtoc_scroll" <?php if(get_option('jcorgtoc_scroll') == "No" ) echo "checked='checked'";?>
                            value="No" 
                            /> No 
                    </td>
                </tr> 
                <tr valign="top">
                    <th scope="row">Auto minimize?</th>
                    <td><input type="radio" name="jcorgtoc_minimize" <?php if(get_option('jcorgtoc_minimize') == "Yes"|| get_option('jcorgtoc_minimize') == "") echo "checked='checked'";?>
                            value="Yes" 
                            /> Yes
                            <input type="radio" name="jcorgtoc_minimize" <?php if(get_option('jcorgtoc_minimize') == "No" ) echo "checked='checked'";?>
                            value="Yes" 
                            /> No 
                    </td>
                </tr> 
                <tr valign="top">
                    <th scope="row">Table default height</th>
                    <td><input type="number" name="jcorgtoc_height"
                            value="<?php echo get_option('jcorgtoc_height'); ?>"  style="padding:5px" size="40"/>px (numberic value e.g. 300)</td>
                </tr>
                <tr valign="top">
                    <th scope="row">Table Title</th>
                    <td><input type="text" name="jcorgtoc_title"
                            value="<?php echo get_option('jcorgtoc_title'); ?>"  style="padding:5px" size="40"/></td>
                </tr>
                <tr valign="top">
                    <th scope="row">Easing type</th>
                    <td>
                    <select name="jcorgtoc_easingType">
                    <option value="linear" <?php if(get_option('jcorgtoc_easingType') == "linear"){  _e('selected');}?> >linear</option>
                    <option value="swing" <?php if(get_option('jcorgtoc_easingType') == "swing") { _e('selected');}?> >swing</option>
                    <option value="easeInQuad" <?php if(get_option('jcorgtoc_easingType') == "easeInQuad") { _e('selected');}?> >easeInQuad</option>
                    <option value="easeOutQuad" <?php if(get_option('jcorgtoc_easingType') == "easeOutQuad") { _e('selected');}?> >easeOutQuad</option>
                    <option value="easeInOutQuad" <?php if(get_option('jcorgtoc_easingType') == "easeInOutQuad") { _e('selected');}?> >easeInOutQuad</option>
                    <option value="easeInCubic" <?php if(get_option('jcorgtoc_easingType') == "easeInCubic") { _e('selected');}?> >easeInCubic</option>
                    <option value="easeOutCubic" <?php if(get_option('jcorgtoc_easingType') == "easeOutCubic") { _e('selected');}?> >easeOutCubic</option>
                    <option value="easeInOutCubic" <?php if(get_option('jcorgtoc_easingType') == "easeInOutCubic") { _e('selected');}?> >easeInOutCubic</option>
                    <option value="easeInQuart" <?php if(get_option('jcorgtoc_easingType') == "easeInQuart") { _e('selected');}?> >easeInQuart</option>
                    <option value="easeOutQuart" <?php if(get_option('jcorgtoc_easingType') == "easeOutQuart") { _e('selected');}?> >easeOutQuart</option>
                    <option value="easeInOutQuart" <?php if(get_option('jcorgtoc_easingType') == "easeInOutQuart") { _e('selected');}?> >easeInOutQuart</option>
                    <option value="easeInQuint" <?php if(get_option('jcorgtoc_easingType') == "easeInQuint") { _e('selected');}?> >easeInQuint</option>
                    <option value="easeOutQuint" <?php if(get_option('jcorgtoc_easingType') == "easeOutQuint") { _e('selected');}?> >easeOutQuint</option>
                    <option value="easeInOutQuint" <?php if(get_option('jcorgtoc_easingType') == "easeInOutQuint") { _e('selected');}?> >easeInOutQuint</option>
                    <option value="easeInSine" <?php if(get_option('jcorgtoc_easingType') == "easeInSine") { _e('selected');}?> >easeInSine</option>
                    <option value="easeOutSine" <?php if(get_option('jcorgtoc_easingType') == "easeOutSine") { _e('selected');}?> >easeOutSine</option>
                    <option value="easeInOutSine" <?php if(get_option('jcorgtoc_easingType') == "easeInOutSine") { _e('selected');}?> >easeInOutSine</option>
                    <option value="easeInExpo" <?php if(get_option('jcorgtoc_easingType') == "easeInExpo") { _e('selected');}?> >easeInExpo</option>
                    <option value="easeOutExpo" <?php if(get_option('jcorgtoc_easingType') == "easeOutExpo") { _e('selected');}?> >easeOutExpo</option>
                    <option value="easeInOutExpo" <?php if(get_option('jcorgtoc_easingType') == "easeInOutExpo") { _e('selected');}?> >easeInOutExpo</option>
                    <option value="easeInElastic" <?php if(get_option('jcorgtoc_easingType') == "easeInElastic") { _e('selected');}?> >easeInElastic</option>
                    <option value="easeOutElastic" <?php if(get_option('jcorgtoc_easingType') == "easeOutElastic") { _e('selected');}?> >easeOutElastic</option>
                    <option value="easeInOutElastic" <?php if(get_option('jcorgtoc_easingType') == "easeInOutElastic") { _e('selected');}?> >easeInOutElastic</option>
                    <option value="easeInOutBack" <?php if(get_option('jcorgtoc_easingType') == "easeInOutBack") { _e('selected');}?> >easeInOutBack</option>
                    <option value="easeInOutBounce" <?php if(get_option('jcorgtoc_easingType') == "easeInOutBounce") { _e('selected');}?> >easeInOutBounce</option>
                    </select>
               </tr> 
                <tr valign="top">
                    <th scope="row">Position</th>
                    <td><input type="radio" name="jcorgtoc_position" <?php if(get_option('jcorgtoc_position') == "left" || get_option('jcorgtoc_position') == "") echo "checked='checked'";?>
                            value="left" 
                            /> Left<br>
                            <input type="radio" name="jcorgtoc_position" <?php if(get_option('jcorgtoc_position') == "bottomcenter" ) echo "checked='checked'";?>
                            value="bottomcenter" 
                            /> Bottom Center <br>
                            <input type="radio" name="jcorgtoc_position" <?php if(get_option('jcorgtoc_position') == "right") echo "checked='checked'";?>
                            value="right" 
                            /> Right  <br>
                            <input type="radio" name="jcorgtoc_position" <?php if(get_option('jcorgtoc_position') == "bottomright") echo "checked='checked'";?>
                            value="bottomright" 
                            /> Botom Right  <br>
                            <input type="radio" name="jcorgtoc_position" <?php if(get_option('jcorgtoc_position') == "bottomleft") echo "checked='checked'";?>
                            value="bottomleft" 
                            /> Botom Left  <br>
                            <input type="radio" name="jcorgtoc_position" <?php if(get_option('jcorgtoc_position') == "topleft") echo "checked='checked'";?>
                            value="topleft" 
                            /> Top Left <br>
                            <input type="radio" name="jcorgtoc_position" <?php if(get_option('jcorgtoc_position') == "topright") echo "checked='checked'";?>
                            value="topright" 
                            /> Top Right <br> 
                            <input type="radio" name="jcorgtoc_position" <?php if(get_option('jcorgtoc_position') == "sticky") echo "checked='checked'";?>
                            value="sticky" 
                            /> Sticky 
                    </td>
                </tr>   
                <tr valign="top">
                    <th scope="row">Add powered by link</th>
                    <td><input type="checkbox" name="jcorgtoc_linkback"
                            value="Yes" <?php if(get_option('jcorgtoc_linkback') =="Yes") echo "checked='checked'";?> /> <br>
                            <Strong>An inivisible link will be placed in the footer which points to http://jaspreetchahal.org, author of this plugin. This is one way of showing your support</strong></td>
                </tr> 
        </table>
        <p class="submit">
            <input type="submit" class="button-primary"
                value="Save Changes" />
        </p>          
            </form>
        </div>
        <?php     
        echo "<div style='float:left;margin-left:20px;margin-top:75px'>".jcorgtocfeeds()."</div>";
    }
    
    function jcorgtocDonationDetail() {
        ?>    
        <style type="text/css"> .jcorgcr_donation_uses li {float:left; margin-left:20px;font-weight: bold;} </style> 
        <div style="padding: 10px; background: #f1f1f1;border:1px #EEE solid; border-radius:15px;width:98%"> 
        <h2>If you like this Plugin, please consider donating</h2> 
        You can choose your own amount. Developing this awesome plugin took a lot of effort and time; days and weeks of continuous voluntary unpaid work. 
        If you like this plugin or if you are using it for commercial websites, please consider a donation to the author to 
        help support future updates and development. 
        <div class="jcorgcr_donation_uses"> 
        <span style="font-weight:bold">Main uses of Donations</span><ol ><li>Web Hosting Fees</li><li>Cable Internet Fees</li><li>Time/Value Reimbursement</li><li>Motivation for Continuous Improvements</li></ol> </div> <br class="clear"> <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=MHMQ6E37TYW3N"><img src="https://www.paypalobjects.com/en_AU/i/btn/btn_donateCC_LG.gif" /></a> <br><br><strong>For help please visit </strong><br> 
        <a href="http://jaspreetchahal.org/wordpress-simple-table-of-contents-plugin">http://jaspreetchahal.org/wordpress-simple-table-of-contents-plugin</a> <br><strong> </div>
        
        <?php
        
    }
    function jcorgtocfeeds() {
        $list = "
        <table style='width:400px;' class='widefat'>
        <tr>
            <th>
            Latest posts from JaspreetChahal.org
            </th>
        </tr>
        ";
        $max = 5;
        $feeds = fetch_feed("http://feeds.feedburner.com/jaspreetchahal/mtDg");
        $cfeeds = $feeds->get_item_quantity($max); 
        $feed_items = $feeds->get_items(0, $cfeeds); 
        if ($cfeeds > 0) {
            foreach ( $feed_items as $feed ) {    
                if (--$max >= 0) {
                    $list .= " <tr><td><a href='".$feed->get_permalink()."'>".$feed->get_title()."</a> </td></tr>";}
            }            
        }
        return $list."</table>";
    }
    
    
    function jcorgtocMakeErrorsHtml($errors,$type="error")
    {
        $class="jcorgberror";
        $title=__("Please correct the following errors","jcorgbot");
        if($type=="warnings") {
            $class="jcorgberror";
            $title=__("Please review the following Warnings","jcorgbot");
        }
        if($type=="warning1") {
            $class="jcorgbwarning";
            $title=__("Please review the following Warnings","jcorgbot");
        }
        $strCompiledHtmlList = "";
        if(is_array($errors) && count($errors)>0) {
                $strCompiledHtmlList.="<div class='$class' style='width:90% !important'>
                                        <div class='jcorgb-errors-title'>$title: </div><ol>";
                foreach($errors as $error) {
                      $strCompiledHtmlList.="<li>".$error."</li>";
                }
                $strCompiledHtmlList.="</ol></div>";
        return $strCompiledHtmlList;
        }
    }