<?php

require __DIR__ . '/shortcuts.php';
require __DIR__ . '/../vendor/autoload.php';

$configurator = new Nette\Configurator;

$configurator->setDebugMode('127.0.0.1');
$configurator->enableDebugger(__DIR__ . '/../log');
$configurator->setTempDirectory(__DIR__ . '/../temp');

$configurator->createRobotLoader()
	->addDirectory(__DIR__)
	->register();

$configurator->addConfig(__DIR__ . '/.config.neon');
$configurator->addConfig(__DIR__ . '/.config.critical.neon');

$container = $configurator->createContainer();

// LazyDataMapper
$sqliteStorage = $container->getService('sqliteStorage');
$lazyDataMapperAccessor = loadLazyDataMapper(new \LazyDataMapperCache($sqliteStorage));

$container->getService('application')->run();
