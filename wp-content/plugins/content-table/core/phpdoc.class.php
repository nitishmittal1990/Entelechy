<?php
/*
Core SedLex Plugin
VersionInclude : 3.0
*/ 

/** =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*
* This PHP class enables the generation of the documentation of the PHP files used for the framework
* Please note that the methods of this class is not supposed to be called from your plugin. Thus, its methods are not displayed here.
*/
if (!class_exists("phpDoc")) {
	class phpDoc {
	
		var $content ;
		
		/** ====================================================================================================================================================
		* Constructor
		* 
		* @access private
		* @param string $file the file to scan
		* @return void
		*/
		function phpDoc() {
			$this->content = array() ; 
			$this->result = array() ; 
		}
		
		/** ====================================================================================================================================================
		* Add a file
		* 
		* @access private
		* @param string $file the file to scan
		* @return void
		*/
		function addFile($file) {
			$this->content[] = @file_get_contents($file) ; 
		}

		/** ====================================================================================================================================================
		* Get the classes name in the file
		* 
		* @access private
		* @return array
		*/
		public function parse() {
		
			$c = array() ; 
			
			foreach ($this->content as $content) {
				$matches = array() ; 
				
				$tokens = token_get_all($content);
				$class_token = false;
				foreach ($tokens as $token) {
					if (is_array($token)) {
						if ($token[0] == T_CLASS) {
							$class_token = true;
						} else if ($class_token && $token[0] == T_STRING) {
							$class_token = false;
							//FOUND
							$matches[] = $token[1] ; 
						}
					}       
				}
				
				foreach($matches as $id => $cl){
					
					$methods = get_class_methods($cl) ;  
					$reflector = new ReflectionClass($cl);
					
					$desc = $reflector->getDocComment();
					
					$m = array() ; 
					
					foreach ($methods as $method) {
						$gm = $reflector->getMethod($method) ; 
						
						$parameters = $gm->getParameters();
						$comment = $gm->getDocComment();
						
						
						$d = $this->parseComments($comment) ; 
						$d = $this->parseParameters($d, $parameters) ; 
						
						$m = array_merge($m, array($method => $d)) ; 
					}
					
					$c = array_merge($c, array($cl => array('methods'=>$m, 'description' => $this->parseComments($desc) ))) ; 
				}
			}
			
			$this->result = $c ;
		}
		
		
		/** ====================================================================================================================================================
		* Parse comments 
		* 
 		* @access private
		* @param string $comment the comment of each method 
		* @return array a array of string or of array which contains the formated comment and arguments
		*/
		private function parseComments($comment) {
			$comment = str_replace("\r","" ,$comment) ; 
			$lignes = explode("\n", $comment) ; 
			$result = array( 	"comment" => "",
			
						"abstract" => "",
						"access" => "",
						"author" => "",
						"category" => "",
						"copyright" => "",
						"deprecated" => "",
						"example" => "",
						"final" => "",
						"filesource" => "",
						"global" => "",
						"ignore" => "",
						"internal" => "",
						"license" => "",
						"link" => "",
						"method" => "",
						"name" => "",
						"package" => "",
						"param" => "",
						"property" => "",
						"return" => "",
						"see" => "",
						"since" => "",
						"static" => "",
						"staticvar" => "",
						"subpackage" => "",
						"todo" => "",
						"tutorial" => "",
						"uses" => "",
						"var" => "",
						"version" => "") ; 
			foreach ($lignes as $l) {
				if (preg_match("/^\s*\*([^\/].*)$/",trim($l), $matches)) {
					$l = trim($matches[1]) ; 
					$found = false ; 
					foreach ($result as $n => $r) {
						if (preg_match("/^\s*@".$n."\s*(.*)$/",$l, $matches)) {
							$found = true ; 
							$m = htmlentities($matches[1]) ; 
							if ($r!="") {
								if (is_array($result[$n])){
									$result[$n] = array_merge(array($m), $r) ; 
								} else {
									$result[$n] = array($m, $r) ; 
								}
							} else {
								$result[$n] = $m ; 
							}
						}
					}
					if (!$found) {
						$result['comment'] .= $l."\n" ; 
					}
				}
			}
			return $result ; 
		} 
		
		/** ====================================================================================================================================================
		* Add Missing Parameters to the parsed Comment
		* 
		* @access private
		* @param array parsedComment the array returned by the parseComments function
		* @param array $params an array of object containing parameters of the function
		* @return void
		*/
		private function parseParameters($parsedComment, $params) {
			
			$array_params = array() ; 
			
			foreach ($params as $p) {
				if ($p->isOptional()) {
					$array_params[] = array(	'name' => $p->getName(), 
										'default' => $p->getDefaultValue(), 
										'position' => $p->getPosition(), 
										'description' => '??', 
										'type' => '??' ) ; 
				} else {
					$array_params[] = array(	'name' => $p->getName(), 
										'position' => $p->getPosition(), 
										'description' => '??', 
										'type' => '??' ) ; 
				
				}
			}
			
			$desc = $parsedComment['param'] ; 
			
			if ($desc != "") {
				if (!is_array($desc)) $desc = array($desc) ; 
				
				foreach ($desc as $d) {
					$de = explode(" ", trim($d), 3) ; 
					foreach ($array_params as $i => $ch) {
						if ($de[1]=="$".$ch['name']) {
							$array_params[$i]['type'] = $de[0] ;
							if (isset($de[2])) {
								$array_params[$i]['description'] = $de[2] ;
							} else {
								$array_params[$i]['description'] = "" ;
							}
						}
					}
				}
				
			}
	
	
			
			$parsedComment['param'] = $array_params  ; 
			
			
			
			return $parsedComment ; 
			
		} 
		
		/** ====================================================================================================================================================
		* Print the documentation of classes
		* 
		* @access private
		* @param array $rc the array containing the phpDoc format 
		* @return void
		*/
		
		function flush()  {
		
			// We check if we have a cached file for the version of framework. If so we return the cached file. If not we create the cache file
			$path = WP_PLUGIN_DIR.'/'.str_replace(basename( __FILE__),"",plugin_basename( __FILE__)) ; 
			if (is_file($path.'../core.nfo')) {
				$info = file_get_contents($path.'../core.nfo') ; 
				$info = explode("#", $info) ; 
				$md5 = $info[0] ; 
				if (is_file($path.'data/phpdoc_'.$md5.'.html')) {
					echo @file_get_contents($path.'data/phpdoc_'.$md5.'.html') ; 
					echo "<p style='color:#CCCCCC'>".sprintf(__('Saved on %s', 'SL_framework'), date_i18n(get_option('date_format') , filemtime($path.'data/phpdoc_'.$md5.'.html')) ) ."<p>" ; 
					return ; 
				}
			} 
			echo "<p style='color:#CCCCCC'>".__('No cache file found... Regenerating one!', 'SL_framework') ."<p>" ; 

			// We delete all phpdoc cache files
			$dir = @opendir($path.'data/'); 
			while(false !== ($item = readdir($dir))) {
				if ('.' == $item || '..' == $item)
					continue;
				if (preg_match("/^phpdoc/", $item, $h)) {
					unlink($path.'data/'.$item) ; 
				}
			}
			
			ob_start() ; 
	
				$rc = $this->result ; 
				
				$allowedtags = array('a' => array('href' => array()),'code' => array(), 'p' => array() ,'br' => array() ,'ul' => array() ,'li' => array() ,'strong' => array());
			
				// Print the summary of the method
				echo "<h3>".__('PHP Doc for the SL Framework', 'SL_framework')."</h3>" ; 
				echo "<p>".__('Here are the classes of the SL framework:', 'SL_framework')."</p>" ; 
				
				echo "<ul>" ; 
				foreach ($rc as $name => $cl) {
					if ($name!="coreSLframework") {
						$descr = wp_kses($cl['description']['comment'], $allowedtags);
						$descr = explode("\n", $descr ) ; 
						
						echo "<li class='li_class'><b><a href='#class_".$name."'>".$name."</a></b>: ".$descr[0]."</li>" ; 
					}
				}		
				echo "</ul>" ; 
	
				foreach ($rc as $name => $cl) {
					if ($name=="coreSLframework") {
						continue ; 
					}
					
					echo "<a name='class_".$name."'></a>" ; 
					ob_start() ; 
						
						$cl['description']['comment'] = wp_kses($cl['description']['comment'], $allowedtags);
						$cl['description']['comment'] = explode("\n", $cl['description']['comment'] ) ; 
						foreach($cl['description']['comment'] as $c) {
							if (trim($c)!="") 
								echo "<p>".trim($c)."</p>" ; 
						}
						echo "<p>".__('Here is the method of the class:', 'SL_framework')."</p>" ; 
						
						// Print the summary of the method
						echo "<ul>" ; 
						foreach ($cl['methods'] as $name_m => $method) {
							if (($method['access']!='private')&&($method['return']!="")) {
								$descr = wp_kses($method['comment'], $allowedtags);
								$descr = explode("\n", $descr) ; 
								echo "<li class='li_class'><b><a href='#".$name."_".$name_m."'>".$name."::".$name_m."</a></b>: ".$descr[0]."</li>" ; 
							}
						}				
						echo "</ul>" ; 
						
						$table = new adminTable() ; 
						$table->title(array(__('Methods', 'SL_framework'), __('Details', 'SL_framework') ) ) ; 
						
						foreach ($cl['methods'] as $name_m => $method) {
							
							if (($method['access']!='private')&&($method['return']!="")) {
								ob_start() ; 
									echo "<a name='".$name."_".$name_m."'>" ; 
									echo "<p><b>".$name_m ."</b><span class='desc_phpDoc'>".__('[METHOD]', 'SL_framework')."</span></p>" ; 
									
									$method['comment'] = wp_kses($method['comment'], $allowedtags);
									$method['comment'] = explode("\n", $method['comment']) ; 
									
									foreach($method['comment'] as $c) {
										if (trim($c)!="") {
											echo "<p>".trim($c)."</p>" ; 
										}
									}
								$cel1 = new adminCell(ob_get_clean()) ;
								
								ob_start() ;
									$typical = " $name_m (" ; 
									if (count($method['param'])>0) {
										echo "<p><b>".__('Parameters:','SL_framework')."</b></p>" ; 
										foreach ($method['param'] as $p) {
											echo "<p style='padding-left:30px;'>" ; 
											if (isset($p['default'])) {
												if (is_array($p['default'])) 
													$p['default'] = "[".implode(", ", $p['default'])."]" ; 
												echo "<b>$".$p['name']."</b> ".__('[optional]', 'SL_framework')." (<i>".$p['type']."</i>) ".$p['description']." ".__('(by default, its value is:', 'SL_framework')." ".htmlentities($p['default']).") "; 
											} else{
												echo "<b>$".$p['name']."</b> (<i>".$p['type']."</i>) ".$p['description'] ; 
											}
											
											echo "</p>" ; 
											if ($p['position']>0)
												$typical = $typical.', ' ; 
											if (isset($p['default'])) {
												$typical = $typical."[$".$p['name']."]" ; 
											} else {
												$typical = $typical."$".$p['name'] ; 
											}
										}
									} else {
										echo "<p><b>".__('Parameters:','SL_framework')." </b></p><p style='padding-left:30px;'>".__('No param','SL_framework')."</p>" ; 
									}
									$typical = $typical.") ; " ; 
									
									$return = explode(" ",$method['return']." ",2) ; 
									echo "<p><b>".__('Return value:','SL_framework')."</b></p>" ; 
									echo "<p style='padding-left:30px;'><b>".$return[0]."</b> ".trim($return[1])."</p>" ; 
									
									echo "<p><b>".__('Typical call:','SL_framework')."</b></p>" ; 
									echo "<p style='padding-left:30px;'><code>".$return[0].$typical."</code></p>" ; 
									
									if ($method['see'] !="") {
										echo "<p><b>".__('See also:','SL_framework')."</b></p>" ; 
										if (is_array($method['see'] )) {
											foreach ($method['see'] as $s) {
												echo "<p style='padding-left:30px;'><a href='#".str_replace('::','_',$s)."'>".$s."</a></p>" ; 
											}
										} else {
											echo "<p style='padding-left:30px;'><a href='#".str_replace('::','_',$method['see'] )."'>".$method['see'] ."</a></p>" ; 
										}
									}
									
								$cel2 = new adminCell(ob_get_clean()) ;
								$table->add_line(array($cel1, $cel2), '1') ; 							
							}
						}
						echo $table->flush() ; 
					$box = new boxAdmin ($name."<span class='desc_phpDoc'>".__('[CLASS]', 'SL_framework')."</span>" , ob_get_clean()) ; 
					echo $box->flush() ; 
				}
			$content = ob_get_clean() ; 
			// We cache the result
			if (is_file($path.'../core.nfo')) {
				$info = file_get_contents($path.'../core.nfo') ; 
				$info = explode("#", $info) ; 
				$md5 = $info[0] ; 
				@file_put_contents($path.'data/phpdoc_'.$md5.'.html', $content) ; 
			}
			// We print 			
			echo $content ;
		}
	}
	
	
}

?>