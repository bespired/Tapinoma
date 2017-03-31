<?php

namespace Bespired\Tapinoma;

use Bespired\Tapinoma\JSqueeze;


class JsImp
{

	public $src_file;
	public $src_path;
	public $jsrc_file;
	public $includes = [];
	public $imports  = [];
	public $fatJs    = [];

	function __construct($src_file) {

		$this->src_file = realpath($src_file);
		$this->src_path = dirname($this->src_file). '/';

	}

	public function rollup($export)
	{

		$this->imports($this->src_file);

		$jz = new JSqueeze();

		$minifiedJs = $jz->squeeze(
			join("\n", $this->fatJs)
		);

		$minifiedJs= str_replace(';class ', ";\nclass ", $minifiedJs);
		$minifiedJs= str_replace(';new ', ";\nnew ", $minifiedJs);
		$minifiedJs= trim($minifiedJs);

//		file_put_contents($export, $jz);
		file_put_contents($export, $minifiedJs);

	}


	private function imports($path)
	{
		if ( array_search($path, $this->imports) !== false ) return;
		$this->imports[] = $path;

		$fatJs= file_get_contents($path);
		$re = '/import([\s\S]+?)from\s[\'|"](?\'path\'[\S]+)[\'|"]/';
		preg_match_all($re, $fatJs, $matches);

		if (isset($matches))
		{
			foreach ($matches['path'] as $match) {
				$route = $this->src_path . $match . '.js';
				$this->imports($route);
			}
		}

		$re    = '/(import[\s\S]+?)class/';
		$fatJs = preg_replace($re, 'class', $fatJs);

		$re = '/export [\s\S]+$/';
		$fatJs = preg_replace($re, '', $fatJs);

		$this->fatJs[$this->name2key($path)] = $fatJs;

	}

	private function name2key($path)
	{
		return str_replace(['/','.'], ['_','-'], $path);
	}

}
