<?php
use core\Controller;
use core\extension\ui\view;

class Databases extends Controller {
    public function __construct() {
        parent::__construct(__CLASS__);
    }

    public function index() {
        $this->setData('connections',(new Connection())->getConnections());
        $this->setHtml(data('app.ui.nav.main.right'),view::phtml('views/databases/index.phtml'));
    }

    public function editConnections() {
        $this->setHtml(data('app.ui.nav.main.left'),view::phtml('views/databases/editConnections.phtml'));
    }
}
