<?php

    require __DIR__ . '/../../vendor/autoload.php';

	use Bespired\Tapinoma\CssSass;
	
	$compiler = new CssSass(__DIR__);
	$compiler->serve();