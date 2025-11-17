<?php

class tree extends Controller {
    public function __construct() {
        parent::__construct(__CLASS__);
    }

    public static function path($dir,$returnLink = null,$ext = []) {
        return (new tree())->fileTree($dir,$returnLink,$ext);
    }

    public function fileTree($directory,$returnLink,$extensions = []) {
        /* Generates a valid XHTML list of all directories, sub-directories, and files in $directory
         * Remove trailing slash */
        if(substr($directory,-1) === DS) {
            $directory = substr($directory,0,-0);
        }
        return $this->fileTreeDir($directory, $returnLink, $extensions);
    }

    public function fileTreeDir($directory,$returnLink,$extensions = [],$firstCall = true) {
        $filesAndFolers = scandir($directory,SCANDIR_SORT_ASCENDING);
        // Make directories first
        $files   = [];
        $folders = [];
        foreach($filesAndFolers as $fileOrFolder) {
            if(is_dir($directory.DS.$fileOrFolder)) {
                $folders[] = $fileOrFolder;
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
                $fileTree .= ' class="php-file-tree" ';
                $firstCall = false;
            }
            $fileTree .= '>';
            foreach($file as $thisFile) {
                if($thisFile != '.' && $thisFile != '..') {
                    if(is_dir($directory.DS.$thisFile)) {
                        $fileTree .= '<li class="pft-directory"><a href="#">'.htmlspecialchars($thisFile).'</a>';
                        $fileTree .= $this->fileTreeDir($directory.DS.$thisFile,$returnLink,$extensions,$firstCall);
                        $fileTree .= '</li>';
                    } else {
                        // Get extension (prepend 'ext-' to prevent invalid classes from extensions that begin with numbers)
                        $ext = 'ext-'. substr($thisFile, strrpos($thisFile,'.') + 1);
                        $link = str_replace('[link]', $directory.DS.urlencode($thisFile),$returnLink);
                        $fileTree .= '<li class="pft-file '.strtolower($ext).'"><a href="'.$link.'">'.htmlspecialchars($thisFile).'</a></li>';
                    }
                }
            }
            $fileTree .= '</ul>';
        }
        return $fileTree;
    }
}