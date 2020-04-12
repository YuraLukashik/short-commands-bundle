<?php

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Console\Output\OutputInterface;

return function (Filesystem $filesystem, OutputInterface $output) {
    $currentFileExists = $filesystem->exists(__FILE__);
    $message = $currentFileExists
        ? 'current file really exists'
        : 'current file does not exist';
    $output->writeln("I've just checked and {$message}");
};
