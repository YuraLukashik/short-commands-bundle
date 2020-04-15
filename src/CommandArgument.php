<?php

namespace ShortCommands;


use ReflectionParameter;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;

class CommandArgument
{
    private string $name;
    private int $mode;
    private $defaultValue;
    private ?string $type;

    public static function fromParameter(ReflectionParameter $parameter)
    {
        $argument = new self();
        $argument->name = $parameter->getName();
        $argument->mode = $parameter->isDefaultValueAvailable() ? InputArgument::OPTIONAL : InputArgument::REQUIRED;
        $argument->defaultValue = $parameter->isDefaultValueAvailable() ? $parameter->getDefaultValue() : null;
        if ($parameter->hasType()) {
            $argument->type = $parameter->getType()->getName();
        }
        return $argument;
    }

    public function declareInCommand(Command $command)
    {
        $command->addArgument(
            $this->name,
            $this->mode,
            (string) $this->type,
            $this->defaultValue
        );
    }

    public function takeFrom(InputInterface $input)
    {
        $value = $input->getArgument($this->name());
        if ($value === null) {
            return $value;
        }
        if ("int" === $this->type) {
            return (int) $value;
        }
        if ("bool" === $this->type) {
            return (bool) $value;
        }
        if ("float" === $this->type) {
            return (float) $value;
        }
        if ("array" === $this->type) {
            return (array) $value;
        }
        return $value;
    }

    public function name()
    {
        return $this->name;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'mode' => $this->mode,
            'defaultValue' => $this->defaultValue,
            'type' => $this->type,
        ];
    }

    public static function fromArray(array $raw)
    {
        $argument = new self();
        $argument->name = $raw['name'];
        $argument->mode = $raw['mode'];
        $argument->defaultValue = $raw['defaultValue'];
        $argument->type = $raw['type'];
        return $argument;
    }

    private function __construct()
    {
    }
}