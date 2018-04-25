<?php
namespace core\extension\element;

class element extends \core\Element {
    /*                                        TRAITS TO USE                                           */
    use traits\importHelpers;                      // Functions for importing a new file or checking if the file already exists in the cache
    use traits\importMimeTypes;                    // Properties returning Mime Type Properties
    use traits\encodingChecks;                     // Functions for encoding file content
    use traits\importEncoding;                     // Functions for encoding file content
    use traits\encodingProperties;                 // Functions returning Encoding Properties

    public $element;
    /*                                        FILE PROPERTIES                                         */
    public $src;
    public $path;                            // Path to the file without filename but ending with DIRECTORY_SEPARATOR
    public $fullPath;                        // Full Path of the File Including File Path And File Name. completely Un-parsed
    public $fileName;                        // The Name of the File Including The File Extension
    public $fileExtension;                   // The Extension of the File
    public $hashPath;                        // MD5 Hash of Path
    public $cachedFileName;                  // fileName + .elem
    public $cashedFilePath;

    /*                                        CACHE PROPERTIES                                          */
    private $cachedFiles        = [];                      // array of files that are cached, what encodings has been applied and where they are located. This property is loaded from the $cacheFolder
    private $serverCache        = true;                    // server-side cache true or false. This property determines if any cashing will be done or if the file will just be returned.
    private $serverCacheCheck   = true;               // if you change it to false, the files will not be checked for modifications and always cached files will be used (for better performance)
    private $clientCache        = false;                   // to set client-side cache true or false
    private $clientCacheCheck   = true;               // Setting this to false will force the browser to use cached files without checking for changes.                                   */
    public  $mimeTypeDetails    = [];                   // MimeType details assigned from encodingProperties trait
    public  $cacheMethods       = [                        // Methods to attempt for caching. It will depend on mimeType and encoding properties set in encodingProperties.php
                                                           'characterSetEncode'          => ['active'   => true,
                                                                                             // Is the method active
                                                                                             'function' => 'characterSetEncode',
                                                                                             // Function name to perform caching method
                                                                                             'checks'   => [                           // Checks to perform before caching method is performed
                                                                                                                                       'class_properties'   => [             // Properties that must be true of class: element
                                                                                                                                                                             'serverCache',
                                                                                                                                                                             'characterSetEncode'],
                                                                                                                                       'element_properties' => [           // Properties of the element that must be true
                                                                                                                                                                           'characterSetEncode'],
                                                                                                                                       'mimeTypeDetails'    => [              // MimeTypeDetails Property of this mimeType that must be true
                                                                                                                                                                              'charsetUpdatable'],
                                                                                                                                       'functions'          => []]],
                                                           'minify'                      => ['active'   => true,
                                                                                             'function' => 'minifyFileContent',
                                                                                             'checks'   => ['class_properties'   => ['serverCache',
                                                                                                                                     'minify'],
                                                                                                            'element_properties' => ['minify'],
                                                                                                            'mimeTypeDetails'    => ['minify']]],
                                                           'concatenate'                 => ['active'   => true,
                                                                                             'function' => 'concatenateFileContent',
                                                                                             'checks'   => ['class_properties'   => ['serverCache',
                                                                                                                                     'concatenate'],
                                                                                                            'element_properties' => ['concatenate']]],
                                                           'gzip'                        => ['active'   => true,
                                                                                             'function' => 'gzipFileContent',
                                                                                             'checks'   => ['class_properies'   => ['serverCache'],
                                                                                                            'element_properies' => ['gzip'],
                                                                                                            'functions'         => [                    //Functions to run from which the result must be true
                                                                                                                                                        'checkHttpAcceptedEncoding' => 'gzip'],
                                                                                                            'mimeTypeDetails'   => ['gzip']]],
                                                           'setMaxImageSize'             => ['active'   => true,
                                                                                             'function' => 'setMaxImageSize',
                                                                                             'checks'   => ['class_properies'   => ['serverCache'],
                                                                                                            'element_properies' => ['setMaxImageSize'],
                                                                                                            'functions'         => [                    // Functions to run from which the result must be true
                                                                                                            ],
                                                                                                            'mimeTypeDetails'   => ['gzip']]],
                                                           'imageCompression'            => ['active'   => true,
                                                                                             'function' => 'compressImage',
                                                                                             'checks'   => ['class_properies'   => ['serverCache'],
                                                                                                            'element_properies' => ['setMaxImageSize'],
                                                                                                            'functions'         => [                    // Functions to run from which the result must be true
                                                                                                            ],
                                                                                                            'mimeTypeDetails'   => ['gzip']]],
                                                           'resizeImagesToSetDimentions' => ['active'   => true,
                                                                                             'function' => 'resizeImage',
                                                                                             'checks'   => ['class_properies'   => ['serverCache'],
                                                                                                            'element_properies' => ['imageCompression'],
                                                                                                            'functions'         => [                    // Functions to run from which the result must be true
                                                                                                            ],
                                                                                                            'mimeTypeDetails'   => []]]];
    public  $activeCacheMethods = [];                // Cache Methods above that passed all the checks
    public  $characterSetEncode = true;              // Must The File Be Encoded With $characterSet
    public  $characterSet       = 'utf-8';                 // Default CharacterSet If Any files are being encoded. Later this will be customizable on the element's html attributes
    public  $minify             = true;                          // Default State Of $this->minify
    public  $concatenate        = true;                     // Concatenate files with a mimeType that support concatenation (JS,CSS). Files must be in the same folder
    public  $gzip               = true;                            // Default Value of $this->gzip
    public  $setMaxImageSize    = true;                 // Resize images to the maxImageSize set by the encoding properties
    public  $imageCompression   = true;                // Compress Image to $imageCompressionQuality

