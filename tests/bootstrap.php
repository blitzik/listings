<?php declare(strict_types = 1);

//tester.bat d:\dev\www\subdom\listings\tests\Libs\Utils\Time\Time.phpt

require __DIR__ . '/../vendor/autoload.php';

Tester\Environment::setup();

date_default_timezone_set('Europe/Prague');

$loader = new \Nette\Loaders\RobotLoader();
$loader->setTempDirectory(__DIR__ . '/temp');

$loader->addDirectory(__DIR__ . '/../src');
$loader->addDirectory(__DIR__ . '/../libs');
$loader->register();