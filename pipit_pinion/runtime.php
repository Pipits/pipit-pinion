<?php
PerchSystem::register_feather('PipitPinion');
include('config.php');
include('lib/PipitPinion_Helper.class.php');

class PerchFeather_PipitPinion extends PerchFeather
{
	
	public function get_css($opts, $index, $count)
	{
		$Helper = new PipitPinion_Helper();

		if(isset($opts['dev']) &&  $opts['dev'] && PERCH_PRODUCTION_MODE !== "PERCH_DEVELOPMENT")
		{
			return false;
		}
		
		$out = array();
		$prefix = '/';
		
		if(isset($opts['files']))
		{
			$files = $opts['files'];
			if(isset($opts['cache-bust']))
			{
				$files = $Helper->auto_version($files, false, $opts['cache-bust']);
			}
		}
		else
		{
			$dir = $Helper->get_dir($opts, 'css');
			$files = $Helper->get_files($dir['path']);
			$prefix = $dir['url'];
			
			if(isset($opts['pre']))
			{
				$files = $Helper->reorder_files($files, $opts['pre']);
			}
			if(isset($opts['exclude']))
			{
				$files = $Helper->exclude_files($files, $opts['exclude']);
			}
			if(isset($opts['cache-bust']))
			{
				$files = $Helper->auto_version($files, $dir['path'], $opts['cache-bust']);
			}
		}
		
		
		foreach($files as $file)
		{
			if(substr($file, strrpos($file, '.' )+1) == 'css')
			{
				$out[] = $this->_single_tag('link', [
					'rel'=>'stylesheet',
					'href'=>$prefix.$file,
					'type'=>'text/css'
				]);
			}
		}
		
		
		if(isset($opts['fonts']))
		{
			$out[] = $this->_single_tag('link', [
					'rel'=>'stylesheet',
					'href'=>$opts['fonts'],
					'type'=>'text/css'
			]);
		}
			
		return implode("\n\t", $out)."\n";
	}
	
	
	
	
	
	

	public function get_javascript($opts, $index, $count)
	{
		$Helper = new PipitPinion_Helper();

		if(isset($opts['dev']) &&  $opts['dev'] && PERCH_PRODUCTION_MODE !== "PERCH_DEVELOPMENT")
		{
			return false;
		}
		
		$out = array();
		$prefix = '/';
		
		if(isset($opts['files']))
		{
			$files = $opts['files'];
			if(isset($opts['cache-bust']))
			{
				$files = $Helper->auto_version($files, false, $opts['cache-bust']);
			}
		}
		else
		{
			$dir = $Helper->get_dir($opts, 'js');
			$files = $Helper->get_files($dir['path']);
			$prefix = $dir['url'];
			
			if(isset($opts['pre']))
			{
				$files = $Helper->reorder_files($files, $opts['pre']);
			}
			if(isset($opts['exclude']))
			{
				$files = $Helper->exclude_files($files, $opts['exclude']);
			}
			if(isset($opts['cache-bust']))
			{
				$files = $Helper->auto_version($files, $dir['path'], $opts['cache-bust']);
			}
		}
		
		
		
		foreach($files as $file)
		{
			if(substr($file, strrpos($file, '.' )+1) == 'js')
			{
				if(strrpos($file, '/'))
				{
					$component = substr($file, strrpos($file, '/') + 1);						
				}
				else
				{
					$component = $file;
				}
				
				
				if (!$this->component_registered($component)) 
				{
					$attrs = [];

					if(isset($opts['cache-bust']))
					{
						$opts = $Helper->match_attrs_filename($opts, $files);
					}

					if(isset($opts['attrs'][$file]))
					{
						$attrs = $opts['attrs'][$file];
					}
					$attrs['src'] = $prefix.$file;
					
					$out[] = $this->_script_tag($attrs);
					$this->register_component($component);
				}
			}
		}
		
		
		return implode("\n\t", $out)."\n";
	}
	

}
?>