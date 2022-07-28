<?php
namespace core\element\core;
use core\Heepp;
use core\extension\ui\view;
use core\Element;
use core\system\route;

class coreobject extends Element {
    /* The core will modify the content of the following attributes: */
    public $src;
    public $data = '';
    /* 'src'
    |-------
    | will actually set the 'data' attribute */

    public $route;
    public $verb   = 'get';
    /* 'route'
    |---------
    | Here you can reference any root that has already been declared */

    /* 'viewdata'
    |-----------
    | This property may contain a string with a dotData request */
    public $viewdata = [];
    public $view;
    /* 'view' because the content of the "<object>" tag does noy actual do anything according to the html5 spec
    |--------
    | So the view attribute will create create a "virtual" route with the same name as the view except the view
    | path can be omitted (Default "base path" for these views is env('project.views.path') */

    /* normal implementation of the following attributes */
    public $id;
    public $style;
    public $class;
    /* The name of valid browsing context (HTML5), or the name of the control (HTML 4).
     * List of types: https://developer.mozilla.org/en-US/docs/Glossary/MIME_type */
    public $type = 'text/html';
    public $defaultype = 'text/html';

    /* This Boolean attribute indicates if the type attribute and the actual content
     * type of the resource must match to be used. */
    public $typemustmatch = false;

    /* The name of valid browsing context (HTML5), or the name of the control (HTML 4). */
    public $name;

    /* The width of the display resource, in CSS pixels.
     * (Absolute values only.  NO percentages) */
    public $height;
    public $width;

    /* The form element, if any, that the object element is associated with (its form owner).
     * The value of the attribute must be an ID of a <form> element in the same document. */
    public $form;

    /* A hash-name reference to a <map> element; that is a '#' followed by the value of a
     * name of a map element. */
    public $usemap;
    public $children;
    public $unique;

    public function __construct() {
        $this->element = __class__;
        parent::__construct(__class__);
    }

    public function render() {
        if (isset($this->src)) {
            $this->data = $this->src;
        }

        if (isset($this->view)) {
            // if a route property is not specified the view will be used as the route
            if (!isset($this->route) || empty($this->route)) {
                $this->route = str_replace('.phtml','',$this->view);
            }
            if (isset($this->viewdata)) {
                $this->viewdata = array_merge_recursive(Heepp::data($this->viewdata),[
                    'request' => [
                        'count' => 0,
                        'log'   => [
                            '${session.user.id|1}.' => [
                                'log' => [
                                    'ip'       => '${app.request.ip}',
                                    'method'   => '${app.request.method}',
                                    'location' => (object)[
                                        'url' => '${app.request.url},',
                                        'referer' => '${app.request.referer}'
                                    ]
                                ]
                            ],
                        ]
                    ]
                ]);
            } else {
                $fileName = basename($this->view);
                $this->route = str_replace('/'.$fileName,'',$this->view);
            }
        }

        if (!isset($this->route) && empty($this->route)) {
            route::{$this->verb}($this->route);
            //call_user_func_array();
        } elseif(isset($this->view)) {
            $this->route = str_replace('.phtml','',$this->view);
        }

        /* Can only reference "$_GET" method routes */
        if (isset($this->route)) {
            $this->data = env('http.host').$this->route;
        }
        //pd($this->data);
        $this->unique = uniqid();
        $this->children = $this->child;
        //pd($this->src);

        return view::mold('core-object.phtml',__DIR__,$this);
        //$html = (new view('core-object.phtml',__DIR__,$this))->html;
        //pd($html);
    }
}
