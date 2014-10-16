<?php

namespace LightXml;

/**
 * @author Ilya Zelenin <wyster@make.im>
 * @package LightXml
 */
class ConfigWriter extends AbstractConfig
{
    /**
     * Смещение номерных ключей вправо
     */
    const SHIFT_KEYS_RIGHT = 1;
    /**
     * Смещение номерных ключей влево
     */
    const SHIFT_KEYS_LEFT = 2;

    /**
     * @var string
     */
    public $rootNodeName = 'root';
    /**
     * @var bool
     */
    public $xmlDeclaration = TRUE;
    /**
     * @var string
     */
    public $documentCharset = 'utf-8';
    /**
     * @var bool
     */
    public $formatOutput = FALSE;
    /**
     * Не убирать лишние пробелы и отступы
     * @var bool
     */
    public $preserveWhiteSpace = TRUE;
    /**
     * По умолчанию ноды в которых присутсвуют символьные данные будут обернуты в cdata
     * @var bool
     */
    public $cdata = TRUE;
    /**
     * Offset numeric keys
     * @var int
     */
    public $numArrayKeys = self::SHIFT_KEYS_LEFT;
}