<?php
use core\extension\ui\view;

class Blog extends Controller {

    public function __construct ($class = 'Controller') {
        parent::__construct($class);
    }

    public function index() {
        return view::phtml('views/index.phtml');
    }
}
