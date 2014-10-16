<?php

namespace LightXml;

use DOMDocument;
use DOMElement;
use DOMText;
use Closure;

/**
 * @author Ilya Zelenin <wyster@make.im>
 * @package LightXml
 */
class Writer implements WriterInterface
{
    /**
     * @var ConfigWriter
     */
    protected $config;

    /**
     * @param array|Closure|ConfigWriter $config
     */
    public function __construct($config = null)
    {
        $this->setConfig($config);
    }

    /**
     * @param array|Closure|ConfigWriter $config
     */
    protected function setConfig($config)
    {
        if ($this->config === null) {
            $this->config = new ConfigWriter();
        }
        if ($config === null) {
            return;
        }
        if (is_array($config)) {
            $this->config = new ConfigWriter($config);
        }
        if ($config instanceof ConfigWriter) {
            $this->config = $config;
        }
        if ($config instanceof Closure) {
            $config($this->config);
        }
    }

    /**
     * Set, get config
     * @param array|Closure|ConfigWriter $config
     * @return mixed
     */
    public function config($config = null)
    {
        if ($config === null) {
            return $this->config;
        }

        $this->setConfig($config);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function toString($data)
    {
        $dom = new DOMDocument('1.0', $this->config->documentCharset);
        $dom->preserveWhiteSpace = $this->config->preserveWhiteSpace;
        $dom->formatOutput = $this->config->formatOutput;
        $root = $dom->createElement($this->config->rootNodeName ?: 'root');
        $this->createNodes($data, $root, $dom);
        $dom->appendChild($root);

        if ($this->config->rootNodeName === false) {
            $xml = '';
            foreach ($root->childNodes as $node) {
                $xml .= $dom->saveXML($node);
            }

            return $xml;
        }

        if ($this->config->xmlDeclaration === true) {
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
     * @param bool        $isList
     */
    private function createNodes($vars, &$currentNode, &$dom, $parentNode = null, $isList = false)
    {
        $append = function (DOMElement &$newNode, $value) use ($dom) {
            // Если нужно оборачивать в cdata
            if ($this->config->cdata) {
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
                    if ($this->config->numArrayKeys === ConfigWriter::SHIFT_KEYS_LEFT) {
                        if ($parentNode !== null) {
                            $currentNode = $dom->createElement($currentNode->nodeName);
                            $this->createNodes($value, $currentNode, $dom, $parentNode, true);
                            $parentNode->appendChild($currentNode);
                        } else {
                            $temp = $currentNode;
                            $this->createNodes($value, $temp, $dom, $currentNode, true);
                        }
                        continue;
                    }
                    if ($this->config->numArrayKeys === ConfigWriter::SHIFT_KEYS_RIGHT) {
                        $rForeach = function ($values) use ($dom, $currentNode, $parentNode, &$rForeach) {
                            foreach ($values as $propertyName => $value) {
                                if (filter_var($propertyName, FILTER_VALIDATE_INT) !== false) {
                                    $rForeach($value);
                                    continue;
                                }
                                $this->createNodes([$propertyName => $value], $currentNode, $dom, $parentNode, true);
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
