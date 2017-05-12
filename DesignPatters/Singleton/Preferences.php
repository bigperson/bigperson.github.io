<?php
/**
 * This file is part of lesson Design Pattern Singleton
 *
 * Matt Zandstra PHP: Objects, Patterns, and Practice
 */

declare(strict_types=1);


namespace Bigperson\DesignPatterns;


class Preferences
{
    /**
     * @var array
     */
    private $properties = [];

    /**
     * In this variable, we store a single instance of the object
     *
     * @var $this
     */
    private static $instance;

    /**
     * Preferences constructor.
     *
     * Close private method
     */
    private function __construct()
    {
        //Closed
    }

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * @return array
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param array $properties
     */
    public function setProperties(array $properties)
    {
        $this->properties = $properties;
    }


}