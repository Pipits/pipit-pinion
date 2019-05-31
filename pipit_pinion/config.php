<?php
    // add to htaccess: RewriteRule ^(.+)\.(\d+)\.(js|css)$ $1.$3 [L]
	define('PIPIT_PINION_ASSETS_DIR', 'assets');
	define('PIPIT_PINION_ASSETS_DEV_DIR', 'src');
	define('PIPIT_PINION_CACHE_BUST_MODE', 'name');
	
    define('PIPIT_PINION_ASSETS_PATH', dirname(PERCH_PATH) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR , PIPIT_PINION_ASSETS_DIR));
    define('PIPIT_PINION_ASSETS_DEV_PATH', dirname(PERCH_PATH) . DIRECTORY_SEPARATOR . str_replace('/', DIRECTORY_SEPARATOR , PIPIT_PINION_ASSETS_DEV_DIR));
   