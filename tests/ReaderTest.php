<?php

namespace LightXml\Tests;

use LightXml\Reader;
use LightXml\SerializedItem;
use LightXml\SerializedList;

/**
 * @author Ilya Zelenin <wyster@make.im>
 */
class ReaderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Reader
     */
    private $target;

    protected function setUp()
    {
        $this->target = new Reader();
    }

    public function testNumList()
    {
        $xmlString = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<root><item><id>1</id><value>bar</value></item><item><id>2</id><value>baz</value></item><item><id>3</id><value>bag</value></item><item2><value>bar</value><value>bar2</value><value>bar3</value></item2></root>
XML;
        $result = $this->target->fromString($xmlString);
        $this->assertFalse($result['item'] instanceof SerializedItem);
        $this->assertTrue($result['item'] instanceof SerializedList);
        $this->assertTrue($result->item instanceof SerializedList);
        $this->assertCount(3, $result->item);
    }

    public function testFromString()
    {
        $item = [
            'item' => [
                'siteAttribute' => 'localhost',
                'first' => [
                    0 => 'test&nbsp;test',
                    1 => '<b>&body;</b>',
                    'second' => [
                        0 => ['id' => 1],
                        1 => ['id' => 2]
                    ],
                    'third' => [
                        'n1' => 'test',
                        'n2' => 'test2' . "\r\n" . 'test3'
                    ],
                    'fourth' => [
                        0 => 'first<br />test',
                        1 => 'second'
                    ],
                    3 => [
                        'third first tag'
                    ]
                ]
            ]
        ];

        $resultXmlString = <<<XML
<item site="localhost"><first>test&amp;nbsp;test</first><first>&lt;b&gt;&amp;body;&lt;/b&gt;<second><id>1</id></second><second><id>2</id></second><third><n1>test</n1><n2>test2
test3</n2></third><fourth>first</fourth><fourth>second</fourth></first><first>third first tag</first></item>
XML;

        $result = $this->target->fromString($resultXmlString);
        $this->assertFalse(is_array($result->first));
        $this->assertFalse(is_array($result['first']));
        $this->assertTrue(is_array($result['first']->getArrayCopy()));
        $this->assertTrue(is_array($result->first->getArrayCopy()));
        $this->assertTrue($result['first'][1]['third'] instanceof SerializedItem);
        $this->assertTrue($result->first[1]->third instanceof SerializedItem);
        $this->assertTrue($result->first[1]->third->n1 === $item['item']['first']['third']['n1']);
    }
}