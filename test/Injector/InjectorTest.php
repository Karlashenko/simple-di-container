<?php

use Injector\Blueprints\Injector as InjectorBlueprint;
use Injector\Injector;
use PHPUnit\Framework\TestCase;

class InjectorTest extends TestCase
{
    public function testInjectorThrowsExceptionWhenClassIsNotInstantiatable()
    {
        $this->expectException('\Injector\Exceptions\UninstantiatableClassInjectorException');

        $injector = new Injector();
        $injector->bind(InjectorBlueprint::class, InjectorBlueprint::class);
        $injector->make(InjectorBlueprint::class);
    }

    public function testInjectorThrowsExceptionWhenClassDoesNotExist()
    {
        $this->expectException('\Injector\Exceptions\ClassNotFoundInjectorException');

        $injector = new Injector();
        $injector->bind(InjectorBlueprint::class, 'RandomClassNameHere');
        $injector->make(InjectorBlueprint::class);
    }

    public function testInjectorWillTryInstantiateGivenClassIfConainerIsEmpty()
    {
        $injector = new Injector();
        $injector->make(Injector::class);

        $this->assertTrue($injector instanceof Injector);
    }

    public function testInjectorResolvingDependencies()
    {
        $injector = new Injector();
        $injector->bind(InjectorBlueprint::class, Injector::class);

        $dummy = $injector->make(Dummy::class);

        $this->assertTrue($dummy->getInjector() instanceof Injector);
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
