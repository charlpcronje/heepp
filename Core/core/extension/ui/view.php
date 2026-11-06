<?php
namespace core\extension\ui;
use core\Heepp;
use core\Element;
use core\extension\parser\html\htmLawed\htmLawed;
use SplFileInfo;

class view extends Heepp {
    public $html                        = '';
    private $createChildObjects         = true;
    private static $strReplaceElemArray = ['<repeat>','</repeat>','<if>','</if>'];
    private static $allowedPHPFunctions = ['year','month','day','slugify','current','end','next','explode','implode','count','strtolower','strtoupper','ucfirst','ucwords','substr','strlen','str_replace','trim','strpos'];
    private $pregReplaceArray           = ['/\s\s+/'];
    private $loadAttributeBind          = true;
    private $ignoreAttributes           = [];
    private $debug                      = true;
    private $ignoreTypes                = ['style'];
    //private $treatAsXML                 = ['xml','xhtml','phtml','pml','fo','frag'];
    //private $treatAsHTML                = ['html','htm'];
    //private $treatAsMarkdown            = ['md','markdown'];
    private $addComments                = false;
    private $autonumber                 = 0;
    private $viewPath;
    private $postProcess                = false;
    private $postProcessOptionsPath;
    private $OptionsPath;
    private $postProcessOptions         = [];
    private static $defaultViewExt      = 'phtml';
    private $viewObject;
    private $view;
    private $iterator;
    private $constants;
    protected $element;

    public function __construct($view,$viewPath = null,$element = null) {
        $this->viewPath = env('project.path');
        $this->postProcessOptionsPath = env('core.extension.path').'parser'.DS.'html'.DS.'htmLawed'.DS.'settings.php';
        parent::__construct();
        $this->setDebug();
        if (isset($viewPath)) {
            if (substr($viewPath,-1) !== DS) {
                $this->viewPath = $viewPath.DS;
            }
        }
        if (isset($element)) {
            $this->element = $element;
            foreach((array)classProperties($element) as $prop => $value) {
                $this->setData($prop,$value);
            }
        }
        // Set user defined constants to $this->constants
        $this->constants = (object)get_defined_constants(true)['user'];
        /* Sometimes a view might be in a special place to it won't be found in the views folder
         * If files are imported the $this->viewPath is also sent for in case the imports are in the same viewPath.
         * If the view is not found in the viewPath it will look where it was specified the in the $view argument */
        $this->view = $view;
        if (is_string($view)) {
            $this->view = $this->viewPath.$view;
        }
        // Create view Object
        $this->viewObject = new \stdClass();
        // Set view object and cache the view
        $this->setViewObject();
        // Go over each element (node) and parse them to html
        $this->parseView();
    }

    public static function phtml($view,$viewPath = null,$element = null) {
        if (strpos($view,'.phtml') === false) {
            $view .= '.phtml';
        }
        return self::mold($view,$viewPath,$element);
    }

    // This can be used to quickly use views with different extensions
    public static function __callStatic($viewExt,$viewOrOptions = []) {
        $fileName = $viewOrOptions[0];
        $viewPath = env('project.path');

        /* If you only need to give the name of the view with no path or
           element a string can be used */
        if (is_string($viewOrOptions[0]) && !isset($viewOrOptions[1])) {
            return self::mold($fileName.'.'.$viewExt,$viewPath);
        }
        if (is_array($viewOrOptions[0])) {
            $viewOrOptions = (object)$viewOrOptions;
        }

        /* If you want to specify a with with the view path or maybe and element,
           then an array or obj can be parsed. */
        if(is_object($viewOrOptions)) {
            $viewObj = (object)[
                'view' => '',
                'path' => null,
                'element' => null
            ];
            if (isset($viewOrOptions->view)) {
                $viewObj->view = $viewOrOptions->view;
            }
            $viewObj->path = $viewPath;
            if (isset($viewOrOptions->path)) {
                $viewObj->path = $viewOrOptions->path;
            }
            if (isset($viewOrOptions->element)) {
                $viewObj->element = $viewOrOptions->element;
            }
            return self::mold($viewObj->view.'.'.$viewExt,$viewObj->path,$viewObj->element);
        }
    }

    private function setDebug() {
        if ($this->dataKeyExist('debug.core.extension.ui.view')) {
            $this->debug = $this->getData('debug.core.extension.ui.view');
        }
        $this->debug = false;
    }

    public static function mold($view,$viewPath = null,$element = null) {
        $view = new View($view,$viewPath,$element);
        return $view->postProcess();
    }

    public static function preProcess($codeOrFile,$options = [
        'valid_xhtml' => 1,
        'hook_tag'    => 'view::preProcessTagsAndAttr'
    ]) {
        $code = $codeOrFile;
        if (file_exists((string)$codeOrFile) && is_file((string)$codeOrFile)) {
            $code = file_get_contents($codeOrFile);
        }
        return htmLawed::hl($code,$options);
    }

