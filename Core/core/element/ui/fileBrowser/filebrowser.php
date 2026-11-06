<?php
namespace core\element\ui\filebrowser;
use core\Element;
use core\extension\ui\view;

//ini_set('open_basedir', dirname(__FILE__) . DIRECTORY_SEPARATOR);
class filebrowser extends Element {
    public $base;
    public $path;
    private $fileTypes;
    private $fileTypeColors;
    public $unique;

    public function __construct() {
        parent::__construct(__class__);
    }

    protected function real($path) {
        $temp = realpath($path);
        if (!$temp) {
            $this->setError('Path does not exist: ' . $path);
        }
        if($this->base && strlen($this->base)) {
            if(strpos($temp, realpath($this->base.'\\')) !== 0) {
                return $this->setError('Path is not inside base ('.$this->base.'): ' . $temp);
            }
        }
        return $temp;
    }

    protected function path($id) {
        $id = str_replace('/', DS, $id);
        $id = trim($id, DS);
        // $id = $this->real($this->base . DS . $id);
        return realpath($id);
    }

    protected function id($path) {
        $path = $this->real($path);
        $path = substr($path, strlen($this->base));
        $path = str_replace(DS, '/', $path);
        $path = trim($path, '/');
        return strlen($path) ? $path : '/';
    }

    private function stdNodeObj() {
        return (object)[
            'description'                        => 'file',
            'show-in-file-tree'                  => true,
            'icon-library'                       => 'devicons',
            'icon'                               => 'code',
            'icon-color'                         => '#999',
            'icon-color-class'                   => 'filetype-color-default',
            'file-color'                         => '#999',
            'file-hover-color'                   => '#FFF',
            'file-active-color'                  => '#FFF',
            'file-active-bg-color'               => '#5fa2db',
            'file-unsaved-bg-color'              => '#ff973a',
            'file-contains-task-bg-color'        => '#1976d2',
            'file-part-of-task-bg-color'         => '#2e2e2e',
            'file-part-of-current-task-bg-color' => '#a2ff00',
            'file-used-in-last-run-bg-color'     => '#3c3c3c',
            'web-file'                           => false,
            'layout-file'                        => false,
            'contains-html'                      => false,
            'contains-css'                       => false,
            'contains-javascript'                => false,
            'contains-xml'                       => false,
            'contains-image'                     => false,
            'contains-audio'                     => false,
            'contains-video'                     => false,
            'contains-font'                      => false,
            'allow-access'                       => true,
            'include-in-version-control'         => true,
            'keep-history'                       => true,
            'parse-server-side'                  => false,
            'parse-client-side'                  => false];
    }

    public function lst($id, $with_root = false) {
        $this->loadFileTypeSettings();
        $dir = $this->path($id);
        $lst = @scandir($dir,SCANDIR_SORT_NONE );
        if(!$lst) {
            $this->setError('Could not list path: ' . $dir);
        }
        $res = [];
        foreach($lst as $item) {
            if($item == '.' || $item == '..' || $item === null) { continue; }
            $tmp = preg_match('([^ a-zа-я-_0-9.]+)ui', $item);
            if($tmp === false || $tmp === 1) {
                continue;
            }
            if(is_dir($dir . DS . $item)) {
                $res[] = [
                    'text' => $item,
                    'children' => true,
                    'id' => $this->id($dir . DS . $item),
                    'icon' => 'fa fa-folder-o'
                ];
            } else {
                $nodeExt = substr($item, strrpos($item,'.')+ 1);
                if (isset($this->fileTypes->{$nodeExt})) {
                    $nodeAttr = $this->fileTypes->{$nodeExt}->attributes;

                } else {
                    $nodeAttr = $this->stdNodeObj();
                }

                $res[] = [
                    'text' => $item,
                    'children' => false,
                    'id' => $this->id($dir . DS . $item),
                    'type' => $nodeAttr->description,
                    'color' => $nodeAttr->{'file-color'},
                    'icon' => $nodeAttr->{'icon-library'}.' '.$nodeAttr->{'icon-library'}.'-'.$nodeAttr->icon.' '.$nodeAttr->{'icon-color-class'},
                    'iconColor' => $nodeAttr->{'icon-color'}
                ];
                //($res);
            }
        }
        if($with_root && $this->id($dir) === '/') {
            $res = [
                    [
                        'text' => basename($this->base),
                        'children' => $res,
                        'id' => '/',
                        'icon' =>'fa fa-folder-open-o',
                        'state' => [
                            'opened' => true,
                            'disabled' => true
                        ]
                    ]
                ];
        }
        return $res;
    }

