<?php

class SWER_aptools_shortcodes{

    function showsingle( $atts ){
    	extract( shortcode_atts( array(
    		'postid' => '',
    		'pageid' => '',
    		'showheader' => 'true',
    		'header' => '2',
    		'headerclass' => 'aptools-single-header',
    		'wrapper' => 'false',
    		'wrapperclass' => 'aptools-wrapper'
    	), $atts ) );

        $hopen = '<h'.$header.' class='.$headerclass.'>';
        $hclose = '</h'.$header.'>';

        if( $postid === '' && $pageid !== '' ):
            $args = array( 'page_id' => $pageid, 'posts_per_page' => 1 );
        elseif( $pageid === '' && $postid !== '' ):
            $args = array( 'p' => $postid, 'posts_per_page' => 1 );
        endif;

        $page = new WP_Query( $args );
        if( $page->have_posts() ):
            if( $wrapper !== 'false'){
                echo '<div class="'.$wrapperclass.'">';
            }
            while( $page->have_posts() ):
                $page->the_post();
                if( $showheader === 'true' ) echo $hopen . get_the_title() . $hclose;
                echo get_the_content();
            endwhile;
            if( $wrapper !== 'false'){
                echo '</div>';
            }
        endif;
        wp_reset_postdata();                
    }


    function showlist( $atts ){
    	extract( shortcode_atts( array(
    		'catid' => '',
    		'lenght' => '10',
    		'listclass' => 'aptools-list',
    		'header' => '2',
    		'headerclass' => 'aptools-list-header',
    		'excerpt' => 'false',
    		'image' => 'false',
    		'wrapper' => 'false',
    		'wrapperclass' => 'aptools-list-wrapper'
    	), $atts ) );

        $hopen = '<h'.$header.' class='.$headerclass.'>';
        $hclose = '</h'.$header.'>';

        if( $catid !== '' ):
            $args = array( 'category__in' => array($catid), 'posts_per_page' => $lenght );
        endif;

        $page = new WP_Query( $args );
        if( $page->have_posts() ):
            if( $wrapper !== 'false'){
                echo '<div class="'.$wrapperclass.'">';
            }
            echo '<ul class="'.$listclass.'">';
            while( $page->have_posts() ):
                $page->the_post();
                echo '<li>';
                echo '<a href="'.get_permalink().'">'.get_the_title().'</a>'; 
                if( $image !== 'false' && has_post_thumbnail() ){ the_post_thumbnail( $image ); }
                if( $excerpt === 'true' ) echo ' <span>'.get_the_excerpt().'</span>';
                echo '</li>';
            endwhile;
            echo '</ul>';
            if( $wrapper !== 'false'){
                echo '</div>';
            }
        endif;
        wp_reset_postdata();                
    }

    
    function showauto(){
        global $cat;
        $query_args = array(
            'post_type' => 'page',
            'meta_key' => 'aptools_archive_link',
            'meta_value' => $cat,
            'posts_per_page' => 1
        );

        $pages = new WP_Query( $query_args );

        if( $pages->have_posts() ):
            while( $pages->have_posts() ):
                $pages->the_post();
                echo '<h2>'.get_the_title().'</h2>';
                echo '<div class="aptools-category-content">'.get_the_content().'</div>';
            endwhile;
        endif;
        wp_reset_postdata();                

    }

}

add_shortcode( 'showsingle', array( 'SWER_aptools_shortcodes', 'showsingle' ) );
add_shortcode( 'showlist', array( 'SWER_aptools_shortcodes', 'showlist' ) );
add_shortcode( 'showauto', array( 'SWER_aptools_shortcodes', 'showauto' ) );

class SWER_aptools_admin{

    function __construct(){
        add_meta_box( 'aptools_archive_link', 'Category Pages & Posts', array( &$this, 'aptools_custom_metabox'), 'page', 'side', 'core' );        
    }

    function manage_pages_columns( $post_columns ){
        $post_columns['aptools'] = 'Category';
        return $post_columns;
    }
    
    function manage_pages_custom_column( $column, $post_id ){
        $selected = (int) get_post_meta( $post_id, 'aptools_archive_link', true );        
        
    	$category = &get_category( $selected );
    	if ( is_wp_error( $category ) ) return false;

        if( $category ):
        echo '<a 
            href="'.admin_url( 'edit-tags.php?action=edit&taxonomy=category&tag_ID='.$selected.'&post_type=post' ).'">'
            .$category->name
            .'</a>';
        endif;
    }
    
