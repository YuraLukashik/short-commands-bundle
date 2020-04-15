<?php

use Symfony\Component\Console\Output\OutputInterface;

return function (
    string $name,
    OutputInterface $output,
    int $age = null
) {
    $output->writeln("Hey {$name}, how are you?");
    if ($age !== null) {
        $output->writeln("BTW, I know you are {$age} years old");
    }
};
