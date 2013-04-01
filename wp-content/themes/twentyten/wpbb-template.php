<?php

/*
	Template Name: WPBB
*/

/*
	This file is created apon activation or theme switch in your current theme directory.
	
	Do not remove this file at any time as it is loads all the neccessary files for wp-bb to run on the frontend
*/

get_header();

require_once(ABSPATH.'/wp-content/plugins/wp-bulletin-board/php/wpbb-forum.php');

get_footer();

?>