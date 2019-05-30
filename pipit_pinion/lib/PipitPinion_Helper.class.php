<?php

class PipitPinion_Helper
{

	public function is_dev_mode($opts) {
		if(isset($opts['dev']) && $opts['dev'] && PERCH_PRODUCTION_MODE !== PERCH_DEVELOPMENT) return false;

		return true;
	}
	
	public function get_dir($opts, $dir_name)
	{
		$dir = [];
		
		if(isset($opts['dir']))
		{
			$dir['path'] = dirname(PERCH_PATH) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR , $opts['dir']);
			
			$dir['url'] = '/'.$opts['dir'];
			if(substr($opts['dir'], -1) !== '/')
			{
				$dir['url'] = '/'.$opts['dir'].'/';
			}
		}
		else if(PERCH_PRODUCTION_MODE == "PERCH_DEVELOPMENT")
		{
			$dir['path'] = PIPIT_PINION_ASSETS_DEV_PATH . DIRECTORY_SEPARATOR . $dir_name;
			$dir['url'] = '/' . PIPIT_PINION_ASSETS_DEV_DIR . '/' . $dir_name . '/';
		}
		else
		{
			$dir['path'] = PIPIT_PINION_ASSETS_PATH . DIRECTORY_SEPARATOR . $dir_name;
			$dir['url'] = '/' . PIPIT_PINION_ASSETS_DIR . '/' . $dir_name . '/';
		}
		
		return $dir;
	}
	
	
	
	public function get_files($dir)
	{
		$files = scandir($dir);
		unset($files[array_search('.', $files, true)]);
		unset($files[array_search('..', $files, true)]);
		
		
		foreach($files as $file)
		{
			if(is_dir($dir . DIRECTORY_SEPARATOR . $file))
			{
				$sub_files = $this->get_files($dir . DIRECTORY_SEPARATOR . $file);
				
				foreach($sub_files as $sub_file)
				{
					$files[] = $file . '/' . $sub_file;
				}
				
				unset($files[array_search($file, $files, true)]);
			}
		}
		
		return $files;
	}
	

	
	public function reorder_files($files, $pre)
	{
		$files = array_values($files);
		for($i = count($pre); $i > 0; $i--)
		{
			if(array_search($pre[$i-1], $files, true))
			{				
				unset($files[array_search($pre[$i-1], $files, true)]);
				array_unshift($files, $pre[$i-1]);
			}
		}
		
		return $files;
	}
	

	
	public function exclude_files($files, $excludes)
	{
		$files = array_values($files);
		foreach($excludes as $exclude)
		{
			$result = array_search($exclude, $files, true);
			unset($files[$result]);
		}
		
		return array_values($files);
	}



	public function auto_version($files, $dir_path=false, $cache_bust)
	{
		$files = array_values($files);

		if(is_array($cache_bust))
		{
			foreach($cache_bust as $file)
			{
				if($dir_path)
				{
					$full_path = $dir_path . DIRECTORY_SEPARATOR . $file;
				}
				else
				{
					//work it out from url '/some/path'
					$full_path = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $file;
				}
				
				$path_info = pathinfo($file);

				if (file_exists($full_path)) 
				{
					//$filename = rtrim($file, '.'.$path_info['extension']);
					$filename = substr($file, 0, strrpos($file, '.'));
					$result = array_search($file, $files, true);
					$files[$result] = $filename . '.' . filemtime($full_path) . '.' . $path_info['extension'];
				}
			}
		}
		else
		{
			foreach($files as $key => $file)
			{
				if($dir_path)
				{
					$full_path = $dir_path . DIRECTORY_SEPARATOR . $file;
				}
				else
				{
					//work it out from url '/some/path'
					$full_path = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $file;
				}

				$path_info = pathinfo($full_path);

				if (file_exists($full_path)) 
				{
					//$filename = rtrim($file, '.'.$path_info['extension']);
					$filename = substr($file, 0, strrpos($file, '.'));
					$files[$key] = $filename . '.' . filemtime($full_path) . '.' . $path_info['extension'];
				}
			}
		}

		
		return $files;
	}




	function match_attrs_filename($opts, $files)
	{
		if(isset($opts['attrs'])) 
		{
			foreach($opts['attrs'] as $key => $file_with_attr)
			{
				foreach($files as $file)
				{
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

	


	function str_lreplace($search, $replace, $subject)
	{
		$pos = strrpos($subject, $search);
	
		if($pos !== false)
		{
			$subject = substr_replace($subject, $replace, $pos, strlen($search));
		}
	
		return $subject;
	}
}