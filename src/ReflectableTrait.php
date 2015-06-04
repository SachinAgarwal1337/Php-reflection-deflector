<?php namespace SKAgarwal\Reflection;

use ReflectionClass;
use SKAgarwal\Reflection\Exceptions\NotFoundException;

/**
 * For easy testing of provate\protected methods
 * For easy testing of provate\protected properties
 *
 * trait ReflectableTrait
 *
 * @package Tests\Traits
 */
trait ReflectableTrait
{
    /**
     * @var ReflectionClass
     */
    protected $reflection;

    /**
     * Object of the class to be reflected.
     *
     * @var
     */
    protected $classObj;

    /**
     * Single use reflcetion.
     *
     * @var ReflectionClass
     */
    protected $reflectionOn;

    /**
     * Single use class object.
     *
     * @var
     */
    protected $classObjOn;

    /**
     * For checking if called after the on() method.
     *
     * @var bool
     */
    private $isCalledAfterOn = false;

    /**
     * Check if the dynamic method is called on given type.
     *
     * @param $type
     * @param $name
     *
     * @return int
     */
    private function is($type, $name)
    {
        return preg_match("/^{$type}/", $name);
    }


    /**
     * Extract the dynamic name from given type
     *
     * @param $from
     * @param $name
     *
     * @return string
     */
    private function extract($type, $name)
    {
        return lcfirst(preg_replace("/{$type}/", '', $name, 1));
    }

    /**
     * $classObj and $reflection properties should be defined.
     *
     * @throws NotFoundException
     */
    private function checkClassObjAndReflectionProperties()
    {
        if (!$this->classObj || !$this->reflection) {
            throw new NotFoundException("No class object reflected");
        }
    }

    /**
     * check if property or method
     * is private or protected.
     *
     * @param $object ReflectionMethod / ReflectionProperty
     *
     * @return bool
     */
    private function setAccessibleOn($object)
    {
        if ($object->isPrivate() || $object->isProtected()) {
            $object->setAccessible(true);
        }
    }

    /**
     * get the Reflection and ClassObject
     *
     * @return array
     */
    private function getReflectionAndClassObject()
    {
        if ($this->isCalledAfterOn) {

            $this->isCalledAfterOn = false;
            $classObj = $this->classObjOn;
            $reflection = $this->reflectionOn;

            unset($this->classObjOn);
            unset($this->reflectionOn);

            return [$reflection, $classObj];
        }

        return [$this->reflection, $this->classObj];
    }

    /**
     * Getting the reflection.
     *
     * @param $classObj Object of the class the reflection to be created.
     */
    public function reflect($classObj)
    {
        $this->classObj = $classObj;
        $this->reflection = new ReflectionClass($this->classObj);
    }

    /**
     * Getting the reflection.
     *
     * @param $classObj Object of the class the reflection to be created.
     *
     * @return $this
     */
    public function on($classObj)
    {
        $this->classObjOn = $classObj;
        $this->reflectionOn = new ReflectionClass($classObj);
        $this->isCalledAfterOn = true;

        return $this;
    }

    /**
     * Call to public/private/protected methods.
     *
     * @param       $method    Method name to be called (case sensitive)
     * @param array $arguments Arguments to be passed to the method
     *
     * @return $this
     * @throws NotFoundException
     */
    public function call($method, $arguments = [])
    {
        $this->checkClassObjAndReflectionProperties();

        list($reflection, $classObj) = $this->getReflectionAndClassObject();
        $method = $reflection->getMethod($method);
        $this->setAccessibleOn($method);

        return $method->invokeArgs($classObj, $arguments);
    }

    /**
     * Get value of public/private/protected properties.
     *
     * @param $name Property name to be accessed (Case sensitive).
     *
     * @return mixed
     * @throws NotFoundException
     */
    public function get($name)
    {
        $this->checkClassObjAndReflectionProperties();

        list($reflection, $classObj) = $this->getReflectionAndClassObject();

        $property = $reflection->getProperty($name);
        $this->setAccessibleOn($property);

        return $property->getValue($classObj);
    }

    /**
     * Set value of public/private/protected properties.
     *
     * @param $name
     * @param $value
     * @throws NotFoundException
     */
    public function set($name, $value)
    {
        $this->checkClassObjAndReflectionProperties();

        list($reflection, $classObj) = $this->getReflectionAndClassObject();

        $property = $reflection->getProperty($name);
        $this->setAccessibleOn($property);

        $property->setValue($classObj, $value);
    }

    /**
     * @param       $method
     * @param array $arguments
     *
     * @return ReflectableTrait
     * @throws NotFoundException
     */
    public function __call($method, $arguments = [])
    {
        if ($this->is('call', $method)) {
            $methodName = $this->extract('call', $method);

            return $this->call($methodName, $arguments);
        }

        throw new NotFoundException("Method '{$method}' not found.");
    }

    /**
     * @param $name
     *
     * @return mixed
     * @throws NotFoundException
     */
    public function __get($name)
    {
        if ($this->is('get', $name)) {
            $name = $this->extract('get', $name);

            return $this->get($name);
        }

        throw new NotFoundException("Property '{$name}' not found.");
    }

    /**
     * @param $name
     * @param $value
     *
     * @throws NotFoundException
     */
    public function __set($name, $value)
    {
        if ($this->is('set', $name)) {
            $name = $this->extract('set', $name);

            $this->set($name, $value);
        }
        else {
            throw new NotFoundException("Property '{$name}' not found.");
        }
    }
}
