<?php
use core\extension\ui\view;

class Notes extends Console {
    public $dataStore = 'flow.note';

    public function __construct() {
        parent::__construct(__CLASS__);
    }

    public function index() {
        $this->getValueStore('flow.note');
        $this->setData('connections',(new Connection())->getConnections());
        $this->setHtml(data('app.ui.nav.main.right'),view::phtml('views/databases/index.phtml'));
    }
}
