<?php namespace Tests;

class Foo
{
    public $name = 'Foo';

    protected $number = 12345;

    private $address = 'slug address';

    /**
     * @param mixed $name
     */
    public function sayHello()
    {
        return 'Hello';
    }

    /**
     * @return mixed
     */
    protected function sayWorld()
    {
        return 'World';
    }

    /**
     * @param mixed $address
     */
    private function setAddress($address)
    {
        $this->address = $address;
    }


}
