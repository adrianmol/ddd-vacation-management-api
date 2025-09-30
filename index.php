<?php
declare(strict_types=1);

define('PROJECT_ROOT', __DIR__);

require PROJECT_ROOT . '/vendor/autoload.php';

use VM\Infrastructure\Core\AppKernel;

$kernel = new AppKernel();
$kernel->run();