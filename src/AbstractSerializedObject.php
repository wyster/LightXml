<?php

namespace LightXml;

use ArrayObject;

/**
 * @author Ilya Zelenin <wyster@make.im>
 * @package LightXml
 */
abstract class AbstractSerializedObject extends ArrayObject
{
    /**
     * @var string
     */
    protected $nodeName = 'root';

    /**
     * @param array  $input
     * @param int    $flag
     * @param string $iterator_class
     */
    public function __construct($input = [], $flag = ArrayObject::ARRAY_AS_PROPS, $iterator_class = "ArrayIterator")
    {
        parent::__construct($input, $flag, $iterator_class);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->offsetGet($name);
    }

    /**
     * @param $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->offsetExists($name);
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $writer = new Writer(['rootNodeName' => $this->getNodeName()]);
        return $writer->toString($this);
    }

    /**
     * @param ConfigWriter|Closure|array $config
     * @return string
     */
    public function toString($config = [])
    {
        $writer = new Writer(array_merge(['rootNodeName' => $this->getNodeName()], $config));
        return $writer->toString($this);
    }

    /**
     * @param string $value
     */
    public function setNodeName($value)
    {
        $this->nodeName = $value;
    }

    /**
     * @return string
     */
    public function getNodeName()
    {
        return $this->nodeName;
    }
}