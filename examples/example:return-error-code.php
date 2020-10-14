<?php

use Symfony\Component\Console\Output\OutputInterface;

return function (OutputInterface $output) {
    $output->writeln('this command just returns error code 1');
    return 1;
};
