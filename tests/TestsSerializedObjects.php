<?php

namespace LightXml;

/**
 * @author Ilya Zelenin <wyster@make.im>
 */
class TestsSerializedObjects extends \PHPUnit_Framework_TestCase
{
    public function testData()
    {
        $item = new SerializedItem();

        $this->assertSame('root', $item->getNodeName());

        $item->setNodeName('main');
        $this->assertSame('main', $item->getNodeName());
    }
}