<?php
namespace core\element\core;
use core\extension\ui\view;
use core\Element;

class toast extends Element {
    /* 'top-right', 'top-center', 'top-left',
     * 'bottom-right', 'bottom-center', 'bottom-left' */
    public $position       = 'bottom-center';
    /* The duration the toast will stay visible before it fades out (ms)
    public $duration       = 5000;
    /* a css class that will be added to every toast */
    public $className      = 'toast-notification';
    public $containerClass = '';
    public $message;
    /* The ACTION text on the toast. Example: SAVE
    public $actionText     = '';
    /* js function to call when action is clicked */
    public $actionCall     = 'undefined';
    /* project route to load when action is clicked */
    public $actionRoute    = 'undefined';
    /* Make the toast the full with of the screen */
    public $fullWidth      = 'false';
    public $fitToScreen    = 'false';
    /* Icon object icon { name: 'check', after : true, color : '#222' } */
    public $icon;
    public $iconPosition   = 'before';
    public $type           = 'success';
    public $theme          = 'primary';
    public $completeCall   = 'undefined';

    public function __construct() {
        $this->element = __class__;
        parent::__construct(__class__);
    }

    public static function success(string $message,array $options = []) {
        $options['message'] = $message;
        $options['type'] = 'success';

        $toast = new toast();
        $toast->mergeOptionsWithProps($options);
        $toast->render();
    }

    public function mergeOptionsWithProps($options) {
        if (is_array($options) && count($options)) {
            foreach($options as $prop => $value) {
                if (property_exists($this,$prop)) {
                    $this->{$prop} = $value;
                }
            }
        }
    }

    public function render() {
        return view::mold('toast.phtml',__DIR__,$this);
    }
}