    public function browserData($id) {
        if(strpos($id,':')) {
            $id = array_map([$this,'id'], explode(':', $id));
            return [
                'type'    => 'multiple',
                'content' => 'Multiple selected: '.implode(' ',$id)
            ];
        }
        $dir = $this->path($id);
        if(is_dir($dir)) {
            return [
                'type'    => 'folder',
                'content' => $id
            ];
        }
        if(is_file($dir)) {
            $ext = '';
            if (strpos($dir, '.') !== false) {
                $ext = substr($dir, strrpos($dir, '.') + 1);
            }
            $dat = [
                'type'    => $ext,
                'content' => ''
            ];
            switch($ext) {
                case 'txt':
                case 'text':
                case 'md':
                case 'js':
                case 'json':
                case 'css':
                case 'html':
                case 'xhtml':
                case 'htm':
                case 'xml':
                case 'pml':
                case 'c':
                case 'cpp':
                case 'h':
                case 'sql':
                case 'log':
                case 'py':
                case 'rb':
                case 'htaccess':
                case 'php':
                    $dat['content'] = file_get_contents($dir);
                break;
                
                case 'jpg':
                case 'jpeg':
                case 'gif':
                case 'png':
                case 'bmp':
                    $dat['content'] = 'data:'.finfo_file(finfo_open(FILEINFO_MIME_TYPE), $dir).';base64,'.base64_encode(file_get_contents($dir));
                break;
                
                default:
                    $dat['content'] = 'File not recognized: '.$this->id($dir);
                break;
            }
            return $dat;
        }
        throw new \Exception('Not a valid selection: ' . $dir);
    }

    public function create($id, $name, $mkdir = false) {
        $dir = $this->path($id);
        if(preg_match('([^ a-zа-я-_0-9.]+)ui', $name) || !strlen($name)) {
            throw new \Exception('Invalid name: ' . $name);
        }
        if($mkdir) {
            if (!mkdir($dir.DS.$name) && !is_dir($dir.DS.$name)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created',$dir.DS.$name));
            }
        }
        else {
            file_put_contents($dir . DS . $name, '');
        }
        return array('id' => $this->id($dir . DS . $name));
    }

    public function rename($id, $name) {
        $dir = $this->path($id);
        if($dir === $this->base) {
            throw new \Exception('Cannot rename root');
        }
        if(preg_match('([^ a-zа-я-_0-9.]+)ui', $name) || !strlen($name)) {
            throw new \Exception('Invalid name: ' . $name);
        }
        $new = explode(DS, $dir);
        array_pop($new);
        $new[] = $name;
        $new   = implode(DS, $new);
        if($dir !== $new) {
            if(is_file($new) || is_dir($new)) { throw new \Exception('Path already exists: ' . $new); }
            rename($dir, $new);
        }
        return array('id' => $this->id($new));
    }

    public function remove($id) {
        $dir = $this->path($id);
        if($dir === $this->base) {
            throw new \Exception('Cannot remove root');
        }
        if(is_dir($dir)) {
            foreach(array_diff(scandir($dir,SCANDIR_SORT_NONE ), ['.','..']) as $f) {
                $this->remove($this->id($dir . DS . $f));
            }
            rmdir($dir);
        }
        if(is_file($dir)) {
            unlink($dir);
        }
        return array('status' => 'OK');
    }

