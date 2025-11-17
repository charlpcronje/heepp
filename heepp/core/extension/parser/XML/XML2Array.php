<?php
namespace core\extension\parser\XML;

class XML2Array extends \core\extension\Extension {
    public $results;
    public $inputArrayOrXML = null;
    public $inputType = 'array';
    public $outputType = 'stdClass';
    public $rootNodeName = 'root';
    public $useConstants = true;
    
    public function __construct($inputArrayOrXML = null,$rootNodeName = 'root',$useConstants = true,$outputType = 'stdClass') {
        parent::__construct();
        $this->inputArrayOrXML = $inputArrayOrXML;
        $this->useConstants = $useConstants;
        $this->outputType = $outputType;
        
        if (!empty($this->inputArrayOrXML) && count($this->inputArrayOrXML,COUNT_RECURSIVE) > 0) {
            if (is_array($this->inputArrayOrXML)) {
                $this->inputType = 'xml';
                $this->setResults($this->toXml($this->inputArrayOrXML,$rootNodeName));
            } else {
                if (file_exists($this->inputArrayOrXML) && !is_dir($this->inputArrayOrXML)) {
                    $this->inputType = 'array';
                    $this->setResults($this->toArray(file_get_contents($this->inputArrayOrXML)));
                } else {
                    throw new \Exception('XML File '.$this->inputArrayOrXML.' was not found in: '.\core\system\helpers\getCallingClass());
                }
            }
        } else {
            if ($this->inputType == 'xml') {
                $this->setResults('');
            } else {
                $this->setResults(array());
            }
        }
    }
    /**
     * @staticvar string - String to use as key for node attributes into array
     */
    const attr_arr_string = 'attributes';

    /**
     * The main function for converting to an XML document.
     * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
     *
     * @static
     * @param  array $data
     * @param  string $rootNodeName - what you want the root node to be - defaultsto data.
     * @param  SimpleXMLElement $xml - should only be used recursively
     * @return string XML
     */
    public function toXml($data, $rootNodeName = 'root', &$xml = NULL) {
        if (is_null($xml)) {
            $xml = new \SimpleXMLElement('<' . $rootNodeName . '/>');
        }
        // loop through the data passed in.
        foreach ($data as $key => $value) {
            // if numeric key, assume array of rootNodeName elements
            if (is_numeric($key)) {
                $key = $rootNodeName;
            }
            // Check if is attribute
            if ($key == XML2Array::attr_arr_string) {
                // Add attributes to node
                foreach ($value as $attr_name => $attr_value) {
                    if ($this->useConstants) {
                        $attr_value = $this->replaceConstants($attr_value);
                    }
                    $xml->addAttribute($attr_name, $attr_value);
                }
            } else {
                // delete any char not allowed in XML element names
                $key = preg_replace('/[^a-z0-9\-\_\.\:]/i', '', $key);
                // if there is another array found recrusively call this function
                if (is_array($value)) {
                    // create a new node unless this is an array of elements
                    $node = XML2Array::isAssoc($value) ? $xml->addChild($key) : $xml;
                    // recrusive call - pass $key as the new rootNodeName
                    XML2Array::toXml($value, $key, $node);
                } else {
                    // add single node.
                    $value = htmlentities($value);
                    $xml->addChild($key, $value);
                }
            }
        }
        // pass back as string. or simple xml object if you want!
        return $xml->asXML();
    }
    
    private function replaceConstantsSplitBy($string,$delimeter) {
        if (is_string($string) && strpos($string,$delimeter)) {
            $stringParts = explode($delimeter,$string);
            foreach($stringParts as $part) {
                if (defined($part)) {
                    $string = str_replace($part,constant($part),$string);
                }
            }
        }
        return $string;
    }
    
    private function replaceConstants($string) {        
        $delimeters = array('.','/','\\',' ');
        foreach($delimeters as $delimeter) {
            $string = $this->replaceConstantsSplitBy($string,$delimeter);
        }
        return $string;
    }

    /**
     * The main function for converting to an array.
     * Pass in a XML document and this recrusively loops through and builds up an array.
     *
     * @static
     * @param  string $obj - XML document string (at start point)
     * @param  array  $arr - Array to generate
     * @return array - Array generated
     */
    public function toArray($obj, &$arr = NULL) {
        if (is_null($arr))
            $arr = array();
        if (is_string($obj))
            $obj = new \SimpleXMLElement($obj);
        // Get attributes for current node and add to current array element
        $attributes = $obj->attributes();
        foreach ($attributes as $attrib => $value) {
            if ($this->useConstants) {
                $value = $this->replaceConstants((string)$value);
            }
            $arr[XML2Array::attr_arr_string][$attrib] = (string)$value;
        }
        $children = $obj->children();
        $executed = FALSE;
        // Check all children of node
        foreach ($children as $elementName => $node) {
            // Check if there are multiple node with the same key and generate a multiarray
            if (isset($arr[$elementName]) && $arr[$elementName] != NULL) {
                if (isset($arr[$elementName][0])) {
                    $i = count($arr[$elementName]);
                    if ($this->useConstants) {
                        if (isset($arr[$elementName][$i])) {
                            $arr[$elementName][$i] = $this->replaceConstants($arr[$elementName][$i]);
                        }
                    }
                    XML2Array::toArray($node, $arr[$elementName][$i]);
                } else {
                    $tmp = $arr[$elementName];
                    $arr[$elementName] = array();
                    $arr[$elementName][0] = $tmp;
                    $i = count($arr[$elementName]);
                    if ($this->useConstants) {
                        if (isset($arr[$elementName][$i])) {
                            $arr[$elementName][$i] = $this->replaceConstants($arr[$elementName][$i]);
                        }
                    }
                    XML2Array::toArray($node, $arr[$elementName][$i]);
                }
            } else {
                $arr[$elementName] = array();
                if ($this->useConstants) {
                    $arr[$elementName] = $this->replaceConstants($arr[$elementName]);
                }
                XML2Array::toArray($node, $arr[$elementName]);
            }
            $executed = TRUE;
        }
        // Check if is already processed and if already contains attributes
        if (!$executed && $children->getName() == "" && !isset($arr[XML2Array::attr_arr_string])) {
            $arr = (String) $obj;
        }
        return $arr;
    }

    /**
     * Determine if a variable is an associative array
     *
     * @static
     * @param  array $obj - variable to analyze
     * @return boolean - info about variable is associative array or not
     */
    private static function isAssoc($array) {
        return (is_array($array) && 0 !== count(array_diff_key($array, array_keys(array_keys($array)))));
    }

    public function setOutputType($outputType) {
        $this->outputType = $outputType;
    }
    
    private function setResults($results) {
        switch($this->outputType) {
            case 'array':
                $this->results = $results;
            break;
            case 'stdClass':
                $this->results = (new \core\extension\parser\arrays\Array2Object($results))->results;
            break;
            case 'json':
                $this->results = json_encode($results);
            break;
        }
    }
}
