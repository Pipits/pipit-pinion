<?php

class PipitPinion_Helper {

	/**
	 * Check whether development assets should be added to the page
	 * 
	 * @param array $opts	Options array
	 * @return boolean
	 */
	public function is_dev_mode($opts) {
		if(isset($opts['dev']) && $opts['dev'] && PERCH_PRODUCTION_MODE !== PERCH_DEVELOPMENT) return false;

		return true;
	}
	






	/**
	 * Get paths of the files that should be added to the page
	 * 
	 * @param string $dir_name		Directory name
	 * @param array $ops			Options array
	 * @return boolean
	 */
	public function get_filepaths($dir_name = 'css', $ext = 'css', $opts) {
		$prefix = '/';

		if(isset($opts['files'])) {
			$files = $opts['files'];

			if(isset($opts['cache-bust'])) {
				$files = $this->auto_version($files, false, $opts['cache-bust']);
			}

		} else {
			$dir = $this->_get_dir($opts, $dir_name);
			$files = $this->_get_files($dir['path'], $ext);
			
			//PerchUtil::mark($dir_name);
			//PerchUtil::debug($files);
			$prefix = $dir['url'];
			
			if(isset($opts['pre'])) {
				$files = $this->reorder_files($files, $opts['pre']);
			}

			if(isset($opts['exclude'])) {
				$files = $this->exclude_files($files, $opts['exclude']);
			}

			if(isset($opts['cache-bust'])) {
				$files = $this->auto_version($files, $dir['path'], $opts['cache-bust']);
			}
		}


		//PerchUtil::debug($files);

		return ['files' => $files, 'prefix' => $prefix];
	}







	/**
	 * Get directory path and web URL
	 * 
	 * @param array $opts			Options array
	 * @param string $dir_name		Directory name
	 * 
	 * @return array
	 */
	private function _get_dir($opts, $dir_name) {
		$dir = [];
		
		if(isset($opts['dir'])) {
			$dir['path'] = PerchUtil::file_path(dirname(PERCH_PATH) . '/' . $opts['dir']);
			$dir['url'] = '/'.$opts['dir'];

			// check last charact. Not a slash? Add one.
			if(substr($dir['url'], -1) !== '/') $dir['url'] .= '/';

		} elseif(PERCH_PRODUCTION_MODE == "PERCH_DEVELOPMENT") {

			$dir['path'] = PerchUtil::file_path(PIPIT_PINION_ASSETS_DEV_PATH . '/' . $dir_name);
			$dir['url'] = '/' . PIPIT_PINION_ASSETS_DEV_DIR . '/' . $dir_name . '/';

		} else {

			$dir['path'] = PerchUtil::file_path(PIPIT_PINION_ASSETS_PATH . '/' . $dir_name);
			$dir['url'] = '/' . PIPIT_PINION_ASSETS_DIR . '/' . $dir_name . '/';

		}

		
		return $dir;
	}
	
	
	




	/**
	 * Get files from a directory
	 * 
	 * @param string $dir_path		The path to the directory from which to retrieve the files
	 * @param string $ext			The file extention required
	 * @param string $prefix_path	
	 * 
	 * @return array
	 */
	private function _get_files($dir_path, $ext, $prefix_path = '') {
		$files = array();

		if ($dir_handler = opendir($dir_path)) {
			while (($file = readdir($dir_handler)) !== false) {
				if(substr($file, 0, 1) != '.') {
					$file_extenstion = PerchUtil::file_extension($file);
					if($file_extenstion == $ext) {
						$files[] = $prefix_path . $file;
					} else {
						//PerchUtil::mark('sub files');
						//PerchUtil::debug($file);
						if(is_dir("$dir_path/$file")) {
							$sub_files = $this->_get_files("$dir_path/$file", $ext,   $prefix_path . $file . '/');
							$files = array_merge($files, $sub_files);
						} 
					}
				}

			}
			closedir($dir_handler);
		}

		
		return $files;
	}
	

	




	/**
	 * Reorder files by prioritising files in $pre array
	 * 
	 * TODO: review and comment
	 */
	public function reorder_files($files, $pre) {
		$files = array_values($files);
		
		for($i = count($pre); $i > 0; $i--) {
			if(array_search($pre[$i-1], $files, true)) {				
				unset($files[array_search($pre[$i-1], $files, true)]);
				array_unshift($files, $pre[$i-1]);
			}
		}
		
		return $files;
	}
	

	




	/**
	 * Exclude files
	 */
	public function exclude_files($files, $excludes) {
		$files = array_values($files);
		foreach($excludes as $exclude) {
			$result = array_search($exclude, $files, true);
			unset($files[$result]);
		}
		
		return array_values($files);
	}







	/**
	 * Add a timestamp to files for cache busting
	 * 
	 * @param array $files 					Array of files to be versioned
	 * @param boolean|string $dir_path		Directory path if not document root
	 * @param array $cache_bust_files		Array of files to be versioned from $files. The rest to be left as is.
	 * 
	 * @return array
	 */
	public function auto_version($files, $dir_path=false, $cache_bust_files) {
		$files = array_values($files);

		foreach($files as $key => $file) {
			$result = false;

			if(!is_array($cache_bust_files)) {
				$result = $this->_version_file($file, $dir_path);				
			} elseif(in_array($file, $cache_bust_files)) {
				$result = $this->_version_file($file, $dir_path);
			}

			if($result) $files[$key] = $result;
		}

		
		return $files;
	}




	/**
	 * Add a timestamp to a single file for cache busting
	 * 
	 * @param string $file			File name
	 * @param string $dir_path		Path to the directory that contains the file
	 * 
	 * @return string|boolean
	 */
	private function _version_file($file, $dir_path) {
		$mode = 'name';
		if(defined('PIPIT_PINION_CACHE_BUST_MODE')) {
			$mode = PIPIT_PINION_CACHE_BUST_MODE;
		}

		$path_info = pathinfo($file);

		if(!$dir_path) $dir_path = $_SERVER['DOCUMENT_ROOT'];
		$full_path = $dir_path . DIRECTORY_SEPARATOR . $file;
		
		if (file_exists($full_path))  {
			if($mode == 'query') {
				return $file . '?v=' . filemtime($full_path);
			} else {
				$filename = substr($file, 0, strrpos($file, '.'));
				return $filename . '.' . filemtime($full_path) . '.' . $path_info['extension'];
			}
		}

		return false;
	}







	/**
	 * 
	 */
	function match_attrs_filename($opts, $files) {
		if(isset($opts['attrs']))  {
			foreach($opts['attrs'] as $key => $file_with_attr) {
				foreach($files as $file) {
					$path_info = pathinfo($file);
					$filename = $path_info['filename'];

					$time = substr($filename, strrpos($filename, '.') + 1);
					if(is_numeric($time))
					{
						$original_filename = substr($filename, 0, strrpos($filename, '.'));

						$attr_path_info = pathinfo($key);
						$attr_filename = $attr_path_info['filename'];

						if($attr_filename === $original_filename)
						{
							$opts['attrs'][$file] = $opts['attrs'][$key];
						}
					}
				}
			}
		}

		return $opts;
	}

	
	


}