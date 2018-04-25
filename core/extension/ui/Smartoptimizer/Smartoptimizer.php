<?php
/* SmartOptimizer v1.8
 * SmartOptimizer enhances your website performance using techniques
 * such as compression, concatenation, minifying, caching, and embedding on demand.
 * 
 * 2014-12-27
 * This script was modified by Charl Cronje to make it a class
 * 
 * Copyright (c) 2006-2010 Ali Farhadi (http://farhadi.ir/)
 * Released under the terms of the GNU Public License.
 * See the GPL for details (http://www.gnu.org/licenses/gpl.html).
 *
 * Author: Ali Farhadi (a.farhadi@gmail.com)
 * Website: http://farhadi.ir/
 */
namespace core\extension\ui\Smartoptimizer;

define('CACHE_DIR',env('core.extension.path').'layout'.DS.'Smartoptimizer'.DS.'cache'.DS);
class Smartoptimizer extends \core\Heepp {
    //base dir (a relative path to the base directory)
    private $baseDir = BASE_PATH;
    //Encoding of your js and css files. (utf-8 or iso-8859-1)
    private $charSet = 'utf-8';
    //Show error messages if any error occurs (true or false)
    private $debug = true;
    //use this to set gzip compression On or Off
    private $gzip = true;
    //use this to set gzip compression level (an integer between 1 and 9)
    private $compressionLevel = 9;
    //these types of files will not be gzipped nor minified
    private $gzipExceptions = array('gif','jpeg','jpg','png','swf','ico');
    //use this to set Minifier On or Off
    private $minify = true;
    //use this to set file concatenation On or Off
    private $concatenate = true;
    //specifies whether to emebed files included in css files using the data URI scheme or not (only CSS Files) 
    private $embed = true;
    //The maximum size of an embedded file. (use 0 for unlimited size)
    private $embedMaxSize = 5120;
    //these types of files will not be embedded
    private $embedExceptions = array('htc');
    //to set server-side cache On or Off
    private $serverCache = true;
    //if you change it to false, the files will not be checked for modifications and always cached files will be used (for better performance)
    private $serverCacheCheck = true;
    //cache dir
    private $cacheDir = CACHE_DIR;
    //prefix for cache files
    private $cachePrefix = 'so_';
    //to set client-side cache On or Off
    private $clientCache = false;
    //Setting this to false will force the browser to use cached files without checking for changes.
    private $clientCacheCheck = true;
    
    private $mimeTypes = array(
	"js"	=> "text/javascript",
	"css"	=> "text/css",
	"htm"	=> "text/html",
	"html"	=> "text/html",
	"xml"	=> "text/xml",
	"txt"	=> "text/plain",
	"jpg"	=> "image/jpeg",
	"jpeg"	=> "image/jpeg",
	"png"	=> "image/png",
	"gif"	=> "image/gif",
	"swf"	=> "application/x-shockwave-flash",
	"ico"	=> "image/x-icon",
    );
    
    private $files = array();
    private $filesmtime = null;
    public $query = null;
    private $fileTypes = array();
    private $fileType = null;
    private $cashedFileName = null;

    function __construct() {
        $this->cashedFileName = $this->startOptimizer();
    }
    
    public function __toString() {
        return $this->cashedFileName;
    }
    
    function headerExit($status) {
        header("HTTP/1.0 $status");
        exit();
    }

    function headerNoCache() {
        // already expired
        header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");

        // always modified
        header("Last-Modified: " . $this->gmdatestr());

        // HTTP/1.1
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Cache-Control: max-age=0", false);

        // HTTP/1.0
        header("Pragma: no-cache");

        //generate a unique Etag each time
        header('Etag: '.microtime());
    }

    function headerNeverExpire(){
        header("Expires: " . $this->gmdatestr(time() + 315360000));
        header("Cache-Control: max-age=315360000");
    }

    function debugExit($msg){
        if (!$this->debug) {
            $this->headerExit('404 Not Found');
        }
        $this->headerNoCache();
        header('Content-Type: text/html; charset='.$this->charSet);
        header("Content-Encoding: none");
        $this->setError('SmartOptimizer Error: '.$msg);
        exit();
    }

    function gmdatestr($time = null) {
        if (is_null($time)) { 
            $time = time();
        }
        return gmdate("D, d M Y H:i:s", $time) . " GMT";
    }