    public static function preProcessTagsAndAttr($element,$attributeArray = 0) {
        // If second argument is not received, it means a closing tag is being handled
        if(is_numeric($attributeArray)){
            return "</$element>";
        }
        static $id = 0;
        // Inject param for allowScriptAccess
        $newElement = '';
        if($element == 'object') {
            $newElement = '<param id="my_'. $id.'" allowscriptaccess="never" />';
             ++$id;
        }
        $string = '';
        foreach($attributeArray as $k => $v){
            $string .= " {$k}=\"{$v}\"";
        }
        static $emptyElements = ['area'=>1,'br'=>1,'col'=>1,'command'=>1,'embed'=>1,'hr'=>1,'img'=>1,'input'=>1,'isindex'=>1,'keygen'=>1,'link'=>1,'meta'=>1,'param'=>1,'source'=>1,'track'=>1,'wbr'=>1];
        return "<{$element}{$string}". (array_key_exists($element,$emptyElements) ? ' /' : ''). '>'. $newElement;
    }

    public function postProcess($options = [
        'tidy' => 1
    ]) {
        if (!$this->postProcess) {
            return $this->html;
        }
        $this->postProcessOptions = $this->getPostProcessOptions();
        $this->postProcessOptions = array_replace_recursive($this->postProcessOptions,$options);
        return str_replace(['&lt;','&gt;'],['<','>'],htmLawed::hl($this->html,$this->postProcessOptions));
    }

    public function getPostProcessOptions() {
        $this->postProcessOptions = include $this->postProcessOptionsPath;
        return $this->postProcessOptions;
    }

    private function setViewObject() {
        // The view can be a file or a string or an simpleXml object
        if (!is_object($this->view) && file_exists($this->view) && is_file($this->view)) {
            // Get some info about the file: dirname,basename,extension,filename
            $fileInfo = (object)pathinfo($this->view);
            $fileInfo->filemtime = filemtime($this->view);
            $cacheKey = 'file.view.'.$this->view.$fileInfo->filemtime;
            if (!$this->cached($cacheKey)) {
                $this->viewObject->isFile    = true;
                $this->viewObject->path      = str_replace(['\\\\','//','/','\\'],['\\','/',DS,DS],$fileInfo->dirname);
                $this->viewObject->fileName  = $fileInfo->basename;
                $this->viewObject->type      = strtolower($fileInfo->extension);
                $this->viewObject->content   = file_get_contents($this->view);
                $this->viewObject->xml       = '<pml>'.$this->viewObject->content.'</pml>';
                $this->viewObject->xml       = $this->viewObject->content;
                $this->viewObject->filemtime = $fileInfo->filemtime;
                $this->cache($cacheKey,$this->viewObject);

            } else {
                // Get view object from memcache
                $this->viewObject = $this->cache($cacheKey);
            }
        } elseif (is_object($this->view) && $this->view instanceof \SimpleXMLElement) {
            $cacheKey = md5($this->view);
            $this->addComments = false;
            if (!$this->cached($cacheKey)) {
                $this->viewObject->isFile    = false;
                $this->viewObject->path      = null;
                $this->viewObject->fileName  = null;
                $this->viewObject->type      = 'xml';
                $this->viewObject->content   = $this->viewObject->asXML();
                $this->viewObject->xml       = '<xml>'.$this->viewObject->asXML().'</xml>';
                $this->viewObject->filemtime = null;
                $this->cache($cacheKey,$this->viewObject);
            } else {
                // Get view object from memcache
                return $this->cache($cacheKey);
            }
        } elseif (is_string($this->view)) {
            $cacheKey = md5($this->view);
            $this->addComments = false;
            if (!$this->cached($cacheKey)) {
                $this->viewObject->isFile    = false;
                $this->viewObject->path      = null;
                $this->viewObject->fileName  = null;
                $this->viewObject->type      = 'xml';
                $this->viewObject->content   = $this->view;
                $this->viewObject->xml       = '<root>'.$this->viewObject->content.'</root>';
                $this->viewObject->filemtime = null;
                $this->cache($cacheKey,$this->viewObject);
            } else {
                // Get view object from memcache
                return $this->cache($cacheKey);
            }
        }

        foreach(libxml_get_errors() as $error) {
            if (isset($this->viewObject->fileName)) {
                $this->setError($error->message.' IN FILE: '.$this->viewObject->fileName);
            } else {
                $this->setError($error->message.' In Fragment');
            }
        }
    }

    private function addFileComment($position) {
        if ($this->addComments) {
            return strtoupper("\r\n \r\n <!-- ## ".$position.' ## '.$this->viewObject->path.DIRECTORY_SEPARATOR.$this->viewObject->fileName.' ## '.$position." ## -->\r\n \r\n");
        }
    }

    public function loadXML($xml) {
        $this->loadAttributeBind = false;
        $object      = $this->createObjects($xml->getName(), $xml);
        $this->html .= $this->addFileComment('start');
        $this->html .= $object->render();
        $this->html .= $this->addFileComment('end');
        $this->html  = str_replace(self::$strReplaceElemArray,'',$this->html);
        $this->html  = preg_replace($this->pregReplaceArray,'', $this->html);
    }

    public function callObjectMethodArray($func,$obj,$params = []) {
        if ($obj === $this->element) {
            $func = new \ReflectionMethod($obj,$func);
            return $func->invokeArgs($this->element,$params);
        }
        $func = new \ReflectionMethod($obj,$func);
        return $func->invokeArgs($obj,$params);
    }

