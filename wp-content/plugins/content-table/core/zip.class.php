<?php
/*
Core SedLex Plugin
VersionInclude : 3.0
*/ 

require_once (ABSPATH . 'wp-admin/includes/class-pclzip.php');

/** =*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*=*
* This PHP class creates zip file (multipart if needed)
* It requires the gzcompress function. Otherwise, a fatal error will be raised
* For instance : 
* <code>$z = new SL_Zip;<br/>$z -> addFile("/www/test/File.txt","/www/test/","/newroot/");<br/>$z -> addDir("/www/test/Folder","/www/test/","/newroot/") ; <br/>$z -> createZip("/pathToZip/backup.zip",1048576);</code>
*/
if (!class_exists("SL_Zip")) {
	class SL_Zip {
		var $filelist = array();
		var $dirlist = array();
		var $starttime =0 ; 
		
		function SL_Zip() {
			$this->starttime = microtime(true) ; 
			if (!@function_exists('gzcompress')) {
				die(sprintf(__('Error: %s function is not found', 'SL_framework'), "<code>gzcompress()</code>"));
			}
		}
		
		/** ====================================================================================================================================================
		* Return the progression ratio
		* 
		* @param $file the zip file that is being created
		* @return string the progress nb_file_included/nb_file
		*/
		
		static function progress($file) {
			if (is_file($file.".tmp")) {
				// We retrieve the process
				$content = @file_get_contents($file.".tmp") ; 
				list($nbentry, $nbfolder, $pathToReturn, $disk_number, $filelist, $files_not_included_due_to_filesize) = unserialize($content) ; 
				return $nbentry."/".(count($filelist)+$nbentry) ; 
			} 
			return "0/0" ; 
		}	
		
		/** ====================================================================================================================================================
		* Add files to the archive
		* 
		* @param string $filename the path of the file to add
		* @param string $remove the part of the path to remove
		* @param string $add the part of the path to add
		* @return void
		*/
		
		function addFile($filename, $remove="", $add="") {
			if(is_file($filename)) {
				$this->filelist[] = array(str_replace('\\', '/', $filename), $remove, $add) ;
			} else {
				SL_Debug::log(get_class(), "Failed to add file to list: ".str_replace('\\', '/', $filename), 3) ; 
			}
		}
		
		/** ====================================================================================================================================================
		* Add directory to the archive (reccursively)
		* 
		* @param string $dirname the path of the folder to add
		* @param string $remove the part of the path to remove
		* @param string $add the part of the path to add
		* @param array $exclu a list of folder that are no to be included in the zip file
		* @return void
		*/
		
		function addDir($dirname, $remove="", $add="", $exclu=array()) {
			if ($handle = opendir($dirname)) { 
				while (false !== ($filename = readdir($handle))) { 
					// We check if exclu
					$exclu_folder = false ; 
					foreach($exclu as $e) {
						$path = str_replace("//", "/", $dirname . '/' . $filename) ; 
						if (($e==$path)||($e==$path."/")) {
							$exclu_folder=true ; 
							SL_Debug::log(get_class(), "The folder has been excluded: ".$path, 4) ; 
						}
					}
					// On recursive
					if ($filename != "." && $filename != ".." && !$exclu_folder)  {
						if (is_file($dirname . '/' . $filename)) {
							$this->addFile($dirname . '/' . $filename, $remove, $add);
						} 
						if (is_dir($dirname . '/' . $filename)) {
							$this->addDir($dirname . '/' . $filename, $remove, $add, $exclu);
						}
					}
				} 
				closedir($handle); 
			} else {
				//Nothing
			}
		}
		
		/** ====================================================================================================================================================
		* reset the zip process
		* 
		* @param $path the path in which the zip should be created
		* @return void
		*/
		
		static function reset($path) {
			if (is_file($path."/zip_in_progress")) {
				unlink($path."/zip_in_progress") ; 
			}
			$files = scandir($path) ; 
			foreach ($files as $f) {
				// Fichier tmp
				if (preg_match("/^(.*)\zip\.tmp$/", $f, $match)) {
					unlink($path."/".$f) ; 
					if (is_file($path."/".$match[1]."zip")) {
						unlink($path."/".$match[1]."zip") ; 
					}
					$i = 0 ; 
					while (true) {
						if (is_file($path."/".$match[1]."z".sprintf("%02d", $i))) {
							unlink($path."/".$match[1]."z".sprintf("%02d", $i)) ; 
							$i ++ ; 
						} else {
							break ; 
						}
					}
				}
				if (preg_match("/^(.*)\centraldirectory\.tmp$/", $f)) {
					unlink($path."/".$f) ; 
				}
			}
		}
		
		/** ====================================================================================================================================================
		* Tells whether a zip file is being created or not
		* 
		* @param $path the path in which the zip should be created
		* @return array the 'step' could be 'in progress' (a process is still running), 'nothing' (no zip is being zipped) or 'to be completed' (and the 'name_zip' will be the name of the zip file being zipped) or 'error' (and the 'error' will display the error messgae)
		*/
		
		static function is_inProgress($path) {
			
			if (is_file($path."/zip_in_progress")) {
				$timestart = @file_get_contents($path."/zip_in_progress")  ;
				if ($timestart===FALSE) {
					SL_Debug::log(get_class(), "The file ".$path."/zip_in_progress cannot be read", 2) ; 
					return array("step"=>"error", "error"=>sprintf(__('The file %s cannot be read. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path."/zip_in_progress</code>")) ; 
				}

				// We ensure that the process has not been started a too long time ago				
				return array("step"=>"in progress", "for"=>(time() - (int)$timestart)) ; 
			} 
			
			// We search for a tmp file
			$files = @scandir($path) ;
			if ($files===FALSE) {
				SL_Debug::log(get_class(), "The folder ".$path." cannot be opened", 2) ; 
				return array("step"=>"error", "error"=>sprintf(__('The folder %s cannot be opened. You should have a problem with folder permissions or security restrictions.', 'SL_framework'),"<code>".$path."</code>")) ; 
			}
			foreach ($files as $f) {
				if (preg_match("/zip[.]tmp$/i", $f)) {
					$name_file = str_replace(".zip.tmp", ".zip",$f) ; 
					SL_Debug::log(get_class(), "Zip process has to be completed with the file ".$name_file, 4) ; 
					return array("step"=>"to be completed", 'name_zip' => $name_file) ; 
				} 
			}
			SL_Debug::log(get_class(), "No zip process in progress", 4) ; 
			return array("step"=>"nothing") ; 
		}	
		
		/** ====================================================================================================================================================
		* Create the archive and split it if necessary
		* 
		* @param string $splitfilename the path of the zip file to create
		* @param integer $chunk_size the maximum size of the archive
		* @param integer $maxExecutionTime the maximum execution time in second (if this time is exceeded, the function will return false. You just have to relaunch this function to complete the zip from where it has stopped)
		* @param integer $maxExecutionTime the maximum memory allocated by the process (in bytes)
		* @return array with the name of the file (or 'finished' => false if an error occured see 'error' for the error message)
		*/
		
		function createZip($splitfilename, $chunk_size=1000000000000000, $maxExecutionTime=150, $maxAllocatedMemory=4000000) {
			
			// Init variables
			//---------------------
			
			$zipfile_comment = "Compressed/Splitted by the SL framework (SedLex)";
			
			$path = str_replace(basename ($splitfilename), "", $splitfilename) ; 
			
			$pathToReturn = array() ; 
			
			$disk_number = 1 ; 
			$split_signature = "\x50\x4b\x07\x08"; // Optionnal
			$nbentry = 0 ; 
			$nbfolder = 0 ; 
			$data_segments = "" ; 
			$files_not_included_due_to_filesize = array() ; 
			
			//  We check whether a process is running
			//----------------------------------------------

			if (is_file(dirname($splitfilename)."/zip_in_progress")) {
				$timestart = @file_get_contents(dirname($splitfilename)."/zip_in_progress")  ;
				
				// We cannot read the lock file
				if ($timestart===FALSE) {
					if (!Utils::rm_rec($path."/zip_in_progress")) {
						SL_Debug::log(get_class(), "The file ".$path."/zip_in_progress cannot be deleted", 2) ; 
						return array("step"=>"error", "error"=>sprintf(__('The file %s cannot be deleted. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path."/zip_in_progress</code>")) ; 
					}
					SL_Debug::log(get_class(), "The file ".dirname($splitfilename)."/zip_in_progress cannot be deleted", 2) ; 
					return array('finished'=>false, "error"=>sprintf(__('The file %s cannot be read. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".dirname($splitfilename)."/zip_in_progress</code>")) ; 
				}
				$timeprocess = time() - (int)$timestart ; 

				SL_Debug::log(get_class(), "An other process is still running  for ".$timeprocess, 2) ; 
				return array('finished'=>false, 'error' => sprintf(__("An other process is still running (it runs for %s seconds)", "SL_framework"), $timeprocess)) ; 
			}
			
			//  We create a lock file
			//----------------------------------------------

			$r = @file_put_contents(dirname($splitfilename)."/zip_in_progress", time()) ; 
			if ($r===FALSE) {
				SL_Debug::log(get_class(), "The file ".$path."/zip_in_progress cannot be modified/created", 2) ; 
				if (!Utils::rm_rec($path."/zip_in_progress")) {
					SL_Debug::log(get_class(), "The file ".$path."/zip_in_progress cannot be deleted", 2) ; 
					return array("step"=>"error", "error"=>sprintf(__('The file %s cannot be deleted. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path."/zip_in_progress</code>")) ; 
				}
				return array('finished'=>false, "error"=>sprintf(__('The file %s cannot be modified/created. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".dirname($splitfilename)."/zip_in_progress</code>")) ; 
			} else {
				SL_Debug::log(get_class(), "The file ".$path."/zip_in_progress has be created to ensure that a lock file exists", 5) ; 
			}
					
			//  We retrieve old saved param
			//      if the .tmp file exists, it means that we have to restart the zip process where it stopped
			//----------------------------------------------

			if (is_file($splitfilename.".tmp")) {
				// We retrieve the process
				$content = @file_get_contents($splitfilename.".tmp") ; 
				
				if ($content===FALSE) {
					SL_Debug::log(get_class(), "The file ".$splitfilename.".tmp cannot be read", 2) ; 
					if (!Utils::rm_rec($path."/zip_in_progress")) {
						SL_Debug::log(get_class(), "The file ".$path."/zip_in_progress cannot be deleted", 2) ; 
						return array("step"=>"error", "error"=>sprintf(__('The file %s cannot be deleted. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path."/zip_in_progress</code>")) ; 
					}
					return array('finished'=>false, "error"=>sprintf(__('The file %s cannot be read. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$splitfilename.".tmp</code>")) ; 
				}
				
				list($nbentry, $nbfolder, $pathToReturn, $disk_number, $this->filelist, $files_not_included_due_to_filesize) = unserialize($content) ; 
				SL_Debug::log(get_class(), "Get the unserialized content of  ".$splitfilename.".tmp", 4) ; 
			
			
			//  We start a new process if nothing have yet started
			//----------------------------------------------
			
			} else {
							
				// We add the signature in the zip file
				
				$r = @file_put_contents($path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number) ,$split_signature) ; 
				$pathToReturn[time()] = $path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number) ;
				if ($r===FALSE) {
					SL_Debug::log(get_class(), "The signature of the zip file cannot be added to ".$path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number), 2) ; 
					if (!Utils::rm_rec($path."/zip_in_progress")) {
						SL_Debug::log(get_class(), "The file ".$path."/zip_in_progress cannot be deleted", 2) ; 
						return array("step"=>"error", "error"=>sprintf(__('The file %s cannot be deleted. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path."/zip_in_progress</code>")) ; 
					}
					return array('finished'=>false, "error"=>sprintf(__('The file %s cannot be created. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number)."</code>")) ; 
				} else {
					SL_Debug::log(get_class(), "The signature of the zip file has been added to ".$path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number), 4) ; 
				}
				
				// We create the list of folder
				SL_Debug::log(get_class(), "Begin the list of folders" , 4) ; 
				foreach ($this->filelist as $k => $filename_array) {
					$add_t = $filename_array[2] ; 
					$remove_t = $filename_array[1] ; 
					$filename = $filename_array[0] ; 
					$newfilename = str_replace("//", "/", $add_t.str_replace(str_replace("\\", "/", $remove_t), "", str_replace("\\", "/", $filename))) ; 
					
					if (!is_file($filename)) {
						continue ; 
					}
					
					$ch = explode("/", $newfilename) ; 
					// We delete the last item because it is the filename of the file
					unset($ch[count($ch)-1]) ; 
					while (count($ch)>0) {
						$chemin = implode("/", $ch)."/" ; 
						if (!isset($this->dirlist[md5($chemin)])) {
							$this->dirlist[md5($chemin)] = $chemin; 
						}
						unset($ch[count($ch)-1]) ; 
					}
				}
				
				usort($this->dirlist, array($this, 'sort_by_length_dir')) ; 
				usort($this->filelist, array($this, 'sort_by_length_file')) ; 
				
				// We add the folder
				foreach($this->dirlist as $dir) {
					$relative_offset_in_disk = 0 ; 
					if (is_file(($path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number)))) {
						clearstatcache() ; 
						$relative_offset_in_disk = @filesize($path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number)) ; 
					}
					
					$dir = Utils::convertUTF8($dir) ; 

					$local_file_header  = "\x50\x4b\x03\x04";						// 4 bytes  (0x04034b50) local_file_header_signature
					$local_file_header .= "\x14\x00"; 					// 2 bytes version_needed_to_extract
					$local_file_header .= "\x00\x08"	;							// 2 bytes general_purpose_bit_flag (we say that the name should be encode in UTF8)
					$local_file_header .= pack('v', 0); 								// 2 bytes compression_method
					$local_file_header .= pack('v', 0); 						// 2 bytes last mod file time
					$local_file_header .= pack('v', 0);  							// 2 bytes last mod file time
					$local_file_header .= pack('V', 0);  							// 4 bytes crc_32
					$local_file_header .= pack('V', 0);	// 4 bytes compressed_size
					$local_file_header .= pack('V', 0);				// 4 bytes uncompressed_size
					$local_file_header .= pack('v', strlen($dir));			// 2 bytes filename_length
					$local_file_header .= pack('v', 0);  							// 2 bytes extra_field_length
					$local_file_header .= $dir  ; 							// variable size filename
					$local_file_header .= ""  ;  									// variable size extra fields 
				
					$r = @file_put_contents($path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number) ,$local_file_header, FILE_APPEND) ; 
					if ($r===FALSE) {
						SL_Debug::log(get_class(), "The folder ".$dir." can not be added to zip file ".$path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number), 2) ; 
						if (!Utils::rm_rec($path."/zip_in_progress")) {
							SL_Debug::log(get_class(), "The file ".$path."/zip_in_progress cannot be deleted", 2) ; 
							return array("step"=>"error", "error"=>sprintf(__('The file %s cannot be deleted. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path."/zip_in_progress</code>")) ; 
						}
						return array('finished'=>false, "error"=>sprintf(__('The file %s cannot be modified/created. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number)."</code>")) ; 
					} else {
						SL_Debug::log(get_class(), "The folder ".$dir." has been added to zip file ".$path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number), 5) ; 
					}
					
					//Set central File Header
					$central_file_header  = "\x50\x4b\x01\x02";							// 4 bytes (0x02014b50) central file header signature
					$central_file_header .= "\x14\x00"; 							// 2 bytes version made by (0 = MS-DOS and OS/2 compatible)
					$central_file_header .= "\x14\x00"; 								// 2 bytes version needed to extract
					$central_file_header .= "\x00\x08" ;						// 2 bytes general_purpose_bit_flag (we say that the name should be encode in UTF8)
					$central_file_header .= pack('v', 0);  								// 2 bytes compression_method
					$central_file_header .= pack('v', 0); 						// 2 bytes last mod file time
					$central_file_header .= pack('v', 0); 						// 2 bytes last mod file time
					$central_file_header .= pack('V', 0); 				// 4 bytes crc_32
					$central_file_header .= pack('V', 0);	// 4 bytes compressed_size
					$central_file_header .= pack('V', 0);				// 4 bytes uncompressed_size
					$central_file_header .= pack('v', strlen($dir));			// 2 bytes filename_length
					$central_file_header .= pack('v', 0);  								// 2 bytes extra_field_length
					$central_file_header .= pack('v', 0); 								// 2 bytes  comment length
					$central_file_header .= pack('v', $disk_number-1); 	// 2 bytes disk number start
					$central_file_header .= pack('v', 0) ; 								// 2 bytes internal file attribute
					$central_file_header .= pack('V', 16) ; 							// 4 bytes external file attribute
					$central_file_header .= pack('V', $relative_offset_in_disk);	// 4 bytes relative offset of local header
					$central_file_header .= $dir  ; 							// variable size filename
					$central_file_header .= ""  ;  										// variable size extra fields 
					$central_file_header .= "" ; 										// variable size file comment
					
					$nbfolder ++ ; 
									
					$r = @file_put_contents($splitfilename.".centraldirectory.tmp" ,$central_file_header, FILE_APPEND) ; 
					if ($r===FALSE) {
						SL_Debug::log(get_class(), "The folder ".$dir." cannot be added to central header file ".$splitfilename.".centraldirectory.tmp", 2) ; 
						if (!Utils::rm_rec($path."/zip_in_progress")) {
							SL_Debug::log(get_class(), "The file ".$path."/zip_in_progress cannot be deleted", 2) ; 
							return array("step"=>"error", "error"=>sprintf(__('The file %s cannot be deleted. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path."/zip_in_progress</code>")) ; 
						}
						return array('finished'=>false, "error"=>sprintf(__('The file %s cannot be modified/created. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$splitfilename.".centraldirectory.tmp</code>")) ; 
					} else {
						SL_Debug::log(get_class(), "The folder ".$dir." has been added to central header file ".$splitfilename.".centraldirectory.tmp", 5) ; 
					}
				}
				SL_Debug::log(get_class(), "End the list of  folders" , 4) ; 
			}
				
			//  The creation of the zip begin
			//----------------------------------------------
			
			SL_Debug::log(get_class(), "Begin the loop for ".count($this->filelist)." files" , 4) ; 
			foreach($this->filelist as $k => $filename_array) {
				$add_t = $filename_array[2] ; 
				$remove_t = $filename_array[1] ; 
				$filename = $filename_array[0] ; 
				
				//  If the time limit / memory limit exceed, we save into temp files
				//----------------------------------------------
				
				$nowtime = microtime(true) ; 
				if ($maxExecutionTime!=0) {
					if ($nowtime - $this->starttime > $maxExecutionTime){
						// We remove the files already inserted in the zip
						$this->filelist =  array_slice($this->filelist,$k);
						// We save the content on the disk
						
						$r = @file_put_contents($splitfilename.".tmp" ,serialize(array($nbentry, $nbfolder, $pathToReturn, $disk_number, $this->filelist, $files_not_included_due_to_filesize))) ; 
						if ($r===FALSE) {
							SL_Debug::log(get_class(), "The serialized information cannot be written in ".$splitfilename.".tmp", 2) ; 
							if (!Utils::rm_rec($path."/zip_in_progress")) {
								SL_Debug::log(get_class(), "The file ".$path."/zip_in_progress cannot be deleted", 2) ; 
								return array("step"=>"error", "error"=>sprintf(__('The file %s cannot be deleted. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path."/zip_in_progress</code>")) ; 
							}
							return array('finished'=>false, "error"=>sprintf(__('The file %s cannot be modified/created. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$splitfilename.".tmp</code>")) ; 
						} else {
							SL_Debug::log(get_class(), "The serialized information has been written successfully in ".$splitfilename.".tmp", 4) ; 
						}
						// we inform that the process is finished
						if (!Utils::rm_rec($path."/zip_in_progress")) {
							SL_Debug::log(get_class(), "The file ".$path."/zip_in_progress cannot be deleted", 2) ; 
							return array("step"=>"error", "error"=>sprintf(__('The file %s cannot be deleted. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path."/zip_in_progress</code>")) ; 
						}
						SL_Debug::log(get_class(), "The Zip process is delayed (".$nbentry."/".(count($this->filelist)+$nbentry)." files)", 4) ; 
						return  array('finished'=>false, 'nb_to_finished' => count($this->filelist), 'nb_finished' => $nbentry, 'not_included'=>$files_not_included_due_to_filesize) ; 
					}
				}
				
				
				//  Check if the file to be inserted in the zip file still exists
				//----------------------------------------------
				
				
				if (!is_file($filename)) {
					SL_Debug::log(get_class(), "The file ".$filename." does not exists and is ignored", 3) ; 
					continue ; 
				}
				
				// Check the length of the file
				clearstatcache() ; 
				if (@filesize($filename)>$maxAllocatedMemory) {
					SL_Debug::log(get_class(), "The file ".$filename." is too big (i.e. ".@filesize($filename).") and is then ignored", 3) ; 
					$files_not_included_due_to_filesize[] = $filename ; 
					continue ; 
				}
				
				//  Compress
				//----------------------------------------------

				$nbentry ++ ; 
				
				//Get the data
				$filedata = @file_get_contents($filename);
				if ($filedata===FALSE) {
					SL_Debug::log(get_class(), "The file ".$filename." can not be read", 2) ; 
					if (!Utils::rm_rec($path."/zip_in_progress")) {
						SL_Debug::log(get_class(), "The file ".$path."/zip_in_progress can not be deleted", 2) ; 
						return array("step"=>"error", "error"=>sprintf(__('The file %s cannot be deleted. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path."/zip_in_progress</code>")) ; 
					}
					return array('finished'=>false, "error"=>sprintf(__('The file %s cannot be read. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$filename."</code>")) ; 
				} else {
					SL_Debug::log(get_class(), "The content of the file ".$filename." has been read", 5) ; 
				}
				
				//Compressing data
				$c_data = @gzcompress($filedata);
				if ($c_data===FALSE) {
					SL_Debug::log(get_class(), "The content of the file ".$filename." can not be compressed", 2) ; 
					if (!Utils::rm_rec($path."/zip_in_progress")) {
						SL_Debug::log(get_class(), "The file ".$path."/zip_in_progress can not be deleted", 2) ; 
						return array("step"=>"error", "error"=>sprintf(__('The file %s cannot be deleted. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path."/zip_in_progress</code>")) ; 
					}
					return array('finished'=>false, "error"=>sprintf(__('The file %s cannot be compressed.', 'SL_framework'),"<code>".$filename."</code>")) ; 
				} else {
					SL_Debug::log(get_class(), "The content of the file ".$filename." has been compressed", 5) ; 
				}
				$compressed_filedata = substr(substr($c_data, 0, strlen($c_data) - 4), 2); // fix crc bug
								
				// Get the time
				clearstatcache();
				$filetime = @filectime($filename);
				if ($filetime == 0) { 
					$timearray = getdate() ;
				} else { 
					$timearray = getdate($filetime) ; 
				}
				if ($timearray['year'] < 1980) {
					$timearray['year']    = 1980;
					$timearray['mon']     = 1;
					$timearray['mday']    = 1;
					$timearray['hours']   = 0;
					$timearray['minutes'] = 0;
					$timearray['seconds'] = 0;
				} 
				$dostime = (($timearray['year'] - 1980) << 25) | ($timearray['mon'] << 21) | ($timearray['mday'] << 16) | ($timearray['hours'] << 11) | ($timearray['minutes'] << 5) | ($timearray['seconds'] >> 1);
				$dtime    = dechex($dostime);
				$hexdtime = '\x' . $dtime[6] . $dtime[7] . '\x' . $dtime[4] . $dtime[5];
				$hexddate = '\x' . $dtime[2] . $dtime[3]. '\x' . $dtime[0] . $dtime[1];
				eval('$hexdtime = "' . $hexdtime . '";');
				eval('$hexddate = "' . $hexddate . '";');
				$last_mod_file_time = $hexdtime;
				$last_mod_file_date = $hexddate;
							
				//Set Local File Header
				$newfilename = str_replace("//", "/", $add_t.str_replace(str_replace("\\", "/", $remove_t), "", str_replace("\\", "/", $filename))) ; 
				if (substr($newfilename, 0, 1)=="/") {
					$newfilename = substr($newfilename, 1) ; 
				}
				
				/*
				 A.  Local file header:
					local file header signature     4 bytes  (0x04034b50)
					version needed to extract       2 bytes
					general purpose bit flag        2 bytes
					compression method              2 bytes
					last mod file time              2 bytes
					last mod file date              2 bytes
					crc-32                          4 bytes
					compressed size                 4 bytes
					uncompressed size               4 bytes
					file name length                2 bytes
					extra field length              2 bytes
					file name 						(variable size)
					extra field 					(variable size)
				*/
				
				$newfilename = Utils::convertUTF8($newfilename) ; 
				
				$local_file_header  = "\x50\x4b\x03\x04";						// 4 bytes  (0x04034b50) local_file_header_signature
				$local_file_header .= "\x14\x00"; 								// 2 bytes version_needed_to_extract
				$local_file_header .= "\x00\x08" ;							// 2 bytes general_purpose_bit_flag (we say that the name should be encode in UTF8)
				$local_file_header .= "\x08\x00";  								// 2 bytes compression_method
				$local_file_header .= $last_mod_file_time ;						// 2 bytes last mod file time
				$local_file_header .= $last_mod_file_date ;						// 2 bytes last mod file time
				$local_file_header .= pack('V', crc32($filedata)); 				// 4 bytes crc_32
				$local_file_header .= pack('V', strlen($compressed_filedata));	// 4 bytes compressed_size
				$local_file_header .= pack('V', strlen($filedata));				// 4 bytes uncompressed_size
				$local_file_header .= pack('v', strlen($newfilename));			// 2 bytes filename_length
				$local_file_header .= pack('v', 0);  							// 2 bytes extra_field_length
				$local_file_header .= $newfilename  ; 							// variable size filename
				$local_file_header .= ""  ;  									// variable size extra fields 
			
				// We add the local header in the zip files
				clearstatcache() ; 
				if (strlen($local_file_header) + @filesize($path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number))<=$chunk_size) {
					// We get the index of the file
					$relative_offset_in_disk = @filesize($path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number)) ; 

					$r = @file_put_contents($path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number) ,$local_file_header, FILE_APPEND) ; 
					if ($r===FALSE) {
						SL_Debug::log(get_class(), "The local file header of the file cannot be been added to ".$path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number), 2) ; 
						if (!Utils::rm_rec($path."/zip_in_progress")) {
							SL_Debug::log(get_class(), "The file ".$path."/zip_in_progress can not be deleted", 2) ; 
							return array("step"=>"error", "error"=>sprintf(__('The file %s cannot be deleted. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path."/zip_in_progress</code>")) ; 
						}
						return array('finished'=>false, "error"=>sprintf(__('The file %s cannot be modified/created. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number)."</code>")) ; 
					} else {
						SL_Debug::log(get_class(), "The local file header of the file has been added to ".$path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number), 5) ; 
					}
				// If the local header will be split, we create a new disk
				} else {
					$disk_number ++ ; 
					// We get the index of the file
					$relative_offset_in_disk = 0 ; 
					
					$pathToReturn[time()] = $path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number) ;
					$r = @file_put_contents($path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number) ,$local_file_header) ; 
					if ($r===FALSE) {
						SL_Debug::log(get_class(), "The local file header of the file cannot be been added to ".$path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number), 2) ; 
						if (!Utils::rm_rec($path."/zip_in_progress")) {
							SL_Debug::log(get_class(), "The file ".$path."/zip_in_progress can not be deleted", 2) ; 
							return array("step"=>"error", "error"=>sprintf(__('The file %s cannot be deleted. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path."/zip_in_progress</code>")) ; 
						}
						return array('finished'=>false, "error"=>sprintf(__('The file %s cannot be modified/created. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number)."</code>")) ; 
					} else {
						SL_Debug::log(get_class(), "The local file header of the file has been been added to ".$path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number), 5) ; 
					}
				}
				$disk_number_of_local_header = $disk_number ;
				
				/* 
				 B.  File data
					  Immediately following the local header for a file
					  is the compressed or stored data for the file. 
					  The series of [local file header][file data]
					  repeats for each file in the .ZIP archive. 
				*/
								
				// We add the compressed file in the zip files
				clearstatcache() ; 
				if (strlen($compressed_filedata) + @filesize($path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number))<=$chunk_size) {
					$r = @file_put_contents($path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number) ,$compressed_filedata, FILE_APPEND) ; 
					if ($r===FALSE) {
						SL_Debug::log(get_class(), "The compressed content of the file cannot be been added to ".$path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number), 2) ; 
						if (!Utils::rm_rec($path."/zip_in_progress")) {
							SL_Debug::log(get_class(), "The file ".$path."/zip_in_progress can not be deleted", 2) ; 
							return array("step"=>"error", "error"=>sprintf(__('The file %s cannot be deleted. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path."/zip_in_progress</code>")) ; 
						}
						return array('finished'=>false, "error"=>sprintf(__('The file %s cannot be modified/created. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number)."</code>")) ; 
					} else {
						SL_Debug::log(get_class(), "The compressed content of the file has been been added to ".$path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number), 5) ; 
					}
					
				// If the compressed file will be split, we create a new disk
				} else {
					clearstatcache() ; 
					$part1 = substr($compressed_filedata, 0, $chunk_size - @filesize($path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number))) ; 
					$part2 = substr($compressed_filedata , $chunk_size - @filesize($path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number))) ; 
					$r = @file_put_contents($path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number) ,$part1, FILE_APPEND) ; 
					if ($r===FALSE) {
						SL_Debug::log(get_class(), "The first part of the compressed content of the file cannot be added to ".$path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number), 2) ; 
						if (!Utils::rm_rec($path."/zip_in_progress")) {
							SL_Debug::log(get_class(), "The file ".$path."/zip_in_progress can not be deleted", 2) ; 
							return array("step"=>"error", "error"=>sprintf(__('The file %s cannot be deleted. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path."/zip_in_progress</code>")) ; 
						}
						return array('finished'=>false, "error"=>sprintf(__('The file %s cannot be modified/created. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number)."</code>")) ; 
					} else {
						SL_Debug::log(get_class(), "The first part of the compressed content of the file has been added to ".$path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number), 5) ; 
					}
					$disk_number ++ ; 
					$pathToReturn[time()] = $path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number) ;
					$r = @file_put_contents($path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number) ,$part2) ; 
					if ($r===FALSE) {
						SL_Debug::log(get_class(), "The second part of the compressed content of the file cannot be added to ".$path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number), 2) ; 
						if (!Utils::rm_rec($path."/zip_in_progress")) {
							SL_Debug::log(get_class(), "The file ".$path."/zip_in_progress can not be deleted", 2) ; 
							return array("step"=>"error", "error"=>sprintf(__('The file %s cannot be deleted. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path."/zip_in_progress</code>")) ; 
						}
						return array('finished'=>false, "error"=>sprintf(__('The file %s cannot be modified/created. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number)."</code>")) ; 
					} else {
						SL_Debug::log(get_class(), "The second part of the compressed content of the file has been added to ".$path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number), 5) ; 
					}
				}
				
				/*
				  F.  Central directory structure:
					  [file header 1]
					  ...
					  [file header n]
				
					 File header:
						central file header signature   4 bytes  (0x02014b50)
						version made by                 2 bytes
						version needed to extract       2 bytes
						general purpose bit flag        2 bytes
						compression method              2 bytes
						last mod file time              2 bytes
						last mod file date              2 bytes
						crc-32                          4 bytes
						compressed size                 4 bytes
						uncompressed size               4 bytes
						file name length                2 bytes
						extra field length              2 bytes
						file comment length             2 bytes
						disk number start               2 bytes
						internal file attributes        2 bytes
						external file attributes        4 bytes
						relative offset of local header 4 bytes
						file name 						(variable size)
						extra field 					(variable size)
						file comment 					(variable size)

				*/
				
				//Set central File Header
				$central_file_header  = "\x50\x4b\x01\x02";							// 4 bytes (0x02014b50) central file header signature
				$central_file_header .= "\x14\x00"; 								// 2 bytes version made by (0 = MS-DOS and OS/2 compatible)
				$central_file_header .= "\x14\x00"; 								// 2 bytes version needed to extract
				$central_file_header .= "\x00\x08"	;							// 2 bytes general_purpose_bit_flag (we say that the name should be encode in UTF8)
				$central_file_header .= "\x08\x00";  								// 2 bytes compression_method
				$central_file_header .= $last_mod_file_time ;						// 2 bytes last mod file time
				$central_file_header .= $last_mod_file_date;						// 2 bytes last mod file time
				$central_file_header .= pack('V', crc32($filedata)); 				// 4 bytes crc_32
				$central_file_header .= pack('V', strlen($compressed_filedata));	// 4 bytes compressed_size
				$central_file_header .= pack('V', strlen($filedata));				// 4 bytes uncompressed_size
				$central_file_header .= pack('v', strlen($newfilename));			// 2 bytes filename_length
				$central_file_header .= pack('v', 0);  								// 2 bytes extra_field_length
				$central_file_header .= pack('v', 0); 								// 2 bytes  comment length
				$central_file_header .= pack('v', $disk_number_of_local_header-1); 	// 2 bytes disk number start
				$central_file_header .= pack('v', 0) ; 								// 2 bytes internal file attribute
				$central_file_header .= pack('V', 32) ; 							// 4 bytes external file attribute
				$central_file_header .= pack('V', $relative_offset_in_disk);	// 4 bytes relative offset of local header
				$central_file_header .= $newfilename  ; 							// variable size filename
				$central_file_header .= ""  ;  										// variable size extra fields 
				$central_file_header .= "" ; 										// variable size file comment
								
				$r = @file_put_contents($splitfilename.".centraldirectory.tmp" ,$central_file_header, FILE_APPEND) ; 
				if ($r===FALSE) {
					SL_Debug::log(get_class(), "The central header cannot been added to ".$splitfilename.".centraldirectory.tmp", 2) ; 
					if (!Utils::rm_rec($path."/zip_in_progress")) {
						SL_Debug::log(get_class(), "The file ".$path."/zip_in_progress can not be deleted", 2) ; 
						return array("step"=>"error", "error"=>sprintf(__('The file %s cannot be deleted. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path."/zip_in_progress</code>")) ; 
					}
					return array('finished'=>false, "error"=>sprintf(__('The file %s cannot be modified/created. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$splitfilename.".centraldirectory.tmp</code>")) ; 
				} else {
					SL_Debug::log(get_class(), "The central header  has been added to ".$splitfilename.".centraldirectory.tmp", 5) ; 
				}		
			}
			
			//  Finalization
			//----------------------------------------------	
			/*
			 I.  End of central directory record:
				end of central dir signature    												4 bytes  (0x06054b50)
				number of this disk            		 											2 bytes
				number of the disk with the start of the central directory  					2 bytes
				total number of entries in the central directory on this disk  					2 bytes
				total number of entries in the central directory           						2 bytes
				size of the central directory  					 								4 bytes
				offset of start of central directory with respect to the starting disk number   4 bytes
				.ZIP file comment length        												2 bytes
				.ZIP file comment       														(variable size)
			*/
						
			// We finalize	
			clearstatcache() ; 
			$end_central_dir_record  = "\x50\x4b\x05\x06";					// 4 bytes  (0x06054b50)
			$end_central_dir_record .= pack('v', $disk_number-1);				// 2 bytes number of this disk    
			$end_central_dir_record .= pack('v', $disk_number-1);				// 2 bytes number of the disk with the start of the central directory
			$end_central_dir_record .= pack('v', $nbentry+$nbfolder);					// 2 bytes total number of entries in the central directory on this disk 
			$end_central_dir_record .= pack('v', $nbentry+$nbfolder);					// 2 bytes total number of entries in the central directory  
			$end_central_dir_record .= pack('V', @filesize($splitfilename.".centraldirectory.tmp" ));  	// 4 bytes size of the central directory  
			$end_central_dir_record .= pack('V', @filesize($path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number))); // 4 bytes offset of start of central directory with respect to the starting disk number
			$end_central_dir_record .= pack('v', strlen($zipfile_comment)); 			// 2 bytes .ZIP file comment length    
			$end_central_dir_record .= $zipfile_comment; 						// variable size .ZIP file comment     
					
			// We complete the data segments file
			$r = @file_put_contents($splitfilename.".centraldirectory.tmp" , $end_central_dir_record, FILE_APPEND) ; 
			if ($r===FALSE) {
				SL_Debug::log(get_class(), "The end of the central header cannot been added to ".$splitfilename.".centraldirectory.tmp", 2) ; 
				if (!Utils::rm_rec($path."/zip_in_progress")) {
					SL_Debug::log(get_class(), "The file ".$path."/zip_in_progress can not be deleted", 2) ; 
					return array("step"=>"error", "error"=>sprintf(__('The file %s cannot be deleted. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path."/zip_in_progress</code>")) ; 
				}
				return array('finished'=>false, "error"=>sprintf(__('The file %s cannot be modified/created. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$splitfilename.".centraldirectory.tmp"."</code>")) ; 
			} else {
				SL_Debug::log(get_class(), "The end of the central header has been added to ".$splitfilename.".centraldirectory.tmp", 4) ; 
			}
			
			// We copy the content of the central directory into the last file
			// TODO boucler sur des petits morceaux pour eviter une saturation mémoire
			// TODO ne pas depasser la taille max (sans pour autant couper une entrée)
			
			$r = @file_put_contents($path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number) , @file_get_contents($splitfilename.".centraldirectory.tmp"), FILE_APPEND) ; 
			if ($r===FALSE) {
				SL_Debug::log(get_class(), "The content of the file ".$splitfilename.".centraldirectory.tmp cannot be copied into ".$path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number), 2) ; 
				if (!Utils::rm_rec($path."/zip_in_progress")) {
					SL_Debug::log(get_class(), "The file ".$path."/zip_in_progress can not be deleted", 2) ; 
					return array("step"=>"error", "error"=>sprintf(__('The file %s cannot be deleted. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path."/zip_in_progress</code>")) ; 
				}
				return array('finished'=>false, "error"=>sprintf(__('The file %s cannot be modified/created. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$splitfilename.".centraldirectory.tmp</code>")) ; 
			} else {
				SL_Debug::log(get_class(), "The content of the file ".$splitfilename.".centraldirectory.tmp has been copied into ".$path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number), 4) ; 
			}
			
			// rename the last file
			$r = @rename($path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number), $splitfilename) ; 
			if ($r===FALSE) {
				SL_Debug::log(get_class(), "The file ".$path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number)." cannot be renamed into ".$splitfilename, 2) ; 			
				if (!Utils::rm_rec($path."/zip_in_progress")) {
					SL_Debug::log(get_class(), "The file ".$path."/zip_in_progress can not be deleted", 2) ; 
					return array("step"=>"error", "error"=>sprintf(__('The file %s cannot be deleted. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path."/zip_in_progress</code>")) ; 
				}
				return array('finished'=>false, "error"=>sprintf(__('The file %s cannot be renamed. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number)."</code>")) ; 
			} else {
				SL_Debug::log(get_class(), "The file ".$path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number)." has been renamed into ".$splitfilename, 4) ; 
			}
			$pathToReturn[time()] = $splitfilename ;
			$newpathToReturn = array() ; 

			// Remove from the above list the last file (because it has just been renamed)
			foreach ($pathToReturn as $k=>$f) {
				if ($f!= $path . basename ($splitfilename,".zip") . ".z" . sprintf("%02d",$disk_number)) {
					$newpathToReturn[$k]=$f ; 
				}
			}
			$pathToReturn = $newpathToReturn ; 
			
			// delete the  temp file 
			if (!Utils::rm_rec($splitfilename.".centraldirectory.tmp" )) {
				SL_Debug::log(get_class(), "The file ".$splitfilename.".centraldirectory.tmp cannot be deleted", 2) ; 
				if (!Utils::rm_rec($path."/zip_in_progress")) {
					SL_Debug::log(get_class(), "The file ".$path."/zip_in_progress can not be deleted", 2) ; 
					return array("step"=>"error", "error"=>sprintf(__('The file %s cannot be deleted. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path."/zip_in_progress</code>")) ; 
				}
				return array('finished'=>false, "error"=>sprintf(__('The file %s cannot be renamed. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$splitfilename.".centraldirectory.tmp"."</code>")) ; 
			} else {
				SL_Debug::log(get_class(), "The file ".$splitfilename.".centraldirectory.tmp has been deleted", 4) ; 
			}
			
			if (!Utils::rm_rec($splitfilename.".tmp")) {
				SL_Debug::log(get_class(), "The file ".$splitfilename.".tmp cannot be deleted", 2) ; 
				if (!Utils::rm_rec($path."/zip_in_progress")) {
					SL_Debug::log(get_class(), "The file ".$path."/zip_in_progress can not be deleted", 2) ; 
					return array("step"=>"error", "error"=>sprintf(__('The file %s cannot be deleted. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$path."/zip_in_progress</code>")) ; 
				}
				return array("step"=>"error", "error"=>sprintf(__('The file %s cannot be deleted. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".$splitfilename.".tmp</code>")) ; 
			} else {
				SL_Debug::log(get_class(), "The file ".$splitfilename.".tmp has been deleted", 4) ; 
			}
			// we inform that the process is finished
			if (!Utils::rm_rec(dirname($splitfilename)."/zip_in_progress")) {
				SL_Debug::log(get_class(), "The file ".$path."/zip_in_progress can not be deleted", 2) ; 
				return array("step"=>"error", "error"=>sprintf(__('The file %s cannot be deleted. You should have a problem with file permissions or security restrictions.', 'SL_framework'),"<code>".dirname($splitfilename)."/zip_in_progress</code>")) ; 
			} else {
				SL_Debug::log(get_class(), "The file ".$path."/zip_in_progress has been deleted", 2) ; 
			}
			SL_Debug::log(get_class(), "The ZIP process has ended by compressing ".$nbentry." files and ignoring ".count($files_not_included_due_to_filesize)." files due to filesize limitations", 4) ; 
			return array('finished'=>true, 'nb_finished'=>$nbentry,'nb_to_finished'=>0, 'not_included'=>$files_not_included_due_to_filesize, 'nb_files'=>$nbentry , 'path'=>$pathToReturn) ; 
		}
		
		/** ====================================================================================================================================================
		* To sort by length 
		* 
		* @access: private
		*/

		function sort_by_length_dir( $a, $b ) {
			return strlen($a)-strlen($b) ;
		}
		
		/** ====================================================================================================================================================
		* To sort by length 
		* 
		* @access: private
		*/

		function sort_by_length_file( $a, $b ) {
			return strlen($a[0])-strlen($b[0]) ;
		}
		
	} 
}


?>