<?php

use Symfony\Component\Console\Output\OutputInterface;

return function (OutputInterface $output) {
    $output->writeln('greeting');
};
