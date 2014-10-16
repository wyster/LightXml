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
    protected $nodeName = null;

    /**
     * @param array $input
     * @param mixed $nodeName
     */
    public function __construct($input = [], $nodeName = null)
    {
        $this->setNodeName($nodeName);
        parent::__construct($input);
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