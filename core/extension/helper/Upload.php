<?php
namespace core\extension\helper;
use core\element\text\strtolower;

class Upload extends \core\extension\Extension {
    
    public $file = [];
    public $path;
    public $folder;
    public $fileName;
    public $fileSize;
    public $fileType;
    public $fileError;
    public $fileTempName;
    public $result;
    public $fileExtension;
    public $allowedExtensions = [];
    public $uploadType;
    public $maxFileSize = 10485761;
    public $overwrightFile = true;
    
    public function __construct($uploadType = 'image',$folder = null) {
        parent::__construct();
        if (isset($folder)) {
            $this->folder = $folder;
        }
        if (isset($uploadType)) {
            $this->uploadType = $uploadType;
        }
        
        //Get max file size
        $max_upload        = (int)(ini_get('upload_max_filesize'));
        $max_post          = (int)(ini_get('post_max_size'));
        $memory_limit      = (int)(ini_get('memory_limit'));
        $upload_mb         = min($max_upload, $max_post, $memory_limit);
        $this->maxFileSize = $upload_mb * 1048576;
        
        if (!isset($_FILES['file'])) {
            $fName = array_keys($_FILES)[0];
        } else {
            $fName = 'file';
        }
        
        if (empty($_FILES[$fName])) {
            $this->setError('No file was specified, check the following php.ini setting: "post_max_size"');
        }

        $this->file = $_FILES[$fName];
        if (is_array($this->file['name'])) {
            $this->fileName = $this->file['name'][0];
        } else {
            $this->fileName = $this->file['name'];
        }

        if (is_array($this->file['size'])) {
            $this->fileSize = $this->file['size'][0];
        } else {
            $this->fileSize = $this->file['size'];
        }

        if (is_array($this->file['type'])) {
            $this->fileType = $this->file['type'][0];
        } else {
            $this->fileType = $this->file['type'];
        }

        if (is_array($this->file['error'])) {
            $this->fileError = $this->file['error'][0];
        } else {
            $this->fileError = $this->file['error'];
        }

        if (is_array($this->file['tmp_name'])) {
            $this->fileTempName = $this->file['tmp_name'][0];
        } else {
            $this->fileTempName = $this->file['tmp_name'];
        }
        $this->getAllowedExtensions();
    }

    public function uploadFile() {
        if (!isset($this->fileName) || empty($this->fileName)) {
            $this->setError('No file uploaded');
            return false;
        }
        
        if (isset($this->uploadType) || !empty($this->uploadType)) {
            if (!$this->validateExtension()) {
                return false;
            }
        } else {
            $this->setError('No uploadType specified (image,audio,video,archive,torrent,font,cad,vector,flash,web,data,document,pdf)');
            return false;
        }
        
        if (!isset($this->path)) {
           $this->setError("No upload path specified");
           return false;
        }

        if(is_array($this->fileSize)) {
            $this->fileSize = $this->fileSize[0];
        }

        if ($this->maxFileSize > $this->fileSize) {
            if ($this->checkFileExists()) {
                if (move_uploaded_file($this->fileTempName,$this->path.$this->fileName)) {
                    $this->setNotify('success','File: <strong>'.$this->fileName.'</strong> ('.$this->allowedExtensions[$this->fileExtension].') successfully uploaded');
                    return $this->path.$this->fileName;
                }

                $this->setError('Moving <strong>'.$this->fileName.'</strong> to <strong>'.$this->path.'</strong> failed');

                return false;
            } else {
                $this->setError('The file you are uploading already exists');
                return false;
            }
        } else {
            $this->setError('File size <strong>('.$this->formatBytes($this->fileSize).')</strong> larger than the allowed maximum size of <strong>'.$this->formatBytes($this->maxFileSize).'</strong>');
            return false;
        }
    }
    
    public function checkFileExists() {
        if (file_exists($this->path.$this->fileName)) {
            if ($this->overwrightFile) {
                return true;
            } else {
                return false;
            }
        } else {
            if (!file_exists($this->path)) {
                if (!mkdir($this->path,0777,true)) {
                    $this->setError('Failed to create folder: <strong>'.$this->path.'</strong>');
                    return false;
                }
            }
            return true;
        }
    }
    
