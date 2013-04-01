<?php
/*
Core SedLex Plugin
VersionInclude : 3.0
*/ 
/** =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*
* This PHP class enables the creation of a box in the admin backend
*/


if (!class_exists("deprecatedSL")) {
	class deprecatedSL {
				
		/** ====================================================================================================================================================
		* Constructor of the class
		* 
		* @access private
		* @param string $title the title of the box
		* @param string $content the HTML code of the content of the box
		* @return boxAdmin the box object
		*/
		
		function deprecatedSL() {
			// Nothing to do
		}
		
		/** ====================================================================================================================================================
		* Display deprecated function
		* 
		* @access private
		* @return void
		*/
		
		static function log_function( $function, $replacement, $version ) {
		
			$frmk = new coreSLframework() ; 
			
			if ($frmk->get_param("deprecated")) {
				$backtrace = debug_backtrace();
				$deprecated = $function . '()';
				$hook = null;
				$bt = 4;
				// Check if we're a hook callback.
				if ( ! isset( $backtrace[4]['file'] ) && 'call_user_func_array' == $backtrace[5]['function'] ) {
					$hook = $backtrace[6]['args'][0];
					$bt = 6;
				}
				
				$in_file = deprecatedSL::strip_abspath( $backtrace[ $bt ]['file'] );
				$on_line = $backtrace[ $bt ]['line'];
				
				deprecatedSL::log( 'function', compact( 'deprecated', 'replacement', 'version', 'hook', 'in_file', 'on_line'  ) );
			}
		}

		/** ====================================================================================================================================================
		* Display deprecated hook
		* 
		* @access private
		* @return void
		*/
		 
		static function log_hook( $hook, $replacement, $version, $message ) {
			global $wp_filter;

			$frmk = new coreSLframework() ; 
			
			if ($frmk->get_param("deprecated")) {
				$backtrace = debug_backtrace();
				
				$callbacks_attached = array();
				foreach ( $wp_filter[ $hook ] as $priority => $callbacks ) {
					foreach ( $callbacks as $callback ) {
						$callbacks_attached[] = deprecatedSL::callback_to_string( $callback['function'] );
					}
				}

				// For actions fired within a function.
				$in_file = deprecatedSL::strip_abspath( $backtrace[3]['file'] );
				$on_line = $backtrace[3]['line'] + 1; // _deprecated_file() is one line before do_action()

				$deprecated = $hook;
				foreach ( $callbacks_attached as $callback ) {
					deprecatedSL::log( 'hook', compact( 'deprecated', 'replacement', 'version', 'in_file', 'on_line', 'callback' ) );
				}
			}
		}

		/**
		 * Returns a string representation of a callback.
		 */
		
		static function callback_to_string( $callback ) {
			if ( is_array( $callback ) ) {
				if ( is_object( $callback[0] ) )
					return get_class( $callback[0] ) . '::' . $callback[1];
				else
					return $callback[0] . '::' . $callback[1];
			}
			return $callback;
		}
		
		/** ====================================================================================================================================================
		* Display deprecated wrong action
		* 
		* @access private
		* @return void
		*/
		
		static function log_wrong( $function, $message, $version ) {
			$frmk = new coreSLframework() ; 
			
			if ($frmk->get_param("deprecated")) {
				$backtrace = debug_backtrace();
				$deprecated = $function . '()';
				$in_file = deprecatedSL::strip_abspath( $backtrace[ 4 ]['file'] );
				$on_line = $backtrace[ 4 ]['line'];
				deprecatedSL::log( 'wrong', compact( 'deprecated', 'message', 'version', 'in_file', 'on_line' ) );
			}
		}

		/** ====================================================================================================================================================
		* Display deprecated argument
		* 
		* @access private
		* @return void
		*/
		
		static function log_argument( $function, $message, $version ) {
			$frmk = new coreSLframework() ; 
			
			if ($frmk->get_param("deprecated")) {
				$backtrace = debug_backtrace();
				$deprecated = $function . '()';
				$menu = $in_file = $on_line = null;
				// @todo [core] Introduce _deprecated_message() or something.
				switch ( $function ) {
					case 'options.php' :
						$deprecated = __( 'Unregistered Setting', 'log-deprecated' );
						deprecatedSL::log( 'functionality', compact( 'deprecated', 'message', 'version' ) );
						return;
					case 'has_cap' :
						if ( 0 === strpos( $backtrace[7]['function'], 'add_' ) && '_page' == substr( $backtrace[7]['function'], -5 ) ) {
							$bt = 7;
							if ( 0 === strpos( $backtrace[8]['function'], 'add_' ) && '_page' == substr( $backtrace[8]['function'], -5 ) )
								$bt = 8;
							$in_file = deprecatedSL::strip_abspath( $backtrace[ $bt ]['file'] );
							$on_line = $backtrace[ $bt ]['line'];
							$deprecated = $backtrace[ $bt ]['function'] . '()';
						} elseif ( '_wp_menu_output' == $backtrace[7]['function'] ) {
							$deprecated = 'current_user_can()';
							$menu = true;
						} else {
							$in_file = deprecatedSL::strip_abspath( $backtrace[6]['file'] );
							$on_line = $backtrace[6]['line'];
							$deprecated = 'current_user_can()';
						}
						break;
					case 'get_plugin_data' :
						$in_file = deprecatedSL::strip_abspath( $backtrace[4]['args'][0] );
						break;
					case 'define()' :
					case 'define' :
						if ( 'ms_subdomain_constants' == $backtrace[4]['function'] ) {
							$deprecated = 'VHOST';
							deprecatedSL::log( 'constant', compact( 'deprecated', 'message', 'menu', 'version' ) );
							return;
						}
						// Fall through.
					default :
						$in_file = deprecatedSL::strip_abspath( $backtrace[4]['file'] );
						$on_line = $backtrace[4]['line'];
						break;
				}
				deprecatedSL::log( 'argument', compact( 'deprecated', 'message', 'menu', 'version', 'in_file', 'on_line' ) );
			}
		}

		/**
		 * Strip ABSPATH from an absolute filepath. Also, Windows is lame.
		 */
		
		static function strip_abspath( $path ) {
			return ltrim( str_replace( array( untrailingslashit( ABSPATH ), '\\' ), array( '', '/' ), $path ), '/' );
		}
		
		/**
		 * Strip ABSPATH from an absolute filepath. Also, Windows is lame.
		 */
		 
		static function catchErrorWarningNotice($errno, $errstr, $errfile, $errline) {
			$frmk = new coreSLframework() ; 
			if ($frmk->get_param("deprecated")) {
				$deprecated = $errstr." (".$errno.")" ; 
				$in_file = deprecatedSL::strip_abspath($errfile) ; 
				$on_line = $errline ; 
				
				deprecatedSL::log( 'phperror', compact( 'deprecated', 'in_file', 'on_line' ) );
				
				/* Don't execute PHP internal error handler */
				return true;
			} else {
				return false ; 
			}
		}

		/**
		 * Used to log deprecated usage.
		 *
		 * @todo Logging what I end up displaying is probably a bad idea.
		 */
		 
		static function log( $type, $args ) {
			global $wpdb;
						
			extract( $args, EXTR_SKIP );	
			//echo $in_file."<br>" ; 
			// Check if this is one of the SL plugins
			$SLPlugin = false ; 
			$plugin_dir = deprecatedSL::strip_abspath(WP_PLUGIN_DIR) ; 
			$tempfile = str_replace($plugin_dir."/", "", $in_file) ; 
			if ($in_file!=$tempfile) {
				$plugin = explode("/", $tempfile) ; 
				$plugin = $plugin[0] ; 
				if (file_exists(WP_PLUGIN_DIR."/".$plugin."/core.php")) {
					if (file_exists(WP_PLUGIN_DIR."/".$plugin."/core.class.php")) {
						$SLPlugin = true ; 
					}
				}
			}			
			if (!$SLPlugin) {
				return ; 
			}

			switch ( $type ) {
				case 'phperror' :
					$deprecated = sprintf( __( 'PHP Error/Warning/Notice: %s', 'SL_framework' ), $deprecated );
					break;
				case 'functionality' :
					$deprecated = sprintf( __( 'DEPRECATED Functionality: %s', 'SL_framework' ), $deprecated );
					break;
				case 'constant' :
					$deprecated  = sprintf( __( 'DEPRECATED Constant: %s', 'SL_framework' ), $deprecated );
					break;
				case 'function' :
					$deprecated = sprintf( __( 'DEPRECATED Function: %s', 'SL_framework' ), $deprecated );
					break;
				case 'file' :
					$deprecated = sprintf( __( 'DEPRECATED File: %s', 'SL_framework' ), $deprecated );
					break;
				case 'argument' :
					$deprecated = sprintf( __( 'DEPRECATED Argument in %s', 'SL_framework' ), $deprecated );
					break;
				case 'wrong' :
					$deprecated = sprintf( __( 'DEPRECATED Incorrect Use of %s', 'SL_framework' ), $deprecated );
					break;
				case 'hook' :
					$deprecated = sprintf( __( 'DEPRECATED Hook: %s', 'SL_framework' ), $deprecated );
					break;
			}

			$content = '';
			if ( ! empty( $replacement ) )
				// translators: %s is name of function.
				$content = sprintf( __( 'Use %s instead.', 'SL_framework' ), $replacement );
			if ( ! empty( $message ) )
				$content .= ( strlen( $content ) ? ' ' : '' ) . (string) $message;
			if ( empty( $content ) )
				$content = __( 'No alternative available.', 'SL_framework' );
			if ( 'wrong' == $type )
				$content .= "\n" . sprintf( __( 'This message was added in version %s.', 'SL_framework' ), $version );
			else if ( 'phperror' == $type )
				$content = sprintf( __( 'Date: %s.', 'SL_framework' ), date_i18n("F j, Y g:i a") );			
			else
				$content .= "\n" . sprintf( __( 'Deprecated in version %s.', 'SL_framework' ), $version );

			if ( 'phperror' == $type ) {
				$excerpt = sprintf( __( 'This message has been fired in %1$s on line %2$d.', 'SL_framework'), $in_file, $on_line);
			} else if ( 'hook' == $type ) {
				$excerpt = sprintf( __( 'The callback %3$s() is attached to this hook, which is fired in %1$s on line %2$d.', 'SL_framework'), $in_file, $on_line, $callback );
			} elseif ( ! empty( $hook ) ) {
				$excerpt = sprintf( __( 'Attached to the %1$s hook, fired in %2$s on line %3$d.', 'SL_framework'), $hook, $in_file, $on_line );
			} elseif ( ! empty( $menu ) ) {
				$excerpt = __( 'An admin menu page is using user levels instead of capabilities (but it is impossible to say which one ...)', 'SL_framework');
			} elseif ( ! empty( $on_line ) ) {
				$excerpt = sprintf( __( 'Used in %1$s on line %2$d.', 'SL_framework'), $in_file, $on_line );
			} elseif ( ! empty( $in_file ) ) {
				// translators: %s is file name.
				$excerpt = sprintf( __( 'Used in %s.', 'SL_framework'), $in_file );
			} else {
				$excerpt = $in_file." ".$on_line;
			}

			
			$frmk = new coreSLframework() ; 
			if ((!IS_AJAX_SL)&&(is_admin())) {
				echo "<div class='updated'>" ; 
				echo "<p><b>$deprecated</b></p>" ; 
				echo "<p>$content</p>" ; 
				echo "<p>$excerpt</p>" ; 
				echo "</div>" ; 
			} else if (IS_AJAX_SL) {
				$content_out = "<div class='updated'>" ; 
				$content_out .= "<p><i>AJAX:</i> <b>$deprecated</b></p>" ; 
				$content_out .= "<p>$content</p>" ; 
				$content_out .= "<p>$excerpt</p>" ; 
				$content_out .= "</div>" ;
				$alert = $frmk->get_param("deprecated_front") ; 
				if (is_array($alert)) {
					if (count($alert) < 100) {
						$alert[] = $content_out; 
						$frmk->set_param("deprecated_front",$alert) ; 
					}
				} else {
					$frmk->set_param("deprecated_front",array($content_out)) ; 
				}			
			} else {
				$content_out = "<div class='updated'>" ; 
				$content_out .= "<p><i>FRONT:</i> <b>$deprecated</b></p>" ; 
				$content_out .= "<p>$content</p>" ; 
				$content_out .= "<p>$excerpt</p>" ; 
				$content_out .= "</div>" ;
				$alert = $frmk->get_param("deprecated_front") ; 
				if (is_array($alert)) {
					if (count($alert) < 100) {
						$alert[] = $content_out; 
						$frmk->set_param("deprecated_front",$alert) ; 
					}
				} else {
					$frmk->set_param("deprecated_front",array($content_out)) ; 
				}
			}
		}
		
		/**
		 * Used to log deprecated usage.
		 *
		 * @todo Logging what I end up displaying is probably a bad idea.
		 */
		 
		static function show_front() {
			$frmk = new coreSLframework() ; 
			$cont = $frmk->get_param("deprecated_front") ; 
			if (count($cont)>0) {
				foreach ($cont as $a) {
					echo $a ; 
				}
			}
			$frmk->set_param("deprecated_front",array()) ; 
		}


	}
	
	set_error_handler(array('deprecatedSL', "catchErrorWarningNotice"));
}






?>