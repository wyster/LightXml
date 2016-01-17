<?php
namespace Config;
use LightXml\Config\AbstractConfig;
use LightXml\Config\Reader as ConfigReader;

/**
 * @author Ilya Zelenin <wyster@make.im>
 */
class AbstractTest extends \PHPUnit_Framework_TestCase
{
    public function testSetConfig()
    {
        /**
         * @var AbstractConfig|\PHPUnit_Framework_MockObject_MockObject $stub
         */
        $stub = $this->getMockForAbstractClass('\LightXml\Config\AbstractConfig');
        $stub->outputCharset = 'utf-8';

        $this->assertEquals('utf-8', $stub->outputCharset);
        $stub->exchangeArray(['outputCharset' => 'windows-1251']);

        $this->assertEquals('windows-1251', $stub->outputCharset);

        $stub->exchangeArray(function($config) {
            $this->assertTrue($config  instanceof AbstractConfig);
        });

        $readerConfig = new ConfigReader();
        $readerConfig->outputCharset = 'cp1251';
        $readerConfig->valueNotExists = 1;
        $stub->exchangeArray($readerConfig);
        $this->assertEquals('cp1251', $stub->outputCharset);
        $this->assertFalse(isset($stub->valueNotExists));
    }
}