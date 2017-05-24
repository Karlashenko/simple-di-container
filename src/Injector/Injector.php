<?php

namespace Injector;

use Injector\Blueprints\Injector as InjectorBlueprint;
use Injector\Exceptions\ClassNotFoundInjectorException;
use Injector\Exceptions\UninstantiatableClassInjectorException;
use Injector\Traits\Singleton;

class Injector implements InjectorBlueprint
{
    use Singleton;

    /**
     * @var array
     */
    protected $container = [];

    /**
     * @inheritdoc
     */
    public function bind(string $class, string $instance) : InjectorBlueprint
    {
        $this->container[$class] = $instance;
        return $this;
    }

    /**
     * @inheritdoc
     *
     * @throws \Injector\Exceptions\ClassNotFoundInjectorException
     * @throws \Injector\Exceptions\UninstantiatableClassInjectorException
     */
    public function make(string $class, array $parameters = null)
    {
        $instanceClass = $this->container[$class];

        if ($instanceClass === null) {
            $instanceClass = $class;
        }

        if (!class_exists($instanceClass) && !interface_exists($instanceClass)) {
            throw new ClassNotFoundInjectorException("Class '$instanceClass' does not exist.");
        }

        $reflection = new \ReflectionClass($instanceClass);

        if (!$reflection->isInstantiable()) {
            throw new UninstantiatableClassInjectorException("Class '$instanceClass' is not instantiable.");
        }

        if ($parameters === null) {
            $parameters = $this->resolveParameters($reflection->getConstructor());
        }

        $instance = $reflection->newInstanceArgs($parameters);

        return $instance;
    }

    /**
     * @param \ReflectionMethod $reflectionMethod
     *
     * @return array
     */
    private function resolveParameters(?\ReflectionMethod $reflectionMethod)
    {
        $dependencies = [];

        if ($reflectionMethod == null) {
            return $dependencies;
        }

        foreach ($reflectionMethod->getParameters() as $parameter) {
            $paramName = $parameter->getName();
            $paramClassName = $parameter->getClass()->getName();

            $dependencies[$paramName] = $this->make($paramClassName);
        }

        return $dependencies;
    }

}