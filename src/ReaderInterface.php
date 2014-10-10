<?php

namespace LightXml;

/**
 * @author Ilya Zelenin <wyster@make.im>
 * @package LightXml
 */
interface ReaderInterface
{
    /**
     * Read from a string and create object
     *
     * @param  string $string
     * @return AbstractSerializedObject
     */
    public function fromString($string);
} 