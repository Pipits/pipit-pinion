<?php
PerchSystem::register_feather('PipitPinion');
include('config.php');
include('lib/PipitPinion_Helper.class.php');

class PerchFeather_PipitPinion extends PerchFeather {
	
	public function get_css($opts, $index, $count) {
		$Helper = new PipitPinion_Helper();

		// development assets only
		if(!$Helper->is_dev_mode($opts)) return false;
		

		$out = array();
		$result = $Helper->get_filepaths('css', 'css', $opts);
		$files = $result['files'];
		$prefix = $result['prefix'];

		

		// render link stylsheet tags
		foreach($files as $file) {
			$out[] = $this->_single_tag('link', [
				'rel' => 'stylesheet',
				'href' => $prefix.$file,
				'type' => 'text/css'
			]);
		}
		
		
		//
		if(isset($opts['fonts'])) {
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

		// development assets only
		if(!$Helper->is_dev_mode($opts)) return false;
		


		$out = array();
		$result = $Helper->get_filepaths('js', 'js', $opts);
		$files = $result['files'];
		$prefix = $result['prefix'];
		$attrs_files = $result['attrs_files'];
		
		
		// create script tags
		foreach($files as $key => $file) {
			$attrs = array();

			if(isset($attrs_files[$key])) {
				$attrs = $attrs_files[$key];
			}

			$attrs['src'] = $prefix.$file;
			$out[] = $this->_script_tag($attrs);
		}

		
		
		return implode("\n\t", $out)."\n";
	}
	

}
?>