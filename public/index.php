<?php

declare(strict_types=1);
define('PULEENO_CMS_START', microtime(true));

use Puleeno\Bootstrap;

require __DIR__ . '/../app/bootstrap.php';
$bootstrap = new Bootstrap();
$bootstrap->boot();
