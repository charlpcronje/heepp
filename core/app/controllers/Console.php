<?php
use core\Heepp;
use core\extension\ui\view;
use core\Output;
use core\system\handlers\ProjectLoader;

class Console extends Controller {

    public function __construct ($class = 'Controller') {
        parent::__construct($class);
    }

    public function index() {
        // Set the bottom-offset of the console to 0 for the login page
        $this->setData('offsetAttr','offset-bottom: '.$this->getData('app.console.offset.bottom'));
        if (!$this->isSignedIn(false)) {
            $this->setData('offsetAttr','');
            $this->setData('sectionClass','login-section');
        }
        return view::phtml('views/index.phtml');
    }

    public function loadHistory() {
        if ($this->sessionKeyExist('history')) {
            foreach($this->session('history') as $history) {
                $exploded = explode('/',$history,2);
                $_GET['controller'] = $exploded[0];
                $_GET['params'] = '/'.$exploded[1];
                ProjectLoader::loadController(null,null,[],false);
            }
        }
        $this->setHide('.restore-ui-session-button');
    }

    public function sessionHistoryExist() {
        if ($this->sessionKeyExist('history')) {
            $this->setCallback('core.console.ui.showRestoreSessionButton',[],'window');
        }
    }

    public function getFileExplorer($path = null) {
        pd(env('project.path'));
        $this->setData('explorer.path',$path ?? env('project.path'));
        $this->setHtml(data('app.ui.nav.main.left'),view::phtml('views/databases/editConnections.phtml'));
    }
}
