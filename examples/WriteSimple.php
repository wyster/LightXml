<?php

require '../vendor/autoload.php';

/**
 * @author Ilya Zelenin <wyster@make.im>
 */
$array = [
    'name' => 'foo',
    'item' => [
        [
            'id' => 1,
            'value' => 'bar'
        ],
        [
            'id' => 2,
            'value' => 'baz'
        ]
    ]
];

$writer = new \LightXml\Writer();
echo $writer->toString($array);

