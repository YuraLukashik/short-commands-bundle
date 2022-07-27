<?php

namespace ShortCommands;


use Symfony\Component\Config\Resource\SelfCheckingResourceInterface;

class ShortCommandResource implements SelfCheckingResourceInterface
{
    private $path;
    private $hash;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    public function __serialize(): array
    {
        if (null === $this->hash) {
            $this->hash = $this->computeHash();
        }
        return [$this->path, $this->hash];
    }

    public function __unserialize(array $serialized)
    {
        [$this->path, $this->hash] = $serialized;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function isFresh($timestamp): bool
    {
        if (null === $this->hash) {
            $this->hash = $this->computeHash();
        }
        return $this->hash === $this->computeHash();
    }

    private function computeHash()
    {
        $function = self::loadCommand($this->path);
        $reflection = new \ReflectionFunction($function);
        $hash = hash_init('md5');

        foreach ($reflection->getParameters() as $parameter) {
            hash_update($hash, (string) $parameter);
        }

        return hash_final($hash);
    }

    /**
     * @throws \Exception
     */
    public static function loadCommand(string $path): callable
    {
        try {
            $function = include $path;
        } catch (\Throwable $e) {
            throw new \Exception("command {$path} is broken", 0, $e);
        }
        if (!is_callable($function)) {
            throw new \Exception("command {$path} should return a callable");
        }
        return $function;
    }
}