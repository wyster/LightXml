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
     */
    public function setConfig($config)
    {
        if ($config === null) {
            return;
        }
        if (is_array($config) || is_object($config)) {
            $this->getConfig()->setConfig($config);
        }
        if ($config instanceof Config\AbstractConfig) {
            $this->config = $config;
        }
        if ($config instanceof Closure) {
            $config($this->getConfig());
        }
    }
}