<?php

namespace LightXml\Tests;

use LightXml\SerializedItem;
use LightXml\SerializedList;

/**
 * @author Ilya Zelenin <wyster@make.im>
 */
class SerializedObjectsTest extends \PHPUnit_Framework_TestCase
{
    public function testData()
    {
        $item = new SerializedItem([], 'root');

        $xml = '<root/>';
        $this->assertXmlStringEqualsXmlString($xml, $item);
        $this->assertXmlStringEqualsXmlString($xml, $item->toString());

        $item->setNodeName('main');

        $xml = '<main/>';
        $this->assertXmlStringEqualsXmlString($xml, $item);
        $this->assertXmlStringEqualsXmlString($xml, $item->toString());

        $xml = '<item><id>1</id><value>foo</value></item>';
        $item = new SerializedItem(['item' => ['id' => 1, 'value' => 'foo']], FALSE);
        $this->assertXmlStringEqualsXmlString($xml, $item);
        $this->assertXmlStringEqualsXmlString($xml, $item->toString());

        $xml = '<item><id>1</id><value>foo</value></item>';
        $item = new SerializedItem(['id' => 1, 'value' => 'foo'], 'item');
        $this->assertXmlStringEqualsXmlString($xml, $item);
        $this->assertXmlStringEqualsXmlString($xml, $item->toString());

        $xml = '<root><item><id>2</id><value>bar</value></item><item><id>1</id><value>foo</value></item></root>';
        $item = new SerializedList([
            new SerializedItem(['id' => 2, 'value' => 'bar'], 'item'),
            new SerializedItem(['id' => 1, 'value' => 'foo'], 'item')
        ], 'root');
        $this->assertXmlStringEqualsXmlString($xml, $item);
        $this->assertXmlStringEqualsXmlString($xml, $item->toString());

        $xmlString = '<root><id>1</id><value>foo</value></root>';
        $item = new SerializedItem(['id' => 1, 'value' => 'foo'], 'root');
        $this->assertTrue(isset($item->id));
        $this->assertFalse(isset($item->test));
        $this->assertXmlStringEqualsXmlString($xmlString, $item);
        $this->assertXmlStringEqualsXmlString($xmlString, $item->toString());
    }
}