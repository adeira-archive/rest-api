<?php

require dirname(__DIR__) . '/vendor/autoload.php';

Testbench\Bootstrap::setup(__DIR__ . '/_temp', function (\Nette\Configurator $configurator) {
	$configurator->addConfig(__DIR__ . '/tests.neon');
});
