<?php

namespace Injector;

use Injector\Blueprints\Injector as InjectorBlueprint;
use Injector\Exceptions\ClassNotFoundInjectorException;
use Injector\Exceptions\UninstantiableClassInjectorException;
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
     * @throws \Injector\Exceptions\UninstantiableClassInjectorException
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
            throw new UninstantiableClassInjectorException("Class '$instanceClass' is not instantiable.");
        }

        if ($parameters === null) {
            $parameters = $this->resolveParameters($reflection->getConstructor());
        }

        return $reflection->newInstanceArgs($parameters);
    }

    /**
     * @param \ReflectionMethod $reflectionMethod
     *
     * @return array
     * @throws \Injector\Exceptions\UninstantiableClassInjectorException
     * @throws \Injector\Exceptions\ClassNotFoundInjectorException
     */
    private function resolveParameters(?\ReflectionMethod $reflectionMethod) : array
    {
        $dependencies = [];

        if ($reflectionMethod === null) {
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
