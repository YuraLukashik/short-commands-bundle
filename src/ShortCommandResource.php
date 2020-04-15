<?php

namespace ShortCommands;


use Symfony\Component\Config\Resource\SelfCheckingResourceInterface;

class ShortCommandResource implements SelfCheckingResourceInterface, \Serializable
{
    private $path;
    private $hash;

    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * @inheritDoc
     */
    public function serialize()
    {
        if (null === $this->hash) {
            $this->hash = $this->computeHash();
        }
        return serialize([$this->path, $this->hash]);
    }

    /**
     * @inheritDoc
     */
    public function unserialize($serialized)
    {
        [$this->path, $this->hash] = unserialize($serialized);
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        return $this->path;
    }

    /**
     * @inheritDoc
     */
    public function isFresh($timestamp)
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