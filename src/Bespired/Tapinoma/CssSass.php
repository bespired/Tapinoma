<?php

namespace Bespired\Tapinoma;

use Leafo\ScssPhp\Compiler;
use Leafo\ScssPhp\Server;


class CssSass
{
	public $root;
	public $sass_file;

	function __construct($directory) {

		$uri_parts= explode('/',$_SERVER['REQUEST_URI']);
		$sass_file= array_pop($uri_parts);
		
		$this->root= $directory . DIRECTORY_SEPARATOR;
		$this->sass_file = $sass_file;
		$this->set_env();
	
	}

	public function serve()
	{
		$config= $this->config();

		$root_path   = realpath($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . '..');
		$directory   = $root_path . DIRECTORY_SEPARATOR . $config->sass;
		$storage     = $this->root . '/sass_cache/';

		$scss = new Compiler();
		$scss->setFormatter('Leafo\ScssPhp\Formatter\Compressed');
		$scss->setImportPaths($directory);


		if (isset($config->build->colors))
		{
			$mysqli = $this->opendb();
			$result = $mysqli->query('select name,color from colors');
	    	while ($row = $result->fetch_assoc()) {
				$colors[]= $row["name"].':'.$row["color"];
	    	}
			$mysqli->close();

			$scss->setVariables([
				'color-list' => sprintf('( %s )', join(',', $colors))
			]);
		}

		$server = new Server($directory, $storage, $scss);
		$server->serve();

	}

	private function config()
	{
		$config      = [];
		$root_path   = realpath($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . '..');
		$app_config  = strtr(sprintf('%s:config:tapinoma.php', $root_path),':', DIRECTORY_SEPARATOR);

		$config_path = realpath(strtr(sprintf('%s:..:..:..:config:tapinoma.php', __DIR__), ':', DIRECTORY_SEPARATOR));
		
		if (file_exists($config_path)) $config= include $config_path;
		if (file_exists($app_config )) $config= array_merge( $config, include $app_config);

		return json_decode(json_encode($config), FALSE);
	}

	private function opendb()
	{
		$host = getenv('DB_HOST');
		$port = getenv('DB_PORT');
		$dbas = getenv('DB_DATABASE');
		$user = getenv('DB_USERNAME');
		$pass = getenv('DB_PASSWORD');
		$mysqli = new \mysqli($host, $user, $pass, $dbas);
		//  todo: open errors
		return $mysqli;
	}

	private function set_env()
	{
		$host= getenv('DB_HOST');
		if(!$host){
			$root_path   = realpath($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . '..');
			$env= file_get_contents($root_path.DIRECTORY_SEPARATOR.'.env');

			foreach (explode(PHP_EOL, $env) as $single) {
				if (strlen(trim($single)))
				{
					list($var, $val) = [
						trim(explode("=", $single)[0]), 
						trim(explode("=", $single)[1])
					];
					if (!getenv($var)){
						putenv($single);	
					}
				} 
			}
		}
	}

}