    public function loadFunction($value) {
        $params = explode(',',(string)$value,3);
        $classExp = explode('/', $params[0], 2);

        $funcParams = (object)[
            'parameters'  => $params,
            'classObj'    => $params[0],
            'classExp'    => $classExp,
            'class'       => $classExp[0],
            'classParams' => [],
            'obj'         => '',
            'array'       => null
        ];

        if (isset($funcParams->classExp[1])) {
            $funcParams->classParams = explode('/', $funcParams->classExp[1]);
        }
        if ($funcParams->class === 'this') {
            $funcParams->obj = getCallingClass();
        } else {
            if (class_exists($funcParams->class)) {
                if (isset($funcParams->classExp[1])) {
                    $objRef = new \ReflectionClass($funcParams->class);
                    $funcParams->obj = $objRef->newInstanceArgs($funcParams->classParams);
                } else {
                    $funcParams->obj = new $funcParams->class();
                }
            }
        }

        $funcParams->array = $funcParams->parameters[1];
        $function          = $funcParams->parameters[2];
        $params            = explode('(', $function);
        $function          = $params[0];
        if (isset($params[1])) {
            $params = $this->getParams($params[1]);
        } else {
            $params = [];
        }
        if (!is_string($this->callObjectMethodArray($function, $funcParams->obj, $params))) {
            $this->output->data->{$funcParams->array} = $this->callObjectMethodArray($function,$funcParams->obj,$params);
        } else {
            $this->output->data->{$funcParams->array}  = $this->callObjectMethodArray($function,$funcParams->obj,$params);
        }
    }

    public function getParams($params) {
        $params = str_replace(')', '', $params);
        if (strpos($params, ',') !== false) {
            $param = explode(',', $params);
            $params = $param;
        } else {
            $param[0] = $params;
            $params = $param;
        }
        return $params;
    }

    private function recursivelyFindTextNodes($domElement) {
        $return = [];
        foreach($domElement->childNodes as $domChild) {
            switch($domChild->nodeType) {
                case XML_TEXT_NODE:
                    if (!empty(trim($domChild->nodeValue))) {
                        $return[] = $domChild->nodeValue;
                    }
                break;
                //case XML_ELEMENT_NODE:
                //    //$return = array_merge($return,$this->recursivelyFindTextNodes($domChild));
                //break;
            }
        }
        return $return;
    }