    public function __construct() {
        parent::__construct();
        $this->loadCachedFiles();
    }

    public function loadCachedFiles() {
        if (null !== $_SESSION['element']['cachedFiles']) {
            $this->cachedFiles = $_SESSION['element']['cachedFiles'];

            return true;
        }
        if (file_exists(env('project.cache.path').'cachedFiles.elem')) {
            $this->cachedFiles = unserialize(file_get_contents(env('project.cache.path').'cachedFiles.elem'));

            return true;
        }

        return false;
    }

    public function openElement() {
        $this->element = unserialize(file_get_contents($this->cashedFilePath.$this->cachedFileName));
    }

    public function createElement() {
        $this->element                 = new \stdClass();
        $this->element->src            = $this->src;
        $this->element->path           = $this->path;
        $this->element->fullPath       = $this->fullPath;
        $this->element->fileName       = $this->fileName;
        $this->element->fileExtension  = $this->fileExtension;
        $this->element->hashPath       = $this->hashPath;
        $this->element->cashedFilePath = $this->cashedFilePath;
        $this->element->cachedFileName = $this->cachedFileName;

        $fileStats                     = stat($this->element->path.$this->element->fileName);
        $this->element->fileSize       = $fileStats['size'];                          //Size in Bytes
        $this->element->lastAccessDate = $fileStats['atime'];
        $this->element->modifiedDate   = $fileStats['mtime'];
    }

    public function loadCurrentFileContent() {
        $this->element->fileContent['original'] = file_get_contents($this->path.$this->fileName);
    }

    public function checkActiveCacheMethods() {
        $this->activeCacheMethods = [];
        foreach($this->cacheMethods as $method => $options) {
            $addMethod = true;
            if ($options['active']) {
                if (isset($options['checks']['class_properties'])) {
                    foreach($options['checks']['class_properties'] as $property) {
                        if (!$this->$property) {
                            $addMethod = false;
                        }
                    }
                }
                if (isset($options['checks']['element_properties'])) {
                    foreach($options['checks']['element_properties'] as $property) {
                        if (!$this->element->$property) {
                            $addMethod = false;
                        }
                    }
                }
                if (isset($options['checks']['functions'])) {
                    foreach($options['checks']['functions'] as $function => $param) {
                        if (!$this->$function($param)) {
                            $addMethod = false;
                        }
                    }
                }
                if (isset($options['checks']['mimeTypeDetails'])) {
                    foreach($options['checks']['mimeTypeDetails'] as $detail) {
                        if (!$this->mimeTypeDetails[$detail]) {
                            $addMethod = false;
                        }
                    }
                }
            }
            if ($addMethod) {
                $this->activeCacheMethods[] = $method;
            }
        }
    }

    public function importFile() {
        $this->getFileProperties();                                                 // Get file properties from actual file
        $this->getMimeTypeDetails($this->fileExtension);                            // Get mimeTypeProperties from encodingProperties trait
        if ($this->isFileCached()) {                                                // Check if the file is already cached
            $this->openElement();                                                   // Open the existing element from cache
        } else {
            $this->createElement();                                                 // Create a new element (New StdClass)
            $this->loadCurrentFileContent();                                        // Load the Current file content into $this->element->fileContent['original']
        }
        $this->mimeTypeDetails = $this->getMimeTypeDetails($this->fileExtension);
        $this->assignMimeTypeProperties();                                          // Get the mimeType Properties that as appropriate for this mimeType and add it to the element as properties
        $this->checkActiveCacheMethods();                                           // Check which cache methods pass all the checks
    }

    public function render() {
        //Get All File Properties
        $this->importFile();

        //Render this element
        //return parent::render();
        serialize($this);
    }

    public function getCachedFilePath() {
        return $this->element->hashPath.DIRECTORY_SEPARATOR.$this->element->fileExtension.DIRECTORY_SEPARATOR.$this->fileName;
    }

    public function __sleep() {
        if (isset($this->element,$this->element->hashPath)) {
            if (!in_array($this->cashedFilePath.$this->cachedFileName,$this->cachedFiles,true)) {
                $this->cachedFiles[] = $this->cashedFilePath.$this->cachedFileName;                       //Add Current Element to $this->cachedFiles
            }

            if (!file_exists($this->cashedFilePath)) {                                                      //Create folder for cached element object
                mkdir($this->cashedFilePath,'0777',true);
            }
            //Serialize element to: CACHED_FILES_PATH
            file_put_contents($this->cashedFilePath.$this->cachedFileName,serialize($this->element));    //Save current element object as serialized text in a file
        }

        if (!file_exists(env('project.cache.path'))) {
            mkdir(env('project.cache.path'),'0777',true);
        }
        file_put_contents(env('project.cache.path').'cachedFiles.elem',serialize($this->cachedFiles));           //Save $this->cachedFiles array as serialized text in a file

        return ['cachedFiles'];
    }

}