    function filesmtime() {
        if ($this->filesmtime) {
            return $this->filesmtime;
        } else {
            $this->filesmtime = filemtime('index.php');
        }
        foreach ($this->files as $file) {
            if (!file_exists($file->path.$file->fileName)) {
                $this->debugExit("File not found (".$file->path.$file->fileName.")");
            }
            $this->filesmtime = max(filemtime($file->path.$file->fileName),$this->filesmtime);
        }
        return $this->filesmtime;
    }
    
    function convertUrl($url, $count) {
        return $url;
        
        $baseUrl = $this->baseDir;

        $url = trim($url);

        if (preg_match('@^[^/]+:@', $url)) {
            echo $url;
        }

        $this->fileType = substr(strrchr($url, '.'), 1);
        if (isset($this->mimeTypes[$this->fileType]))
            $mimeType = $this->mimeTypes[$this->fileType];
        elseif (function_exists('mime_content_type'))
            $mimeType = mime_content_type($url);
        else
            $mimeType = null;

        if (!$this->embed ||
                !file_exists($this->fileDir . $url) ||
                ($this->embedMaxSize > 0 && filesize($this->fileDir . $url) > $this->embedMaxSize) ||
                !$this->fileType ||
                in_array($this->fileType, $this->embedExceptions) ||
                !$mimeType ||
                $count > 1) {
            if (strpos($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME'] . '?') === 0 ||
                    strpos($_SERVER['REQUEST_URI'], rtrim(dirname($_SERVER['SCRIPT_NAME']), '\/') . '/?') === 0) {
                if (!$baseUrl)
                    return $this->fileDir . $url;
            }
            return $baseUrl . $url;
        }
        
        $contents = file_get_contents($this->fileDir . $url);

        if ($this->fileType == 'css') {
            $oldFileDir = $this->fileDir;
            $this->fileDir = rtrim(dirname($this->fileDir . $url), '\/') . '/';
            $oldBaseUrl = $baseUrl;
            $baseUrl = 'http' . (@$_SERVER['HTTPS'] ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . rtrim(dirname($_SERVER['SCRIPT_NAME']), '\/') . '/' . $fileDir;
            $contents = minify_css($contents);
            $this->fileDir = $oldFileDir;
            $baseUrl = $oldBaseUrl;
        }

        $base64 = base64_encode($contents);
        return 'data:' . $mimeType . ';base64,' . $base64;
    }

    function minify_css($str) {
        $res = '';
        $i = 0;
        $inside_block = false;
        $current_char = '';
        while ($i + 1 < strlen($str)) {
            if ($str[$i] == '"' || $str[$i] == "'") {//quoted string detected
                $res .= $quote = $str[$i++];
                $url = '';
                while ($i < strlen($str) && $str[$i] != $quote) {
                    if ($str[$i] == '\\') {
                        $url .= $str[$i++];
                    }
                    $url .= $str[$i++];
                }
                if (strtolower(substr($res, -5, 4)) == 'url(' || strtolower(substr($res, -9, 8)) == '@import ') {
                    $url = $this->convertUrl($url, substr_count($str, $url));
                }
                $res .= $url;
                $res .= $str[$i++];
                continue;
            } elseif (strtolower(substr($res, -4)) == 'url(') {//url detected
                $url = '';
                do {
                    if ($str[$i] == '\\') {
                        $url .= $str[$i++];
                    }
                    $url .= $str[$i++];
                } while ($i < strlen($str) && $str[$i] != ')');
                $url = $this->convertUrl($url, substr_count($str, $url));
                $res .= $url;
                $res .= $str[$i++];
                continue;
            } elseif ($str[$i] . $str[$i + 1] == '/*') {//css comment detected
                $i+=3;
                while ($i < strlen($str) && $str[$i - 1] . $str[$i] != '*/')
                    $i++;
                if ($current_char == "\n")
                    $str[$i] = "\n";
                else
                    $str[$i] = ' ';
            }

            if (strlen($str) <= $i + 1)
                break;

            $current_char = $str[$i];

            if ($inside_block && $current_char == '}') {
                $inside_block = false;
            }

            if ($current_char == '{') {
                $inside_block = true;
            }

            if (preg_match('/[\n\r\t ]/', $current_char))
                $current_char = " ";

            if ($current_char == " ") {
                $pattern = $inside_block ? '/^[^{};,:\n\r\t ]{2}$/' : '/^[^{};,>+\n\r\t ]{2}$/';
                if (strlen($res) && preg_match($pattern, $res[strlen($res) - 1] . $str[$i + 1]))
                    $res .= $current_char;
            } else
                $res .= $current_char;

            $i++;
        }
        if ($i < strlen($str) && preg_match('/[^\n\r\t ]/', $str[$i]))
            $res .= $str[$i];
        return $res;
    }
    
    function minify_js($str) {
        $res = '';
        $maybe_regex = true;
        $i = 0;
        $current_char = '';
        while ($i + 1 < strlen($str)) {
            if ($maybe_regex && $str[$i] == '/' && $str[$i + 1] != '/' && $str[$i + 1] != '*' && @$str[$i - 1] != '*') {//regex detected
                if (strlen($res) && $res[strlen($res) - 1] === '/')
                    $res .= ' ';
                do {
                    if ($str[$i] == '\\') {
                        $res .= $str[$i++];
                    } elseif ($str[$i] == '[') {
                        do {
                            if ($str[$i] == '\\') {
                                $res .= $str[$i++];
                            }
                            $res .= $str[$i++];
                        } while ($i < strlen($str) && $str[$i] != ']');
                    }
                    $res .= $str[$i++];
                } while ($i < strlen($str) && $str[$i] != '/');
                $res .= $str[$i++];
                $maybe_regex = false;
                continue;
            } elseif ($str[$i] == '"' || $str[$i] == "'") {//quoted string detected
                $quote = $str[$i];
                do {
                    if ($str[$i] == '\\') {
                        $res .= $str[$i++];
                    }
                    $res .= $str[$i++];
                } while ($i < strlen($str) && $str[$i] != $quote);
                $res .= $str[$i++];
                continue;
            } elseif ($str[$i] . $str[$i + 1] == '/*' && @$str[$i + 2] != '@') {//multi-line comment detected
                $i+=3;
                while ($i < strlen($str) && $str[$i - 1] . $str[$i] != '*/')
                    $i++;
                if ($current_char == "\n")
                    $str[$i] = "\n";
                else
                    $str[$i] = ' ';
            } elseif ($str[$i] . $str[$i + 1] == '//') {//single-line comment detected
                $i+=2;
                while ($i < strlen($str) && $str[$i] != "\n" && $str[$i] != "\r") {
                    $i++;
                }
            }

            $LF_needed = false;
            if (preg_match('/[\n\r\t ]/', $str[$i])) {
                if (strlen($res) && preg_match('/[\n ]/', $res[strlen($res) - 1])) {
                    if ($res[strlen($res) - 1] == "\n")
                        $LF_needed = true;
                    $res = substr($res, 0, -1);
                }
                while ($i + 1 < strlen($str) && preg_match('/[\n\r\t ]/', $str[$i + 1])) {
                    if (!$LF_needed && preg_match('/[\n\r]/', $str[$i]))
                        $LF_needed = true;
                    $i++;
                }
            }

            if (strlen($str) <= $i + 1)
                break;

            $current_char = $str[$i];

            if ($LF_needed)
                $current_char = "\n";
            elseif ($current_char == "\t")
                $current_char = " ";
            elseif ($current_char == "\r")
                $current_char = "\n";

            // detect unnecessary white spaces
            if ($current_char == " ") {
                if (strlen($res) &&
                        (
                        preg_match('/^[^(){}[\]=+\-*\/%&|!><?:~^,;"\']{2}$/', $res[strlen($res) - 1] . $str[$i + 1]) ||
                        preg_match('/^(\+\+)|(--)$/', $res[strlen($res) - 1] . $str[$i + 1]) // for example i+ ++j;
                        ))
                    $res .= $current_char;
            } elseif ($current_char == "\n") {
                if (strlen($res) &&
                        (
                        preg_match('/^[^({[=+\-*%&|!><?:~^,;\/][^)}\]=+\-*%&|><?:,;\/]$/', $res[strlen($res) - 1] . $str[$i + 1]) ||
                        (strlen($res) > 1 && preg_match('/^(\+\+)|(--)$/', $res[strlen($res) - 2] . $res[strlen($res) - 1])) ||
                        (strlen($str) > $i + 2 && preg_match('/^(\+\+)|(--)$/', $str[$i + 1] . $str[$i + 2])) ||
                        preg_match('/^(\+\+)|(--)$/', $res[strlen($res) - 1] . $str[$i + 1])// || // for example i+ ++j;
                        ))
                    $res .= $current_char;
            } else
                $res .= $current_char;

            // if the next charachter be a slash, detects if it is a divide operator or start of a regex
            if (preg_match('/[({[=+\-*\/%&|!><?:~^,;]/', $current_char))
                $maybe_regex = true;
            elseif (!preg_match('/[\n ]/', $current_char))
                $maybe_regex = false;

            $i++;
        }
        if ($i < strlen($str) && preg_match('/[^\n\r\t ]/', $str[$i]))
            $res .= $str[$i];
        return $res;
    }
    
    function getAbsolutePath($path) {
        $path = str_replace(array('/', '\\'), DIRECTORY_SEPARATOR, $path);
        $parts = array_filter(explode(DIRECTORY_SEPARATOR, $path), 'strlen');
        $absolutes = array();
        foreach ($parts as $part) {
            if ('.' == $part) continue;
            if ('..' == $part) {
                array_pop($absolutes);
            } else {
                $absolutes[] = $part;
            }
        }
        return implode(DIRECTORY_SEPARATOR, $absolutes);
    }

    function startOptimizer() {
        //Get Query String
        list($this->query) = explode('?', urldecode($_GET['params']));
        $files = explode($this->separator,$this->query);
        
        /*
         * $matchResult[0] = Full path of file
         * $matchResult[1] = Folder of file
         * $matchResult[2] = FileName of File
         */
        $i = 0;
        foreach($files as $file) {
            $this->files[$i] = new \stdClass();
            if (preg_match('/^\/?(.+\/)?(.+)$/',$file,$matchResult)) {
                $this->files[$i]->fullPath = str_replace(env('http.host'),'',$matchResult[0]);
                $this->files[$i]->path = $this->baseDir.str_replace(env('http.host'),'',$matchResult[1]);
                $this->files[$i]->fileName = str_replace(env('http.host'),'',$matchResult[2]);
                $this->files[$i]->fileType = substr(strrchr(str_replace(env('http.host'),'',$matchResult[2]),'.'),1);
            } else { 
                $this->debugExit("Invalid file name ($this->query)");
            }
            //Check if the folder is part of the realpath($this->baseDir)
            if (strpos(realpath($this->files[$i]->path),realpath($this->baseDir)) !== 0)  {
                $this->debugExit("File is out of base directory");
            } else {
                $i++;
            }
        }
        
        //Check if all the files should be joined together
        if ($this->concatenate) {
            $this->concatenate = count($this->files) > 1;
        }
        
        //Check for unsupported filenames
        foreach ($this->files as $key => $file) {
            if (preg_match('/^[^\x00]+\.([a-z0-9]+)$/i',$file->path.$file->fileName,$matchResult)) {
                $this->fileTypes[] = strtolower($matchResult[1]);
            } else {
                $this->debugExit("Unsupported file (".$this->files[$key]->fullPath.")");
            }
        }
        
        //Check that all the files are of the same type
        if ($this->concatenate) {
            if (count(array_unique($this->fileTypes)) > 1) {
                $this->debugExit("Files must be of the same type.");
            }
        }

        //Set The fileType to the first of fileTypes (since all of them are the same)
        $this->fileType = $this->fileTypes[0];
        
        //Check that the fileType is a supported mimeType
        if (!isset($this->mimeTypes[$this->fileType])) {
            if ($this->fileType == 'gz' && file_exists($this->files[0]->path.$this->files[0]->fileName)) {
                header("Content-Encoding: gzip");
                header('Content-Length: ' . filesize($this->files[0]->path.$this->files[0]->fileName));
                readfile($this->files[0]->path.$this->files[0]->fileName);
                return true;
            }
            $this->debugExit("Unsupported file type (".$this->fileType.")");
        }
        
        //Set The header mimeType to the mimeType of the selected fileType
        header("Content-Type: {$this->mimeTypes[$this->fileType]}; charset=".$this->charSet);
        /*
         * Check the fileType is not one of the gzipExceptions
         * Check if gzip is one of the accepted encodings ($_SERVER['HTTP_ACCEPT_ENCODING'])
         * Check if the gzip function exists (function_exists('gzencode'))
         */
        $this->gzip = ($this->gzip && !in_array($this->fileType,$this->gzipExceptions) && in_array('gzip',array_map('trim',explode(',',@$_SERVER['HTTP_ACCEPT_ENCODING']))) &&	function_exists('gzencode'));

        //Set the Content Encoding to gzip if gzip is enabled
        if ($this->gzip) {
            if (!COMBINE_INCLUDES && !$this->concatenate) {
            
            } else {
                header("Content-Encoding: gzip");
            }
        }
        
        /*
         * Build the function name to minify the relative fileType
         * minify_css or minify_js
         */
        $minifyFunction = 'minify_'.$this->fileType;
        
        //Check if the method to minify exists
        $this->minify = $this->minify && method_exists($this,$minifyFunction);
        
        //Check if url() in css should be encoded to base64
        $this->embed = $this->embed && $this->fileType == 'css' && (!preg_match('/msie/i', $_SERVER['HTTP_USER_AGENT']) || preg_match('/msie 8|opera/i', $_SERVER['HTTP_USER_AGENT']));
        
        //Check if serverCache is enabled and at least 1 of: minify, gzip, concatenate, embed
        $this->serverCache = $this->serverCache && ($this->minify || $this->gzip || $this->concatenate || $this->embed);
        if ($this->serverCache) {
            $cachedFile = $this->cacheDir.$this->cachePrefix.md5($this->query.($this->embed?'1':'0')).'.'.$this->fileType.($this->gzip ? '.gz' : '');
            $generateContent = ((!$this->serverCache && (!$this->clientCache || !$this->clientCacheCheck || !isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) || $_SERVER['HTTP_IF_MODIFIED_SINCE'] != $this->gmdatestr($this->filesmtime()))) || ($this->serverCache && (!file_exists($cachedFile) || ($this->serverCacheCheck && $this->filesmtime() > filemtime($cachedFile)))));
        }

        //if $this->clientCache == true and $this->clientCacheCheck == true
        if ($this->clientCache && $this->clientCacheCheck) {
            //if $this->serverCache == true
            //$generateContent == false
            if ($this->serverCache && !$generateContent) {
                $mtime = filemtime($cachedFile);
            } elseif ($this->serverCache) {
                $mtime = time();
            } else {
                $mtime = $this->filesmtime();
                $mtimestr = gmdatestr($mtime);
            }
        }
        
        if (!$this->clientCache || !$this->clientCacheCheck || !isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) || $_SERVER['HTTP_IF_MODIFIED_SINCE'] != $mtimestr) {
            if ($this->clientCache && $this->clientCacheCheck) {
                header("Last-Modified: " . $mtimestr);
                header("Cache-Control: must-revalidate");
            } elseif ($this->clientCache) {
                $this->headerNeverExpire();
            } else {
                $this->headerNoCache();
            }

            if ($generateContent) {
                if ($this->minify) {
                    $minifyFunction = 'minify_'.$this->fileType;
                }
                $content = array();
                foreach ($this->files as $file) {
                    (($content[] = @file_get_contents($file->path.$file->fileName)) !== false) || $this->debugExit("File not found (".$file->fullPath.")");
                }

                $content = implode("\n", $content);
                if ($this->minify) {
                    $content = $this->$minifyFunction($content);
                }

                if ($this->gzip) {
                    $content = gzencode($content, $this->compressionLevel);
                }
                
                if ($this->serverCache || (COMBINE_INCLUDES && $this->concatenate)) {
                    file_put_contents($cachedFile, $content);
                    return $cachedFile;
                }
                
                header('Content-Length: ' . strlen($content));
                echo $content;
            } else {
                if (COMBINE_INCLUDES && $this->concatenate) {
                    return $cachedFile;
                } else {
                    header('Content-Length: ' . filesize($cachedFile));
                    readfile($cachedFile);
                }
            }
        } else {
            if (COMBINE_INCLUDES && $this->concatenate) {
                return $cachedFile;
            } else {
                headerExit('304 Not Modified');
            }
        }
    }
}
