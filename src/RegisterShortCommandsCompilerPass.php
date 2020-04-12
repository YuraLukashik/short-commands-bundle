<?php

namespace ShortCommands;


use ShortCommands\DependencyInjection\ShortCommandsExtension;
use Symfony\Component\Config\Resource\GlobResource;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RegisterShortCommandsCompilerPass implements CompilerPassInterface
{

    /**
     * @inheritDoc
     * @throws \Exception
     */
    public function process(ContainerBuilder $container)
    {
        /** @var ShortCommandsExtension $extension */
        $extension = $container->getExtension('short_commands');
        foreach ($extension->commandsDirectories() as $directory) {
            if (!file_exists($directory) || !is_dir($directory)) {
                throw new \Exception("\"{$directory}\" is not a directory with commands");
            }
            $this->registerCommandsInDirectory((string) $directory, $container);
        }
    }

    /**
     * @throws \Exception
     */
    private function registerCommandsInDirectory(
        string $path,
        ContainerBuilder $container
    )
    {
        $resource = new GlobResource($path, '/*', false);
        $container->addResource($resource);
        foreach (scandir($path) as $commandFileName) {
            $commandPath = $path . '/' . $commandFileName;
            if (!is_file($commandPath)) {
                continue;
            }
            $function = ShortCommandResource::loadCommand($commandPath);
            $container->addResource(new ShortCommandResource($commandPath));
            $definition = new Definition(
                ShortCommandExecutor::class,
                [$commandPath, $this->argumentsDefinitions($function, $container)]
            );
            $definition->addTag('console.command');
            $container->setDefinition($commandPath, $definition);
        }
    }

    private function argumentsDefinitions(callable $function, ContainerBuilder $container)
    {
        $functionReflection = new \ReflectionFunction($function);
        $definitions = [];
        foreach ($functionReflection->getParameters() as $parameter) {
            $parameterClassName = $parameter->getClass()->getName();
            if (in_array($parameterClassName, ShortCommandExecutor::CAN_RESOLVE_ARGUMENTS)) {
                $definitions[] = $parameterClassName; // should be passed by executor
                continue;
            }
            $definitions[] = $container->findDefinition($parameterClassName);
        }
        return $definitions;
    }
}