    public function formatBytes($bytes,$precision = 2) {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        $bytes = max($bytes, 0);
        if (is_array($bytes)) {
            $bytes = $bytes[0];
        }

        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));

        $pow = min($pow, count($units) - 1);
        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow));
        return round($bytes, $precision) . ' ' . $units[$pow]; 
    } 
    
    public function validateExtension() {
        if (is_array($this->fileName)) {
            $this->fileName = $this->fileName[0];
        }
        $this->fileExtension = strtolower(pathinfo($this->fileName, PATHINFO_EXTENSION));
        if ($this->uploadType == 'none') {
            return true;
        }
        foreach($this->allowedExtensions as $key => $value) {
            $extensions[] = $key;
        }
        if (in_array($this->fileExtension, $extensions)) {
            return true;
        } else {
            $this->setError('The file you uploaded is not an allowed file type');
            return false;
        }
    }
    
    public function getAllowedExtensions() {
        switch ($this->uploadType) {
            case 'none':
                $this->path = env('project.upload.path');
            break;
            case 'image':
                $this->allowedExtensions = [
                    'tiff' => 'Tagged Image File Format',
                    'tif'  => 'Tagged Image File Format',
                    'png'  => 'Portable Network Graphics',
                    'gif'  => 'Graphics Interchange Format',
                    'jpg'  => 'Joint Photographic Experts Group',
                    'jpeg' => 'Joint Photographic Experts Group',
                    'svg'  => 'Scalable Vector Graphics',
                    'psd'  => 'Photoshop Document',
                    'bmp'  => 'Windows bitmap',
                    'ss'   => 'Bitmap graphics; Splash'
                ];
                $this->path = env('project.upload.path').'images'.DS;
            break;
            case 'audio':
                $this->allowedExtensions = [
                    'mp3'  => 'MP3 Audio File',
                    'wav'  => 'Waveform Audio Format',
                    'wma'  => 'Windows Media Audio',
                    'mp4'  => 'MPEG-4 Part 14',
                    'aif'  => 'Audio Interchange File',
                    'dvf'  => 'Sony Compressed Voice File',
                    'aiff' => 'Audio Interchange File Format',
                    'mid'  => 'Musical Instrument Digital Interface',
                    'ram'  => 'RealAudio',
                    'amr'  => 'Adaptive Multi-Rate audio'
                ];
                $this->path = env('project.upload.path').'audio'.DS;
            break;
            case 'video':
                $this->allowedExtensions = [
                    'wmv'  => 'Windows Media Video',
                    'avi'  => 'Audio Video Interleave',
                    'mpg'  => 'MPEG-1 Video',
                    'mov'  => 'QuickTime Video',
                    'mp4'  => 'MPEG-4 Part 14',
                    'm4a'  => 'MPEG-4 Part 14',
                    'rmvb' => 'RealMedia Variable Bitrate',
                    'mpeg' => 'MPEG 1 System Stream',
                    'rm'   => 'RealMedia',
                    'flv'  => 'Flash video',
                    'm4b'  => 'MPEG-4 Part 14',
                    'vob'  => 'DVD-Video Object',
                    'm4p'  => 'Protected AAC File',
                    'divx' => 'DivX video',
                    'm4v'  => 'MPEG-4 Part 14',
                    'mp2'  => 'MPEG-1 Audio Layer II'
                ];
                $this->path = env('project.upload.path').'videos'.DS;
            break;
            case 'archive':
                $this->allowedExtensions = [
                    'zip' => 'Zip archive',
                    'rar' => 'RAR Archive',
                    'jar' => 'Java Archive',
                    'dmg' => 'Disk image',
                    'iso' => 'Optical disk image',
                    '7z'  => '7z archive',
                    'gz'  => 'Gzip archive',
                    'msi' => 'Windows Installer',
                    'ace' => 'ACE archive',
                    'pst' => 'Microsoft Personal Storage',
                    'cab' => 'Microsoft Windows compressed archive',
                    'sea' => 'Self-Extracting compressed Macintosh file Archive',
                    'tgz' => 'Archive; WinZipNT - TAR - GNUzip',
                    'dll' => 'Dynamic-link library',
                    'vcd' => 'Virtual CD-ROM CD Image File',
                    'bup' => 'Backup file'
                ];
                $this->path = env('project.upload.path').'archives'.DS;
            break;
            case 'torrent':
                $this->allowedExtensions = [
                    'torrent' => 'BitTorrent'
                ];
                $this->path = env('project.upload.path').'torrents'.DS;
            break;
            case 'font':
                $this->allowedExtensions = [
                    'ttf' => 'TrueType font'
                ];
                $this->path = env('project.upload.path').'fonts'.DS;
            break;
            case 'cad':
            case 'vector':
            case 'flash':
                $this->allowedExtensions = [
                    'dwg' => 'AutoCAD DWG',
                    'swf' => 'SWF vector graphics',
                    'flv' => 'Flash video',
                    'fla' => 'Adobe Flash',
                    'cdl' => 'CADKEY Advanced Design Language (CADL)',
                    'xtm' => 'CmapTools Exported Topic Map',
                    'mcd' => 'MathCad file; MathCad'
                ];
                switch($this->uploadType) {
                    case 'cad':
                        $this->path = env('project.upload.path').'cad'.DS;
                    break;
                    case 'vector':
                        $this->path = env('project.upload.path').'vector'.DS;
                    break;
                    case 'flash':
                        $this->path = env('project.upload.path').'flash'.DS;
                    break;
                }
            break;
            case 'web':
                $this->allowedExtensions = [
                    'htm'  => 'Hypertext Markup Language (HTML)',
                    'html' => 'Hypertext Markup Language'
                ];
                $this->path = env('project.upload.path').'web'.DS;
            break;
            case 'data':
                $this->allowedExtensions = [
                    'mdb' => 'Microsoft Access',
                    'log' => 'Log file',
                    'eml' => 'E-mail message',
                    'sql' => 'Structured Query Language'
                ];
                $this->path = env('project.upload.path').'data'.DS;
            break;
            case 'document':
                $this->allowedExtensions = [
                    'pdf'  => 'PDF Document',
                    'doc'  => 'Microsoft Word Document',
                    'ppt'  => 'Microsoft PowerPoint Presentation',
                    'xls'  => 'Microsoft Excel spreadsheet',
                    'csv'  => 'Comma Seperated Value',
                    'txt'  => 'Plain text file',
                    'pps'  => 'PowerPoint Show',
                    'pub'  => 'Microsoft Publisher',
                    'wpd'  => 'WordPerfect Document',
                    'qxd'  => 'QuarkXpress Document',
                    'rtf'  => 'Rich Text Format',
                    'qbw'  => 'Spreadsheet; QuickBooks for Windows',
                    'log'  => 'Log file',
                    'eml'  => 'E-mail message',
                    'wps'  => 'Text document; MS Works',
                    'docx' => 'Microsoft Word Document',
                    'docm' => 'Microsoft Macro Enabled Word Document',
                    'xlsx' => 'Microsoft Excel Workbook',
                    'xlsm' => 'Microsoft Excel Macro-enabled workbook',
                    'pptx' => 'Microsoft Powerpoint Presentation',
                    'pptm' => 'Microsoft Macro-enabled Powerpoint Presentation',
                    'ppsx' => 'Microsoft Powerpoint Show',
                    'ppsm' => 'Microsoft Macro-enabled Powerpoint Show'
                ];
                $this->path = env('project.upload.path').'documents'.DS;
            break;
            case 'pdf':
                $this->allowedExtensions = [
                    'pdf' => 'PDF Document'
                ];
                $this->path = env('project.upload.path').'documents'.DS;
            break;
        }
        if (isset($this->folder)) {
            $this->path .= $this->folder.DS;
        }
    }
}
