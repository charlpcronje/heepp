<?php
namespace core\extension\element\traits;

trait encodingChecks {
    //Check if the file File Exists. If it Does not An Exception will be triggered
    public function checkIfFileExists($file) {
        if (file_exists($file['path'] . $file['fileName'])) {
            return true;
        }
        $this->createError('File Does Not Exist: ' . $file['path'] . $file['fileName']);
    }
}
