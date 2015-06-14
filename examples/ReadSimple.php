<?php

require '../autoload.php';

/**
 * @author Ilya Zelenin <wyster@make.im>
 */
$xmlString = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<root><name>foo</name><item><id>1</id><value>bar</value></item><item><id>2</id><value>baz</value></item></root>
XML;

$writer = new \LightXml\Reader();
print_r($writer->fromString($xmlString));