    public function move($id, $par) {
        $dir = $this->path($id);
        $par = $this->path($par);
        $new = explode(DS, $dir);
        $new = array_pop($new);
        $new = $par . DS . $new;
        rename($dir, $new);
        return array('id' => $this->id($new));
    }

    public function copy($id, $par) {
        $dir = $this->path($id);
        $par = $this->path($par);
        $new = explode(DS, $dir);
        $new = array_pop($new);
        $new = $par . DS . $new;
        if(is_file($new) || is_dir($new)) { throw new \Exception('Path already exists: ' . $new); }

        if(is_dir($dir)) {
            if (!mkdir($new) && !is_dir($new)) {
                throw new \RuntimeException(sprintf('Directory "%s" was not created',$new));
            }
            foreach(array_diff(scandir($dir,SCANDIR_SORT_NONE ), ['.','..']) as $f) {
                $this->copy($this->id($dir . DS . $f), $this->id($new));
            }
        }
        if(is_file($dir)) {
            copy($dir, $new);
        }
        return array('id' => $this->id($new));
    }

    public function action($action = null,$id = null,$textOrParent = null,$type = null) {

        if ($this->inputSet('id')) {
            $id = $this->input('id');
        }
        $rslt = null;
        switch($action) {
            case 'get_node':
                $node = isset($id) && $id !== '#' ? $id : DS;
                $rslt = $this->lst($node,isset($id) && $id === '#');
            break;
            case 'get_content':
                $node = isset($id) && $id !== '#' ? $id : DS;
                $rslt = $this->browserData($node);
            break;
            case 'create_node':
                $node = isset($id) && $id !== '#' ? $id : DS;
                $rslt = $this->create($node, isset($textOrParent) ? $textOrParent : '',!isset($type) || $type !== 'file');
            break;
            case 'rename_node':
                $node = isset($id) && $id !== '#' ? $id : DS;
                $rslt = $this->rename($node, isset($textOrParent) ? $textOrParent : '');
            break;
            case 'delete_node':
                $node = isset($id) && $id !== '#' ? $id : DS;
                $rslt = $this->remove($node);
            break;
            case 'move_node':
                $node = isset($id) && $id !== '#' ? $id : DS;
                $parn = isset($textOrParent) && $textOrParent !== '#' ? $textOrParent : DS;
                $rslt = $this->move($node, $parn);
            break;
            case 'copy_node':
                $node = isset($id) && $id !== '#' ? $id : DS;
                $parn = isset($textOrParent) && $textOrParent !== '#' ? $textOrParent : DS;
                $rslt = $this->copy($node, $parn);
            break;
            default:
                throw new \Exception('Unsupported operation: ' . $action);
            break;
        }
        header('Content-Type: application/json; charset=utf-8');
        die(json_encode($rslt));
    }

    private function defaultBasePath() {
        if (empty($this->base) && $this->dataKeyExist('session.element.filebrowser.base')) {
            $this->base = $this->getData('session.element.filebrowser.base');
        } else {
            $this->setData('session.element.filebrowser.base',$this->base);
        }
        if (empty($this->path) && $this->dataKeyExist('session.element.filebrowser.path')) {
            $this->path = $this->getData('session.element.filebrowser.path');
        } else {
            $this->setData('session.element.filebrowser.path',$this->path);
        }
    }

    private function loadFileTypeSettings() {
        $this->fileTypes = loadJSON(__DIR__.DS.'fileTypes.json');
    }

    private function loadFileTypeColors() {
        $this->fileTypeColors = include __DIR__.DS.'fileColors.php';
    }

    public function render() {
        $this->loadFileTypeSettings();
        $this->loadFileTypeColors();
        $this->defaultBasePath();
        $this->base = $this->real($this->base);
        if(!$this->base) {
            return $this->setError("Base directory ('".$this->base."') does not exist");
        }
        return (new view('filebrowser.phtml',__DIR__))->html;
    }
}
