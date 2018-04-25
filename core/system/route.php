<?php
namespace core\system;
use core\Heepp;

/*--------------------------------------------------------------------------
| You can register any routes that respond to any HTTP verb
| Supported VERBS: get, post, put, patch, delete, options
|---------------------------------------------------------------------------
*/
class route extends Heepp {
    public static $routes         = [];
    public static $route          = '/';
    public static $appType        = 'web';
    public static $appTypePrefix  = '';
    public static $appTypeBefore  = [];
    public static $appTypeAfter   = [];
    public static $routeExists    = false;
    public static $routeGroup     = '';
    public static $routePrefix    = '';
    public static $yieldOutput    = [];
    public static $requestRoute   = '';
    public static $groupBefore    = [];
    public static $groupAfter     = [];
    public static $before         = [];
    public static $after          = [];
    public static $exists         = false;
    public static $routeOptions   = [];
    public static $routeDetails;

    private static function resetProperties() {
        self::$routes         = [];
        self::$route          = '/';
        self::$appType        = 'web';
        self::$appTypePrefix  = '';
        self::$appTypeBefore  = [];
        self::$appTypeAfter   = [];
        self::$routeExists    = false;
        self::$routeGroup     = '';
        self::$routePrefix    = '';
        self::$yieldOutput    = [];
        self::$requestRoute   = '';
        self::$groupBefore    = [];
        self::$groupAfter     = [];
        self::$before         = [];
        self::$after          = [];
        self::$exists         = false;
        self::$routeOptions   = [];
        self::$routeDetails   = null;
    }

    public static function appType($appType) {
        self::$appType       = $appType;
        self::$appTypePrefix = '';
        if (Heepp::dataKeyExists('app.request.types.'.self::$appType.'.prefix')) {
            self::$appTypePrefix = Heepp::data('app.request.types.'.self::$appType.'.prefix');
        }
        self::$appTypeBefore = [];
        if (Heepp::dataKeyExists('app.request.types.'.self::$appType.'.before')) {
            self::$appTypeBefore = (array)Heepp::data('app.request.types.'.self::$appType.'.before');
        }
        self::$appTypeAfter = [];
        if (Heepp::dataKeyExists('app.request.types.'.self::$appType.'.after')) {
            self::$appTypeAfter = Heepp::data('app.request.types.'.self::$appType.'.after');
        }
        self::$before = self::$appTypeBefore;
        self::$after  = self::$appTypeAfter;
    }

    public static function group($options,$routes) {
        // Add Prefix
        if (isset($options['prefix'])) {
            self::prefix($options['prefix']);
        }
        if (isset($options['before'])) {
            self::$groupBefore = $options['before'];
        }
        if (isset($options['after'])) {
            self::$groupAfter = $options['after'];
        }

        if (is_array(self::$groupBefore)) {
            self::$before = array_merge(self::$appTypeBefore,self::$groupBefore);
        } else {
            self::$appTypeBefore[] = self::$groupBefore;
            self::$before = array_merge(self::$before,self::$appTypeBefore);
        }
        self::$after = array_merge(self::$appTypeAfter,self::$groupAfter);

        $routes->__invoke();
        self::$groupBefore = [];
        self::$groupAfter  = [];
        self::$before      = array_replace_recursive(self::$appTypeBefore,self::$groupBefore);
        self::$after       = array_replace_recursive(self::$appTypeAfter,self::$groupAfter);
        self::prefix('');
    }

    public static function prefix($prefix) {
        if (0 === strpos($prefix,'/')) {
            $prefix = substr($prefix,0,2);
        }
        if (substr($prefix,-1,1) === '/' && $prefix !== '') {
            $prefix .= '/';
        }
        self::$routePrefix = $prefix;
    }

    private static function checkRoute($verb,$route,$actions = null) {
        self::$routeOptions = (object)[];
        if (in_array($verb,Heepp::data('app.request.types.'.self::$appType)->allowedVerbs)) {
            self::$routeOptions->verb          = $verb;
            self::$routeOptions->route         = $route;
            self::$routeOptions->requestRoute  = '';
            self::$routeOptions->appType       = self::$appType;
            self::$routeOptions->before        = self::$before;
            self::$routeOptions->after         = self::$after;
            self::$routeOptions->contentType   = env('app.header.type');
            self::$routeOptions->routePrefix   = self::$routePrefix;
            self::$routeOptions->appTypePrefix = self::$appTypePrefix;
            self::$routeOptions->actions       = (object)[];
            self::$routeOptions->data          = (object)[];

            if (is_array(self::$routeOptions->before)) {
                $actions = array_merge_recursive(self::$routeOptions->before,$actions);
            }
            if (is_array(self::$routeOptions->after)) {
                $actions = array_merge_recursive($actions,self::$routeOptions->after);
            }

            if (isset($actions) && is_array($actions)) {
                foreach($actions as $i => $action) {
                    if (is_string($action)) {
                        self::$routeOptions->actions->string[] = $action;
                    } elseif(is_array($action)) {
                        self::$routeOptions->actions->object[] = (object)$action;
                    } elseif(is_closure($action)) {
                        self::$routeOptions->actions->closure[] = $action;
                    }
                }
            } elseif(isset($actions) && is_closure($actions)) {
                self::$routeOptions->actions->closure[] = $actions;
            } elseif(isset($actions) && is_string($actions)) {
                self::$routeOptions->actions->string[] = $actions;
            }
            self::addRoute(self::$routeOptions);
        }
    }

