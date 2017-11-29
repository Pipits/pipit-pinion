<?php

class PipitPinion_Helper
{
	
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
}