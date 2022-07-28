<?php
namespace core\extension\element\traits;

trait importEncoding {
    //Send Error Message to Exception Handler and the script will stop after the exception is handled
    public function createError($message) {
        throw new \Exception($message);
    }

    //CSS Minifier
    public function minifyCSS(array $cssFiles = []) {
        $css = '';
        foreach ($cssFiles as $cssFile) {
            $css .= file_get_contents($cssFile);
        }

        $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);                         // Remove comments
        // Remove space after colons
        $css = str_replace(array(': ',"\r\n","\r","\n","\t",'  ','    ','    '),array(':','','','','','','',''),$css);    // Remove whitespace

        return $css;                                                                             // return combined output
    }
    
    //JS Minifier
    public function minifyJS($files = array()) {
        $js = "";

        foreach ($files as $file) {
            $js .= file_get_contents($file);
        }

        $js = preg_replace("/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/", "", $js);     //remove comments
        $js = str_replace(array("\r\n", "\r", "\t", "\n", '  ', '    ', '     '), '', $js);     //remove tabs, spaces, newlines, etc
        $js = preg_replace(array('(( )+\))', '(\)( )+)'), ')', $js);                            //remove other spaces before/after
        return $js;
    }
    
    public function characterSetEncode($charset) {
        //Check if Character Set Encoding Must Be Done
        if ($this->characterSetEncodeFile && isset($this->element->mimeTypeDetails['charsetUpdatable']) && $this->element->mimeTypeDetails['charsetUpdatable'] && $charset != $this->element->characterSet) {
            $this->element->oldCharacterSet = $this->element->characterSet;
            
            //Set file content to the encoded string of the file
            $this->element->fileContent = mb_convert_encoding(file_get_contents($this->element->path.$this->element->fileName),$this->characterSet,$this->element->oldCharacterSet);
            
            //Add Conversion To Element
            $this->element->fileContentConversions['charset'][] = $charset;
            
            $this->element->characterSet = $this->characterSet; 
        }  else {
            $this->element->fileContent = file_get_contents($this->element->path.$this->element->fileName);
        }
    }

    public function minifyFileContent() {
        //Check $this->minify == true && if mimeType can be minified and if it is selected to do so
        if ($this->minify && isset($this->element->mimeTypeDetails['minify']) && $this->element->mimeTypeDetails['minify'] && $this->element->minify) {
            $this->element->minify = true;
            
            //If Element is Already Minified, Return
            if ($this->element->minified) {
                return true;
            }
            
            //Use minifier specified by mimeType
            $minifierFunction = $this->element->mimeTypeDetails['minifier'];
            $this->element->fileContent = $this->$minifierFunction(array($this->element->path.$this->element->fileName));
            $this->element->fileContentConversions['minified'][] = true;
        } else {
            if ($this->element->minified) {
                $this->element->fileContentConversions['minified'][] = false;
            }
            $this->element->minify = false;
            $this->element->minified = false;

            $this->element->fileContent = file_get_contents($this->element->path.$this->element->fileName);
        }
    }
    
    public function gzipFileContent() {
        //Check if all the conditions are met for gzip and do it
        if ($this->gzip && isset($this->element->mimeTypeDetails['gzip']) && $this->element->mimeTypeDetails['gzip'] && $this->element->gzip) {
            //If the element is already gzipped, return. 
            if ($this->element->gzipped) {
                return true;
            }
            if (in_array('zip',explode(',',trim($_SERVER['HTTP_ACCEPT_ENCODING'])))) {   //Check if Browser Supports gzip
                $this->clientBrowserGzipCompatible = true;
                $this->element->clientBrowserGzipCompatible = true;
                if (\function_exists('gzencode')) {                                      //Check if the gzip function is available to php
                    $this->element->gzip = true;
                    header("Content-Encoding: gzip");
                    $this->element->fileContent = gzencode($this->element->fileContent,$this->gzipCompressionLevel);
                    $this->element->gzipped = true;
                    $this->element->fileContentConversions['gzipped'] = true;
                }
            } else {
                $this->element->gzip = false;
                $this->element->fileContentConversions['gzipped'] = false;
            }
        } else {
            $this->element->gzip = false;
            $this->element->fileContentConversions['gzipped'] = false;
        }
    }
}
