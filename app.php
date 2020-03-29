<?php

require __DIR__.'/vendor/autoload.php';

use App\Command\RenameByDateCommand;
use Symfony\Component\Console\Application;

$application = new Application();

$application->addCommands([
    new RenameByDateCommand(),
]);

$application->run();
