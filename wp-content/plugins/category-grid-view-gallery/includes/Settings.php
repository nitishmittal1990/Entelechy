<?php
/**
Settings for Category Grid View Gallery
Author : Anshul Sharma (contact@anshulsharma.in)
 */

//Default Plugin Settings
function cgview_default_settings(){
	$defaults = array(
    'default_image' => CGVIEW_URL.'/includes/default.jpg',
	'custom_image' => CGVIEW_URL.'/includes/default.jpg',
    'credits' => 1,
    'color_scheme' => 'light',
    'image_source' => 'first',
	'lightbox_width' => '700',
	'lightbox_height' => '400',
	'load_comments' => 0);
  return $defaults;
}

 
function cgview_verify_options(){
  $default_settings = cgview_default_settings();
  $current_settings = get_option('cgview');
  if(!$current_settings):
   cgview_setup_options();
  else:
  //   if (version_compare($current_settings['plugin_version'], PLUGIN_VERSION, '!=')):
     // check for new options
     foreach($default_settings as $option=>$value):
      if(!array_key_exists($option, $current_settings)) $current_settings[$option] = $default_settings[$option];
     endforeach;

    // update theme version
   // $current_settings['plugin_version'] = $plugin_data['Version'];
    update_option('cgview' , $current_settings);

 // endif;
  endif;
      do_action('cgview_verify_options');
}

function cgview_setup_options() {
 
  cgview_remove_options();
  $default_settings = cgview_default_settings();
  update_option('cgview' , $default_settings);
  do_action('cgview_setup_options');
}


function cgview_remove_options() {
  delete_option('cgview');
  do_action('cgview_remove_options');
}

function get_cgview_option($option) {
  $get_cgview_options = get_option('cgview');
  return $get_cgview_options[$option];
}


function print_cgview_option($option) {
  $get_cgview_options = get_option('cgview');
  echo $get_cgview_options[$option];
}


