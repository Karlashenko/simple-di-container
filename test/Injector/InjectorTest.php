<?php

use Injector\Exceptions\ClassNotFoundInjectorException;
use Injector\Exceptions\UninstantiableClassInjectorException;
use Injector\Blueprints\Injector as InjectorBlueprint;
use Injector\Injector;
use PHPUnit\Framework\TestCase;

class InjectorTest extends TestCase
{
    public function testInjectorThrowsExceptionWhenClassIsNotInstantiable() : void
    {
        $this->expectException(UninstantiableClassInjectorException::class);

        $injector = new Injector();
        $injector->bind(InjectorBlueprint::class, InjectorBlueprint::class);
        $injector->make(InjectorBlueprint::class);
    }

    public function testInjectorThrowsExceptionWhenClassDoesNotExist() : void
    {
        $this->expectException(ClassNotFoundInjectorException::class);

        $injector = new Injector();
        $injector->bind(InjectorBlueprint::class, 'RandomClassNameHere');
        $injector->make(InjectorBlueprint::class);
    }

    public function testInjectorWillTryInstantiateGivenClassIfConainerIsEmpty() : void
    {
        $injector = new Injector();
        $injector->make(Injector::class);

        $this->assertInstanceOf(Injector::class, $injector);
    }

    public function testInjectorResolvingDependencies() : void
    {
        $injector = new Injector();
        $injector->bind(InjectorBlueprint::class, Injector::class);

        $dummy = $injector->make(Dummy::class);

        $this->assertInstanceOf(Injector::class, $dummy->getInjector());
    }
}

class Dummy
{
    /**
     * @var \Injector\Blueprints\Injector
     */
    private $injector;

    /**
     * @return \Injector\Blueprints\Injector
     */
    public function getInjector() : InjectorBlueprint
    {
        return $this->injector;
    }

    /**
     * Create a new Dummy instance.
     *
     * @param \Injector\Blueprints\Injector $injector
     */
    public function __construct(InjectorBlueprint $injector)
    {
        $this->injector = $injector;
    }
}
