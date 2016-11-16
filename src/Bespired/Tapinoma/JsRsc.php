<?php

namespace Bespired\Tapinoma;

use Patchwork\JSqueeze;



class JsRsc
{

	public $root;
	public $source;
	public $srcpath;
	public $jsrc_file;
	public $config;

	function __construct($directory) {

		$uri_parts = explode('/',$_SERVER['REQUEST_URI']);
		$root_path = realpath($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . '..');
		$jsrc_file = array_pop($uri_parts);
		$config    = $this->config();

		$this->root      = $directory . DIRECTORY_SEPARATOR;
		$this->jsrc_file = $jsrc_file;
		$this->srcpath   = $root_path . DIRECTORY_SEPARATOR . $config->jsrc;
		$this->source    = $this->srcpath . DIRECTORY_SEPARATOR . $jsrc_file;
		$this->config    = $config;
	
	}

	public function serve()
	{

		if ( !file_exists($this->source) ) {
			echo "Cannot find ".$this->jsrc_file;
			return;
		}

		echo "/* JsRsc v0.2 */\n";

		$files= $this->getFiles();

		$changed= '';
		foreach ($files as $filename) {
			$changed .= (string)filemtime($filename)."\n";
		}
		$md5= md5($changed);


		$cache_root= $this->root . 'jsrc_cache' . DIRECTORY_SEPARATOR;
		$cache_file= $cache_root . $md5 . '.js';
		if (file_exists($cache_file)) {
			echo file_get_contents($cache_file);
			return;
		}

		$fatJs= '';
		foreach ($files as $filename) {
			$fatJs .= file_get_contents($filename)."\n";
		}
		
		$jz = new JSqueeze();

		$minifiedJs = $jz->squeeze(
    		$fatJs,
    			$this->config->singleLine,
    			$this->config->keepImportantComments,
    			$this->config->specialVarRx
			);

		echo $minifiedJs;
		if(!file_exists($cache_root)) mkdir($cache_root);
		file_put_contents($cache_file, $minifiedJs);

	}

	private function import($filename) {

		$filename = trim($filename);
		if (substr($filename,-3) != '.js') $filename .= '.js';
		return $this->srcpath . DIRECTORY_SEPARATOR. $filename;
	}

	private function getFiles() {
		$jsrc = file_get_contents($this->source);
		$lines= explode(";", $jsrc);
		$files=[];
		foreach ($lines as $line) {
			$line= trim($line);
			if (substr($line, 0, 7) == '@import') $files[]= $this->import(substr($line, 7));
		}
		return $files;
	}

	private function config()
	{
		$config      = [];
		$root_path   = realpath($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . '..');
		$app_config  = strtr(sprintf('%s:config:tapinoma.php', $root_path),':', DIRECTORY_SEPARATOR);

		$config_path = realpath(strtr(sprintf('%s:..:..:..:config:tapinoma.php', __DIR__), ':', DIRECTORY_SEPARATOR));
		
		if (file_exists($config_path)) $config= include $config_path;
		if (file_exists($app_config )) $config= array_merge( $config, include $app_config);

		return (object)$config;
	}

}