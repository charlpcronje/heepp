<?php
/*
    This class can transform XML data to an array and vice-versa using DOM XML API.
    It preserves encoding, namespaces and attributes.
    It is mainly used for creating XML documents from data taken from HTML forms.
 */
class Array2XML {

    var $config = array(
        'encoding' => 'ISO-8859-15',
        'xmlns' => array(
            'ino' => 'http://namespaces.softwareag.com/tamino/response2'
        )
    );

    function Array2XML($array) {
        if (!is_array($array)) {
            return false;
        }
        $this->array = $array;
        $this->dom = domxml_new_doc("1.0");
    }

    function setEncoding($enc) {
        $this->config['encoding'] = ( $enc != '' ) ? $enc : $this->config['encoding'];
    }

    function addNamespaces($assoc) {
        $this->config['xmlns'] = array_merge($this->config['xmlns'], $assoc);
    }

    function getResult($format = true) {
        $doc_root = array_shift(array_keys($this->array));
        $root_element = $this->dom->create_element($doc_root);
        $this->_recArray2Node($root_element, $this->array[$doc_root]);
        $this->dom->append_child($root_element);

        // check for namespaces ? add each to doc
        if (is_array($this->used_namespaces)) {
            foreach ($this->used_namespaces as $ns) {
                $root_element->add_namespace($this->config["xmlns"][$ns], $ns);
            }
        }

        // <b>Warning</b>:  dump_mem(): xmlDocDumpFormatMemoryEnc:  Failed to identify encoding handler for character set 'ISO-8859-15'
        return $this->dom->dump_mem($format, $this->config['encoding']);
    }

    function _recArray2Node($parent_node, $array) {
        foreach ($array as $key => $value) {
            $org_key = $key;
            list( $ns, $key ) = split(':', str_replace("@", "", $org_key));
            if (!$key) {
                $key = $ns;
            } elseif ($ns == "xmlns") {
                $this->addNamespaces(array($key => $value));
                break;
            } else {
                if ($this->config["xmlns"][$ns]) {
                    $this->used_namespaces[] = $ns;
                    $key = $ns . ":" . $key;
                } else {
                    die("Namespace for $ns does not exist! Use obj->addNamespaces( \$assoc ) for adding.");
                }
            }

            if (substr($org_key, 0, 1) == '@') {
                // attribute
                $parent_node->set_attribute($key, $value);
                continue;
            } elseif ($key == '#text' || !is_array($value)) {
                // text node
                // check if valid text & not empty
                if ($value == '0' | !empty($value)) {
                    $element = $this->dom->create_text_node($value);
                    $parent_node->append_child($element);
                }
                continue;
            } else {
                // child node
                // check for enumeration
                $enum = false;
                while (list( $k, $v ) = each($value)) {
                    if (is_numeric($k)) {
                        // enumeration of multiple nodes
                        $enum = true;
                        $element = $this->dom->create_element($key);
                        $this->_recArray2Node($element, $v);
                        $parent_node->append_child($element);
                    }
                }

                // check for enumeration
                if ($enum == false) {
                    $element = $this->dom->create_element($key);
                    $this->_recArray2Node($element, $value);
                    $parent_node->append_child($element);
                }
            }
        }
    }
}