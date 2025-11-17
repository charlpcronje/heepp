<?php
namespace core\element\files\fileTree;
use core\Element;
use core\extension\ui\view;

class fileTree extends Element {
    public $base;
    // Action to perform when a file is clicked
    public $callback;
    public $tree;
    public $fileTypes;
    public $ignoreFolders = [
        '.git',
        '.vscode',
        '.idea'
    ];


    public function __construct() {
        $this->element = __class__;
        parent::__construct(__class__);
        $this->fileTypes = include __DIR__.DS.'fileTypes.php';
    }

    public static function path($dir,$fileClickCallback = null,$ext = []) {
        return (new fileTree())->fileTree($dir,$fileClickCallback,$ext);
    }

    public function fileTree($directory,$fileClickCallback,$extensions = []) {
        /* Generates a valid XHTML list of all directories, sub-directories, and files in $directory
         * Remove trailing slash */
        if(substr($directory,-1) === DS) {
            $directory = substr($directory,0,-0);
        }
        return $this->fileTreeDir($directory, $fileClickCallback, $extensions);
    }

    public function fileTreeDir($directory,$fileClickCallback,$extensions = [],$firstCall = true) {
        $path = realpath($directory);
        $filesAndFolers = scandir($path,SCANDIR_SORT_ASCENDING);
        // Make directories first
        $files   = [];
        $folders = [];
        foreach($filesAndFolers as $fileOrFolder) {
            if(is_dir($path.DS.$fileOrFolder)) {
                if (!in_array($fileOrFolder,$this->ignoreFolders)) {
                    $folders[] = $fileOrFolder;
                }
            } else {
                $files[]   = $fileOrFolder;
            }
        }
        $file = array_merge($folders, $files);

        // Filter unwanted extensions
        if(!empty($extensions) ) {
            foreach(array_keys($file) as $key) {
                if(!is_dir($directory.DS.$file[$key])) {
                    $ext = substr($file[$key],strrpos($file[$key],'.') + 1);
                    if(!in_array($ext, $extensions)) {
                        unset($file[$key]);
                    }
                }
            }
        }

        // Use 2 instead of 0 to account for . and .. "directories"
        $fileTree = '';
        if(count($file) > 2) {
            $fileTree .= '<ul';
            if($firstCall) {
                $fileTree .= ' class="element-file-tree" ';
                $firstCall = false;
            }
            $fileTree .= '>';
            foreach($file as $thisFile) {
                if($thisFile != '.' && $thisFile != '..') {
                    if(is_dir($directory.DS.$thisFile)) {
                        $fileTree .= '<li class="pft-directory">
                                        <a href="#1">
                                            <i class="fa fa-caret-right"></i>
                                            ' .htmlspecialchars($thisFile). '
                                        </a>'.
                                            $this->fileTreeDir($directory.DS.$thisFile,$fileClickCallback,$extensions,$firstCall).
                                        '</li>';
                    } else {
                        // Get extension (prepend 'ext-' to prevent invalid classes from extensions that begin with numbers)
                        $extension = substr($thisFile, strrpos($thisFile,'.') + 1);
                        $ext = 'ext-'. $extension;
                        $link = str_replace('[link]', $directory.DS.urlencode($thisFile),$fileClickCallback);
                        $fileInfo = new \stdClass();

                        if (isset($this->fileTypes->{$extension})) {
                            $fileInfo->iconClass = $this->fileTypes->{$extension}->{'icon-css-class'};
                            $fileInfo->iconColor = $this->fileTypes->{$extension}->{'icon-color'};
                            $fileInfo->fileColor = $this->fileTypes->{$extension}->{'file-color'};
                        } else {
                            $fileInfo->iconClass = 'fa fa-file-code-o';
                            $fileInfo->iconColor = 'inherit';
                            $fileInfo->fileColor = 'inherit';
                        }
                        $fileTree .= '<li class="pft-file '.strtolower($ext).'">
                                        <a core.event.click.load="'.$this->callback.' '.base64urlEncode($link).'" href="#'.$this->callback.'">
                                            <i class="ft-icon '.$ext.' '.$fileInfo->iconClass.'" style="color: '.$fileInfo->iconColor.'"></i>
                                            '.htmlspecialchars($thisFile).'
                                        </a>
                                      </li>';
                    }
                }
            }
            $fileTree .= '</ul>';
        }
        return $fileTree;
    }

    private function heading() {
        /* Use the last folder of the base path as a heading */
        $dirPieces = explode(DS,$this->base);
        return '<div class="element-file-tree-heading-container">
                    <h3 class="element-file-tree-heading">'.array_pop($dirPieces).'</h3>
                </div>';
    }

    public function render() {
        $this->base = realpath($this->base);
        if (!isset($this->base)) {
            $this->base = env('project.path');
        }
        $this->tree = $this->heading().$this->fileTree($this->base,$this->callback);
        return view::mold('fileTree.phtml',__DIR__,$this);
    }
}