    private function createObjects($tagName,$xhtml) {
        // Deals with php name spacing
        // This is to stop the dotSyntax to break
        $tagName = str_replace(['-','.'],['\\','_'],$tagName);
        // Check that the $tagName (node) is not saved in the session as a html element (If it is it will not even try to instantiate the $tagName (node) as a class.
        if (!$this->sessionKeyExist('isElement.'.$tagName)) {
            // Checks if there are a shortcut with the same name as the $tagName (node)
            if ($this->sessionKeyExist('shortcuts.'.$tagName)) {
                $tagName = $this->session('shortcuts.'.$tagName);
            }
            // The autoloader will check if the file and class exists. The name spacing and folder structure matches
            if (class_exists($tagName)) {
                $object = new $tagName($tagName);
            } else {
                // If the class was not found a normal html tag is built and the $tagName (node) is added to the isElement list
                $object = new Element($tagName);

                $this->session('isElement.'.$tagName,true);
            }

        } else {
            $object = new Element($tagName);
        }
        // Runs over each attr to check if any special attr(s) was used
        foreach ($xhtml->attributes() as $key => $value) {
            // Replace '-' with '_' because php classes don't accept properties that container the '-'
            // $key = str_replace('-','_',$key);
            if (!in_array($key,$this->ignoreAttributes)) {
                // Parses the attr value to a function that will replace all ${} with the values in $this->output->data
                $value = $this->parseString($value,$tagName);
            }
            if (strpos($key,':') !== false) {
                $nameSPAndAttr = explode(':',$key,2);
                $namespace = $nameSPAndAttr[0];
                $attrib = $nameSPAndAttr[1];
                switch($namespace) {
                    case 'elem.render':
                        if ($attrib === 'if') {
                            $condition = 'return '.$value.';';
                            if (!eval($condition)) {
                                unset($object);
                                return null;
                            }
                        }
                    break;
                    case 'child.render':
                        if ($attrib === 'if') {
                            $condition = 'return '.$value.';';
                            if (!eval($condition)) {
                                $this->createChildObjects = false;

                            }
                        }
                    break;
                    case 'data':
                        if ($attrib === 'get') {
                            call_user_func_array([$this,'getData'],explode(',',$value));
                        }
                        if ($attrib === 'set') {
                            call_user_func_array([$this,'setData'],explode(',',$value));
                        }
                    break;
                    case 'data.get':
                        $default = null;
                        if (!empty($value)) {
                            $default = $value;
                        }
                        $this->getData($attrib,$default);
                    break;
                    case 'session':
                        if ($attrib === 'get') {
                            $this->session($value);
                        }
                        if ($attrib === 'set') {
                            call_user_func_array([$this,'session'],explode(',',$value));
                        }
                    break;
                    case 'session.set':
                        $this->session($attrib,$value);
                    break;
                    case 'call.class':
                        $classAndMethod = explode('.',$attrib);
                        $method = array_pop($classAndMethod);
                        $class = implode('\\',$classAndMethod);
                        $classObj = new $class;
                        call_user_func_array([$classObj,$method],explode(',',$value));
                    break;
                    case 'call.elem':
                        if (isset($this->element)) {
                            if (method_exists($this->element,$attrib)) {
                                call_user_func_array([$this->element,$attrib],explode(',',$value));
                            }
                        }
                    break;
                    case 'this':
                        $callingClass = getCallingClass();
                        if (method_exists($callingClass,$attrib)) {
                            call_user_func_array([$callingClass,$attrib],explode(',',$value));
                        }
                    break;

                }
                continue;
            } else {

                switch(strtolower($key)) {
                    // Sets an attribute that's value should not be ignored from being parsed in the rest of the view
                    case 'debug-xml':
                        $this->debug = (bool)$value;
                        break;
                    case 'ignoreattribute':
                        $this->ignoreAttributes[] = (string)$value;
                        break;
                    case 'import':
                        $value = preg_replace('!\s+!',' ',$value);
                        // Imports can be split by spaces or commas
                        if (strpos($value,' ') !== false || strpos($value,',') !== false) {
                            $imports = explode(' ',$value);

                            if (strpos(',',$value) !== false) {
                                $imports = array_merge($imports,explode(',',$value));
                            }
                            foreach($imports as $import) {
                                // Imports a view into this view
                                $viewInfo = new SplFileInfo($import);
                                $viewExt  = $viewInfo->getExtension();
                                if (empty($viewExt)) {
                                    $viewExt = view::$defaultViewExt;
                                } else {
                                    $viewExt = '';
                                }
                                $viewFile = (string)str_replace([',',' ','\r\n','\n'],'',$import.'.'.$viewExt);
                                $importedHTML = new view($viewFile,$this->viewPath);
                                // Add the parsed imported view to the current node
                                $object->add($importedHTML->html);
                            }
                        } else {
                            // If there were only one import
                            $viewFile = (string)str_replace([',',' ','\r\n','\n'],'',$value);
                            $importedHTML = new view($viewFile,$this->viewPath);
                            $object->add($importedHTML->html);
                        }
                        break;
                    case 'bind':
                        if ($this->loadAttributeBind) {
                            if ($xhtml->count() > 0) {
                                $innerHTML                            = new view(\simplexml_load_string('<pml>'.$xhtml.'</pml>'),__DIR__,$this->element);
                                $this->loadAttributeBind              = true;
                                $this->output->data->{(string)$value} = $innerHTML->html;
                            } else {
                                $this->output->data->{(string)$value} = $this->parseString((string)$xhtml[0],$tagName);
                            }
                        }
                        break;
                    case 'bindchildren':
                        if ($this->loadAttributeBind) {
                            if ($xhtml->count() > 0) {
                                $children = '';
                                foreach($xhtml->children() as $child) {
                                    $children .= $child->asXML();
                                }
                                $newXHTMLDoc = new SmartDOMDocument();
                                $newXHTMLDoc->loadHTML($children);
                                $innerHTML                            = new view(simplexml_import_dom($newXHTMLDoc));
                                $this->loadAttributeBind              = true;
                                $this->output->data->{(string)$value} = $innerHTML->html;
                            } else {
                                $this->output->data->{(string)$value} = $this->parseString((string)$xhtml[0],$tagName);
                            }
                        }
                        break;
                    case 'function':
                        $parameters  = explode(',',$value);
                        $classObj    = $parameters[0];
                        $classExp    = explode('/',$classObj,2);
                        $class       = $classExp[0];
                        $classParams = null;
                        $obj         = null;
                        if (isset($classExp[1])) {
                            $classParams = explode('/',$classExp[1]);
                        }
                        if ($class == 'this') {
                            $obj = $this->fo->reportClass;
                        } else {
                            if (class_exists($class)) {
                                if (isset($classExp[1])) {
                                    $objRef = new \ReflectionClass($class);
                                    $obj    = $objRef->newInstanceArgs($classParams);
                                } else {
                                    $obj = new $class();
                                }
                            }
                        }
                        $function = $parameters[1];
                        if (isset($parameters[2])) {
                            $params = explode('(',$parameters[2]);
                            if (isset($params[1])) {
                                $params = $this->getParams($params[1]);
                            } else {
                                $params = [];
                            }
                        } else {
                            $params = [];
                        }
                        $value = $this->callObjectMethodArray($function,$obj,$params);
                        $object->add((string)$value);
                        break;
                    case 'loadfunction':
                        $this->loadFunction($value);
                        break;
                    case 'repeatfunction':
                        $parameters  = explode(',',(string)$value,3);
                        $classObj    = $parameters[0];
                        $classExp    = explode('/',$classObj,2);
                        $class       = $classExp[0];
                        $obj         = null;
                        $classParams = [];
                        if (isset($classExp[1])) {
                            $classParams = explode('/',$classExp[1]);
                        }
                        if ($class === 'this') {
                            $obj = getCallingClass();
                        } else {
                            if (class_exists($class)) {
                                if (isset($classExp[1])) {
                                    $objRef = new \ReflectionClass($class);
                                    $obj    = $objRef->newInstanceArgs($classParams);
                                } else {
                                    $obj = new $class();
                                }
                            }
                        }
                        $array  = $parameters[1];
                        $repeat = $parameters[2];
                        if (strpos($repeat,'(') !== false) {
                            $function = explode('(',$repeat);
                            $repeat   = $function[0];
                            $params   = $function[1];
                            $params   = str_replace(')','',$params);
                            $params   = explode(',',$params);
                        } else {
                            $params = [];
                        }
                        $this->output->data->{$array} = $this->callObjectMethodArray($repeat,$obj,$params);
                        foreach($this->output->data->{$array} as $repeatKey => $repeatValue) {
                            // TO-DO: Finish the repeatFunction
                            $this->iterator->{$array} = $repeatValue;
                            foreach($xhtml->children() as $child) {
                                $object->add($this->createObjects($child->getName(),$child));
                            }
                        }
                        $this->createChildObjects = false;
                        break;
                    case 'repeat':
                        $this->autonumber = 1;
                        if (!isset($this->iterator)) {
                            $this->iterator = new \stdClass();
                        }
                        $result = explode('.',$value);
                        $repeat = $result[0];
                        if (isset($this->output->data->{$repeat})) {
                            foreach($this->output->data->{$repeat} as $repeatKey => $repeatValue) {
                                $this->iterator->autonumber = $this->autonumber;
                                //Look for nested repeats
                                if (isset($result[3])) {
                                    $origResult = $result[3];
                                    if ($result[3] === 'key') {
                                        $result[3] = $repeatKey;
                                    }
                                    foreach($this->output->data->{$repeat}->{$result[1]}->{$result[2]}->{$result[3]} as $innerKey => $innerValue) {
                                        $this->iterator->{$origResult}->{$origResult} = $innerValue;
                                        $this->iterator->{$origResult}->key           = $innerKey;
                                        foreach($xhtml->children() as $child) {
                                            $object->add($this->createObjects($child->getName(),$child));
                                        }
                                    }
                                    break;
                                }
                                //Look for nested repeats
                                if (isset($result[2])) {
                                    $origResult = $result[2];
                                    if ($result[2] === 'key') {
                                        $result[2] = $repeatKey;
                                    }
                                    foreach($this->iterator->{$repeat}->{$result[1]}->{$result[2]} as $innerKey => $innerValue) {
                                        $this->iterator->{$origResult}->{$origResult} = $innerValue;
                                        $this->iterator->{$origResult}->key           = $innerKey;
                                        foreach($xhtml->children() as $child) {
                                            $object->add($this->createObjects($child->getName(),$child));
                                        }
                                    }
                                    break;
                                }
                                //Look for nested repeats
                                if (isset($result[1])) {
                                    $origResult = $result[1];
                                    if ($result[1] === 'key') {
                                        $result[1] = $repeatKey;
                                    }
                                    if (isset($this->iterator->{$repeat}->{$result[1]})) {
                                        foreach($this->iterator->{$repeat}->{$result[1]} as $innerKey => $innerValue) {
                                            $this->iterator->{$origResult}      = $innerValue;
                                            $this->iterator->{$origResult}->key = $innerKey;
                                            foreach($xhtml->children() as $child) {
                                                $object->add($this->createObjects($child->getName(),$child));
                                            }
                                        }
                                    }
                                    break;
                                }
                                $this->iterator->{$repeat} = $repeatValue;
                                if (is_array($this->iterator->{$repeat})) {
                                    $this->iterator->{$repeat}['key'] = $repeatKey;
                                } else {
                                    @$this->iterator->{$repeat}->key = $repeatKey;
                                }
                                foreach($xhtml->children() as $child) {
                                    $object->add($this->createObjects($child->getName(),$child));
                                }
                                $this->autonumber++;
                            }
                        }
                        $this->createChildObjects = false;
                        break;
                    case 'chunk':
                        $this->value = str_replace(['     ','    ','   ','  '],' ',$value);
                        if (strpos($value,' size ') !== false) {
                            $parts       = explode(' size ',$value);
                            $dotName     = trim($parts[0]);
                            $chunkSize   = $parts[1];
                            $keyAndValue = explode(' as ',$chunkSize);
                            $chunkSize   = trim($keyAndValue[0]);
                            $newDataKey  = trim($keyAndValue[1]);
                            $chunks      = array_chunk($this->getData($dotName),$chunkSize);

                            foreach($chunks as $chunk) {
                                $this->iterator->{$newDataKey} = $chunk;
                                $this->setData($newDataKey,$chunk);
                                $this->iterator->key = $chunk;
                                if (is_array($chunk)) {
                                    $this->iterator->key = key($chunk);
                                }
                                foreach($xhtml->children() as $child) {
                                    $object->add($this->createObjects($child->getName(),$child));
                                }
                            }
                            $this->createChildObjects = false;
                        }
                        break;
                    case 'each':
                    case 'foreach':
                    case 'iterate':
                        $iKey             = '';
                        $this->autonumber = 1;
                        if (!isset($this->iterator)) {
                            $this->iterator = new \stdClass();
                        }
                        $simpleIteration = true;
                        $this->value     = str_replace(['     ','    ','   ','  '],' ',$value);

                        if (strpos($value,' as ') !== false) {
                            $parts       = explode(' as ',$value);
                            $dotName     = $parts[0];
                            $keyValue    = $parts[1];
                            $keyAndValue = explode(' => ',$keyValue);
                            $iValue      = $keyAndValue[0];
                            if (isset($keyAndValue[1])) {
                                $simpleIteration = false;
                                $iKey            = $keyAndValue[1];
                            }
                        } else {
                            return false;
                        }
                        if ($this->dataKeyExist($dotName)) {
                            if ($simpleIteration) {
                                $this->iterator->autonumber = $this->autonumber;
                                foreach($this->getData($dotName) as $iteratorValue) {
                                    $this->iterator->{$iValue} = $iteratorValue;
                                    $this->setData($iValue,$iteratorValue);
                                    $this->iterator->key = $iteratorValue;
                                    if (is_array($iteratorValue)) {
                                        $this->iterator->key = key($iteratorValue);
                                    }
                                    foreach($xhtml->children() as $child) {
                                        $object->add($this->createObjects($child->getName(),$child));
                                    }
                                    $this->autonumber++;
                                }
                                $this->createChildObjects = false;
                            } else {
                                $this->iterator->autonumber = $this->autonumber;
                                foreach($this->getData($dotName) as $iteratorKey => $iteratorValue) {
                                    $this->iterator->{$iKey} = $iteratorKey;
                                    $this->setData($iKey,$iteratorKey);
                                    $this->iterator->{$iValue} = $iteratorValue;
                                    $this->setData($iValue,$iteratorValue);
                                    foreach($xhtml->children() as $child) {
                                        $object->add($this->createObjects($child->getName(),$child));
                                    }
                                    $this->autonumber++;
                                }
                                $this->createChildObjects = false;
                            }
                            $this->autonumber++;
                            break;
                        }
                        break;
                    case 'if':
                        $eval = eval("return $value;");
                        if (!$eval) {
                            $this->createChildObjects = false;
                        }
                        break;
                    case 'src':
                        //$this->getPathFromSrc($value);
                        $srcFileName = $value;
                        /* Check if src starts with double slash '//' In this case it is actually starting with http but automatically selecting the current protocol. In this case no changes are made to the src */
                        if (strpos($value,'//') === 0) {
                            $srcFileName = $value;

                            /* This rule is the same as '//' it is just using a set constant to check the current protocol. */
                        } elseif (strpos($value,env('request.scheme')) === 0) {
                            $srcFileName = $value;

                            // Checks if the first char is a '/' which refers to the root but some servers won't use this correctly
                            // So in this case i'm replacing the '/' with the current project's HTTP Path
                        } elseif (strpos($value,'/') === 0) {
                            $srcFileName = str_replace(DS,'/',env('project.url').substr($value,1));
                            // Checks if a relative src is used and changes the src to the current projects url. strtolower because http and https is always in lc
                        } elseif (strpos(strtolower($value),env('request.scheme'),0) === false && env('base.path') != null) {
                            $srcFileName = str_replace(env('base.path'),'',env('project.path')).str_replace('/',DS,$value);
                        }

                        $object->{$key} = $srcFileName;
                        $object->setAttr($key,$srcFileName);
                        break;
                    case 'srcc':
                        $srcFileName = $value;
                        //if (strpos($value,'/app') === 0 && $tagName !== 'core\element\base\stylesheet' && $tagName !== 'core\element\base\javascript') {
                        if (strpos($value,'/app') === 0) {
                            if ($tagName == 'core\element\base\javascript') {
                                $srcFileName = env('core.url').$value;
                            } elseif ($tagName == 'core\element\base\stylesheet') {
                                $srcFileName = env('core.path').$value;
                            } else {
                                $srcFileName = str_replace(['/app','/'],['',DS],$value);
                            }
                        } elseif (strpos($value,'/') === 0) {
                            $srcFileName = str_replace(DS,'/',env('project.url').substr($value,1));
                        } elseif (strpos(strtolower($value),HTTP,0) === false) {
                            $srcFileName = str_replace(env('base.path'),'',env('project.path')).str_replace('/',DS,$value);
                        }
                        $object->{$key} = $srcFileName;
                        $object->setAttr($key,$srcFileName);
                        break;
                    case 'href':
                        if (strpos($value,'/') === 0) {
                            //$url = str_replace(DS,'/',project.url.substr($value,1));
                            //if (!urlExists($url))  {
                            $url = str_replace(DS,'/',env('base.url').substr($value,1));
                            //}
                            $value = $url;
                        }
                        $object->{$key} = $value;
                        $object->setAttr($key,$value);
                        break;
                    case 'action':
                        if ($tagName === 'form') {
                            if (strpos($value,'/') === 0) {
                                $value = str_replace(DS,'/',env('base.url').substr($value,1));
                            }
                        }
                        $object->{$key} = $value;
                        $object->setAttr($key,$value);
                        break;
                    default:
                        if (isset($object) && property_exists($object,strtolower($key))) {
                            if (isInstanceOf($value,'SimpleXMLElement')) {
                                $value = (string)$value;
                            }
                            $object->{strtolower($key)} = $value;
                        } else {
                            $trimmedValue = trim($value);
                            if ((!empty($trimmedValue) || $trimmedValue === '0' || $trimmedValue === 0) && method_exists($object,'addAttr')) {
                                $object->addAttr($key,$value);
                            }
                        }
                        break;
                }
            }
        }
        if (strlen((string)$xhtml[0]) > 0) {
            if (method_exists($object, 'add')) {
                $string = $this->parseString((string)$xhtml[0],$tagName);
                if (is_string($string) && (strpos($string, '${') !== false || strpos($string, '@{') !== false)) {
                    $string = $this->parseString($string,$tagName);
                }
                $object->add($string);
            }
        }
        /* If the xhtml object has any children it will set the current html object's
         * value to the child's value */
        if ($xhtml->count() > 0) {
            foreach ($xhtml->children() as $child) {
                if (property_exists($object,'createChildObjects')) {
                    $object->render();
                    $this->createChildObjects = $object->createChildObjects;
                }
                if ($this->createChildObjects) {
                    $object->add($this->createObjects($child->getName(), $child));
                }
            }
            $this->createChildObjects = true;
            return $object;
        }
        return $object;
    }

