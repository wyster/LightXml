<?php

namespace LightXml;

/**
 * @author Ilya Zelenin <wyster@make.im>
 */
class Reader implements ReaderInterface
{
    private $result = null;
    private $previous_nodes = [];

    /**
     * @var ConfigReader
     */
    protected $config;

    /**
     * @param array|Closure|ConfigReader $config
     */
    public function __construct($config = null)
    {
        $this->setConfig($config);
    }

    /**
     * @param array|Closure|ConfigReader $config
     */
    protected function setConfig($config)
    {
        if ($this->config === null) {
            $this->config = new ConfigReader();
        }
        if ($config === null) {
            return;
        }
        if (is_array($config)) {
            $this->config = new ConfigReader($config);
        }
        if ($config instanceof ConfigReader) {
            $this->config = $config;
        }
        if ($config instanceof \Closure) {
            $config($this->config);
        }
    }

    /**
     * Set, get config
     * @param array|Closure|ConfigReader $config
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
    public function fromString($string)
    {
        $this->result = null;

        $xmlParser = xml_parser_create($this->config->outputCharset);
        xml_parser_set_option($xmlParser, XML_OPTION_CASE_FOLDING, false);
        xml_set_element_handler($xmlParser, [$this, 'findNewElement'], [$this, 'findEndElement']);
        xml_set_character_data_handler($xmlParser, [$this, 'findTextElement']);

        if (!xml_parse($xmlParser, $string)) {
            throw new \Exception(xml_error_string(xml_get_error_code($xmlParser)));
        }

        xml_parser_free($xmlParser);

        $resultArrayOfProps = $this->convertParsingResult($this->result);

        return $resultArrayOfProps;
    }

    /**
     * @param array $node
     * @return SerializedItem
     */
    protected function convertParsingResult($node)
    {
        $resultArray = new SerializedItem();
        $nodes = $node['childs'];
        $attributes = $node['attributes'];

        if (count($nodes) == 0 && count($attributes) == 0 && array_key_exists('value', $node)) {
            return $node['value'];
        } elseif (!empty($node['value']) && trim($node['value']) != '') {
            $resultArray['value'] = $node['value'];
        }

        foreach ($attributes as $attribute => $value) {
            $attributeName = $attribute . 'Attribute';
            $resultArray[$attributeName] = $value;
        }

        foreach ($nodes as $child_node) {
            if (isset($resultArray[$child_node['name']])) {
                if ($resultArray[$child_node['name']] instanceof SerializedList) {
                    $resultArray[$child_node['name']]->append($this->convertParsingResult($child_node));
                } else {
                    $resultArray[$child_node['name']] = new SerializedList([$resultArray[$child_node['name']], $this->convertParsingResult($child_node)], $child_node['name']);;
                }
            } else {
                $resultArray[$child_node['name']] = $this->convertParsingResult($child_node);
            }
        }

        return $resultArray;
    }

    /**
     * @param        $parser
     * @param string $name
     * @param array  $nodeAttributes
     */
    protected function findNewElement($parser, $name, $nodeAttributes)
    {
        if ($this->result === null) {
            $this->result = ['name' => $name, 'attributes' => $nodeAttributes, 'childs' => []];
        } else {
            if (count($this->previous_nodes) === 0) {
                $this->result['childs'][] = [
                    'name' => $name,
                    'attributes' => $nodeAttributes,
                    'value' => null,
                    'childs' => []
                ];
                $this->previous_nodes[] = &$this->result['childs'][count($this->result['childs']) - 1];
            } else {
                $lastPreviousNodeIndex = count($this->previous_nodes) - 1;
                $this->previous_nodes[$lastPreviousNodeIndex]['childs'][] = [
                    'name' => $name,
                    'attributes' => $nodeAttributes,
                    'value' => null,
                    'childs' => []
                ];
                $this->previous_nodes[] = &$this->previous_nodes[$lastPreviousNodeIndex]['childs'][count($this->previous_nodes[$lastPreviousNodeIndex]['childs']) - 1];
            }
        }
    }

    /**
     * @param $parser
     * @param $data
     */
    protected function findTextElement($parser, $data)
    {
        if (count($this->previous_nodes) != 0) {
            $this->previous_nodes[(count($this->previous_nodes) - 1)]['value'] .= $data;
        }
    }

    /**
     * @param $parser
     * @param $name
     */
    protected function findEndElement($parser, $name)
    {
        array_pop($this->previous_nodes);
    }
} 