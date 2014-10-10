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
     * @param array|Closure $config
     */
    public function __construct($config = NULL)
    {
        if (is_array($config) && count($config) > 0) {
            foreach ($config as $param => $value) {
                if (property_exists($this, $param)) {
                    $this->{$param} = $value;
                }
            }
        }

        if ($config instanceof Closure) {
            $config($this);
        }
    }
} 