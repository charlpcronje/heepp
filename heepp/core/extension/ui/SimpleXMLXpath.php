<?php

class SimpleXMLXpath {
    private $xml;

    public function __construct(SimpleXMLElement $xml) {
        $this->xml = $xml;
    }

    public function query($expression) {
        $context = dom_import_simplexml($this->xml);
        $xpath   = new DOMXPath($context->ownerDocument);
        $result  = [];

        foreach($xpath->query($expression,$context) as $node) {
            switch(true) {
                case $node instanceof DOMText:
                    $result[] = $node->nodeValue;
                    continue;

                case $node instanceof DOMElement:
                case $node instanceof DOMAttr:
                    $result[] = simplexml_import_dom($node);
                    continue;
            }
        }

        return $result;
    }
}