<?php

namespace LightXml\Config;

use Closure;

/**
 * @author Ilya Zelenin <wyster@make.im>
 * @package LightXml\Config
 */
abstract class AbstractConfig
{
    /**
     * @param array|Closure $config
     */
    public function __construct($config = NULL)
    {
        $this->exchangeArray($config);
    }

    /**
     * @param array|object|Closure|AbstractConfig $config
     * @return $this
     */
    public function exchangeArray($config)
    {
        if (is_array($config) || is_object($config) || $config instanceof \LightXml\AbstractConfig) {
            foreach ($config as $param => $value) {
                if (property_exists($this, $param)) {
                    $this->{$param} = $value;
                }
            }
        }

        if ($config instanceof Closure) {
            $config($this);
        }

        return $this;
    }
} 