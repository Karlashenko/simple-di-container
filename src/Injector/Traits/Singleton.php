<?php

namespace Injector\Traits;

trait Singleton
{
    private static $instance;

    /**
     * Get singleton instance.
     *
     * @return static
     */
    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new static;
        }

        return self::$instance;
    }

    /**
     * @param static $newInstance
     *
     * @return void
     */
    public static function setInstance($newInstance)
    {
        if (!($newInstance instanceof static)) {
            throw new \ErrorException("New singleton instance should be of type '{static::class}'.");
        }

        self::$instance = $newInstance;
    }
}