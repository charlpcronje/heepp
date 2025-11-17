<?php
namespace core\extension\element\traits;

trait importHelpers {
    public function getFileProperties() {
        $this->src = str_replace('//','/',$this->src);
        if (preg_match('/^\/?(.+\/)?(.+)$/', urldecode($this->src), $matchResult)) {            
            $this->fullPath = str_replace(HOST, '', $matchResult[0]);
            $this->path = BASE_PATH . str_replace(HOST, '', $matchResult[1]);
            $this->fileName = str_replace(HOST, '', $matchResult[2]);
            $this->fileExtension = substr(strrchr(str_replace(HOST, '', $matchResult[2]), '.'), 1);
            $this->hashPath = md5($this->path);
            $this->cachedFileName = $this->fileName.'.elem';
            $this->cashedFilePath = CACHED_FILES_PATH.$this->hashPath.DS.$this->fileExtension.DS;
        }
    }
    
    public function isFileCached() : bool {
        if (in_array($this->cashedFilePath.$this->cachedFileName,$this->cachedFiles,true)) {
            return true;
        }

        return false;
    }
    
    public function checkHttpAcceptedEncoding($encoding) : bool {
        //print_r($_SERVER['HTTP_ACCEPT_ENCODING']);
        if (in_array($encoding,explode(',',trim($_SERVER['HTTP_ACCEPT_ENCODING'])),true)) {
            return true;
        }
        return false;
    }
}
