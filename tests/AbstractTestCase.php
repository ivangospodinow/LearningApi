<?php

namespace Test;

use PHPUnit\Framework\TestCase;

abstract class AbstractTestCase extends TestCase
{

    protected function setUp(): void
    {
        parent::setUp();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }

    public function invokeMethod($object, $methodName, array $parameters = array())
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($object, $parameters);
    }

    public function getPrivateProp($object, $prop)
    {
        $reflection = new \ReflectionClass($object);
        $objectProp = $reflection->getProperty($prop);
        $objectProp->setAccessible(true);
        return $objectProp->getValue($object);
    }

}
