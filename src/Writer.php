<?php

namespace LightXml;

use Closure;
use DOMDocument;
use DOMElement;
use DOMText;
use LightXml\Config\Writer as ConfigWriter;

/**
 * @author Ilya Zelenin <wyster@make.im>
 * @package LightXml
 */
class Writer extends AbstractConfig implements WriterInterface
{
    /**
     * @param array|Closure|ConfigWriter $config
     */
    public function __construct($config = null)
    {
        $this->setConfig($config);
    }

    /**
     * @return ConfigWriter
     */
    public function getConfig()
    {
        if ($this->config === null) {
            $this->config = new ConfigWriter();
        }

        return $this->config;
    }

    /**
     * @inheritdoc
     */
    public function toString($data)
    {
        $dom = new DOMDocument('1.0', $this->getConfig()->documentCharset);
        $dom->preserveWhiteSpace = $this->getConfig()->preserveWhiteSpace;
        $dom->formatOutput = $this->getConfig()->formatOutput;
        $root = $dom->createElement($this->getConfig()->rootNodeName ?: 'root');
        $this->createNodes($data, $root, $dom);
        $dom->appendChild($root);

        if ($this->getConfig()->rootNodeName === false) {
            $xml = '';
            foreach ($root->childNodes as $node) {
                $xml .= $dom->saveXML($node);
            }

            return $xml;
        }

        if ($this->getConfig()->xmlDeclaration === true) {
            return $dom->saveXML();
        }

        $xml = $dom->saveXML($root);

        return $xml;
    }

    /**
     * @param mixed       $vars
     * @param DOMElement  $currentNode
     * @param DOMDocument $dom
     * @param DOMElement  $parentNode
     */
    private function createNodes($vars, &$currentNode, &$dom, $parentNode = null)
    {
        $append = function (DOMElement &$newNode, $value) use ($dom) {
            // Если нужно оборачивать в cdata
            if ($this->getConfig()->cdata) {
                $regex = "#[\&\"\'\<\>]+#";
                // Если строка без тегов не равна длине исходной, есть специальные символы, html теги, нужно обернуть в cdata
                if (preg_match($regex, $value)) {
                    $newNode->appendChild($dom->createCDATASection($value));
                } else {
                    $newNode->nodeValue = $value;
                }
            } else {
                $newNode->appendChild(new DOMText($value));
            }
        };

        foreach ($vars as $propertyName => $value) {
            if (preg_match('/Attribute$/', $propertyName)) {
                $attributeName = preg_replace('/Attribute$/', '', $propertyName);
                $currentNode->setAttribute($attributeName, $value);
                continue;
            }

            if ($value instanceof AbstractSerializedObject && $value->getNodeName()) {
                $propertyName = $value->getNodeName();
            }

            if ($value instanceof AbstractSerializedObject || is_array($value) || is_object($value)) {
                if (filter_var($propertyName, FILTER_VALIDATE_INT) !== false) {
                    if ($this->getConfig()->numArrayKeys === ConfigWriter::SHIFT_KEYS_LEFT) {
                        if ($parentNode !== null) {
                            $currentNode = $dom->createElement($currentNode->nodeName);
                            $this->createNodes($value, $currentNode, $dom, $parentNode);
                            $parentNode->appendChild($currentNode);
                        } else {
                            $temp = $currentNode;
                            $this->createNodes($value, $temp, $dom, $currentNode);
                        }
                        continue;
                    }
                    if ($this->getConfig()->numArrayKeys === ConfigWriter::SHIFT_KEYS_RIGHT) {
                        $rForeach = function ($values) use ($dom, $currentNode, $parentNode, &$rForeach) {
                            foreach ($values as $propertyName => $value) {
                                if (filter_var($propertyName, FILTER_VALIDATE_INT) !== false) {
                                    $rForeach($value);
                                    continue;
                                }
                                $this->createNodes([$propertyName => $value], $currentNode, $dom, $parentNode);
                            }
                        };
                        $rForeach($value);
                        continue;
                    }
                }

                $newNode = $dom->createElement($propertyName);
                $this->createNodes($value, $newNode, $dom, $currentNode);
            } else {
                if (filter_var($propertyName, FILTER_VALIDATE_INT) !== false) {
                    $currentNode = $dom->createElement($currentNode->nodeName);
                    $append($currentNode, $value);
                    $parentNode->appendChild($currentNode);
                    continue;
                }

                $newNode = $dom->createElement($propertyName);
                $append($newNode, $value);
            }

            $currentNode->appendChild($newNode);
        }
    }
}