    public static function addRoute($options) {
        $routeSoFar = self::routePrefix($options->verb,$options->appTypePrefix,$options->routePrefix);
        $params = explode('/',$options->route);
        foreach($params as $param) {
            if (strpos($param,'${') === 0 || strpos($param,'?{') === 0) {
                if (strpos($param,'${') === 0) {
                    $key = str_replace(['${','}'],'',$param);
                    $exp = explode($routeSoFar,self::$requestRoute);
                    if (isset($exp[1])) {
                        $expl = explode(':',$exp[1]);
                        if (isset($expl[1])) {
                            if ($expl[1] == '0') {
                                $param                 = ':'.$expl[1];
                                $options->data->{$key} = $expl[1];
                            } elseif (!empty($expl[1])) {
                                $param                 = ':'.$expl[1];
                                $options->data->{$key} = $expl[1];
                            } else {
                                return false;
                            }
                        }
                    }
                }

                if (strpos($param,'?{') === 0) {
                    $key = str_replace(['?{','}'],'',$param);
                    $exp = explode($routeSoFar,self::$requestRoute);
                    if (isset($exp[1])) {
                        $expl = explode(':',$exp[1]);
                        if (isset($expl[1])) {
                            if ($expl[1] == '0') {
                                $param = ':'.$expl[1];
                                $options->data->{$key} = $expl[1];
                            } elseif (!empty($expl[1])) {
                                $param = ':'.$expl[1];
                                $options->data->{$key} = $expl[1];
                            }
                        } else {
                            $param = '';
                        }
                    } else {
                        $param = '';
                    }
                }
            } else {
                $param = ':'.$param;
            }
            if (strpos(self::$requestRoute,$routeSoFar) !== false) {
                $routeSoFar .= $param;
            }
        }
        if ($routeSoFar == self::$requestRoute) {
            $options->requestRoute = self::$requestRoute;
            self::$exists = true;
            self::setRouteDetails($options);
            Heepp::data(route::$requestRoute.':match',self::getRouteDetails());
        }
    }

    public static function setRouteDetails($options) {
        // When routes are used the libraries should be loaded each time
        Heepp::forgetKey('session.libraries');
        Heepp::data('app.request.data',$options->data);
        self::$routeDetails = $options;
    }

    public static function __callStatic($verb,$routeWithActions) {
        if ($verb == 'options') {
            serve::object((object)Heepp::data('app.request.types'.self::$appType)->allowedVerbs);
        }
        $route   = '/';
        $actions = [];
        foreach($routeWithActions as $i => $routeOrAction) {
            if ($i == 0) {
                $route = $routeOrAction;
            } else {
                $actions[] = $routeOrAction;
            }
        }
        unset($routeWithActions[0]);
        self::match([$verb],$route,$actions);
    }

    public static function match($verbs,$route,$actions) {
        if (self::$exists) {
            return true;
        }
        if ($route === '/') {
            $route = 'root';
        } else {
            if (strpos($route,'/') === 0) {
                $route = substr($route,1);
            }
        }
        $route = str_replace('//','/',$route);
        self::$route = $route;
        foreach($verbs as $verb) {
            self::checkRoute($verb,$route,$actions);
        }
    }

    public static function load($route,$method = 'get',$appType = 'web') {
        Heepp::data('app.request.method',$method);
        self::resetProperties();
        self::appType($appType);
        if (!empty(self::$appTypePrefix)) {
            route::$requestRoute = 'app.request.route.'.$method.':'.self::$appTypePrefix.':'.str_replace('/',':',$route);
        } else {
            route::$requestRoute = 'app.request.route.'.$method.':'.str_replace('/',':',$route);
        }
        Heepp::data('app.request.route',route::$requestRoute);
        //pdc(Heepp::data('app.request'));
        foreach(Heepp::data('app.request.types') as $type => $options) {
            if (file_exists($options->file) && $options->prefix === self::$appTypePrefix) {
                include $options->file;
            }
        }
        route::invoke(route::getRouteDetails());
    }

    public static function getRoutePrefix($verb = null,$appTypePrefix = '',$routePrefix = '') {
        return self::routePrefix($verb,$appTypePrefix,$routePrefix);
    }

    private static function routePrefix($verb = null,$appTypePrefix = '',$routePrefix = '') {
        if (!isset($verb)) {
            $verb = env('request.method',null,'get');
        }
        if (!empty($appTypePrefix)) {
            $prefix = 'app.request.route.'.$verb.':'.$appTypePrefix;
        } else {
            $prefix = 'app.request.route.'.$verb;
        }

        if (!empty($routePrefix)) {
            $prefix .= ':'.$routePrefix;
        }
        return $prefix;
    }

    public static function exists() {
        return self::$exists;
    }

    public static function getRouteDetails() {
        return self::$routeDetails;
    }

    public static function invoke($details) {
        Heepp::data('app.request.options',$details);
        if(is_object($details)) {
            Heepp::setHeader('app-route',$details->route);
            foreach($details->actions as $objType => $objActions) {
                if (is_array($objActions)) {
                    foreach($objActions as $objAction) {
                        $serveResult = serve::$objType($objAction);
                        $type        = key($serveResult);
                        $value       = current($serveResult);
                        render::$type($value);
                    }
                }
            }
            render::output();
        }
    }

    public static function getAppType() {
        return self::$appType;
    }

    public static function setAppType($appType) {
        self::$appType = $appType;
    }
}
