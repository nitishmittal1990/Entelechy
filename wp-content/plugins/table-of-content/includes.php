<?php

class TableOfContent {
	
	// init matches and counter for setting the anchors.. they have to be global
	private $counter     = 0;
	private $matches     = array();
	private $args        = array();
	private $seen_anchor = array();
	private $anchor_map  = array();
	
	/**
	 * $site = get_page_index_navigation( get_post_content() );
	 * print $site->content;    // replacement for "the_content()"
	 * print $site->navigation; // the actual page navigation
	 */
	public function parse_contents( $post_content = "", $args = array() ) {
		
		// using the whole content if empty content is specified
		$empty = false;
		if ( $post_content == "" ) {
			$post_content = get_the_content();
			$empty = true;
		}
		
		// inti variables for this round ..
		$this->counter = 0;
		$this->matches = array(); 
		$this->args    = array_merge( array(
			'list_type'			=> 'ol',
			'prefix'			=> '',
			'suffix'			=> '<a href="#pni-top%suffix%" class="toplink">^</a>',
			'clear_iterator'	=> true,
			'top_suffix'		=> '',
			'top_prefix'		=> '',
		), $args );
		
		// replace the suffix in the top-links
		$this->args[ 'prefix' ] = preg_replace( '/%prefix%/', $this->args[ 'top_prefix' ], $this->args[ 'prefix' ] );
		$this->args[ 'suffix' ] = preg_replace( '/%suffix%/', $this->args[ 'top_suffix' ], $this->args[ 'suffix' ] );
		
		// generate some random number for the being unique
		#$rand = $this->args[ 'rand' ] = (int)( rand() * 99999999 ); 
		
		// update content
		$post_content = preg_replace_callback(
			'/<h(\d)>(.+?)<\/h\1>/s', array( &$this, '_replace_and_build_navi' ), $post_content );
		
		// having matches, build the navi
		$navigation = '';
		if ( !empty( $this->matches ) ) {
			
			$start_level = 0;
			$last_level  = 0;
			$navigation = '<'. $this->args[ 'list_type' ]. '>';
			
			$i = 0;
			foreach( $this->matches as $match ) {
				$level = $match[0];
				$title = $match[1];
				
				// first level
				if ( !$start_level )
					$start_level = $level;
				
				// same level
				elseif ( $last_level == $level )
					$navigation .= '</li>';
				
				// level UP ..
				elseif ( $last_level < $level ) {
					$diff = $level - $last_level;
					while ( $diff-- > 0 )
						$navigation .= '<'. $this->args[ 'list_type' ]. '>';
				}
				
				// level DOWN
				elseif ( $last_level > $level ) {
					$diff = $last_level - $level;
					while ( $diff-- > 0 )
						$navigation .= '</li></'. $this->args[ 'list_type' ]. '></li>';
				}
				
				// remember last level ..
				$last_level = $level;
				
				// insert current level
				#$navigation .= '<li><a href="#'. $rand. 'pagenav'. $i. '">'. $title. '</a>';
				$navigation .= '<li><a href="#'. $this->anchor_map[ $i ]. '">'. $title. '</a>';
				$i++;
			}
			
			// append finisher
			$diff = $last_level - $start_level;
			$diff ++;
			while ( $diff-- > 0 )
				$navigation .= '</li></'. $this->args[ 'list_type' ]. '>';
		}
		return (object)array(
			'content'		=> $post_content,
			'navigation'	=> $navigation,
			'from_empty'	=> $empty
		);
	}
	
	// replace method for all h-tags ..
	private function _replace_and_build_navi( $match ) {
		
		$level = $match[1];
		$title = $match[2];
		/*$anchor =
			preg_replace( '/\-+$/', '',
			preg_replace( '/^\-+/', '',
			preg_replace( '/\-\-+/', '-',
			preg_replace( '/[^\p{L}\p{N}\-_\.]/u', '-',
				strtolower( $title )
			) ) ) )
		;*/
		$anchor = sanitize_title_with_dashes( strtolower( $title ) );
		
		$anchor_suffix = 0;
		$anchor_prefix = $anchor;
		while ( isset( $this->seen_anchor[ $anchor ] ) ) {
			$anchor = $anchor_prefix. '-'. ++$anchor_suffix;
		}
		$this->seen_anchor[ $anchor ] = true;
		$this->anchor_map[ $this->counter++ ] = $anchor;
		
		// remove leading "123." ..
		if ( $this->args[ 'clear_iterator' ] )
			$title = preg_replace( '/^\s*\d+\.?\s*/', '', $title ); 
		
		// add to matches
		$this->matches []= array( $level, strip_tags( $title ) );
		
		// rebuild and return the h*-tag
		return join( '', array(
			'<h'. $level. '>',
			$this->args[ 'prefix' ],
			#'<a name="'. $this->args[ 'rand' ]. 'pagenav'. $this->counter++. '"></a>',
			'<a name="'. $anchor. '"></a>',
			$title,
			$this->args[ 'suffix' ],
			'</h'. $level. '>'
		) );
	}
}

function toc_get_options() {
	$options = array(
		'toc_title'			=> '',
		'toc_title_tag'		=> '',
		'toc_list_style'	=> 'ol',
	);
	
	foreach ( $options as $k => $v ) {
		$r = get_option( $k );
		if ( !@empty( $r ) )
			$options[ $k ] = $r;
	}
	
	return $options;
}

?>
