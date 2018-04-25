<?php
namespace core\extension\helper;

class Asset extends \core\extension\Extension {
    public $assetsPath;
    public $tempPath;
    public $storagePath;
    public $path;
    public $realpath;
    private $found = false;
    public $possibleSearchPaths = [
        'jpg'  => 'images',
        'png'  => 'images',
        'jpeg' => 'images',

        'json' => 'js',
        'js'   => 'js',

        'css'  => 'css',
        'less' => 'css',
        'scss' => 'css',

        'mp4'  => 'video',
        'webm' => 'video',
        'ogg'  => 'video',
        'ogv'  => 'video'
    ];
    public $directOutputTypes = ['jpg','png','jpeg','json','js','css','less','scss','mp4','webm','ogg','ogv'];
    public $searchPaths = [];
    public $info;

    public function __construct($file,$path = null) {
        parent::__construct();
        $this->info = (object)[
            'dirname'   => null,
            'basename'  => null,
            'extension' => null,
            'filename'  => null,
            'fullname'  => null,
            'filesize'  => 0,
            'nicesize'  => null
        ];
        $this->assetsPath = realpath(env('project.assets.path'));
        $this->storagePath = realpath(env('project.storage.path'));
        $this->tempPath = realpath(env('project.temp.path'));
        $this->searchPaths = [$this->tempPath,$this->storagePath,$this->assetsPath];
        $this->serve($file,$path);
    }

    function recursiveSearch($path,$patternArray) {
        $return = [];
        $iti = new RecursiveDirectoryIterator($path);
        foreach(new RecursiveIteratorIterator($iti) as $file){
            if (in_array(strtolower(array_pop(explode('.',$file))), $patternArray)){
                $return[] = $file;
            }
        }
        return $return;
    }

    public function getAssetInfo($asset) {
        if (!$this->found) {
            new \Exception('The asset you are attempting to get info on does not exist');
        }
        $tempInfo = pathinfo($asset);
        $this->info->dirname   = $tempInfo['dirname'];
        $this->info->basename  = $tempInfo['basename'];
        $this->info->extension = $tempInfo['extension'];
        $this->info->filename  = $tempInfo['filename'];
        $this->info->fullname = realpath($this->info->dirname.DS.$this->info->basename);
        $this->info->filesize = filesize($asset);
        $this->info->nicesize = fileSizeConvert($this->info->filesize);
        return $this->info;
    }

    public function findAsset($file) {
        if (file_exists($file) && is_file($file)) {
            $this->found = true;
            $this->getAssetInfo($file);
            return true;
        }

        // If the path is not set, check the extension and the check in the possiblePath
        $this->info->extension = pathinfo($file,PATHINFO_EXTENSION);
        if (isset($this->possibleSearchPaths[$this->info->extension])) {
            $psp = $this->possibleSearchPaths[$this->info->extension];
            $this->searchPaths[] = realpath(env('project.assets.path').$psp.DS);
        }
    }

    public function setContentHeader() {
        header("Content-Type: ".env($this->info->extension.'.header'));
    }

    private function directOutput() {
        die(file_get_contents($this->info->fullname));
    }

    private function canServeByRange() {

    }

    private function outputAsset() {
        if (in_array($this->info->extension,$this->directOutputTypes)) {
            $this->setContentHeader();
            $this->directOutput();
        }

        if($this->rangeServe()) {
            pd($this->info);
            // The ranged serve was a success stop the execution right here.
            exit;
        }

        // If range serve does not work use direct outut anyway
        $this->directOutput();
    }

    public static function get($file,$path = null) {
        new self($file,$path = null);
    }

    public function serve($file,$path = null) {
        if (!isset($path)) {
            $this->findAsset($file);
        }
        if ($this->found) {
            $this->outputAsset();
        }

        if (!isset($path) && $this->assetExist($file)) {
            $this->getInfo();
        }

        if (isset($path)) {
            $this->path = $path;
        }
    }

    public function assetExist($file) {
        $this->realpath = realpath($file);
        if (file_exists($this->realpath) && is_file($this->realpath)) {
            $this->exists = true;
            return true;
        }
        $this->exists = false;
        return false;
    }

    public function rangeServe() {
        $fp     = fopen($this->info->fullname, 'rb');
        $size   = $this->info->filesize; // File size
        $length = $this->info->filesize; // Content length
        $start  = 0;                    // Start byte
        $end    = $size - 1;            // End byte
        // Now that we've gotten so far without errors we send the accept range header
        /* At the moment we only support single ranges.
         * Multiple ranges requires some more work to ensure it works correctly
         * and comply with the spesifications: http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
         *
         * Multirange support annouces itself with:
         * header('Accept-Ranges: bytes');
         *
         * Multirange content must be sent with multipart/byteranges mediatype,
         * (mediatype = mimetype)
         * as well as a boundry header to indicate the various chunks of data.
         */
	    header("Accept-Ranges: 0-$length");
        // header('Accept-Ranges: bytes');
        // multipart/byteranges
        // http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
        if (isset($_SERVER['HTTP_RANGE'])) {
            $c_start = $start;
            $c_end   = $end;
            // Extract the range string
            list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
            // Make sure the client hasn't sent us a multibyte range
            if (strpos($range, ',') !== false) {

                // (?) Shoud this be issued here, or should the first
                // range be used? Or should the header be ignored and
                // we output the whole content?
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $start-$end/$size");
                // (?) Echo some info to the client?
                exit;
            }
            // If the range starts with an '-' we start from the beginning
            // If not, we forward the file pointer
            // And make sure to get the end byte if spesified
            if ($range0 == '-') {

                // The n-number of the last bytes is requested
                $c_start = $size - substr($range, 1);
            } else {
                $range  = explode('-', $range);
                $c_start = $range[0];
                $c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
		    }
            /* Check the range and make sure it's treated according to the specs.
             * http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
             */
            // End bytes can not be larger than $end.
            $c_end = ($c_end > $end) ? $end : $c_end;
            // Validate the requested range and return an error if it's not correct.
            if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
                header('HTTP/1.1 416 Requested Range Not Satisfiable');
                header("Content-Range: bytes $start-$end/$size");
			    // (?) Echo some info to the client?
			exit;
		    }
            $start  = $c_start;
            $end    = $c_end;
            $length = $end - $start + 1; // Calculate new content length
            fseek($fp, $start);
            header('HTTP/1.1 206 Partial Content');
	    }
        // Notify the client the byte range we'll be outputting
        header("Content-Range: bytes $start-$end/$size");
        header("Content-Length: $length");

	    // Start buffered download
	    $buffer = 1024 * 8;
	    while(!feof($fp) && ($p = ftell($fp)) <= $end) {
            if ($p + $buffer > $end) {
                // In case we're only outputtin a chunk, make sure we don't
                // read past the length
                $buffer = $end - $p + 1;
            }
		    set_time_limit(0); // Reset time limit for big files
		    echo fread($fp, $buffer);
		    flush(); // Free up memory. Otherwise large files will trigger PHP's memory limit.
	    }
	    fclose($fp);
	    return true;
    }
}
