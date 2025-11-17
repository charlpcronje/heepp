<?php
namespace core;

class library extends Heepp {
    public $library;
    public $alias;
    public $status         = 'active';
    public $authentication = false;
    public $namespaces     = [];
    public $explain        = false;
    public $bundles        = [];
    public $jscripts       = [];
    /*
     * If a section is set it will only load that specific section.
     * If no section is specified it will load all the sections.
     * More than one section can be specified by comma separating them.
     */
    public $section;
    public $sections       = [];

    public function __construct() {
        parent::__construct();
    }

    /*
     * TO-DO: Explain Library
     */
    public function explainLibrary($libraries,$library) {
        
    }
    
    public function minifyCSS($css) {
        // Remove comments
        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);
        // Remove whitespace and space after colons
        $css = str_replace([': ',"\r\n","\r","\n","\t",'  ','    ','    '],[':','','','','','','',''],$css);
        return $css;
    }
    
    public function minifyJS($js) {
        //return extension\helper\JShrink::minify($js);
        if (strpos('`',$js) === false) {
            //remove comments
            //$js = preg_replace('/((?:\/\*(?:[^*])*\*+\/)|(?:\/\/ .*))/','', $js);
            //remove tabs, spaces, newlines, etc
            $js = str_replace(["\r\n","\r","\t","\n",'  ','    ','     '],'',$js);
            
        } else {
            //remove comments
            $js = preg_replace('/((?:\/\*(?:[^*])*\*+\/)|(?:\/\/.*))/','', $js);
            //remove tabs, spaces, newlines, etc
            $js = str_replace(["\r\n","\r","\t","\n",'  ','    ','     '],'',$js);
        }
        //remove other spaces before/after
        $js = preg_replace(['(( )+\))','(\)( )+)'], ')', $js);
        return $js;
    }
    
    public function gzipContent($fileContents) {
        //Check if Browser Supports gzip
        if (strpos($_SERVER['HTTP_ACCEPT_ENCODING'],'zip') !== false) {
            //Check if the gzip function is available to php
            if (function_exists('gzencode')) {
                //header("Content-Encoding: gzip");
                $fileContents = gzencode($fileContents);
            }
        }
        return $fileContents;
    }
    
    public function unGzipContent($fileContents) {
        //Check if the gzip function is available to php
        if (function_exists('gzdecode')) {
            //header("Content-Encoding: gzip");
            $fileContents = gzdecode($fileContents);
        }
        return $fileContents;
    }
    
    public function loadLibrary() {
        //Load all library headers
        $libraries = loadXML(env('core.library.path').'libraries.xml',LIBXML_NOCDATA | LIBXML_HTML_NOIMPLIED);
        $this->alias = (string)$libraries->xpath($this->library.'/@alias')[0];
        $this->status = (string)$libraries->xpath($this->library.'/@status')[0];
        $this->authentication = (string)$libraries->xpath($this->library.'/@authentication')[0];

        //Load All Library Section Headers if $this->section is empty
        if (empty($this->section)) {
            $library = $this->library;
            foreach ($libraries->$library->children() as $section) {
                //Library Section
                $this->sections[$section->getName()] = [
                    'alias' => (string)$section->xpath('./@alias')[0],
                    'authentication' => (string)$section->xpath('./@authentication')[0],
                    'status' => (string)$section->xpath('./@status')[0]];
            }
        } else {
            //Load only the sections specified in $this->section
            $sections = explode(',',$this->section);
                foreach($sections as $section) {
                    $this->sections[$section] = [
                        'alias' => (string)$libraries->xpath($this->library.'/@alias')[0],
                        'authentication' => (string)$libraries->xpath($this->library.'/@authentication')[0],
                        'status' => (string)$libraries->xpath($this->library.'/@status')[0]];
            }
        }        
        //Load Specific Library
        $library = loadXML(env('core.library.path').$this->library.DS.$this->library.'.xml',LIBXML_NOCDATA | LIBXML_HTML_NOIMPLIED);
        $this->namespaces = $library->getNamespaces(true);

        //Load Library Sections
        foreach($this->sections as $key => $section) {
            //Check for Pre-Loads
            if ($library->xpath($key.'/preload')) {
                $this->sections[$key]['preload'] = [];
                foreach($library->$key->preload->children() as $preload) {
                    $this->sections[$key]['preload'][][$preload->getName()] = [
                        'library'=> (string)$preload['library'],
                        'section'=> (string)$preload['section']];
                }
            }
            
            //Check for any includes
            if ($library->xpath($key.'/includes')) {
                $this->sections[$key]['includes'] = [];
                foreach($library->$key->includes->children() as $include) {
                    $this->sections[$key]['includes'][][$include->getName()] = [
                        'src'=> (string)$include['src'],
                        'cdn'=> (string)$include['cdn'],
                        'description'=> (string)$include['description'],
                        'bundle'=> (string)$include['bundle'],
                        'minify'=> (string)$include['minify'],
                        'gzip'=> (string)$include['gzip']];
                }
            }
            
            //Check for any elements
            if ($library->xpath($key.'/elements')) {
                //General Attributes for All Elements of This Section of This Library
                $this->sections[$key]['elements'] = [];
                $this->sections[$key]['elements']['tag'] = (string)$library->xpath($key.'/elements/@tag')[0];
                $this->sections[$key]['elements']['namespace'] = (string)$library->xpath($key.'/elements/@namespace')[0];
                $this->sections[$key]['elements']['class'] = (string)$library->xpath($key.'/elements/@class')[0];
                $this->sections[$key]['elements']['prefix'] = (string)$library->xpath($key.'/elements/@prefix')[0];
                $this->sections[$key]['elements']['authentication'] = (string)$library->xpath($key.'/elements/@authentication')[0];
                $this->sections[$key]['elements']['status'] = (string)$library->xpath($key.'/elements/@status')[0];
                $this->sections[$key]['elements']['tags'] = [];
                
                //Create new elements
                foreach($library->$key->elements->children() as $element) {
                    if ($element->xpath('./@alias')) {
                        $tag = (string)$element->xpath('./@alias')[0];
                    } else {
                        $tag = $element->getName();
                    }
                    $this->sections[$key]['elements']['tags'][$tag] = [
                        'description'=> (string)$element->xpath('./@description')[0],
                        'authentication'=> (string)$element->xpath('./@authentication')[0],
                        'status'=> (string)$element->xpath('./@status')[0]];
                    if ($element->xpath('./params')) {
                        foreach($element->params->children() as $param) {
                            //Get parameter description
                            if ($param->xpath('./@description')) {
                                $this->sections[$key]['elements']['tags'][$tag]['params'][$param->getName()]['description'] = (string)$param->xpath('./@description')[0];
                            }
                            //Default value of parameter
                            if ($param->xpath('./@default')) {
                                $this->sections[$key]['elements']['tags'][$tag]['params'][$param->getName()]['default'] = (string)$param->xpath('./@default')[0];
                            }
                            //Check expecter type (int,enum,array...) of parameter
                            if ($param->xpath('./@type')) {
                                $type = (string)$param->xpath('./@type')[0];
                                $this->sections[$key]['elements']['tags'][$tag]['params'][$param->getName()]['type'] = $type;
                                if ($param->xpath('./'.$type)) {
                                    foreach($param->$type->children() as $typeOptions) {
                                        $this->sections[$key]['elements']['tags'][$tag]['params'][$param->getName()][$type][$typeOptions->getName()] = (string)$typeOptions['description'];
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        //$library = new extension\ui\CoreUI($this->library.DIRECTORY_SEPARATOR.$this->library.'.xml');
    }

    public function render() {
        if ($this->dataKeyExist('session.libraries.'.$this->library)) {
            return false;
        }
        $this->session('libraries.'.$this->library,true);
        $this->loadLibrary();

        $html = '';
        $css = '';
        $js = '';
        foreach($this->sections as $section) {
            if (isset($section['preload'])) {
                foreach($section['preload'] as $preload) {
                    if (array_key_exists('core-library',$preload)) {
                        $library = new library();
                        $library->library = $preload['core-library']['library'];
                        if (!empty($preload['core-library']['section'])) {
                            $library->section = $preload['core-library']['section'];
                        }
                        $html .= $library->render();
                    }
                }
            }
            
            if (isset($section['includes'])) {
                foreach($section['includes'] as $include) {
                    if (array_key_exists('jscript',$include)) {
                        // Check if the file should be bundled with other files
                        if (!empty($include['jscript']['bundle'])) {
                            $srcPath = env('core.library.path').$this->library.DS.str_replace('/',DS,$include['jscript']['src']);
                            $srcContents = file_get_contents($srcPath);

                            // Check if file contents should be minified
                            if (!empty($include['jscript']['minify']) && $include['jscript']['minify'] == 'true') {
                                $include['jscript']['bundle'] = str_replace('.js','.min.js',$include['jscript']['bundle']);
                                $srcContents = $this->minifyJS($srcContents);
                            }
                            // Check if file contents Should be gzipped
                            /* TO DO: Fix gzip (The browser is receiving the wrong header information at the moment.
                             * The Content-Encoding header must be set to gzip by apache or php when leading the JS file */
                            if (!empty($include['jscript']['gzip']) && $include['jscript']['gzip'] == 'true') {
                                $include['jscript']['bundle'] = str_replace('.js','.gzipped.js',$include['jscript']['bundle']);
                            }
                            $srcContents = '//----------'.$include['jscript']['description']."----------\r\n\r\n".$srcContents."\r\n";
                            $bundlePath = env('core.library.path').$this->library.DS.str_replace('/',DS,$include['jscript']['bundle']);
                            
                            if (!in_array($include['jscript']['bundle'],$this->bundles)) {
                                $this->bundles[] = $include['jscript']['bundle'];
                                // Check if file contents should be gzipped
                                if (!empty($include['jscript']['gzip']) && $include['jscript']['gzip'] == 'true') {
                                    $srcContents = $this->gzipContent($srcContents);
                                }
                                file_put_contents($bundlePath,$srcContents);
                            } else {
                                if (!empty($include['jscript']['gzip']) && $include['jscript']['gzip'] == 'true') {
                                    $existingContent = $this->unGzipContent(file_get_contents($bundlePath));
                                    $srcContents = $this->gzipContent($existingContent.$srcContents);
                                    file_put_contents($bundlePath,$srcContents);
                                } else {
                                    file_put_contents($bundlePath,$srcContents,FILE_APPEND);
                                }
                            }
                            $include['jscript']['src'] = $include['jscript']['bundle'];
                        }
                        
                        if (!in_array($include['jscript']['src'],$this->jscripts) && !in_array($include['jscript']['cdn'],$this->jscripts)) {
                            $tag = new Element('script');
                            if (!empty($include['jscript']['cdn']) && urlExists($include['jscript']['cdn'])) {
                                $tag->attr('src',$include['jscript']['cdn']);
                            } else {
                                $tag->attr('src',env('core.library.url').$this->library.'/'.$include['jscript']['src']);
                            }
                            
                            if (!empty($include['jscript']['gzip']) && $include['jscript']['gzip'] == 'true') {
                                $tag->attr('type','gzipped');
                            }
                            $html .= $tag->render();
                            $js = $html;
                            
                            if (!empty($include['jscript']['cdn']) && urlExists($include['jscript']['cdn'])) {
                                $this->jscripts[] = $include['jscript']['cdn'];
                            } else {
                                $this->jscripts[] = $include['jscript']['src'];
                            }
                        }
                    }
                    if (array_key_exists('css',$include)) {
                        $tag = new Element('link');
                        $tag->attr('rel','stylesheet');
                        $tag->attr('type','text/css');
                        
                        if (urlExists($include['css']['cdn'])) {
                            $tag->attr('href',$include['css']['cdn']);
                        } elseif(strpos($include['css']['src'],'http') !== false) {
                            $tag->attr('href',$include['css']['src']);
                        } else {
                            $tag->attr('href',env('core.library.url').$this->library.'/'.$include['css']['src']);
                        }
                        $html .= $tag->render();
                        $css = $tag->render();
                    }
                }
            }
        }

        if ($this->inputSet('controller')) {
            $this->appendHtml('head',$css);
            $this->appendHtml('body',$js);
        }
        return $html;
    }
}
