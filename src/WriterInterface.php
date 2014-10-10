<?php

namespace LightXml;

/**
 * @author Ilya Zelenin <wyster@make.im>
 * @package LightXml
 */
interface WriterInterface
{
    /**
     * Write to string
     *
     * @param  array|object|AbstractSerializedObject $data
     * @return string
     */
    public function toString($data);
} 