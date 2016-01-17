<?php

namespace LightXml;

use Closure;

/**
 * @author Ilya Zelenin <wyster@make.im>
 * @package LightXml
 */
abstract class AbstractConfig
{
    /**
     * @var Config\AbstractConfig
     */
    protected $config;

    /**
     * @return Config\AbstractConfig
     */
    abstract public function getConfig();

    /**
     * @param array|object|Closure|Config\AbstractConfig $config
     * @return $this
     */
    public function setConfig($config)
    {
        return $this->getConfig()->exchangeArray($config);
    }
}