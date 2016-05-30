<?php

use groupcash\php\algorithms\EccAlgorithm;
use groupcash\socialhours\app\Launcher;
use groupcash\socialhours\app\SendmailPostOffice;
use groupcash\socialhours\app\Session;
use rtens\domin\delivery\web\Url;
use watoki\karma\stores\StoringEventStore;
use watoki\stores\stores\FileStore;

require_once __DIR__ . '/vendor/autoload.php';

$context = (new \watoki\curir\WebEnvironment($_SERVER, [], []))->getContext();

(new Launcher(
    new StoringEventStore(new FileStore(__DIR__ . '/user/data/events')),
    new EccAlgorithm(),
    new SendmailPostOffice('noreply@groupcash.org'),
    new Session(),
    new Url($context->getScheme(), $context->getHost(), $context->getPort())
))->run();