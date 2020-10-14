<?php

namespace ShortCommands;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShortCommandExecutor extends Command
{
    private string $path;
    private array $pathInfo;
    private $arguments;

    const CAN_RESOLVE_ARGUMENTS = [
        InputInterface::class,
        OutputInterface::class,
    ];

    public function __construct(string $path, $arguments)
    {
        $this->path = $path;
        $this->pathInfo = pathinfo($path);
        $this->arguments = \array_map(function ($argument) {
            if (\is_array($argument)) {
                return CommandArgument::fromArray($argument);
            }
            return $argument;
        }, $arguments);
        parent::__construct($this->pathInfo['filename']);
    }

    public function configure()
    {
        foreach ($this->arguments as $argument) {
            if ($argument instanceof CommandArgument) {
                $argument->declareInCommand($this);
            }
        }
    }

    /**
     * @throws \Exception
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $command = ShortCommandResource::loadCommand($this->path);
        $arguments = array_map(function ($argument) use ($input, $output) {
            if ($argument instanceof CommandArgument) {
                return $argument->takeFrom($input);
            }
            if ($argument === OutputInterface::class) {
                return $output;
            }
            if ($argument === InputInterface::class) {
                return $input;
            }
            return $argument;
        }, $this->arguments);
        $executionResult = call_user_func($command, ...$arguments);
        return \is_int($executionResult) ? $executionResult : 0;
    }
}