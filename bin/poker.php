<?php

declare(strict_types=1);

use Poker\Commands;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

require __DIR__ . '/../bootstrap.php';

$application = new Application(APPLICATION_NAME, APPLICATION_VERSION);

$application->addCommands([
    new Commands\Odds(),
]);
$application->run(new ArgvInput(), new ConsoleOutput());
