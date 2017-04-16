<?php

//include '.maintenance.php';

$container = require __DIR__ . '/../bootstrap.php';

$container->getByType(Nette\Application\Application::class)->run();
