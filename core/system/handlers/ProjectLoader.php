<?php
namespace core\system\handlers;
use core\Heepp;
use core\Output;
use core\system\route;
use core\extension\helper\Asset;

class ProjectLoader extends Loader {
    public static $projectList = [];

    public static function projectAutoLoader($include) {
        foreach((array)Heepp::data('env.projects') as $project => $settings) {
            if (is_object($settings) && isset($settings->path)) {
                self::$projectList[$project] = $settings->path;
            }
        }
        $exp = explode('\\',$include);

        if (Heepp::dataKeyExists('env.projects.'.$exp[0].'.path') || strpos($include,self::$watchNamespace.'\\') !== false) {
            self::$callingClass = self::getCallingClass();
            $projectPath = env('projects.'.$exp[0].'.path').DS;
            $path = $projectPath.'controllers'.DS.$exp[1].'.php';
            if ($exp[0] !== 'core' && !file_exists($path)) {
                $path = env('project.controllers.path').$exp[1].'.php';
            }

            if ($exp[0] !== 'core' && file_exists($path)) {
                self::$includedFiles[] = $include;
                if (!in_array($path,self::$includedFiles)) {
                    new \core\extension\parser\env\DotEnv($projectPath,'.env',$exp[0]);
                    include $path;
                    self::$includedFiles[] = $path;
                    class_alias($exp[1],$include);
                    self::$includedFiles[] = $include;
                }
                return true;
            }
        }
        self::$watchNamespace = null;
        self::$sessionExists = self::sessionExists();

        // Load shortcuts to memory
        if (!sessionSet('shortcuts')) {
            if (!isset($_SESSION['core'])) {
                $_SESSION['core'] = new \stdClass();
            }

            //if (!sessionSet('shortcuts')) {
            //    session('shortcuts',new \stdClass());
            //    //$_SESSION['core']->shortcuts = new \stdClass();
            //}

            $xml = loadXML(env('core.shortcuts.path'));
            foreach($xml->children() as $child) {
                /** @var \simpleXMLElement $child */
                $element = str_replace('.','_',(string)$child->getName());
                session('shortcuts.'.$element,str_replace('.','_',(string)$child['class']));
            }

            if (file_exists(env('project.shortcuts.path'))) {
                $xml = loadXML(env('project.shortcuts.path'));
                foreach($xml->children() as $child) {
                    /*
                     * Remove the comment below if you want to add the project to the namespace. This will assist if you want to have classes with
                     * the same name in the same namespace
                     */
                    $element = str_replace('.','_',(string)$child->getName());
                    session('shortcuts.'.$element,str_replace('.','_',(string)$child['class']));
                }
            }
        }

        // Check if shortcut exists
        if (self::$sessionExists && sessionSet('shortcuts.'.$include)) {
            $include = session('shortcuts.'.$include);
        }
        // Check if file is already included
        if (!in_array($include,self::$includedFiles)) {
            self::$includedFiles[] = $include;
            // Include class or trait
            $include = str_replace('\\',DS,$include);
            if (file_exists(BASE_PATH.$include.'.php')) {
                include BASE_PATH.$include.'.php';
                return true;
            }
            if (file_exists(env('project.controllers.path').$include.'.php')) {
                include env('project.controllers.path').$include.'.php';
                return true;
            }
            if (file_exists(env('project.controllers.path').$include.DS.$include.'.php')) {
                include env('project.controllers.path').$include.DS.$include.'.php';
                return true;
            }
            return false;
        }
        return true;
    }

    public static function loadAsset() {
        $path = env('project.path').input('controller').input('params');
        Asset::get($path);
    }

    public static function getCallingClass() {
        $Bugtrace = debug_backtrace();
        foreach($Bugtrace as $trace) {
            if ($trace['function'] == 'autoLoad') {
                return $trace['args'][0];
            }
        }
    }

    public static function loadProject() {
        if (file_exists(env('project.path').'project.php')) {
            include env('project.path').'project.php';
        }
    }

    public static function loadJSConstants() {
        die(file_get_contents(env('project.path').'uiConstants.js'));
    }

    // $fromProject = if output is being rendered from loading the project
    public static function doOutput($output,$fromProject = false) {
        $result = [];
        if ($fromProject) {
            return $result;
        }
        echo json_encode($output->ui);
    }

    private static function buildGETParams($method,$arguments = []) {
        $getParams[] = '';
        $getParams[] = $method;
        if (count((array)$arguments) > 0) {
            foreach((array)$arguments as $arg) {
                $getParams[] = $arg;
            }
        }
        setInput('params',implode('/',$getParams));
    }

