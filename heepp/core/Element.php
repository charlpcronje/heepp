<?php
namespace core;

class Element extends Heepp {
    public $element;
    protected $child;
    protected $html;
    protected $explain           = false;
    protected $attr              = [];
    public    $SELF_CLOSING_TAGS = [
        'area',
        'base',
        'col',
        'command',
        'embed',
        'keygen',
        'input',
        'img',
        'hr',
        'br',
        'meta',
        'param',
        'source',
        'track',
        'wbr',
        'link',
        '!DOCTYPE'
    ];

    public function __construct($element = null) {
        if (isset($element)) {
            $this->element = $element;
        }
        parent::__construct();
    }

    /* $elem = 'html' Tag
     * $attr = [] array of key value pair that will be added as tag attributes */
    public static function mold($elem,$attr = null,$content = null) {
        if (!isset($elem) || !is_string($elem)) {
            return false;
        }

        $element = new Element($elem);
        if (isset($attr) && is_array($attr)) {
            foreach($attr as $key => $value) {
                $element->attr($key,$value);
            }
        }

        if (isset($content)) {
            $element->add($content);
        }

        return $element;
    }

    /** @noinspection MagicMethodsValidityInspection
     * @param $name
     * @param $value
     */
    public function __set($name,$value) {
        $this->attr($name,$value);
    }

    public function setProperty($key,$value) {
        // Check if the property exists
        if (isset($this->{$key})) {
            $this->{$key} = $value;
            // Check if the property exists if I replace '-' with '_'
        } elseif (isset($this->{str_replace('-','_',$this->{$key})})) {
            $this->{str_replace('-','_',$this->{$key})} = $value;
        } else {
            // If the property did not exist then just by setting the property will create one (overloading)
            $this->{$key} = $value;
        }
    }

    public function getProperty($key) {
        if (!isset($this->{$key})) {
            $key = str_replace('-','_',$key);
        }
        if (isset($this->{$key})) {
            return $this->{$key};
        }
    }

    public function setElement($element) {
        $this->element = $element;

        return $this;
    }

    public function addAttr($attr = null,$value = null) {
        $this->attr($attr,$value);
        return $this;
    }

    public function setAttr($attr = null,$value = null) {
        $this->attr[$attr] = $value;

        return $this;
    }

    public function attr($attr = null,$value = null) {
        if (!isset($this->attr[$attr])) {
            $this->attr[$attr] = '';
        }
        $this->attr[$attr] .= $value;

        return $this;
    }

    public function add($value = null) {
        if ($this->child) {
            if (is_object($value)) {
                $this->child .= $value->render();
            } elseif (is_array($value)) {
                $this->child = $value;
            } else {
                @$this->child .= (string)$value;
            }
        } else {
            $this->child = $value;
            if (is_object($value)) {
                $this->child = $value->render();
            }
        }

        return $this;
    }

    public function removeAttr($attr) {
        unset($this->attr[$attr]);
    }

    public function render() {
        if (strlen($this->element) > 0) {
            if (in_array($this->element,$this->SELF_CLOSING_TAGS)) {
                $this->html = '<'.$this->element.'/>';
            } else {
                $this->html = '<'.$this->element.'></'.$this->element.'>';
            }
            if (count($this->attr) > 0) {
                $i          = 0;
                foreach($this->attr as $key => $value) {
                    $i++;
                    //This is only applicable when you are not working from the BASE_URL
                    if ($key === 'src' && strpos($value,HTTP) === false) {
                        // Check if the requested src is in another project
                        if (strpos($value,'..'.DS) !== false) {
                            $imgParts = explode('..'.DS,$value);
                            $newValue = str_replace('projects'.DS.PROJECT,'projects',$imgParts[0]).$imgParts[1];
                            if (file_exists($newValue)) {
                                $value = env('http.host').$newValue;
                            }
                        } else {
                            $value = env('http.host').$value;
                        }
                    }

                    if (!empty($value) || $value == '0') {
                        $this->html = str_replace('<'.$this->element,'<'.$this->element.' '.$key.'="'.$value.'"',$this->html);
                    } else {
                        $this->html = str_replace('<'.$this->element,'<'.$this->element.' '.$key,$this->html);
                    }
                }
            }
            if (isset($this->child)) {
                $this->html = str_replace('><','>'.$this->child.'<',$this->html);
            }
        } else {
            $this->html = '';
        }

        return $this->html;
    }
}
