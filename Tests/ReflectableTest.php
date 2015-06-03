<?php namespace Tests;

use SKAgarwal\Reflection\ReflectableTrait;

class ReflectableTest extends TestCase
{
    use ReflectableTrait;

    protected $foo;

    protected function setUp()
    {
        $this->foo = new Foo();
        $this->reflect($this->foo);
    }

    /**
     * @test
     */
    public function it_sets_the_properties_with_magic_methods()
    {
        $this->setName = 'Bar';
        $this->setNumber = 1234567890;
        $this->setAddress = 'Some Random Address';

        $this->assertAttributeEquals('Bar', 'name', $this->foo);
        $this->assertAttributeEquals('1234567890', 'number', $this->foo);
        $this->assertAttributeEquals(
            'Some Random Address',
            'address',
            $this->foo
        );
    }

    /**
     * @test
     */
    public function it_sets_the_properties_without_magic_methods()
    {
        $this->set('name', 'Bar');
        $this->set('number', 1234567890);
        $this->set('address', 'Some Random Address');

        $this->assertAttributeEquals('Bar', 'name', $this->foo);
        $this->assertAttributeEquals('1234567890', 'number', $this->foo);
        $this->assertAttributeEquals(
            'Some Random Address',
            'address',
            $this->foo
        );
    }

    /**
     * @test
     */
    public function it_get_the_value_of_properties_with_magic_methods()
    {
        $number = $this->getNumber;
        $name = $this->getName;
        $address = $this->getAddress;

        $this->assertEquals('12345', $number);
        $this->assertEquals('Foo', $name);
        $this->assertEquals('slug address', $address);
    }

    /**
     * @test
     */
    public function it_get_the_value_of_properties_without_magic_methods()
    {
        $number = $this->get('number');
        $name = $this->get('name');
        $address = $this->get('address');

        $this->assertEquals('12345', $number);
        $this->assertEquals('Foo', $name);
        $this->assertEquals('slug address', $address);
    }

    /**
     * @test
     */
    public function it_calls_the_methods_with_magic_methods()
    {
        $hello = $this->callSayHello();
        $world = $this->callSayWorld();
        $this->callSetAddress('21 eat street, HYD');

        $this->assertEquals('Hello', $hello);
        $this->assertEquals('World', $world);
        $this->assertAttributeEquals(
            '21 eat street, HYD',
            'address',
            $this->foo
        );
    }

    /**
     * @test
     */
    public function it_calls_the_methods_without_magic_methods()
    {
        $hello = $this->call('sayHello');
        $world = $this->call('sayWorld');
        $this->call('setAddress', ['21 eat street, HYD']);

        $this->assertEquals('Hello', $hello);
        $this->assertEquals('World', $world);
        $this->assertAttributeEquals(
            '21 eat street, HYD',
            'address',
            $this->foo
        );
    }

    /**
     * @test
     */
    public function it_call_method_from_different_class_with_magic_methods()
    {
        $hello = $this->callSayHello();
        $this->assertEquals('Hello', $hello);

        $hello = $this->on(new FooBar())->callSayHello();
        $this->assertEquals('Hello FooBar', $hello);

        $hello = $this->callSayHello();
        $this->assertEquals('Hello', $hello);
    }

    /**
     * @test
     */
    public function it_call_method_from_different_class_without_magic_methods()
    {
        $hello = $this->call('sayHello');
        $this->assertEquals('Hello', $hello);

        $hello = $this->on(new FooBar())->call('sayHello');
        $this->assertEquals('Hello FooBar', $hello);

        $hello = $this->call('sayHello');
        $this->assertEquals('Hello', $hello);
    }

    /**
     * @test
     */
    public function it_sets_property_of_different_class_with_magic_methods()
    {
        $fooBar = new FooBar();
        $this->on($fooBar)->setName = 'BarFoo';

        $this->assertAttributeEquals('Foo', 'name', $this->foo);
        $this->assertAttributeEquals('BarFoo', 'name', $fooBar);
    }

    /**
     * @test
     */
    public function it_sets_property_of_different_class_without_magic_methods()
    {
        $fooBar = new FooBar();
        $this->on($fooBar)->set('name', 'BarFoo');

        $this->assertAttributeEquals('Foo', 'name', $this->foo);
        $this->assertAttributeEquals('BarFoo', 'name', $fooBar);
    }

    /**
     * @test
     */
    public function it_get_the_property_of_diff_class_with_magic_methods()
    {
        $fooBar = $this->on(new FooBar())->getName;
        $foo = $this->getName;

        $this->assertEquals('FooBar', $fooBar);
        $this->assertEquals('Foo', $foo);
    }

    /**
     * @test
     */
    public function it_get_the_property_of_diff_class_without_magic_methods()
    {
        $fooBar = $this->on(new FooBar())->get('name');
        $foo = $this->get('name');

        $this->assertEquals('FooBar', $fooBar);
        $this->assertEquals('Foo', $foo);
    }

    /**
     * @test
     * @expectedException        SKAgarwal\Reflection\Exceptions\NotFoundException
     * @expectedExceptionMessage  No class object reflected
     */
    public function it_throws_not_found_exception_for_object()
    {
        $this->reflection = null;
        $this->classObj = null;

        $this->callSayHello();
    }

    /**
     * @test
     * @expectedException        SKAgarwal\Reflection\Exceptions\NotFoundException
     * @expectedExceptionMessage  Method 'FooBar' not found.
     */
    public function it_throws_object_not_found_exception_for_method()
    {
        $this->FooBar();
    }

    /**
     * @test
     * @expectedException        SKAgarwal\Reflection\Exceptions\NotFoundException
     * @expectedExceptionMessage  Property 'FooBar' not found.
     */
    public function it_throws_object_not_found_exception_for_property()
    {
        $this->FooBar;
    }
}
