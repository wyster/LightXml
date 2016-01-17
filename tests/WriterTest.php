<?php

namespace LightXml\Tests;

use LightXml\Config\Writer as ConfigWriter;
use LightXml\SerializedItem;
use LightXml\SerializedList;
use LightXml\Writer;

/**
 * @author Ilya Zelenin <wyster@make.im>
 */
class WriterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Writer
     */
    private $target;

    protected function setUp()
    {
        $this->target = new Writer();
    }

    public function testMagic()
    {
        $object = new SerializedItem([
            'id' => 1,
            'value' => 'bar'
        ], 'root');

        $xmlString = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<root><id>1</id><value>bar</value></root>
XML;
        $this->assertXmlStringEqualsXmlString($xmlString, $object);

        $object = new SerializedList([
            new SerializedItem([
                'id' => 1,
                'value' => 'bar'
            ]),
            new SerializedItem([
                'id' => 2,
                'value' => 'baz'
            ]),
            new SerializedItem([
                'id' => 3,
                'value' => 'bag'
            ])
        ], 'item');
        $object->setNodeName('item');

        $xmlString = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<item><id>1</id><value>bar</value><id>2</id><value>baz</value><id>3</id><value>bag</value></item>
XML;
        $this->assertXmlStringEqualsXmlString($xmlString, $object);
    }

    public function testToString()
    {
        $xmlArray = ['test' => 'foo', 'bar' => [0 => 'baz', 1 => 'foo']];
        $resultXmlString = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<root>
  <test>foo</test>
  <bar>baz</bar>
  <bar>foo</bar>
</root>
XML;

        $this->assertXmlStringEqualsXmlString($resultXmlString, $this->target->toString($xmlArray));

        $xmlArray = [
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
                        'n2' => 'test2' . "\n" . 'test3'
                    ],
                    'fourth' => [
                        0 => 'first',
                        1 => 'second'
                    ],
                    3 => [
                        'third first tag'
                    ]
                ]
            ]
        ];

        $resultXmlString = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<root><item site="localhost"><first><![CDATA[test&nbsp;test]]></first><first><![CDATA[<b>&body;</b>]]><second><id>1</id></second><second><id>2</id></second><third><n1>test</n1><n2>test2
test3</n2></third><fourth>first</fourth><fourth>second</fourth></first><first>third first tag</first></item></root>
XML;

        $this->assertXmlStringEqualsXmlString($resultXmlString, $this->target->toString($xmlArray));

        $this->target->setConfig(['cdata' => false]);
        $xmlString = $this->target->toString($xmlArray);
        $resultXmlString = <<<XML
<?xml version="1.0" encoding="utf-8"?>
<root><item site="localhost"><first>test&amp;nbsp;test</first><first>&lt;b&gt;&amp;body;&lt;/b&gt;<second><id>1</id></second><second><id>2</id></second><third><n1>test</n1><n2>test2
test3</n2></third><fourth>first</fourth><fourth>second</fourth></first><first>third first tag</first></item></root>
XML;
        $this->assertXmlStringEqualsXmlString($resultXmlString, $xmlString);
    }

    /**
     * Тестирование режима когда цифровые ключи игнорируются, и рекурсивно идет поиск до первого строкового значения
     */
    public function testIgnoreNumericKeys()
    {
        $xml = <<<XML
<root><nodeFirst><node><item>result</item></node><node2><item>result</item></node2><node3><item>result</item></node3></nodeFirst><nodeParent><item>result</item><nodeChild><item>result</item></nodeChild></nodeParent><nodeParent><nodeChild><item>result</item></nodeChild></nodeParent><node2><item>result</item></node2><node3><item>result</item></node3><node><item>result</item></node><node><item>result</item></node></root>
XML;

        $serialize = new Writer([
            'formatOutput' => false,
            'numArrayKeys' => ConfigWriter::SHIFT_KEYS_RIGHT,
            'xmlDeclaration' => false
        ]);
        $data = [
            'nodeFirst' => [
                [
                    'node' => [
                        'item' => 'result'
                    ]
                ],
                [
                    'node2' => [
                        'item' => 'result'
                    ]
                ],
                [
                    'node3' => [
                        'item' => 'result'
                    ]
                ]
            ],
            [
                [
                    [
                        [
                            'nodeParent' => [
                                'item' => 'result',
                                [
                                    'nodeChild' => [
                                        'item' => 'result'
                                    ]
                                ]
                            ]
                        ],
                        [
                            'nodeParent' => [
                                [
                                    'nodeChild' => [
                                        'item' => 'result'
                                    ]
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    'node2' => [
                        'item' => 'result'
                    ]
                ],
                [
                    'node3' => [
                        'item' => 'result'
                    ]
                ],
                [
                    'node' => [
                        'item' => 'result'
                    ]
                ],
                [
                    'node' => [
                        'item' => 'result'
                    ]
                ]
            ]
        ];

        $this->assertXmlStringEqualsXmlString($xml, $serialize->toString($data));
    }
}