    /**
     * @param null  $controller
     * @param null  $method
     * @param array $arguments
     * @param bool  $doOutput
     *
     * @return mixed|string
     */
    public static function loadController($controller = null,$method = null,$arguments = [],$doOutput = true) {
        $output = null;
        if (isset($controller)) {
            setInput('controller',$controller);
        }
        if (isset($method)) {
            self::buildGETParams($method,$arguments);
        }
        if (!$doOutput) {
            $output = '';
        }

        if (!inputSet('controller')) {
            return $output;
        }
        // Use a '.' instead of '\\' to refer to a class in a namespace
        setInput('controller',str_replace('.','\\',input('controller')));
        $class = input('controller');
        if (class_exists($class) || (sessionSet('shortcuts.'.$class) && class_exists(session('shortcuts.'.$class)))) {
            if (sessionSet('shortcuts.'.$class)) {
                $class = session('shortcuts.'.$class);
            }
            $object = new $class;
        } elseif(class_exists(env('project.name').'\\'.$class)) {
            $classAlias = $class;
            class_alias(env('project.name').'\\'.$class,$classAlias);
            $object = new $classAlias;
        } else {
            $error = "Controller: '".input('controller')."' does not exist";
            Heepp::data('app.request.error.message',$error);
            $search = 'app.request.route.'.Heepp::data('app.request.method');
            Heepp::data('app.request.error.route',str_replace(':','/',str_replace($search,'',Heepp::data('app.request.route'))));
            return route::load('404','get','core');
        }

        //The class could have been changed if it was in the shortcuts
        if (input('controller') != $class) {
            $class = input('controller');
            if (class_exists($class)) {
                $object = new $class;
            }
            //else {
                //$error = "Controller: '".$_GET['controller']."' does not exist";
            //}
        }

        $params = [];
        if (!inputEmpty('params')) {
            $qryStr = input('params');
            $result = explode('/',$qryStr,3);
            $function = $result[1];
            if (isset($result[2])) {
                $param = $result[2];
                $params = explode('/',$param);
            }
            env('request.controller',input('controller'));
            //$_SERVER['CALLED_CONTROLLER'] = $_GET['controller'];
            env('request.method',$function);
            //$_SERVER['CALLED_METHOD'] = $function;
            if (isset($params)) {
                env('request.params',$params);
                //$_SERVER['CALLED_PARAMS'] = $params;
            }

            if (isset($result[1])) {
                if(isset($result[2])) {
                    if (method_exists($object,'hasAccess')) {
                        if ($object->hasAccess($function) && class_exists(input('controller'))) {
                            $obj = new \ReflectionMethod(input('controller'),$function);
                            $output = $obj->invokeArgs($object,$params);
                        }
                    } else {
                        $obj = new \ReflectionMethod(input('controller'),$function);
                        $output = $obj->invokeArgs($object,$params);
                    }
                } else {
                    if (method_exists($object,'hasAccess')) {
                        if ($object->hasAccess($function)) {
                            if (method_exists($object,$function)) {
                                $output = $object->$function();
                            } else {
                                $objectName = get_class($object);
                                $error = "Method '$function' does not exist in Class '$objectName'";
                                echo '{"notify":{"type":"error","message":"'.$error.'"}}';
                                exit;
                            }
                        }
                    } else {
                        if ($object->hasAccess($function)) {
                            if (method_exists($object,$function)) {
                                $output = $object->$function();
                            } else {
                                $objectName = get_class($object);
                                $error = "Method '$function' does not exist in Class '$objectName'";
                                echo '{"notify":{"type":"error","message":"'.$error.'"}}';
                                exit;
                            }
                        }
                    }
                }
                //if (LOG && isset($coreLastLogId)) {
                //    logEnd($coreLastLogId);
                //}
            } else {
                $result['html'] = $output->html;
            }
            //$result = [];
            //$found = 0;
        } else {
            /* Check if index method in the controller and call it */
            if (method_exists($object,'index')) {
                $output = $object->index();
            } else
            /* Check if a method exists with the same name as the controller and call it */
            if (method_exists($object,$_GET['controller'])) {
                $output = $object->$_GET['controller']();
            }
        }

        if ($doOutput) {
            $output       = Output::getInstance();
            $output->hash = self::$coreLastLogId;
            self::doOutput($output);
        } else {
            return $output;
        }
    }
}

/* Define Auto Loader */
spl_autoload_register([ProjectLoader::class,'projectAutoLoader']);
