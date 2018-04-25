<?php
namespace core\extension\object;
/**
 * Description of AnObj
 * Creates an Anonymous PHP Object. PHP7 Has this built in but this will work PHP5.3+
 * @author Charl
 * @date 2016-04-08
 * @time 16:38
 * 
 * @example
        $person = new AnObj(array(
            "name" => "nick",
            "age" => 23,
            "friends" => ["frank", "sally", "aaron"],
            "sayHi" => function() {return "Hello there";}
        ));
        echo $person->name . ' - ';
        echo $person->age . ' - ';
        echo $person->sayHi();
 * 
 * if you want to make a method that needs to use the "$this" pointer you can, 
 * but you have to add that method to the class after the initial construction. 
 * So, in your example, after $person is declared you could declare:
 * $person->getFriend = function($id) use($person) { return $person->friends[$id]; };
 */

class AnObj extends \core\Heepp {

    protected $methods = array();
    protected $properties = array();

    function __construct(array $options) {
        parent::__construct();

        foreach ($options as $key => $opt) {
            //integer, string, float, boolean, array
            if (is_array($opt) || is_scalar($opt)) {
                $this->properties[$key] = $opt;
                unset($options[$key]);
            }
        }
        $this->methods = $options;
        foreach ($this->properties as $k => $value) {
            $this->{$k} = $value;
        }
    }

    public function __call($name, $arguments) {
        $callable = null;
        if (array_key_exists($name, $this->methods)) {
            $callable = $this->methods[$name];
        } elseif (isset($this->$name)) {
            $callable = $this->$name;
        }

        if (!is_callable($callable)) {
            throw new Exception("Method {$name} does not exists");
        }
        return call_user_func_array($callable, $arguments);
    }
}
