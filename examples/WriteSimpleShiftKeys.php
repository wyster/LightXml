<?php

require '../vendor/autoload.php';

/**
 * If you need to generate XML alternating nodes, use setting numArrayKeys with value \LightXml\ConfigWriter::SHIFT_KEYS_RIGHT
 * @author Ilya Zelenin <wyster@make.im>
 */
$array = [
        [
            'item' => [
                'id' => 1,
                'value' => 'bar'
            ]
        ],
        [
            'item2' => [
                'id' => 1,
                'value' => 'bar'
            ]
        ],
        [
            'item' => [
                'id' => 2,
                'value' => 'baz'
            ]
        ],
        [
            'item2' => [
                'id' => 2,
                'value' => 'baz'
            ]
        ],
];

$writer = new \LightXml\Writer(['numArrayKeys' => \LightXml\ConfigWriter::SHIFT_KEYS_RIGHT]);
echo $writer->toString($array);