    public function getFunctionParamValue($param) {
        if (strpos($param, '{') !== false) {
            $param  = str_replace(['{','}'],'',$param);
            $result = explode('.', $param);
            $array  = $result[0];
            if (isset($result[1])) {
                $arrayKey = $result[1];
                return $this->iterator->{$array}->{$arrayKey};
            }
            return $this->iterator->{$array};
        }
        return $param;
    }

    public function replaceParameters($string) {
        if (preg_match_all("/\{([^\{]+?)\}/", $string, $match)) {
            return $match[1];
        }
        return [];
    }

    public function replacePropertyParameters($string) {
        if (preg_match_all('/\@{([^\{]+?)\}/', $string, $match)) {
            return $match[1];
        }
        return [];
    }

    public function parseString($string,$tagName = null) {
        /*| Do not perform this function on <style> Element and check that the attribute |
          | is not in the $this->ignoreAttributes array                                  |*/
        if (isset($tagName) && in_array($tagName,$this->ignoreTypes,false)) {
            return $string;
        }

        $propertyMatches = $this->replacePropertyParameters($string);
        if (count($propertyMatches) > 0) {
            foreach ($propertyMatches as $match) {
                $string = str_replace('@{'.$match.'}',$this->element->getProperty($match)??'', $string);
            }
        }
        $matches = $this->replaceParameters($string);
        if (count($matches) > 0) {
            $subMatchesFound = false;
            foreach ($matches as $match) {

                $found = false;
                $subMatches = explode('|', $match);

                if (count($subMatches) > 1) {
                    $subMatchesFound = true;
                }
                foreach ($subMatches as $innerMatch) {
                    if ($subMatchesFound) {
                        $string = str_replace('|','}${',$string);
                    }
                    if ($found === false) {
                        if ($this->dataKeyExist($innerMatch)) {
                            $found = true;
                            $dataValue = $this->getData($innerMatch);
                            if (is_string($dataValue)) {
                                $string = str_replace('${'.$innerMatch.'}',$dataValue,$string);
                            } else {
                                $string = str_replace('${'.$innerMatch.'}',json_encode($dataValue),$string);
                            }
                        } elseif (isset($this->constants->{$innerMatch})) {
                            $found = true;
                            $string = str_replace('${'.$innerMatch.'}',$this->constants->{$innerMatch},$string);
                        } elseif($this->dataKeyExist($innerMatch,$this->iterator)) {
                            $found = true;
                            $iteratorKeyFound = $this->getData($innerMatch,null,$this->iterator);
                            if (is_string($iteratorKeyFound)) {
                                $string = str_replace('${'.$innerMatch.'}',$iteratorKeyFound,$string);
                            } else {
                                $string = str_replace('${'.$innerMatch.'}',json_encode($iteratorKeyFound),$string);
                            }
                        }
                        // No matches where found set the match to ''
                        if ($found === false) {
                            if (strpos($innerMatch,"'") === 0 && substr($innerMatch,-1,1) === "'") {
                                $found = true;
                                //$innerMatch = str_replace("'",'',$innerMatch);
                                //pd($innerMatch);
                                $string = str_replace(['${'.$innerMatch.'}',"'"],[$innerMatch,''],$string);
                            } else {
                                $string = str_replace('${'.$innerMatch.'}','',$string);
                            }
                        }
                    } elseif ($subMatchesFound) {
                        $nextParams = $this->replaceParameters($string);
                        foreach ($nextParams as $paramKey => $paramValue) {
                            $string = str_replace('${'.$paramValue.'}','',$string);
                        }
                    }
                }
            }
        }
        // Check if any more ${} or @{} variables was parsed in from $this->output->data.
        // If any more are found they will be parsed as well
        if (is_string($string) && (strpos($string,'${') !== false || strpos($string,'@{') !== false)) {
            $string = $this->parseString($string,$tagName);
        }
        if (is_string($string)) {
            return $this->evalAllowedPHPFunction($string);
        }
        return $string;
    }

