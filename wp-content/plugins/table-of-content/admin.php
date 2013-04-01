<?php
/**
 * @package Scaleable Contact Form
 * @author Ulrich Kautz
 * @version 0.6.5
 */
/*
Author: Ulrich Kautz
Version: 0.6.5
Author URI: http://fortrabbit.de
*/

ini_set( 'display_errors', 1 );
require_once( 'includes.php' );


// save formular
if ( !empty( $_POST ) )
	toc_save_admin( $_POST );

// print formular
toc_print_admin_form();




/*
		ADMIN
*/


function toc_save_admin( $data ) {
	$options = toc_get_options();
	foreach ( $options as $k => $v ) {
		$r = @empty( $_POST[ $k ] )
			? ( $k == 'toc_list_style'
				? 'ol'
				: ''
			)
			: $_POST[ $k ]
		;
		update_option( $k, $r );
	}
}


function toc_print_admin_form() {
	$options = toc_get_options();
	$action  = site_url().
		'/wp-admin/admin.php?page='.
		dirname(__FILE__).
		'/admin.php'
	;
	
?>
<div class="wrap">
	<style type="text/css">
		.toc-form {}
		.toc-form em { font-size: 10px }
	</style>
	<h2>Table of content</h2>
	<form action="<?php echo $action ?>" method="post" class="toc-form" style="margin-bottom: 10px; border-bottom: 1px dashed #ccc">
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row">
						<label for="toc_title">
							Default TOC title
						</label>
					</th>
					<td>
						<input type="text" class="regular-text" value="<?php echo htmlentities( $options[ 'toc_title' ] ) ?>" id="toc_title" name="toc_title"/>
						<em>Will be printed above every TOC. Leave blank for no title. Can be overwritten by "title" option.</em>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="toc_title_tag">
							Title HTML Tag
						</label>
					</th>
					<td>
						<input type="text" class="regular-text" value="<?php echo htmlentities( $options[ 'toc_title_tag' ] ) ?>" id="toc_title_tag" name="toc_title_tag"/>
						<em>Default: h5. Can be overwritten by "title-tag" option.</em>
					</td>
				</tr>
				<tr>
					<th scope="row">
						<label for="toc_list_style">
							List HTML style
						</label>
					</th>
					<td>
						<select name="toc_list_style" id="toc_list_style">
							<option value="ol" <?php if ( $options[ 'toc_list_style' ] == 'ol' ) echo 'selected="selected"'; ?>>
								Ordered list
							</option>
							<option value="ul" <?php if ( $options[ 'toc_list_style' ] == 'ul' ) echo 'selected="selected"'; ?>>
								Unordered list
							</option>
						</select>
						<em>Ordered uses ol-tags, unordered ul-tags.</em>
					</td>
				</tr>
			</tbody>
		</table>
	
		<p class="submit">
			<input type="submit" name="submit" value="Save all &raquo;" />
		</p>
	</form>
	
	<h3>Usage</h3>
	
	<h4>Basic example</h4>
	<pre>Some content, which not be included in the TOC even if it contains h-Tags
[table-of-content]
Here is your content. All h-tags will be in the TOC.
[/table-of-content]
Anything after the closing will also not be part of the TOC.</pre>
	
	<h4>Using title and title-tag</h4>
	<pre>Some content, which not be included in the TOC even if it contains h-Tags
[table-of-content title="Table of content" title-tag="p"]
Here is your content. All h-tags will be in the TOC.
[/table-of-content]
Anything after the closing will also not be part of the TOC.</pre>
	<div style="margin-top: 30px">
		<a href="http://blog.foaa.de/plugins/table-of-content/" target="_blank">
			More help and info &gt;
		</a>
	</div>
</div>
<?php
}



?>
