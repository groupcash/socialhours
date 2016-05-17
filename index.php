<?php

use groupcash\php\algorithms\EccAlgorithm;
use groupcash\socialhours\app\Launcher;
use groupcash\socialhours\app\SendmailPostOffice;
use groupcash\socialhours\app\Session;
use watoki\karma\stores\StoringEventStore;
use watoki\stores\stores\FileStore;

require_once __DIR__ . '/vendor/autoload.php';

(new Launcher(
    new StoringEventStore(new FileStore(__DIR__ . '/user/data/events')),
    new EccAlgorithm(),
    new SendmailPostOffice('noreply@groupcash.org'),
    new Session()
))->run();