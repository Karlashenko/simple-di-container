<?php

namespace Injector\Blueprints;

interface Injector
{
    /**
     * @param string $class
     * @param mixed  $instance
     *
     * @return static
     */
    public function bind(string $class, string $instance) : self;

    /**
     * @param string $class
     * @param array  $parameters
     *
     * @return mixed
     */
    public function make(string $class, array $parameters = null);
}