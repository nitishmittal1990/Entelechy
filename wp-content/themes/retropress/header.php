<?php
	// html code of the header placed in top.php
	global $SMTheme, $return;
	$SMTheme->get_layout();
	if(!isset($_POST['smt_layout'])){
		get_template_part('top');
	} else {
		$return['title']=$SMTheme->show_title(false);
	}

	if ( isset($_POST['smt_layout'])&&$_POST['smt_layout']!=$SMTheme->layout ) {
		$return['div']='#content .container';
		ob_start();
	}

	if (!isset($_POST['smt_layout'])||$_POST['smt_layout']!=$SMTheme->layout) {
		get_sidebar(); 
		?> <div id="main_content"> <?php
		
	}
	
	if ( isset($_POST['smt_layout'])&&$_POST['smt_layout']==$SMTheme->layout ) {
		$return['div']='#main_content';
		ob_start();
	}