    function aptools_custom_metabox( $post ){
        $selected = get_post_meta( $post->ID, 'aptools_archive_link', true );
        #print_r($selected);
        wp_nonce_field( plugin_basename( __FILE__ ), 'aptools-nonce' );
        
        $args = array(
            'selected'          => $selected,
            'show_count'        => 0,
            'hide_empty'        => 1,
            'hierarchical'      => 1,
            'show_option_none' => '(None)',
            'name'              => 'aptools-metabox',
            'id'                => 'aptools-metabox',
            'taxonomy'          => 'category'
        );
        wp_dropdown_categories( $args );
        
        echo '<p>Link this page to a category, and use [showauto] shortcode in your category template to embed that page.</p>';
    }
    
    
    // update logic, same for manage_posts_custom_columns
    function save_post( $post_id ){
        if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
        return;

        if ( !isset($_POST[ 'aptools-nonce' ]) || !wp_verify_nonce( $_POST[ 'aptools-nonce' ], plugin_basename( __FILE__ ) ) )
        return;
        
        if ( 'page' == $_POST['post_type'] ){
           if ( !current_user_can( 'edit_page', $post_id ) )
               return;
         }else{
           if ( !current_user_can( 'edit_post', $post_id ) )
               return;
         }
          
        if( $_POST ):
        update_post_meta( $post_id, 'aptools_archive_link', $_POST['aptools-metabox'] );
        endif;
    }
    
    
    function category_add_form_fields( $tag ){
        $args = array(
            'selected'         => 0,
            'echo'             => 0,
            'name'             => 'aptools_page_id',
            'show_option_none' => '(None)'
        );
            
        echo '
            <div class="form-field">
            	<label for="tag-description">'._x('Category Pages & Posts', 'Category Pages & Posts').'</label>
            	'.wp_dropdown_pages( $args ).'
            	<p>'._('Link this category to a page, and use [showauto] shortcode in your category template to embed that page.').'</p>
            </div>
        ';
    }
    
    function category_edit_form_fields( $tag ){

        $query_args = array(
            'post_type' => 'page',
            'meta_key' => 'aptools_archive_link',
            'meta_value' => $tag->term_id,
            'posts_per_page' => 1
        );
        
        $selected = 0;
        $pages = new WP_Query( $query_args );
        if( $pages->have_posts() ):
            while( $pages->have_posts() ):
                $pages->the_post();       
                #echo 'This category is linked with <a href="'.admin_url('post.php?post='.get_the_ID().'&action=edit').'">'.get_the_title().'</a>';
                $selected = get_the_ID();

            endwhile;
        endif;
        
        $pages_args = array(
            'selected'         => $selected,
            'echo'             => 0,
            'name'             => 'aptools_page_id',
            'show_option_none' => '(None)'
        );
        echo '
        
        <input type="hidden" name="aptools_pre_page_id" value="'.$selected  .'" />
    	<tr class="form-field">
			<th scope="row" valign="top"><label for="aptools_page_id">Category Pages & Posts</label></th>
			<td>'.wp_dropdown_pages( $pages_args ).'<br />
			<span class="description">Link this category to a page, and use [showauto] shortcode in your category template to embed that page.</span>
			</td>
		</tr>            	
        ';

        wp_reset_postdata();
    }
    
    function admin_action_editedtag(){
        if( $_POST['aptools_pre_page_id'] !== $_POST['aptools_page_id'] ):            
            update_post_meta( $_POST['aptools_pre_page_id'], 'aptools_archive_link', '' );
            update_post_meta( $_POST['aptools_page_id'], 'aptools_archive_link', $_POST['tag_ID'] );
        endif;
    }
    
    
    function add_post_tag_columns($columns){
        $columns['atptools'] = 'Page';
        return $columns;
    }
    
    function add_post_tag_column_content($content, $column_name, $id){
        $query_args = array(
            'post_type' => 'page',
            'meta_key' => 'aptools_archive_link',
            'meta_value' => $id,
            'posts_per_page' => 1
        );

        $pages = new WP_Query( $query_args );
        if( $pages->have_posts() ):
            while( $pages->have_posts() ):
                $pages->the_post();       
                $content .= '<a href="'.admin_url('post.php?post='.get_the_ID().'&action=edit').'">'.get_the_title().'</a>';
            endwhile;
        endif;

        return $content;
    }    
}

function call_SWER_aptools_admin(){
    return new SWER_aptools_admin();
}

add_action( 'add_meta_boxes', 'call_SWER_aptools_admin' );

add_action( 'admin_action_editedtag' ,          array( 'SWER_aptools_admin', 'admin_action_editedtag' ) );
add_action( 'category_add_form_fields',         array( 'SWER_aptools_admin', 'category_add_form_fields' ) );
add_action( 'category_edit_form_fields',        array( 'SWER_aptools_admin', 'category_edit_form_fields' ) );
add_filter( 'manage_edit-category_columns',     array( 'SWER_aptools_admin', 'add_post_tag_columns' ) );
add_filter( 'manage_category_custom_column',    array( 'SWER_aptools_admin', 'add_post_tag_column_content' ), 10, 3 );
add_filter( 'manage_pages_columns',             array( 'SWER_aptools_admin', 'manage_pages_columns' ) );
add_action( 'manage_pages_custom_column',       array( 'SWER_aptools_admin', 'manage_pages_custom_column' ), 10, 2);
add_action( 'save_post',                        array( 'SWER_aptools_admin', 'save_post' ) );


?>