    private function getPathFromSrc($src = null) {
        if (!iset($src)) {
            return;
        }
        if (strpos('@',$src) !== false){
            $explodedSrc = explode('@',$src);
            //$path =
            //if (file_exists()) {
            //
            //}
        }
    }

    private function evalAllowedPHPFunction($string) {
        $deepestFunctionPos = 0;
        $deepestFunction    = null;
        $functionsFound     = 0;
        foreach(self::$allowedPHPFunctions as $phpFunction) {
            $pos = strpos($string,$phpFunction.'(');
            if ($pos !== false) {
                $functionsFound++;
                if ($pos >= $deepestFunctionPos) {
                    $deepestFunctionPos = $pos;
                    $deepestFunction    = $phpFunction;
                }
            }
        }
        if ($functionsFound > 0) {
            $argStart  = strpos($string,'(',$deepestFunctionPos)+1;
            $argEnd    = strpos($string,')',$argStart);
            $argLength = $argEnd - $argStart;
            $arguments = substr($string,$argStart,$argLength);
            @$string = str_replace($deepestFunction.'('.$arguments.')',call_user_func_array($deepestFunction,explode(',',$arguments)),$string);
        }
        if ($functionsFound > 1) {
            $string = $this->evalAllowedPHPFunction($string);
        }
        return $string;
    }

