<?php namespace Tests;

use SKAgarwal\Reflection\ReflectableTrait;

class MultipleReflectionTest extends TestCase
{
    use ReflectableTrait;

    /**
     * @test
     */
    public function it_tests_on_function()
    {
        $this->on(new FooBar())->callSayHello();
    }

    /**
     * @test
     */
    public function it_tests_get_function()
    {
        $name = $this->on(new FooBar())->getName;
        $this->assertEquals('FooBar', $name);
    }
}
