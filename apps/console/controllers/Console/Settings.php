<?php
namespace Console;
use Console;
use core\extension\database\Model;
use core\extension\ui\view;

class Settings extends Console   {
    public function __construct($class = __CLASS__) {
        parent::__construct($class);
    }

    public function index() {
        /* Load Settings From Value Store */
        $this->setData('settings',$this->getValueStore('console.settings.core'));
        $domElem = data('app.ui.ws.right');
        $this->setHtml($domElem,view::phtml('views/console/settings.phtml'));
        $this->addHistory($domElem,'Console.Settings/index');
    }

    public function saveSettings($keyStore = 'console') {
        $actionsTaken = $this->saveValueStore('console.settings.core',$this->input());
        $this->setNotify('success',$actionsTaken->updated.' Records Updated. '.$actionsTaken->added.' Records Added.');
    }
}