    private function displayXMLError($error) {
        $return = "\n";
        switch ($error->level) {
            case LIBXML_ERR_WARNING:
                $return .= "Warning $error->code: ";
            break;
             case LIBXML_ERR_ERROR:
                $return .= "Error $error->code: ";
            break;
            case LIBXML_ERR_FATAL:
                $return .= "Fatal Error $error->code: ";
            break;
        }
        $return .= trim($error->message) ."\n  Line: $error->line" ."\n  Column: $error->column";
        if (isset($this->viewObject->fileName)) {
            $return .= "\n  File: ".$error->file;
        }
        return $return."\n\n--------------------------------------------\n\n";
    }

    private function str_replace_first($from, $to, $content) {
        $from = '/'.preg_quote($from, '/').'/';

        return preg_replace($from, $to, $content, 1);
    }

    public function parseView() {
        $objects = [];
        if (isset($this->viewObject->xml)) {
            libxml_use_internal_errors($this->debug);
            // Using DomDocument to fix broken XML
            $dom = new SmartDOMDocument();
            /* Replace <script> tags with <jscript> (Element) tags because for some reason nothing inside <script> tags are getting parsed.
               The <jscript> element changes the <jscript> tag back to a <script> tag */
            $dom->loadHTML('<!doctype html>'.str_replace(['<script','</script'],['<jscript','</jscript'],$this->viewObject->xml));
            $doc = str_replace(['<?xml version="1.0" standalone="yes"?>','<!DOCTYPE html>'],'',$dom->saveXML());

            $xml = simplexml_load_string('<pml>'.$doc.'</pml>','SimpleXMLElement',LIBXML_COMPACT|LIBXML_NOERROR|LIBXML_NOEMPTYTAG);
            $newDoc = $doc;
            if (is_object($xml)) {
                $textNodes = $this->recursivelyFindTextNodes(dom_import_simplexml($xml));
                foreach($textNodes as $textNode) {
                    if (!empty(trim($textNode))) {
                        $newDoc = str_replace(trim($textNode),'<text.render.node>'.$textNode.'</text.render.node>',$newDoc);
                    }
                }
            }

            $xhtml = simplexml_load_string('<pml>'.$newDoc.'</pml>','SimpleXMLElement',LIBXML_COMPACT|LIBXML_NOERROR|LIBXML_NOEMPTYTAG);
            // Display error messages
            if ($this->debug) {
                $error = libxml_get_last_error();
                if (!empty($error)) {
                    $error->file = $this->viewObject->path.DS.$this->viewObject->fileName;
                    echo $this->displayXMLError($error);
                }
                libxml_clear_errors();
            }

            if ($xhtml !== null) {
                if (method_exists($xhtml,'children')) {
                    foreach ($xhtml->children() as $child) {
                        if ($this->createChildObjects) {
                            $objects[] = $this->createObjects($child->getName(), $child);
                        }
                    }
                }
                $this->html .= $this->addFileComment('start');
                if (count($objects) > 0) {
                    foreach ($objects as $object) {
                        $this->html .= $object->render();
                    }
                } else {
                    $this->html = '';
                }

                $this->html .= $this->addFileComment('end');
                $this->html = str_replace(self::$strReplaceElemArray,'',$this->html);
                $this->html = preg_replace($this->pregReplaceArray,'',$this->html);
                //$this->html = str_replace(['<script_tag>','</script_tag>'],array('<script>','</script>'),$this->viewObject->xml);
                if (strpos($this->html,'<!DOCTYPE html>') !== 'false') {

                    $this->html = str_replace('<html><head>','',$this->str_replace_first('</body></html>','',$this->str_replace_first('</head></html>','',str_replace('<html><body>','',$this->html))));
                }
            }
        }
    }
}
