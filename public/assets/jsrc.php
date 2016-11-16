<?php

    require __DIR__ . '/../../vendor/autoload.php';

	use Bespired\Tapinoma\JsRsc;

	$compiler = new JsRsc(__DIR__);
	$compiler->serve();
