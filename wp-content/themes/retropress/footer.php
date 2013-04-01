<?php
	// html code of the footer placed in bottom.php
	global $SMTheme, $return;
	if (!isset($_POST['smt_layout'])||$_POST['smt_layout']!=$SMTheme->layout) {
		?> </div><!-- #content --><?php
	}

	if ( isset($_POST['smt_layout'])) {
		$return['content']=ob_get_contents();
		$return['layout']=$SMTheme->layout;
		$return['sidebars_type']=$SMTheme->sidebars_type;
		ob_end_clean();
	}


	if(!isset($_POST['smt_layout'])){
		get_template_part('bottom');
	} else {
		header('Content-type: application/json');
		echo json_encode($return);
	}