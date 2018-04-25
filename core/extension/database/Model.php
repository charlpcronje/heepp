<?php
namespace core\extension\database;
use Exception;
use core\extension\Extension;
use core\mold\php\ModelItem;
use core\mold\php\CollectionMold;
use core\Heepp;

class Model extends Extension implements \Iterator {
    use ModelSetters;
    use ModelGetters;
    use ModelCollection;
    
    // Connection
    private $connection;
    private $_connection;
    
    // Settings
    private $saveLog                 = true;
    private $saveSelectLog           = false;
    private $useDefaults             = false;
    private $ignoreTriggers          = false;
    
    // Model Definition
    private $model;
    private $modelSingle;
    private $modelMany;
    private $modelAlias;
    private $table;
    private $primaryKey;
    private $path;
    private $combKey;
    private $constants               = [];
    private $xml;
    
    // Validation
    private $validateValues          = true;
    private $validations             = [];
    private $lookups                 = [];
    private $requirements            = [];
    private $checkRequired           = true;
    private $defaults                = [];
    
    // SQL Select Query
    private $columns;
    private $objectProperties        = [];
    private $joins                   = [];
    private $filter                  = [];
    private $group                   = [];
    private $order                   = [];
    private $limit                   = [];
    
    // Dataset Details
    private $dataSet;
    private $dataSetAlias;
    private $dataSetDescription;
    private $dataSetColumns         = [];
    private $dataSetFilter          = [];
    private $dataSetFilterId        = 0;
    private $dataSetCurrentBinder   = 'AND';
    private $dataSetBinderGroup     = 1;
    private $dataSetGroup           = [];
    private $dataSetOrder           = [];
    private $dataSetLimit           = [];
    private $dataSetSums            = [];
    private $dataSetConcat          = [];
    private $dataSetColumnCount     = 0;
    private $dataSetClosingBrackets = 0;
    private $dataSetOpeningBrackets = 0;
    private $dataSetReturn          = 'object';
    
    // Expression Details
    private $expressionParams       = [];
    
    // Query Results
    public $results                 = [];
    public $gotResults              = false;
    public $foundDouble             = false;
    public $affectedRows            = 0;
    
    // Logs
    public $sqlQuery;
    private $logQuery;
    private $lastInsertId;
    private $prevDataSetFilterId   = [];
    
    // Post Processing
    private $triggers              = [];

    public function __construct($model = null) {
        parent::__construct();
        if (isset($model)) {
            $this->setModel($model);
        }
    }

    public static function __callStatic($model,$setAndParams = null) {
        if ($setAndParams !== null && count($setAndParams) > 0) {
            if (is_string($setAndParams)) {
                return self::mold($model,$setAndParams);
            }

            if (is_object($setAndParams)) {
                $dataSet = key($setAndParams);
                $params = $setAndParams;
                return self::mold($model,$dataSet,$params);
            }

            if (is_array($setAndParams) && is_string($setAndParams[0]) && is_array($setAndParams[1])) {
                return self::mold($model,$setAndParams[0],$setAndParams[1]);
            }

            if (is_array($setAndParams) && is_string($setAndParams[0]) && isset($setAndParams[2])) {
                $dataSet = $setAndParams[0];
                $params = [];
                for ($x = 1,$xMax = count($setAndParams); $x <= $xMax; $x++) {
                    $params[key($setAndParams[$x])] = $setAndParams[$x];
                }
                return self::mold($model,$dataSet,$params);
            }
        } else {
            return self::mold($model);
        }
    }
    
    public static function mold($model,$dataSet = null,$params = [],$return = 'items') {
        $collection = CollectionMold::mold($model.'Collection');
        if (isset($dataSet)) {
            $collection->loadDataSet($dataSet);
        }
        if (is_array($params) && count($params) > 0) {
            foreach($params as $key => $value) {
                $collection->{$key}($value);
            }
        }
        $moldReturn = $collection;
        if ($dataSet !== null) {
            $collection->runDataSet();
            $moldReturn = $collection->get();
        }
        if ($return === 'items') {
            return $moldReturn;
        } elseif($return === 'collection') {
            return $collection;
        }
    }

    public static function collection($model,$dataSet = null,$params = []) {
        return self::mold($model,$dataSet,$params,'collection');
    }


    
    public function modelExists($model = null) {
        if (isset($model)) {
            $this->model = $model;
        }
        $exp = [];
        // Check if a model from another project is being used
        if (strpos($this->model,'/') !== false || strpos($this->model,'.') !== false) {
            if (strpos($this->model,'/') !== false) {
                $exp = explode('/',$this->model);
            } elseif(strpos($this->model,'.') !== false) {
                $exp = explode('.',$this->model);
            }
            
            if (Heepp::dataKeyExists('env.projects.'.$exp[1].'.path')) {
                $this->model = str_replace('/'.$exp[1].'/','',$this->model);
                env('project.models.path',env('projects.'.$exp[1].'.path'.DS.'models'.DS),env('project.models.path'));
            } else {
                new Exception('You attempted to use model from project: "'.$exp[1].'" but the project could not be found');
            }
            
        }
        $this->path = env('project.models.path').$this->model.'.xml';
        if (file_exists($this->path)) {
            $xml = simplexml_load_string('<model>'.file_get_contents($this->path).'</model>','SimpleXMLElement',LIBXML_NOCDATA);
            $this->xml = $xml;
            $this->getModelConnection($xml);
            $this->constants = (object)get_defined_constants(true)['user'];
            $this->loadModel($xml);
            return true;
        }
        return false;
    }

    public function setModel($model = null) {
        if (!isset($model)) {
            throw new Exception('No model specified');
        }
        $this->model = $model;
        // Checking if the model exists also loads the model
        if (!$this->modelExists()) {
            throw new Exception('Model: "'.$model.'" could not be found.');
        }
    }

    private function getModelConnection($xml) {
        $this->connection = (string)$xml->database['connection'];
        $this->modelAlias = (string)$xml->database['alias'];
    }
    
    public function setExpressionParam($param,$value) {
        $this->lastCommand = 'setExpressionParam';
        $this->expressionParams[$param] = $value;
    }
    
    public function setParams($params = []) {
        foreach($params as $key => $value) {
            $this->$key($value);
        }
    }

    private function findDeepestChildren($children) {
        if ($children->children()->count() > 0) {
            foreach($children->children() as $child) {
                if ($child->count()) {
                    $this->findDeepestChildren($child);
                }
            }
        } else {
            return $children;
        }
    }

    private function addBraces($children,$lvl) {
        if (!is_object($children)) {
            $children = $children[0];
        }
        if ($children != null) {
            if (strtolower($children->getName()) == 'or' || strtolower($children->getName()) == 'and') {
                $i = 0;
                // Step trough all children on this level
                foreach($children->children() as $child) {
                    $i++;
                    /* 
                     * But if this level is an 'or' or this level is an 'and' then it means this level only wraps a few 'ors' and 'ands'
                     * So now I must get the first child of this lvl to add an '('
                     */
                    if ($i == 1 && (strtolower($child->getName()) == 'or' || strtolower($child->getName()) == 'and')) {
                        $a = 0;
                        $firstChild = [];
                        foreach($child->children() as $subChild) {
                            $subChild->addAttribute('lvl',$lvl);
                            $a++;
                            if ($a == 1) {
                                $firstChild = $subChild;
                            }
                        }
                        $this->dataSetOpeningBrackets++;
                        if (!isset($firstChild['beginBraces'])) {
                            $firstChild->addAttribute('beginBraces',1);
                        } else {
                            $braces = $firstChild['beginBraces'];
                            $firstChild['beginBraces'] = (int)$braces + 1;
                        }
                    }
                    if ((strtolower($child->getName()) == 'or' || strtolower($child->getName()) == 'and') && ($this->dataSetClosingBrackets < $this->dataSetOpeningBrackets)) {
                        $a = 0;
                        $lastChild = [];
                        foreach($child->children() as $subChild) {
                            $a++;
                            if ($a == count($child->children())) {
                                $lastChild = $subChild;
                            }
                        }
                        $this->dataSetClosingBrackets++;
                        if (!isset($lastChild['endBraces'])) {
                            $lastChild->addAttribute('endBraces',1);
                        } else {
                            $braces = $lastChild['endBraces'];
                            // Must check if there are more on the same lvl and add it to the last instance
                            $lastChild['endBraces'] = (int)$braces + 1;
                        }
                    } else {
                        if ($i == 1) {
                            if (!isset($child['beginBraces'])) {
                                $child->addAttribute('beginBraces','(');
                                $this->dataSetOpeningBrackets++;
                            } else {
                                $braces = $child['beginBraces'];
                                $child['beginBraces'] = (int)$braces + 1;
                            }
                        }
                        if ($i == count($children) && ($this->dataSetClosingBrackets < $this->dataSetOpeningBrackets)) {
                            $this->dataSetClosingBrackets++;
                            if (!isset($child['endBraces'])) {
                                $child->addAttribute('endBraces',')');

                            } else {
                                $braces = $child['endBraces'];
                                $child['endBraces'] = (int)$braces + 1;
                            }
                        }
                    }
                    if (isset($child['beginBraces']) || isset($child['endBraces'])) {
                        $child->addAttribute('lvl',$lvl);
                    }
                }
            }

            $parent = $children->xpath('..');
            if ((string)$parent[0]->getName() != 'filter') {
                $this->addBraces($children->xpath('parent::*'),$lvl++);
            }
        }
    }

    private function setFilters($filters,$table,$binder = 'AND',$joinId = -1) {
        $i = 0;
        foreach($filters->children() as $child) {
            $i++;
            $this->dataSetCurrentBinder = strtoupper($binder);
            if (strtolower($child->getName()) == 'or' || strtolower($child->getName()) == 'and') {
                $deepestChildren = $this->findDeepestChildren($child->children());
                $this->addBraces($deepestChildren,1);

                $this->setFilters($child,$table,strtoupper($child->getName()),$joinId);
                if (isset($this->dataSetFilter[$this->prevDataSetFilterId])) {
                    $this->dataSetFilter[$this->prevDataSetFilterId]['binder'] = 'AND';
                }
            }
            $this->dataSetFilter[$this->dataSetFilterId]['joinId'] = $joinId;

            /*Check if there is an '.' in the name of the column.
             * If there is: use the column name as is. If there is not add the current table name to the beginning of the column name
             */
            if (strpos($child->getName(),'.') !== false) {
                $column = $child->getName();
            } else {
                $column = $table.'.'.$child->getName();
            }
            foreach($child->attributes() as $attr) {
                switch(strtolower($attr->getName())) {
                    case 'beginbraces':
                        $this->dataSetFilter[$this->dataSetFilterId]['beginBraces'] = (string)$attr;
                    break;
                    case 'endbraces':
                        $this->dataSetFilter[$this->dataSetFilterId]['endBraces'] = (string)$attr;
                    break;
                    case 'lvl':
                        $this->dataSetFilter[$this->dataSetFilterId]['lvl'] = (string)$attr;
                    break;
                    case 'greater':
                    case 'greaterthan':
                    case 'more':
                    case 'morethan':
                    case 'larger':
                    case 'largerthan':
                        $this->dataSetFilter[$this->dataSetFilterId]['column'] = $column;
                        $this->dataSetFilter[$this->dataSetFilterId]['filter'] = 'greater';
                        $this->dataSetFilter[$this->dataSetFilterId]['filterOperator'] = '>';
                        $this->dataSetFilter[$this->dataSetFilterId]['value'] = (string)$attr;
                        $this->dataSetFilter[$this->dataSetFilterId]['binder'] = $this->dataSetCurrentBinder;
                    break;
                    case 'less':
                    case 'lessthan':
                    case 'smaller':
                    case 'smallerthan':
                        $this->dataSetFilter[$this->dataSetFilterId]['column'] = $column;
                        $this->dataSetFilter[$this->dataSetFilterId]['filter'] = 'less';
                        $this->dataSetFilter[$this->dataSetFilterId]['filterOperator'] = '<';
                        $this->dataSetFilter[$this->dataSetFilterId]['value'] = (string)$attr;
                        $this->dataSetFilter[$this->dataSetFilterId]['binder'] = $this->dataSetCurrentBinder;
                    break;
                    case 'greaterorequal':
                    case 'greaterorequals':
                    case 'greaterthanorequal':
                    case 'greaterthanorequals':
                    case 'greaterorsame':
                    case 'greaterorsameas':

                    case 'moreorequal':
                    case 'moreorequals':
                    case 'morethanorequal':
                    case 'morethanorequals':
                    case 'morethanorsame':
                    case 'morethanorsameas':

                    case 'largerorequal':
                    case 'largerorequals':
                    case 'largerthanorequal':
                    case 'largerorsame':
                    case 'largerorsameas':
                        $this->dataSetFilter[$this->dataSetFilterId]['column'] = $column;
                        $this->dataSetFilter[$this->dataSetFilterId]['filter'] = 'greaterorequal';
                        $this->dataSetFilter[$this->dataSetFilterId]['filterOperator'] = '>=';
                        $this->dataSetFilter[$this->dataSetFilterId]['value'] = (string)$attr;
                        $this->dataSetFilter[$this->dataSetFilterId]['binder'] = $this->dataSetCurrentBinder;
                    break;
                    case 'lessorequal':
                    case 'lessorequals':
                    case 'lessthanorequal':
                    case 'lessthanorequals':
                    case 'lessorsame':
                    case 'lessorsameas':

                    case 'smallerorequal':
                    case 'smallerorequals':
                    case 'smallerthanorequal':
                    case 'smallerthanorequals':
                    case 'smallerorsame':
                    case 'smallerorsameas':
                        $this->dataSetFilter[$this->dataSetFilterId]['column'] = $column;
                        $this->dataSetFilter[$this->dataSetFilterId]['filter'] = 'lessorequal';
                        $this->dataSetFilter[$this->dataSetFilterId]['filterOperator'] = '<=';
                        $this->dataSetFilter[$this->dataSetFilterId]['value'] = (string)$attr;
                        $this->dataSetFilter[$this->dataSetFilterId]['binder'] = $this->dataSetCurrentBinder;
                    break;
                    case 'in':
                        $this->dataSetFilter[$this->dataSetFilterId]['column'] = $column;
                        $this->dataSetFilter[$this->dataSetFilterId]['filter'] = 'in';
                        $this->dataSetFilter[$this->dataSetFilterId]['filterOperator'] = 'IN';
                        $this->dataSetFilter[$this->dataSetFilterId]['value'] = (string)$attr;
                        $this->dataSetFilter[$this->dataSetFilterId]['binder'] = $this->dataSetCurrentBinder;
                    break;
                    case 'notin':
                        $this->dataSetFilter[$this->dataSetFilterId]['column'] = $column;
                        $this->dataSetFilter[$this->dataSetFilterId]['filter'] = 'notin';
                        $this->dataSetFilter[$this->dataSetFilterId]['filterOperator'] = 'NOT IN';
                        $this->dataSetFilter[$this->dataSetFilterId]['value'] = (string)$attr;
                        $this->dataSetFilter[$this->dataSetFilterId]['binder'] = $this->dataSetCurrentBinder;
                    break;
                    case 'null':
                        $this->dataSetFilter[$this->dataSetFilterId]['column'] = $column;
                        $this->dataSetFilter[$this->dataSetFilterId]['filter'] = 'null';
                        $this->dataSetFilter[$this->dataSetFilterId]['filterOperator'] = 'NULL';
                        $this->dataSetFilter[$this->dataSetFilterId]['value'] = (string)$attr;
                        $this->dataSetFilter[$this->dataSetFilterId]['binder'] = $this->dataSetCurrentBinder;
                    break;
                    case 'equal':
                    case 'equals':
                    case 'same':
                    case 'sameas':
                        $this->dataSetFilter[$this->dataSetFilterId]['column'] = $column;
                        $this->dataSetFilter[$this->dataSetFilterId]['filter'] = 'equal';
                        $this->dataSetFilter[$this->dataSetFilterId]['filterOperator'] = '=';
                        $this->dataSetFilter[$this->dataSetFilterId]['value'] = (string)$attr;
                        $this->dataSetFilter[$this->dataSetFilterId]['binder'] = $this->dataSetCurrentBinder;
                    break;
                    case 'like':
                        $this->dataSetFilter[$this->dataSetFilterId]['column'] = $column;
                        $this->dataSetFilter[$this->dataSetFilterId]['filter'] = 'like';
                        $this->dataSetFilter[$this->dataSetFilterId]['filterOperator'] = 'LIKE';
                        $this->dataSetFilter[$this->dataSetFilterId]['value'] = (string)$attr;
                        $this->dataSetFilter[$this->dataSetFilterId]['binder'] = $this->dataSetCurrentBinder;
                    break;
                    case 'notlike':
                        $this->dataSetFilter[$this->dataSetFilterId]['column'] = $column;
                        $this->dataSetFilter[$this->dataSetFilterId]['filter'] = 'notlike';
                        $this->dataSetFilter[$this->dataSetFilterId]['filterOperator'] = 'NOT LIKE';
                        $this->dataSetFilter[$this->dataSetFilterId]['value'] = (string)$attr;
                        $this->dataSetFilter[$this->dataSetFilterId]['binder'] = $this->dataSetCurrentBinder;
                    break;
                    case 'notequal':
                    case 'notequalto':
                        $this->dataSetFilter[$this->dataSetFilterId]['column'] = $column;
                        $this->dataSetFilter[$this->dataSetFilterId]['filter'] = 'notequal';
                        $this->dataSetFilter[$this->dataSetFilterId]['filterOperator'] = '!=';
                        $this->dataSetFilter[$this->dataSetFilterId]['value'] = (string)$attr;
                        $this->dataSetFilter[$this->dataSetFilterId]['binder'] = $this->dataSetCurrentBinder;
                    break;
                }
            }
            $this->prevDataSetFilterId = $this->dataSetFilterId;
            if ($i < count($filters) || $child->children()->count() == 0) {
                $this->dataSetFilterId++;
            }
        }
    }

    public function loadDataSet($dataSet) {
        $this->lastCommand            = 'loadDataSet';
        $this->dataSet                = $dataSet;
        $this->dataSetFilter          = [];
        $this->dataSetFilterId        = 0;
        $this->dataSetCurrentBinder   = 'AND';
        $this->dataSetBinderGroup     = 1;
        $this->dataSetOrder           = [];
        $this->dataSetGroup           = [];
        $this->dataSetLimit           = [];
        $this->dataSetClosingBrackets = 0;
        $this->dataSetOpeningBrackets = 0;
        $this->dataSetSums            = [];

        $dataSetAttributes = $this->xml->datasets->$dataSet->attributes();
        if ($dataSetAttributes->xpath('./return')) {
            $this->dataSetReturn = $this->xml->datasets->$dataSet['return'];
        }

        $this->dataSetAlias = $dataSet;
        if ($dataSetAttributes->xpath('./alias')) {
            $this->dataSetAlias = (string)$dataSetAttributes['alias'];
        }

        if ($dataSetAttributes->xpath('./description')) {
            $this->dataSetDescription = (string)$dataSetAttributes['description'];
        } else {
            $this->dataSetDescription = $this->dataSetAlias;
        }

        if (isset($this->xml->datasets->$dataSet->columns)) {
            $this->dataSetColumns = [];
            $this->dataSetColumnCount = 0;
            $this->dataSetConcat = [];
            foreach($this->xml->datasets->$dataSet->columns->children() as $child) {
                $this->dataSetColumns[$this->dataSetColumnCount] = $this->table.'.'.$child->getName();
                $this->AddObjectProperty($child->getName());
                if (isset($child['as'])) {
                    $this->dataSetConcat[$this->dataSetColumnCount]['as'] = (string)$child['as'];
                }
                if (isset($child['concat'])) {
                    $this->dataSetConcat[$this->dataSetColumnCount]['concat'] = (string)$child['concat'];
                }
                $this->dataSetColumnCount++;
            }
        }

        if (isset($this->xml->datasets->$dataSet->sum)) {
            $this->dataSetSums = [];
            foreach($this->xml->datasets->$dataSet->sum->children() as $child) {
                if (strpos($child->getName(),'.') !== false) {
                    $this->dataSetSums[] = $child->getName();
                } else {
                    $this->dataSetSums[] = $this->table.'.'.$child->getName();
                }
            }
        }

        if (isset($this->xml->datasets->$dataSet->join)) {
            foreach($this->xml->datasets->$dataSet->join as $join) {
                $this->joins[] = [
                    'table' => (string)$join['table'],
                    'type'  => (string)$join['type'],
                    'left'  => (string)$join['left'],
                    'right' => (string)$join['right']
                ];
                if (isset($join->columns)) {
                    foreach($join->columns->children() as $child) {
                        if (strpos($join['table'],' AS ') !== false) {
                            $asses = explode(' AS ',$join['table']);
                            $joinTable = $asses[1];
                        } else {
                            $joinTable = $join['table'];
                        }
                        $this->dataSetColumns[$this->dataSetColumnCount] = $joinTable.'.'.$child->getName();
                        $this->AddObjectProperty($child->getName());
                        if (isset($child['as'])) {
                            $this->dataSetConcat[$this->dataSetColumnCount]['as'] = (string)$child['as'];
                        }
                        if (isset($child['concat'])) {
                            $this->dataSetConcat[$this->dataSetColumnCount]['concat'] = (string)$child['concat'];
                        }
                        $this->dataSetColumnCount++;
                    }
                }

                if (isset($join->sum)) {
                    foreach($join->sum->children() as $child) {
                        if (strpos($join['table'],' AS ') !== false) {
                            $asses = explode(' AS ',$join['table']);
                            $joinTable = $asses[1];
                        } else {
                            $joinTable = $join['table'];
                        }
                        $this->dataSetSums[] = $joinTable.'.'.$child->getName();
                        $this->dataSetGroup[] = $joinTable.'.'.$child->getName();
                    }
                }

                if (isset($join->filter)) {
                    $this->setFilters($join->filter,$join['table'],'AND',count($this->joins)-1);
                }

                if (isset($join->order)) {
                    foreach($join->order->children() as $child) {
                        $this->dataSetOrder[$join['table'].'.'.$child->getName()] = $child['order'];
                    }
                }

                if (isset($join->group)) {
                    foreach($join->group->children() as $child) {
                        $this->dataSetGroup[] = $join['table'].'.'.$child->getName();
                    }
                }
            }
        }

        if (isset($this->xml->datasets->$dataSet->filter)) {
            $this->setFilters($this->xml->datasets->$dataSet->filter,$this->xml->database->table['name']);
            $checkLvl = [];
            foreach(array_reverse($this->dataSetFilter,true) as $key => $filter) {
                if (isset($filter['lvl']) && isset($filter['endBraces']) && !isset($checkLvl[$filter['lvl']])) {
                    $checkLvl[$filter['lvl']] = array('lvl'=>$filter['lvl'],'key'=>$key);
                }
                if (isset($filter['lvl']) && isset($checkLvl[$filter['lvl']])) {
                    if ($filter['lvl'] > $checkLvl[$filter['lvl']]['lvl']) {
                        $this->dataSetFilter[$key]['endBraces'] = $this->dataSetFilter[$checkLvl[$filter['lvl']]['key']]['endBraces'];
                        $this->dataSetFilter[$checkLvl[$filter['lvl']]['key']]['endBraces'] = $filter['endBraces'];
                    }
                }
            }
        }

        if (isset($this->xml->datasets->$dataSet->order)) {
            foreach($this->xml->datasets->$dataSet->order->children() as $child) {
                $this->dataSetOrder[$this->xml->database->table['name'].'.'.$child->getName()] = $child['order'];
            }
        }

        if (isset($this->xml->datasets->$dataSet->group)) {
            foreach($this->xml->datasets->$dataSet->group->children() as $child) {
                $this->dataSetGroup[] = $this->xml->database->table['name'].'.'.$child->getName();
            }
        }

        if (isset($this->xml->datasets->$dataSet->limit)) {
            if (isset($this->xml->datasets->$dataSet->limit['start'])) {
                $this->dataSetLimit['start'] = (string)$this->xml->datasets->$dataSet->limit['start'];
            }
            if (isset($this->xml->datasets->$dataSet->limit['rows'])) {
                $this->dataSetLimit['rows'] = (string)$this->xml->datasets->$dataSet->limit['rows'];
            }
        }
        return $this;
    }

    public function __call($name, $arguments) {
        if (isset($this->xml->datasets->{$name})) {
            $this->loadDataSet($name);
            if (isset($arguments) && is_array($arguments) && count($arguments) > 0) {
                foreach($arguments[0] as $argumentName => $argumentValue) {
                    $this->setExpressionParam($argumentName,$argumentValue);
                }
            }
            
        } elseif (isset($arguments) && count($arguments) > 0) {
            $this->setExpressionParam($name,$arguments[0]);
        }
        return $this;
    }

    public function expression($expression = null) {
        if (isset($expression)) {
            $this->expressionParams = [];
            $dataset = $this->dataSet;
            if (isset($this->xml->datasets->$dataset->expressions->$expression)) {
                if (isset($this->xml->datasets->$dataset->expressions->$expression->columns)) {
                    $this->dataSetColumns = [];
                    $this->objectProperties = [];
                    foreach($this->xml->datasets->$dataset->expressions->$expression->columns->children() as $child) {
                        $this->dataSetColumns[$this->dataSetColumnCount] = $this->table.'.'.$child->getName();
                        $this->AddObjectProperty($child->getName());
                        if (isset($child['as'])) {
                            $this->dataSetConcat[$this->dataSetColumnCount]['as'] = (string)$child['as'];
                        }
                        if (isset($child['concat'])) {
                            $this->dataSetConcat[$this->dataSetColumnCount]['concat'] = (string)$child['concat'];
                        }
                        $this->dataSetColumnCount++;
                    }
                }

                if (isset($this->xml->datasets->$dataset->expressions->$expression->filter)) {
                    $this->setFilters($this->xml->datasets->$dataset->expressions->$expression->filter,$this->table);
                }

                if (isset($this->xml->datasets->$dataset->expressions->$expression->order)) {
                    $this->dataSetOrder = [];
                }

                if (isset($this->xml->datasets->$dataset->expressions->$expression->group)) {
                    $this->dataSetGroup = [];
                }

                if (isset($this->xml->datasets->$dataset->expressions->$expression->limit)) {
                    $this->dataSetLimit = [];
                }
            } else {
                $this->setError('Expression: <strong>'.$expression.'</strong> not found in: <strong>'.$this->dataSet.'</strong>');
            }
        } else {
            $this->setError('No expression is set');
        }
        return $this;
    }

    public function first() {
        $this->dataSetLimit = 1;
        $this->select($this->dataSetReturn,$this->dataSetColumns, $this->dataSetFilter, $this->dataSetOrder, $this->dataSetLimit, $this->dataSetGroup, $this->dataSetSums);
        if ($this->gotResults) {
            return $this->getResult(0);
        }
        return false;
    }

    public function runDataSet() {
        $this->select($this->dataSetReturn,$this->dataSetColumns, $this->dataSetFilter, $this->dataSetOrder, $this->dataSetLimit, $this->dataSetGroup, $this->dataSetSums);
        return $this;
    }

    public function countDataSet() {
        $this->dataSetReturn = 'count';
        $this->select($this->dataSetReturn,$this->dataSetColumns, $this->dataSetFilter, $this->dataSetOrder, $this->dataSetLimit, $this->dataSetGroup, $this->dataSetSums);
        return $this;
    }

    public function explainDataSet($dataSet = null) {
        if (isset($dataSet)) {
            $this->dataSet = $dataSet;
        } else {
            $dataSet = $this->dataSet;
        }
        if (empty($this->dataSet)) {
            $this->setError('No Data Set Specified');
        } else {
            if (isset($this->xml->datasets->$dataSet)) {
                $explain = $this->generateDataSetExplain($dataSet);
                $html    = '<h5>Description: '.$explain['description'].'</h5>';
                $html    .= '<hr/>';

                $html .= '<h5>Returns: '.$explain['return'].'</h5>';
                $html .= '<hr/>';

                $html .= '<h5>Alias: '.$explain['alias'].'</h5>';
                $html .= '<hr/>';

                $html .= '<h5>Joins</h5>';
                if (count($explain['joins']) > 0) {
                    foreach($explain['joins'] as $join) {
                        $html .= '<strong>'.$join['table'].'</strong>';
                        $html .= '<ul>';
                        $html .= '<li>Type of join: <strong>'.strtoupper($join['type']).'</strong></li>';
                        $html .= '<li>Left part of join: <strong>'.$join['left'].'</strong></li>';
                        $html .= '<li>Right part of join: <strong>'.$join['right'].'</strong></li>';
                        $html .= '</ul>';

                        $html .= '<h6>Columns on join</h6>';
                        $html .= '<ol>';
                        foreach($join['columns'] as $column) {
                            $html .= '<li>'.$column.'</li>';
                        }
                        $html .= '</ol>';

                        $html .= '<h6>Filters on join</h6>';
                        $html .= '<ol>';
                        $i    = 0;
                        foreach($join['filters'] as $filter) {
                            $html .= '<li><strong>'.$filter['column'].'</strong> '.$filter['filterOperator'].' <strong>'.$filter['value'].'</strong>';
                            if ($i < count($join['filters']) - 1) {
                                $html .= ' '.$filter['binder'];
                            }
                            $html .= '</li>';
                            $i++;
                        }
                        $html .= '</ol>';

                        $html .= '<h6>Order on join</h6>';
                        $html .= '<ol>';
                        foreach($join['orders'] as $order) {
                            $html .= '<li><strong>'.$order['column'].'</strong>: '.$order['order'].'</li>';
                        }
                        $html .= '</ol>';

                        $html .= '<h6>Group on join</h6>';
                        $html .= '<ol>';
                        foreach($join['groups'] as $group) {
                            $html .= '<li>'.$group.'</li>';
                        }
                        $html .= '</ol>';
                    }
                    $html .= '<hr/>';
                }

                $html .= '<h5>Columns</h5>';
                $html .= '<ol>';

                foreach($explain['columns'] as $colomn) {
                    $html .= '<li>'.$colomn.'</li>';
                }
                $html .= '</ol>';

                $html .= '<hr/>';

                $html .= '<h5>Filters</h5>';
                $html .= '<ol>';

                $i = 0;
                foreach($explain['filters'] as $filter) {
                    $html .= '<li><strong>'.$filter['column'].'</strong> '.$filter['filterOperator'].' <strong>'.$filter['value'].'</strong>';
                    if ($i < count($explain['filters']) - 1) {
                        $html .= ' '.$filter['binder'];
                    }
                    $html .= '</li>';
                    $i++;
                }
                $html .= '</ol>';

                $html .= '<hr/>';

                $html .= '<h5>Order</h5>';
                $html .= '<ol>';
                foreach($explain['orders'] as $order) {
                    $html .= '<li><strong>'.$order['column'].'</strong>: '.$order['order'].'</li>';
                }
                $html .= '</ol>';

                $html .= '<hr/>';

                $html .= '<h5>Group</h5>';
                $html .= '<ol>';
                foreach($explain['groups'] as $group) {
                    $html .= '<li>'.$group.'</li>';
                }
                $html .= '</ol>';
                $this->setOffcanvas('Explain Dataset: <strong>'.$this->dataSet.'</strong> from Model: <strong>'.$this->modelAlias.'</strong>',$html,'700px');

                return false;
            }
            $this->setError('Dataset: '.$this->dataSet.' does not exist in Model: '.$this->modelAlias);
        }
    }

    private function generateDataSetExplain($dataSet) {
        $explain = [];
        $dataSetProps = $this->xml->datasets->$dataSet->attributes();
        $explain['description'] = (string)$dataSetProps['description'];
        $explain['return']      = (string)$dataSetProps['return'];
        $explain['alias']       = (string)$dataSetProps['alias'];

        foreach($this->xml->datasets->$dataSet->columns->children() as $column) {
            $explain['columns'][] = $column->getName();
        }

        $this->dataSetFilter = [];
        $this->dataSetFilterId = 0;
        $this->dataSetCurrentBinder = 'AND';
        $this->dataSetBinderGroup = 1;
        if (isset($this->xml->datasets->$dataSet->filter)) {
            $this->setFilters($this->xml->datasets->$dataSet->filter,$this->xml->database->table['name']);
        }
        $explain['filters'] = $this->filter;

        foreach($this->xml->datasets->$dataSet->order->children() as $order) {
            $explain['orders'][] = [
                'column' => $order->getName(),
                'order'  => (string)$order['order']
            ];
        }

        foreach($this->xml->datasets->$dataSet->group->children() as $group) {
            $explain['groups'][] = $group->getName();
        }

        $i = 0;
        foreach($this->xml->datasets->$dataSet->join as $join) {
            $explain['joins'][$i] = [
                'table' => (string)$join['table'],
                'type'  => (string)$join['type'],
                'left'  => (string)$join['left'],
                'right' => (string)$join['right']
            ];

            foreach($join->columns->children() as $column) {
                $explain['joins'][$i]['columns'][] = $column->getName();
            }

            if (isset($join->filter)) {
                $this->setFilters($join->filter,$join['table'],'AND',$i);
            }
            $explain['joins'][$i]['filters'] = $this->filter;

            foreach($join->order->children() as $order) {
                $explain['joins'][$i]['orders'][] = [
                    'column' => $order->getName(),
                    'order'  => (string)$order['order']
                ];
            }

            foreach($join->group->children() as $group) {
                $explain['joins'][$i]['groups'][] = $group->getName();
            }
            $i++;
        }

        foreach($this->xml->datasets->$dataSet->expressions as $expression) {
            echo '<pre>';
            pd($expression);
        }
        return $explain;
    }

    private function loadModel($xml,$ignoreAlters = false) {
        $child = $xml->database->table;
        $this->modelAlias           = (string)$xml->database['alias'];
        // Set Singular of db items
        if (!empty((string)$xml->database['single'])) {
            $this->modelSingle      = (string)$xml->database['single'];
        } else {
            $this->modelSingle      = $this->model;
        }
        
        // Set Plural of db items
        if (!empty((string)$xml->database['many'])) {
            $this->modelMany        = (string)$xml->database['many'];
        } else {
            $this->modelMany        = $this->modelSingle;
        }
        $this->table                = (string)$xml->database->table['name'];
        $this->combKey              = explode(',',(string)$xml->database->table['key']);
        $this->primaryKey           = $this->combKey[0];
        $this->dataSetColumnCount   = 0;
        $this->dataSetConcat        = [];
        $this->columns['name'][]    = $this->table.'.'.$this->primaryKey;
        $this->columns['alias'][]   = 'Primary Key';
        $this->dataSetColumns[$this->dataSetColumnCount] = $this->table.'.'.$this->primaryKey;
        $this->AddObjectProperty($this->primaryKey);
        $this->dataSetColumnCount++;

        foreach($xml->database->table->columns->children() as $child) {
            $this->dataSetColumns[$this->dataSetColumnCount] = $this->table.'.'.$child->getName();
            $this->AddObjectProperty($child->getName());
            $this->columns['name'][$this->dataSetColumnCount] = $this->table.'.'.$child->getName();
            if (isset($child['as'])) {
                $this->dataSetConcat[$this->dataSetColumnCount]['as'] = (string)$child['as'];
            }
            if (isset($child['concat'])) {
                $this->dataSetConcat[$this->dataSetColumnCount]['concat'] = (string)$child['concat'];
            }

            foreach($child->attributes() as $attr) {
                switch(strtolower($attr->getName())) {
                    case 'required':
                        if ((string)$attr == 'true') {
                            $this->requirements[$child->getName()] = (string)$attr;
                        }
                    break;
                    case 'validate':
                        $this->validations[$child->getName()] = (string)$attr;
                    break;
                    case 'default':
                        $this->defaults[$child->getName()] = (string)$attr;
                    break;
                    case 'lookup':
                        $this->lookups[$child->getName()] = (string)$attr;
                    break;
                    default:
                        $this->columns[$attr->getName()][$this->dataSetColumnCount] = (string)$attr;
                        if ($this->useDefaults) {
                            $this->defaults[$child->getName()] = '';
                        }
                    break;
                }
            }
            if ($ignoreAlters == false) {
                if (!empty($child['add'])) {
                    $this->addColumn($child->getName(),$prev = null);
                }
            }

            $prev = $child->getName();
            $this->dataSetColumnCount++;
        }

        if (isset($xml->database->join)) {
            foreach($xml->database->join as $join) {
                $this->joins[] = [
                    'table' => (string)$join['table'],
                    'type'  => (string)$join['type'],
                    'left'  => (string)$join['left'],
                    'right' => (string)$join['right']
                ];
                $table = (string)$join['table'];
                if (strpos($table,' AS ') !== false) {
                    $asses = explode(' AS ',(string)$join['table']);
                    $table = $asses[1];
                } else {
                    $table = $join['table'];
                }
                foreach($join->columns->children() as $child) {
                    $this->dataSetColumns[$this->dataSetColumnCount] = $table.'.'.$child->getName();
                    $this->AddObjectProperty($child->getName());
                    $this->columns['name'][$this->dataSetColumnCount] = $table.'.'.$child->getName();
                    if (isset($child['as'])) {
                        $this->dataSetConcat[$this->dataSetColumnCount]['as'] = (string)$child['as'];
                    }
                    if (isset($child['concat'])) {
                        $this->dataSetConcat[$this->dataSetColumnCount]['concat'] = (string)$child['concat'];
                    }
                    foreach($child->attributes() as $attr) {
                        $this->columns[$attr->getName()][$this->dataSetColumnCount] = (string)$attr;
                        switch(strtolower($attr->getName())) {
                            case 'default':
                                if ($this->useDefaults) {
                                    $this->defaults[$child->getName()] = (string)$attr;
                                }
                            break;
                            default:
                                $this->columns[$attr->getName()][$this->dataSetColumnCount] = (string)$attr;
                                $this->defaults[$child->getName()] = '';
                            break;
                        }
                    }
                    $this->dataSetColumnCount++;
                }
                if (isset($join->filter)) {
                    $this->setFilters($join->filter,$table,'AND',count($this->joins)-1);
                }
            }
        }
        if (isset($xml->database->table->order)) {
            foreach($xml->database->table->order->children() as $order) {
                $this->setDataSetOrder([
                    $this->xml->database->table['name'].'.'.$order->getName() => $child['order']
                ]);
            }
        }


        // Check if there are any INSERT triggers specified
        if (isset($xml->triggers)) {
            foreach($xml->triggers->children() as $trigger) {
                // Insert, Update, Delete, Select
                $type = $trigger->getName();
                foreach($xml->triggers->$type->children() as $class) {
                    $this->triggers[$type][$class->getName()] = [];
                    foreach($class->children() as $method) {
                        $this->triggers[$type][$class->getName()][$method->getName()] = [];
                        if ($method->children()) {
                            foreach($method->children() as $param) {
                                $this->triggers[$type][$class->getName()][$method->getName()][$param->getName()] = trim((string)$param[0]);
                            }
                        }
                    }
                }
            }
        }
    }

    public function getModels() {
        $files = scandir(env('project.models.path'),SCANDIR_SORT_NONE);
        $path = env('project.models.path');
        $model = [];
        foreach($files as $file) {
            if ($file != '..' && $file != '.') {
                $xml = simplexml_load_string('<model>'.file_get_contents($path.$file).'</model>','SimpleXMLElement',LIBXML_NOCDATA);
                if ((string)$xml->database['public'] == 'true') {
                    $model[] = array('name'=>(string)$xml->database['name'],'alias'=>(string)$xml->database['alias']);
                }
            }
        }
        return $model;
    }

    public function newModel() {
        $modal = new modal();
        $fo = new XMLLayout();
        $fo->loadCoreXML('models/newDataSource.xml',$this);
        $modal->setHeading('New Data Source');
        $modal->setBody($fo->html);
        $modal->addButton('Add Data Source','',"submitForm('addDataSourceForm','Model/addModel')");
        $this->setModal($modal->render());
    }

    public function addModel() {
        $alias = $_POST['model_name'];
        $modelName = ucfirst($alias);;
        $fileName = str_replace(' ','',$modelName);
        $tableName = strtolower(str_replace(' ','_',$modelName));
        $xml =
"<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<root>
    <database connection=\"".$_POST['connection']."\" name=\"".$fileName."\" alias=\"".$modelName."\" public=\"".$_POST['public']."\">
        <table name=\"".$tableName."\" key=\"id\">
            <columns>

            </columns>
        </table>
    </database>
</root>";
        $path = env('project.models.path').$fileName.'.xml';
        if (!file_exists($path)) {
            file_put_contents($path,$xml);
            $db = new Database($_POST['connection']);
            $this->_connection = $db->getConnection();

            $sql = "SHOW TABLES LIKE '".$tableName."'";
            $db->query($sql);
            if ($db->num_rows($db->result_set) == 0) {
                $sql = 'CREATE TABLE `'.$tableName.'` (
                            `id` INT(11) AUTO_INCREMENT NOT NULL,
                            PRIMARY KEY(`id`)
                        )
                        ENGINE = InnoDB
                        CHARACTER SET = utf8';
                $db->query($sql);
                $this->setGritter('New data-set model added');
            } else {
                $this->setError('Table: '.$tableName.' already exists');
            }
        } else {
            $this->setError('This data-set model already exists');
            $this->setScript("$('.breadcrumbLink').last().click()");
        }
    }

    public function newColumn($model) {
        $this->__construct($model);
        $modal = new modal();
        $layout = new XMLLayout();
        $layout->loadCoreXML('models/newColumn.xml/'.$this->model.'/'.$this->connection,$this);
        $modal->setBody($layout->html);
        $modal->setHeading('Add Column to '.$this->modelAlias);
        $modal->addButton('Add Column','',"submitForm('addXMLColumnForm','Model/addColumnToXML')");
        $this->setModal($modal->render());
    }

    public function getModelColumns($model = null) {
        if (!empty($model)) {
            $this->__construct($model);
        }
        $columns = [];
        $keys = array_keys($this->columns);
        foreach($keys as $key) {
            foreach($this->columns[$key] as $nr=>$value) {
                if ($key == 'name') {
                    $columns[$nr]['name'] = str_replace($this->table.'.','',$value);
                    foreach($this->joins as $join) {
                        $columns[$nr]['name'] = str_replace($join['table'].'.','',$columns[$nr]['name']);
                    }
                } else {
                    $columns[$nr][$key] = $value;
                }
            }
        }
        return $columns;
    }

    public function addColumnToXML() {
        $this->__construct($_POST['model_name']);
        if (!empty($_POST['column_name'])) {
            $alias = ucfirst($_POST['column_name']);
            $columnName = str_replace(' ','_',strtolower($_POST['column_name']));
            $afterColumn = str_replace($this->table.'.','',$_POST['after_column']);
            if ($_POST['after_column'] != $this->table.'.'.$this->primaryKey) {
                $exists = current($this->xml->xpath('/root/database/table/columns/'.$columnName));
                if (!$exists) {
                    $insert = new \SimpleXMLElement('<'.$columnName.' alias="'.$alias.'" add="true"/>');
                    $target = current($this->xml->xpath('/root/database/table/columns/'.$afterColumn));
                    $this->simplexml_insert_after($insert, $target);
                } else {
                    $this->setError('Column already exists');
                }
            } else {
                $insert = new \SimpleXMLElement('<'.$columnName.' alias="'.$alias.'" add="true"/>');
                $target = current($this->xml->database->table->columns);
                $target->addChild($columnName.' alias="'.$alias.'" add="true"');
            }
            $dom = new \DOMDocument("1.0");
            $dom->preserveWhiteSpace = false;
            $dom->formatOutput = true;
            $dom->loadXML($this->xml->asXML());
            $newXML = $dom->saveXML();
            file_put_contents($this->path, $newXML);
            $this->setGritter('Column Added');
        } else {
            $this->setError('No column name specified');
        }
    }

    public function simplexml_insert_after($insert,$target) {
        $target_dom = dom_import_simplexml($target);
        $insert_dom = $target_dom->ownerDocument->importNode(dom_import_simplexml($insert), true);
        if ($target_dom->nextSibling) {
            return $target_dom->parentNode->insertBefore($insert_dom, $target_dom->nextSibling);
        }
        return $target_dom->parentNode->appendChild($insert_dom);
    }

    private function addColumn($column,$after) {
        $modal = new modal();
        $layout = new XMLLayout();
        $layout->loadCoreXML('models/addColumn.xml/'.$this->model.'/'.$this->modelAlias.'/'.$column.'/'.$after.'/'.$this->table,$this);
        $modal->setBody($layout->html);
        $modal->setHeading('Add Column');
        $modal->addButton('Preview  SQL','','submitForm(\'addColumnForm\',\'Model/generateAddColumn\')');
        $this->setModal($modal->render());
        new \Exception('Denied');
    }

    /**
     * @throws Exception
     */
    public function generateAddColumn() {
        if (empty($_POST['sql'])) {
            $sql = 'ALTER TABLE `'.$_POST['table'].'` ADD COLUMN`'.$_POST['column_name'].'`';
            switch($_POST['data_type']) {
                case 'Int':
                    if (!empty($_POST['length'])) {
                        if (strlen($_POST['length']) < 12) {
                            $sql .= ' INT('.$_POST['length'].") NULL";
                            if (!empty($_POST['default'])) {
                                $sql .= " DEFAULT '".$_POST['default']."'";
                            }
                        } else {
                            $this->setError('The length of this data tsype must be equal or smaller than 11');
                            new \Exception("Denied");
                        }
                    } else {
                        $this->setError('Field length not specified');
                        new \Exception("Denied");
                    }
                break;
                case 'BigInt':
                    if (!empty($_POST['length'])) {
                        if (strlen($_POST['length']) < 22) {
                            $sql .= ' BIGINT('.$_POST['length'].") NULL";
                            if (!empty($_POST['default'])) {
                                $sql .= " DEFAULT '".$_POST['default']."'";
                            }
                        } else {
                            $this->setError('The length of this data type must be equal or smaller than 21');
                            new \Exception("Denied");
                        }
                    } else {
                        $this->setError('Field length not specified');
                        new \Exception("Denied");
                    }
                break;
                case 'Decimal':
                    if (!empty($_POST['length']) && !empty($_POST['decimals'])) {
                        if (strlen($_POST['length']) < 12) {
                            $sql .= ' DECIMAL('.$_POST['length'].",".$_POST['decimals'].") NULL";
                            if (!empty($_POST['default'])) {
                                $sql .= " DEFAULT '".$_POST['default']."'";
                            }
                        } else {
                            $this->setError('The length of this data type must be equal or smaller than 11');
                            new \Exception("Denied");
                        }
                    } else {
                        $this->setError('Field length and the amount decimals must be specified');
                        new \Exception("Denied");
                    }
                break;
                case 'VarChar':
                    if (!empty($_POST['length'])) {
                        if (strlen($_POST['length']) < 1001) {
                            $sql .= ' VARCHAR('.$_POST['length'].") NULL";
                            if (!empty($_POST['default'])) {
                                $sql .= " DEFAULT '".$_POST['default']."'";
                            }
                        } else {
                            $this->setError('The length of this data type must be equal or smaller than 1000');
                            new \Exception("Denied");
                        }
                    } else {
                        $this->setError('Field length must be specified');
                        new \Exception("Denied");
                    }
                break;
                case 'Date':
                    $sql .= ' DATE NULL';
                    if (!empty($_POST['default'])) {
                        $sql .= " DEFAULT '".$_POST['default']."'";
                    }
                break;
                case 'DateTime':
                    $sql .= ' DATETIME NULL';
                    if (!empty($_POST['default'])) {
                        $sql .= " DEFAULT '".$_POST['default']."'";
                    }
                break;
                case 'Enum':
                    if (!empty($_POST['enum']) && !empty($_POST['default'])) {
                        $options = explode(',',$_POST['enum']);
                        $newOptions = [];
                        foreach($options as $option) {
                            $newOptions[] = "'".$option."'";
                        }
                        if (in_array("'".$_POST['default']."'",$newOptions)) {
                            $sql .= " ENUM(".implode(',',$newOptions).") NULL DEFAULT '".$_POST['default']."'";
                        } else {
                            $this->setError('The default value must be one of the options');
                            new \Exception("Denied");
                        }
                    } else {
                        if (empty($_POST['enum'])) {
                            $this->setError('No Enum options specified');
                        }
                        if (empty($_POST['default'])) {
                            $this->setError('No default value specified');
                        }
                        new \Exception("Denied");
                    }
                break;
                case 'Text':
                    $sql .= ' TEXT NULL';
                    if (!empty($_POST['default'])) {
                        $sql .= " DEFAULT '".$_POST['default']."'";
                    }
                break;
                case 'Timestamp':
                    $sql .= ' TIMESTAMP NULL';
                    if (!empty($_POST['currentTimeStamp'])) {
                        $sql .= ' DEFAULT CURRENT_TIMESTAMP';
                    }
                break;
                case 'Year':
                    $sql .= ' YEAR NULL';
                    if (!empty($_POST['default'])) {
                        $sql .= " DEFAULT '".$_POST['default']."'";
                    }
                break;
                case 'Time':
                    $sql .= ' TIME NULL';
                    if (!empty($_POST['default'])) {
                        $sql .= " DEFAULT '".$_POST['default']."'";
                    }
                break;
            }
            if (!empty($_POST['after'])) {
                $sql .= ' AFTER `'.$_POST['after']."`";
            }
            $this->setValue('#DFSQL',$sql);
            $this->setShow('#addColumnFormSQL');
            $this->setHide('#addColumnFormFields');
            $this->setHtml('#modalButton0','Run SQL');
        } else {
            $db = new Database();
            $this->_connection = $db->getConnection();

            $db->query($_POST['sql']);
            $path = env('project.models.path').$_POST['model'].'.xml';
            $xml = file_get_contents($path);
            $xml = preg_replace('/ add="true"/','',$xml,1);
            file_put_contents($path,$xml);
            $this->setMessage('New Column Added');
            $this->setScript("$('#modalClose').click()");
        }
    }

    public function getColumns() {
        return $this->columns['name'];
    }

    public function getAliases() {
        return $this->columns['alias'];
    }

    private function getMatchingPosts() {
        $matches = [];
        if (is_array($this->primaryKey)) {
            foreach($_POST as $key => $value) {
                if (in_array($key, str_replace($this->table.'.','',$this->columns['name'])) && !in_array($key,$this->primaryKey)) {
                    $matches['posts'][] = $key;
                    $matches['columns'][] = $key;
                }
            }
        } else {
            foreach($_POST as $key => $value) {
                if (in_array($key, str_replace($this->table.'.','',$this->columns['name'])) && $key != $this->primaryKey) {
                    $matches['posts'][] = $key;
                    $matches['columns'][] = $key;
                }
            }
        }
        return $matches;
    }

    private function getMatchingKeys($insertArray) {
        $matches = [];
        if (is_array($this->primaryKey)) {
            foreach($insertArray as $key => $value) {
                if (in_array($key, str_replace($this->table.'.','',$this->columns['name'])) && !in_array($key,$this->primaryKey)) {
                    $matches['keys'][] = $key;
                    $matches['columns'][] = $key;
                }
            }
        } else {
            foreach($insertArray as $key => $value) {
                if (in_array($key, str_replace($this->table.'.','',$this->columns['name'])) && $key != $this->primaryKey) {
                    $matches['keys'][] = $key;
                    $matches['columns'][] = $key;
                }
            }
        }
        return $matches;
    }

    public function validateValue($type,$value,$field) {
        $validate = true;
        switch($type) {
            case 'url':
                $validate = filter_var($value,FILTER_VALIDATE_URL);
            break;
            case 'tel':
            case 'telephone':
            case 'phone':
            case 'cell':
            case 'cellphone':
            case 'fax':
                if (filter_var($value,FILTER_VALIDATE_FLOAT) && strlen($value) == 10 && strpos($value,'.') === false) {
                    $validate = true;
                } else {
                    $validate = false;
                }
            break;
            case 'email':
                $validate = filter_var($value,FILTER_VALIDATE_EMAIL);
            break;
            case 'integer':
            case 'int':
            case 'decimal':
            case 'number':
                $validate = filter_var($value,FILTER_VALIDATE_FLOAT);
            break;
        }
        if (!$validate) {
            throw new Exception('Model Validation Error - Field: "'.$field.'" Failed Validation');
        }
    }

    /**
     * Description of Select
     * @fieldsExist = array of fields to check if they already exist
     *
     * @param null $insertArray
     * @param null $fieldsExist
     *
     * @return
     * @throws Exception
     */
    public function insert($insertArray = null,$fieldsExist = null) {
        $this->gotResults = false;
        $this->foundDouble = false;
        if (is_array($insertArray)) {
            $matches = $this->getMatchingKeys($insertArray);
        } else {
            $matches = $this->getMatchingPosts();
        }
        $db = new Database($this->connection);
        $this->_connection = $db->getConnection();

        // Check if doubles exist
        if (is_array($fieldsExist)) {
            $sql = "SELECT
                            *
                        FROM
                            $this->table
                        WHERE ";
            $i = 0;
            foreach($fieldsExist as $field) {
                if (is_array($insertArray)) {
                    $sql .= '`'.$field.'` = \''.$insertArray[$field].'\'';
                } else {
                    $sql .= '`'.$field."` = '".$_POST[$field]."'";
                }
                $i++;
                if ($i < count($fieldsExist)) {
                    $sql .= ' AND ';
                }
            }
            $db->query($sql);
            $this->setResults($db->toArray());
            if (count($this->results) > 0) {
                $this->gotResults = false;
                $this->foundDouble = true;
                return $field;
            }
        }

        $sql = 'INSERT INTO'.' ';
        $sql .= '`'.$this->table.'` (';
        $i = 0;
        // Add Columns with default values to $matches if they do not exist
        if ($this->useDefaults) {
            foreach($this->defaults as $defKey=>$default) {
                if (!in_array($defKey,$matches['columns'])) {
                    $matches['columns'][] = $defKey;
                    $matches['keys'][] = $defKey;
                }
            }
        }

        foreach($matches['columns'] as $column) {
            $i++;
            if ((isset($insertArray[$column]) && is_string((string)$insertArray[$column]) && strlen((string)$insertArray[$column]) > 0) || (isset($_POST[$column]) && is_string($_POST[$column]) && strlen($_POST[$column]) > 0) || ($this->useDefaults && array_key_exists($column,$this->defaults))) {
                $sql.= '`'.$column.'`';
                if ($i < count($matches['columns'])) {
                    $sql .= ', ';
                }
            }
        }

        /* 
         * Check if key was part of array or posted
         * Second OR statement Commented Out
         * (isset($_POST[$this->primaryKey]) && strlen($_POST[$this->primaryKey]) > 0) ||
         */
        if ((isset($insertArray[$this->primaryKey]) && strlen($insertArray[$this->primaryKey]) > 0) || ($this->useDefaults && array_key_exists($this->primaryKey,$this->defaults))) {
            if (is_array($insertArray)) {
                $sql.= ' ,`'.$this->primaryKey.'`';
            }
        }

        $sql .= ') VALUES (';

        $values = [];
        if (is_array($insertArray)) {
            $i = 0;
            foreach($matches['keys'] as $match) {
                $i++;
                $alias = $this->columns['alias'][$i];

                if (isset($insertArray[$match]) && strlen($insertArray[$match]) > 0) {
                    // Validate value
                    if ($this->validateValues && array_key_exists($match,$this->validations)) {
                        $this->validateValue($this->validations[$match],$insertArray[$match],$alias);
                    }
                    $values[] = "'".$db->escape_value($insertArray[$match])."'";
                } else {
                    // Get default value
                    if ($this->useDefaults && array_key_exists($match,$this->defaults)) {
                        $values[] = "'".$db->escape_value($this->defaults[$match])."'";
                    }

                    // Check if required and does not have a default value
                    if ($this->checkRequired && array_key_exists($match,$this->requirements) && !(array_key_exists($match,$this->defaults) && $this->useDefaults)) {
                        new \Exception('Data Requirement Error - Field: "'.$alias.'" Is a Required Field But No Value Was Given');
                    }

                }
            }

            // Check if key was part of array
            if (isset($insertArray[$this->primaryKey])) {
                // Validate value
                if ($this->validateValues && array_key_exists($this->primaryKey,$this->validations)) {
                    $this->validateValue($this->validations[$this->primaryKey],$insertArray[$this->primaryKey],$alias);
                }
                $values[] = "'".$db->escape_value($insertArray[$this->primaryKey])."'";
            } else {
                // Get default value
                if ($this->useDefaults && array_key_exists($this->primaryKey,$this->defaults)) {
                    $values[] = "'".$db->escape_value($this->defaults[$this->primaryKey])."'";
                }

                // Check if required and does not have a default value
                if ($this->checkRequired && array_key_exists($this->primaryKey,$this->requirements) && !(array_key_exists($this->primaryKey,$this->defaults) && $this->useDefaults)) {
                    new \Exception('Data Requirement Error - Field: "'.$alias.'" Is a Required Field But No Value Was Given');
                }
            }
        } else {
            foreach($matches['posts'] as $match) {
                if (strlen($_POST[$match]) > 0) {
                    // Validate value
                    if ($this->validateValues && array_key_exists($match,$this->validations)) {
                        $this->validateValue($this->validations[$match],$_POST[$match],$alias = null);
                    }
                    $values[] = "'".$db->escape_value($_POST[$match])."'";
                } else {
                    // Get default value
                    if ($this->useDefaults && array_key_exists($match,$this->defaults)) {
                        $values[] = "'".$db->escape_value($this->defaults[$match])."'";
                    }

                    // Check if required and does not have a default value
                    if ($this->checkRequired && array_key_exists($match,$this->requirements) && !(array_key_exists($match,$this->defaults) && $this->useDefaults)) {
                        new \Exception('Data Requirement Error - Field: "'.$alias.'" Is a Required Field But No Value Was Given');
                    }
                }
            }

            //Check if key was part of post
            if (strlen($_POST[$this->primaryKey])) {
                // Validate value
                if ($this->validateValues && array_key_exists($this->primaryKey,$this->validations)) {
                    $this->validateValue($this->validations[$this->primaryKey],$_POST[$match],$alias);
                }
                $values[] = "'".$db->escape_value($_POST[$this->primaryKey])."'";
            } else {
                // Get default value
                if ($this->useDefaults && array_key_exists($this->primaryKey,$this->defaults)) {
                    $values[] = "'".$db->escape_value($this->defaults[$this->primaryKey])."'";
                }

                // Check if required and does not have a default value
                if ($this->checkRequired && array_key_exists($this->primaryKey,$this->requirements) && !(array_key_exists($this->primaryKey,$this->defaults) && $this->useDefaults)) {
                    new \Exception('Data Requirement Error - Field: "'.$alias.'" Is a Required Field But No Value Was Given');
                }
            }
        }
        $sql .= implode(',',$values).')';

        // Get the previous last inserted row
        if ($this->saveLog) {
            $checkTableSQL = 'SELECT `'.$this->primaryKey.'` FROM `'.$this->table.'` ORDER BY `'.$this->primaryKey.'` DESC LIMIT 1';
            $db->query($checkTableSQL);
            $row = $db->nextItem();
            $lastId = $row[$this->primaryKey];
        }
        $sql = $this->replaceFunctionParams($sql);
        $this->sqlQuery = $sql;

        $db->query($sql);
        $this->setLastInsertId($db->insert_id());
        

        if ($this->saveLog) {
            $checkNewRowsSQL = 'SELECT * FROM `'.$this->table.'` WHERE `'.$this->primaryKey."` > '".$lastId."'";
            $db->query($checkNewRowsSQL);
            $this->logQuery = $checkTableSQL.'; '.$checkNewRowsSQL;
            $this->saveModelHistory('Insert',$db->toArray());
        }

        // Check if there are any triggers setup for INSERT
        if (isset($this->triggers['insert']) && $this->ignoreTriggers == false) {

            $this->executeTriggers(array(0=>$this->lastInsertId),'insert',$insertArray);
        }
        return $this->lastInsertId;
    }
    
    protected function lastInsertId() {
        if (isset($this->lastInsertId)) {
            return $this->lastInsertId;
        }
        return 0;
    }
    
    private function setLastInsertId($lastInsertId) {
        $this->lastInsertId = $lastInsertId;
    }

    private function executeTriggers($ids,$type,$dataArray) {
        if ($type == 'update') {
            // TO-DO: Add update method to triggers
        }
        $extraParams = array(
            'model_name'=>$this->model,
            'model_alias'=>(string)$this->modelAlias,
            'model_connection'=>$this->connection,
            'model_table'=>$this->table,
            'model_columns'=>$this->columns,
            'model_trigger_type'=>$type,
            'model_ids'=>$ids,
            'model_data_array'=>$dataArray
        );
        // Execute trigger on every record (When insert: lastInsertId, When Update: all ids updated, When Delete: all deleted ids, when select: all selected ids
        foreach($ids as $id) {
            foreach($this->triggers[$type] as $className => $methods) {
                $object = new $className();
                if (count($methods) > 0) {
                    foreach($methods as $method => $params) {
                        $extraParams['trigger_params'] = $params;
                        $extraParams['model_id'] = $id;
                        $object->$method($extraParams);
                    }
                } else {
                    $extraParams['model_id'] = $id;
                    $object->$method($extraParams);
                }
            }
        }
    }

    public function update($updateArray = null,$key = null) {
        // Check if custom key is set
        if (!empty($key)) {
            $oldKey = $this->primaryKey;
            if (is_array($key)) {
                $this->primaryKey = [];
            }
            $this->primaryKey = $key;
        }

        if (is_array($updateArray)) {
            // Check if an Array of keys has been sent for the update
            if (is_array($this->primaryKey) || (isset($oldKey) && is_array($oldKey))) {
                foreach($this->primaryKey as $keyColumn => $key) {
                    //Check if $this->primaryKey is multidimensional array
                    if (!is_int($keyColumn)) {
                        $key = $keyColumn;
                    }
                    if (!isset($updateArray[$key])) {
                        new Exception('`'.$key.'` not set for table: `'.$this->table.'`');
                        return false;
                    }
                }
            } else {
                if (!isset($updateArray[$this->primaryKey])) {
                    new \Exception('`'.$this->primaryKey.'` not set for table: `'.$this->table.'`');
                    return false;
                }
            }
            $matches = $this->getMatchingKeys($updateArray);
            
        } else {
            if (is_array($this->combKey)) {
                $oldKey = $this->primaryKey;
                $this->primaryKey = [];
                $this->primaryKey = $this->combKey;
            }
            if (is_array($this->primaryKey)) {
                foreach($this->primaryKey as $key) {
                    if (!isset($_POST[$key])) {
                        $this->setError('`'.$key.'` not set for table: `'.$this->table.'`');
                        return false;
                    }
                }
            } else {
                if (!isset($_POST[$this->primaryKey])) {
                    $this->setError('`'.$this->primaryKey.'` not set for table: `'.$this->table.'`');
                    return false;
                }
            }
            $matches = $this->getMatchingPosts();
        }
        $db = new Database($this->connection);
        $this->_connection = $db->getConnection();

        $log = 'SELECT * FROM `'.$this->table.'` ';
        $sql = 'UPDATE `'.$this->table.'` SET ';
        $i = 0;

        if (isset($matches['posts'])) {
            foreach($matches['posts'] as $match) {
                $i++;
                $sql .= '`'.$match."` = '".$db->escape_value($_POST[$match])."'";
                if ($i < count($matches['posts'])) {
                    $sql .= ', ';
                }
            }
            // Check if an Array of keys has been sent for the update
            if (is_array($this->primaryKey)) {
                $key = [];
                foreach($this->primaryKey as $curKey=>$curValue) {
                    //Check if $this->primaryKey is multidimensional array
                    if (is_int($curKey)) {
                        $key[] = $_POST[$curValue];
                    } else {
                        $key[] = $curKey;
                    }
                }
            } else {
                if (is_array($this->combKey)) {
                    $key = [];
                    foreach($this->combKey as $curKey) {
                        $key[] = $_POST[$curKey];
                    }
                } else {
                    $key = $_POST[$this->primaryKey];
                }
            }
        }
        
        if (isset($matches['keys'])) {
            foreach($matches['keys'] as $match) {
                $i++;
                if ($updateArray[$match] === null) {
                    $sql .= '`'.$match.'` = null';
                } else {
                    $sql .= '`'.$match."` = '".$db->escape_value($updateArray[$match])."'";
                }
                if ($i < count($matches['keys'])) {
                    $sql .= ', ';
                }
            }
            // Check if an Array of keys has been sent for the update
            if (is_array($this->combKey) && count($this->combKey) > 1) {
                $oldKey = $this->primaryKey;
                $this->primaryKey = [];
                $this->primaryKey = $this->combKey;
            }
            if (is_array($this->primaryKey)) {
                $key = [];
                foreach($this->primaryKey as $curKey) {
                    $key[] = $updateArray[$curKey];
                }
            } else {
                $key = $updateArray[$this->primaryKey];
            }
        }
        
        // Check if an Array of keys has been sent for the update
        if (is_array($key)) {
            $log .= ' WHERE ';
            $sql .= ' WHERE ';
            $i = 0;
            foreach($this->primaryKey as $curKey=>$curValue) {
                // Check if $this->primaryKey is multidimensional array
                if (is_int($curKey)) {
                    $curKey = $curValue;
                    $value = $key[$i];
                } else {
                    $value = $curValue;
                }
                $log .= '`'.$curKey.'` = ';
                $sql .= '`'.$curKey.'` = ';
                if (is_numeric($value)) {
                    $log.= $value;
                    $sql.= $value;
                } else {
                    $log.= "'".$db->escape_value($value)."'";
                    $sql.= "'".$db->escape_value($value)."'";
                }
                if ($i < count($this->primaryKey)-1) {
                    $log .= ' AND ';
                    $sql .= ' AND ';
                }
                $i++;
            }
        } else {
            $log .= ' WHERE `'.$this->primaryKey.'` = ';
            $sql .= ' WHERE `'.$this->primaryKey.'` = ';
            if (is_numeric($key)) {
                $log.= $key;
                $sql.= $key;
            } else {
                $log.= "'".$db->escape_value($key)."'";
                $sql.= "'".$db->escape_value($key)."'";
            }
        }
        
        $this->sqlQuery = $sql;
        $this->logQuery = $log;
        if ($this->saveLog) {
            $db->query($log);
            if ($db->gotResults()) {
                $updateHistory = [];
                $i = 0;
                foreach($db->toArray() as $historyValues) {
                    $updateHistory[$i] = $historyValues;
                    $i++;
                }
                $this->saveModelHistory('Update',$updateHistory);
            }
        }
        $db->query($sql);
        $this->setResults($db->affected_rows(),'string');

        // Check if there are any triggers setup for UPDATE
        if (isset($this->triggers['update']) && $this->results > 0 && $this->ignoreTriggers == false) {
            if (is_array($key)) {
                $this->executeTriggers($key,'update',$updateArray);
            } else {
                $this->executeTriggers(array($key),'update',$updateArray);
            }
        }

        // Check if custom key is set
        if (!empty($key)) {
            if (is_array($this->primaryKey)) {
                $this->primaryKey = null;
            }
            if (isset($oldKey)) {
                $this->primaryKey = $oldKey;
            }
        }

        return true;
    }

    public function delete($keyOrArrayOfKeys = null) {
        $db = new Database($this->connection);
        $this->_connection = $db->getConnection();

        if (is_array($keyOrArrayOfKeys)) {
            $log = 'SELECT * FROM'.' ';
            $log .= '`'.$this->table.'`';
            $log .= ' WHERE ';
            $sql = 'DELETE FROM'.' ';
            $sql .= '`'.$this->table.'`';
            $sql .= ' WHERE ';
            $i = 0;
            foreach($keyOrArrayOfKeys as $deleteKey => $deleteValue) {
                $log .= '`'.$deleteKey.'` = ';
                $sql .= '`'.$deleteKey.'` = ';
                if (is_numeric($deleteValue)) {
                    $log.= $deleteValue;
                    $sql.= $deleteValue;
                } else {
                    $log.= "'".$db->escape_value($deleteValue)."'";
                    $sql.= "'".$db->escape_value($deleteValue)."'";
                }
                if ($i < count($keyOrArrayOfKeys)-1) {
                    $log .= ' AND ';
                    $sql .= ' AND ';
                }
                $i++;
            }
        } else {
            if (!isset($_POST[$this->primaryKey])  && !isset($keyOrArrayOfKeys)) {
                $this->setError('`'.$this->primaryKey.'` not set for table: `'.$this->table.'`');
                return false;
            }

            if (isset($_POST[$this->primaryKey])) {
                $key = $_POST[$this->primaryKey];
            }

            $log = 'SELECT'.' FROM `'.$this->table.'` WHERE `'.$this->primaryKey.'` = ';
            $sql = 'DELETE FROM `'.$this->table.'` WHERE `'.$this->primaryKey.'` = ';
            if (is_numeric($key)) {
                $log.= $key;
                $sql.= $key;
            } else {
                $log.= "'".$db->escape_value($key)."'";
                $sql.= "'".$db->escape_value($key)."'";
            }
        }

        $db->query($sql);
        $this->setResults($db->affected_rows(),'string');
        $deletedRows = $db->affected_rows();
        $this->sqlQuery = $sql;
        $this->logQuery = $log;

        if ($this->saveLog) {
            $db->query($log);
            if ($db->gotResults()) {
                $deleteHistory = [];
                $i = 0;
                foreach($db->toArray() as $historyValues) {
                    $deleteHistory[$i] = $historyValues;
                    $i++;
                }
                $this->saveModelHistory('Delete',$deleteHistory);
            }
        }
        return $deletedRows;
    }

    /*
     * @expression = array(
     *  @columns = array of fields to select, if array is empty it will select *
     *  @filter = array['field'] = 'filter'
     *  @return = string. array | json
     *  @order = array['field'] = 'ASC'
     *  @limit = integer or array('start'=>5,'rows'=>10)
     *  @group = array of fields to group by
     * )
     */
    public function findOld($expression) {
        $columns = '*';
        if (isset($expression['columns'])) {
            $columns = $expression['columns'];
        }

        $filter = false;
        if (isset($expression['filter']) && $expression['filter'] != false && is_array($expression['filter'])) {
            $filter = $expression['filter'];
        }

        $return = 'array';
        if (isset($expression['return'])) {
            $return = strtolower($expression['return']);
        }

        $order = false;
        if (isset($expression['order']) && $expression['order'] != false && is_array($expression['order'])) {
            $order = $expression['order'];
        }

        if (isset($expression['limit'])) {
            if (isset($expression['limit']) && is_array($expression['limit'])) {
                if (isset($expression['start'])) {
                    $limit['start'] = $expression['start'];
                } else {
                    $limit['start'] = 1;
                }
                if (isset($expression['rows'])) {
                    $limit['rows'] = $expression['rows'];
                } else {
                    $limit['rows'] = 0;
                }
            } elseif (isset($expression['limit']) && is_int($expression['limit'])) {
                $limit['start'] = 1;
                $limit['rows'] = $expression['limit'];
            } else {
                $limit = false;
            }
        }

        $group = false;
        if (isset($expression['group']) && is_array($expression['group']) && $expression['group'] != false) {
            $group = $expression['group'];
        }
        $this->isLoaded(true);
        return $this->select($return, $columns, $filter, $order, $limit, $group);
    }

    public function gotResults() {
        return $this->gotResults;
    }

    public function getFirstRecord() {
        $this->gotResults = false;
        $db = new Database($this->connection);
        $this->_connection = $db->getConnection();
        $sql = 'SELECT
                    *
                FROM
                    '.$this->table.'
                ORDER BY
                    '.$this->primaryKey.' ASC
                LIMIT 1';
        $db->query($sql);
        $this->sqlQuery = $sql;
        $this->setResults($db->nextItem(),'string');
        return $this;
    }

    public function replaceFunctionParams($sql) {
        $matches = [];
        preg_match_all('/\[([^\}]+?)\]/', $sql, $matches);
        foreach($matches[1] as $match) {
            switch(strtolower($match)) {
                // Now
                case 'date':
                case 'nowdate':
                case 'now_date':
                    $sql = str_replace('['.$match.']',date('Y-m-d'),$sql);
                break;
                case 'time':
                case 'now_time':
                case 'nowtime':
                case 'now':
                    $sql = str_replace('['.$match.']',date('Y-m-d H:i:s'),$sql);
                break;

                // Days
                case 'tomorrow':
                    $sql = str_replace('['.$match.']',date('Y-m-d',strtotime('+1 days')),$sql);
                break;
                case 'yesterday':
                    $sql = str_replace('['.$match.']',date('Y-m-d',strtotime('-1 days')),$sql);
                break;

                // Weeks
                case 'next_week':
                    $sql = str_replace('['.$match.']',date('Y-m-d',strtotime('+1 weeks')),$sql);
                break;
                case 'last_week':
                case 'previous_week':
                case 'prev_week':
                    $sql = str_replace('['.$match.']',date('Y-m-d',strtotime('-1 weeks')),$sql);
                break;

                // Months
                case 'next_month':
                    $sql = str_replace('['.$match.']',date('Y-m-d',strtotime('+1 months')),$sql);
                break;
                case 'last_month':
                case 'previous_month':
                case 'prev_month':                    
                    $sql = str_replace('['.$match.']',date("y-m-d",strtotime("-1 months")),$sql);
                break;

                // Years
                case 'next_year':
                    $sql = str_replace('['.$match.']',date('Y-m-d',strtotime('+1 years')),$sql);
                break;
                case 'last_year':
                case 'previous_year':
                case 'prev_year':
                    $sql = str_replace('['.$match.']',date('Y-m-d',strtotime('-1 years')),$sql);
                break;
            }
        }
        return $sql;
    }

    /**
     * @fields     = array of fields to select, if array is empty it will select *
     * @filter     = array['field'] = 'filter'
     * @returnType = string. array | json
     * @order      = array['field'] = 'ASC'
     * @limit      = integer
     * @group      = array of fields to group by
     *
     * @param string $returnType
     * @param array  $fields
     * @param array  $filter
     * @param array  $order
     * @param null   $limit
     * @param array  $group
     * @param array  $sum
     *
     * @return array
     */
    public function select($returnType = 'object',$fields = [],$filter = [],$order = [],$limit = null,$group = [],$sum = []) {
        $this->gotResults = false;
        $selects = [];
        $i = 0;
        if (count($fields) == 0 || !is_array($fields)) {
            foreach($this->columns['name'] as $column) {
                $selects[$i] = str_replace('.','`.`','`'.$column.'`');
                if (isset($this->dataSetConcat[$i]['as'])) {
                    $as[$this->dataSetConcat[$i]['as']][] = $selects[$i];
                }
                $i++;
            }
        } else {
            foreach($fields as $field) {
                if (strpos($field,'.') !== false) {
                    $selects[$i] = str_replace('.','`.`','`'.$field.'`');
                } else {
                    $selects[$i] = '`'.$this->table.'`.`'.$field.'`';
                }
                if (isset($this->dataSetConcat[$i]['as'])) {
                    $as[$this->dataSetConcat[$i]['as']][] = $selects[$i];
                }
                $i++;
            }
        }

        $db = new Database($this->connection);
        $this->_connection = $db->getConnection();
        $sql = 'SELECT ';
        $i = -1;
        $concatsDone = [];
        $totalConcats = 0;
            foreach($selects as $select) {
                $i++;
                $e = 0;
                if (isset($this->dataSetConcat[$i]['as']) && isset($as[$this->dataSetConcat[$i]['as']]) && count($as[$this->dataSetConcat[$i]['as']]) > 1 && !in_array($this->dataSetConcat[$i]['as'],$concatsDone)) {
                    $concatsDone[] = $this->dataSetConcat[$i]['as'];
                    $totalConcats++;
                    $sql .= 'CONCAT(';
                    $e = 0;
                    foreach($as[$this->dataSetConcat[$i]['as']] as $innerSelect) {
                        $e++;
                        $sql .= $innerSelect;
                        if (isset($this->dataSetConcat[$i]['concat']) && $e < count($as[$this->dataSetConcat[$i]['as']])) {
                            $sql .= ",'".$this->dataSetConcat[$i]['concat']."'";
                        }
                        if ($e < count($as[$this->dataSetConcat[$i]['as']])) {
                            $sql .= ',';
                        }
                    }
                    $sql .= ') AS `'.$this->dataSetConcat[$i]['as'].'`';
                } elseif (isset($this->dataSetConcat[$i]['as']) && isset($as[$this->dataSetConcat[$i]['as']]) && count($as[$this->dataSetConcat[$i]['as']]) == 1 && !in_array($this->dataSetConcat[$i]['as'],$concatsDone)) {
                    $sql .= ' AS `'.$this->dataSetConcat[$i]['as'].'`';
                } elseif (!isset($this->dataSetConcat[$i]['as']) || !isset($as[$this->dataSetConcat[$i]['as']])) {
                    $totalConcats--;
                    $sql .= $select;
                }

                if ($totalConcats < 1) {
                    $totalConcats = 1;
                }

                if ($i < count($selects) - $totalConcats) {
                    $lastSqlChar = substr($sql,-1,1);
                    if ($lastSqlChar != ',') {
                        $sql .=',';
                    }
                }
            }

            if (count($sum) && $sum != false) {
                $lastSqlChar = substr($sql,-1,1);
                if ($lastSqlChar != ',') {
                    $sql .=',';
                }
                $sql .= 'SUM('.implode(',',$sum).') AS summed_'.  str_replace('.',"_",implode(',',$sum));
            }
        if ($returnType == 'count') {
            $sql .= ',count(*) AS `count`';
        }
        $sql .= ' FROM `'.str_replace('.','`.`',$this->table).'`';

        if (isset($this->joins) && count($this->joins) > 0) {
            foreach($this->joins as $key=>$join) {
                $sql .= ' '.strtoupper($join['type']).' JOIN';
                if (strpos($join['table'],' AS ') !== false) {
                    $asses = explode(' AS ',$join['table']);
                    $sql .= ' `'.$asses[0].'` AS `'.$asses[1].'` ON ';
                } else {
                    $sql .= ' `'.str_replace('.','`.`',$join['table'])."` ON ";
                }
                $sql .= '`'.str_replace('.','`.`',$join['left']).'` = `'.str_replace('.','`.`',$join['right']).'`';

                if (strpos($join['table'],' AS ') !== false) {
                    $join['table'] = $asses[1];
                }

                if (count($filter) > 0 && is_array($filter)) {
                    foreach($filter as $curFilter) {
                        if (isset($curFilter['joinId']) && $key == $curFilter['joinId']) {
                            $sql .= ' AND {JOIN'.$key.'}';
                        }
                    }
                }
            }
        }

        if (count($filter) > 0 && is_array($filter)) {
            $i = 0;
            $whereApplied = false;
            foreach($filter as $key=>$value) {
                $filterSQL = '';
                if (is_array($value)) {
                    if (isset($value['beginBraces'])) {
                        $filterSQL .= ' '.$value['beginBraces'];
                    }
                    $column = $value['column'];
                    $val = $value['value'];
                    $originalVal = $val;

                    if (strpos($column,'.') !== false) {
                        if (strpos($column,' AS ') !== false) {
                            $filterSQL .= str_replace(' AS ','` AS ',str_replace('.','`.`','`'.$column));
                        } else {
                            $filterSQL .= str_replace('.','`.`','`'.$column.'`');
                        }
                    } else {
                        if (strpos($column,' AS ') !== false) {
                            str_replace(' AS ','` AS ','`'.$this->table.'`.`'.$column);
                        } else {
                            $filterSQL .= '`'.$this->table.'`.`'.$column.'`';
                        }
                    }

                    if (is_numeric($val)) {
                        $val = $val;
                    } elseif (is_bool($val)) {
                        if ($val == true) {
                            $val = 1;
                        } else {
                            $val = 0;
                        }
                    } else {
                        $val = "'".$db->escape_value($val)."'";
                    }

                    switch($value['filter']) {
                        case 'greater':
                            $filterSQL .= ' > '.$val;
                        break;
                        case 'less':
                            $filterSQL .= ' < '.$val;
                        break;
                        case 'greaterorequal':
                            $filterSQL .= ' >= '.$val;
                        break;
                        case 'lessorequal':
                            $filterSQL .= ' <= '.$val;
                        break;
                        case 'in':
                            $filterSQL .= ' IN('.$originalVal.')';
                        break;
                        case 'notin':
                            $filterSQL .= " NOT IN('".$originalVal."')";
                        break;
                        case 'null':
                            switch($originalVal) {
                                case 'not':
                                    $filterSQL .= ' IS NOT NULL';
                                break;
                                case 'is':
                                    $filterSQL .= ' IS NULL';
                                break;
                                default:
                                    $filterSQL .= ' IS NOT NULL';
                                break;
                            }
                        break;
                        case 'equal':
                            $filterSQL .= ' = '.$val;
                        break;
                        case 'like':
                            $filterSQL .= ' LIKE '.$val;
                        break;
                        case 'notlike':
                            $filterSQL .= ' NOT LIKE '.$val;
                        break;
                        case 'notequal':
                            $filterSQL .= ' != '.$val;
                        break;
                    }
                    if (isset($value['endBraces'])) {
                        $filterSQL .= $value['endBraces'].' ';
                    }
                    $i++;
                    if ($i < count($filter)) {
                        if (isset($value['binder'])) {
                            $filterSQL.= ' '.$value['binder'].' ';
                        } else {
                            $filterSQL.= ' AND ';
                        }
                    }

                    // Check if SQL must be added to a JOIN or to the end of the SQL string
                    if (isset($value['joinId']) && $value['joinId'] > -1) {
                        $filterSQL = str_replace(' AND ','', $filterSQL);
                        $sql = str_replace('{JOIN'.$value['joinId'].'}', $filterSQL, $sql);
                    } else {
                        if (!$whereApplied) {
                            $sql .= ' WHERE ';
                            $whereApplied = true;
                        }
                        $sql .= $filterSQL;
                    }
                } else {
                    $i++;
                    $filterSQL .= str_replace('.','`.`','`'.$key.'`').' = ';
                    if (is_numeric($value)) {
                        $filterSQL.= $value;
                    } elseif (is_bool($value)) {
                        if ($value == true) {
                            $filterSQL.= 1;
                        } else {
                            $filterSQL.= 0;
                        }
                    } else {
                        $filterSQL .= "'".$db->escape_value($value)."'";
                    }
                    if ($i < count($filter)) {
                        $filterSQL.= ' AND ';
                    }

                    if (!$whereApplied) {
                        $sql .= ' WHERE ';
                        $whereApplied = true;
                    }
                    $sql .= $filterSQL;
                }
            }
            if (in_array($this->table.'status',$this->columns['name'])) {
                $sql .= " AND `$this->table`.`status` != 'Deleted'";
            }
        } else {
            if (in_array($this->table.'.status',$this->columns['name'])) {
                $sql .= " WHERE `$this->table`.`status` != 'Deleted'";
            }
        }

        if (isset($group) && $group != false) {
            if (is_array($order)) {
                $sql .= ' GROUP BY ';
                $i = 0;
                foreach($group as $value) {
                    // hack if there are 2 tables $value (Remove $this->table from $value)
                    if (substr_count($value,'.') + 1 > 2) {
                        $value = str_replace($this->table,'',$value);
                    }
                    $i++;
                    $sql .= str_replace('.','`.`','`'.$value.'`');
                    if ($i < count($group)) {
                        $sql.= ', ';
                    }
                }
            } else {
                $this->setError('The GROUP expression must be of type array. Example: array("column Name","Column Name")');
            }
        }

        if (count($order) && $order != false) {
            if (is_array($order)) {
                $sql.= ' ORDER BY ';
                $i = 0;
                foreach($order as $key => $value) {
                    // Check if there are 2 tables $value (Remove $this->table from $value)
                    if (substr_count($key,'.') + 1 > 2) {
                        $key = str_replace($this->table.'.','',$key);
                    }
                    $i++;
                    $sql .= str_replace('.','`.`','`'.$key.'`').' '.$value;
                    if ($i < count($order)) {
                        $sql.= ', ';
                    }
                }
            } else {
                $this->setError('The ORDER expression must be of type array. Example: array("column Name"=>"ASC","Column Name"=>"DESC")');
            }
        }

        if (isset($limit) && $limit != false) {
            if (is_array($limit)) {
                if (isset($limit['start'])) {
                    $sql .= ' LIMIT '.$limit['start'];
                }
                if (isset($limit['rows']) && isset($limit['start'])) {
                    $sql .= ', '.$limit['rows'];
                }
                if (isset($limit['rows']) != isset($limit['start'])) {
                    $sql .= ' LIMIT '.$limit['rows'];
                }
            } else {
                $sql .= ' LIMIT '.$limit;
            }
        }

        // Replace Expression Params
        foreach($this->expressionParams as $key => $value) {
            $sql = str_replace('${'.$key.'}',$value,$sql);
        }

        // Replace User Defined Constants
        foreach($this->constants as $key => $value) {
            if (!is_array($value)) {
                $sql = str_replace('${'.$key.'}',$value,$sql);
            }
        }

        // Replace coded params (words between '[' and ']')
        $sql = $this->replaceFunctionParams($sql);

        // When CONCAT 2 `` lands up next to each other. So:
        // More Errors with CONCAT
        // More Errors with CONCAT
        $sql = str_replace(['``','`CONCAT',', FROM'],['`,`','`, CONCAT',' FROM'],$sql);

        $this->sqlQuery = $sql;
        $db->query($sql);
        $this->gotResults = $db->gotResults();
        if ($this->saveLog && $this->saveSelectLog) {
            $this->logQuery = $this->sqlQuery;
        }

        if ($this->logQuery && $this->saveSelectLog) {
            $this->saveModelHistory('Select',$this->results);
        }
        $this->isLoaded(true);
        switch(strtolower($returnType)) {
            case 'array':
                if ($this->gotResults) {
                $this->setResults($db->toArray());
                } elseif($this->useDefaults) {
                    $this->setResults($this->defaults,'string');
                } else {
                    $this->setResults([],'string');
                }
                return $this->results;
            case 'json':
                if ($this->gotResults) {
                    $this->setResults($db->toJSON(),'string');
                } elseif($this->useDefaults) {
                    $this->setResults(json_encode($this->defaults),'string');
                } else {
                    $this->setResults(json_encode([]),'string');
                }
                return $this->results;
            case 'count':
                if ($this->gotResults) {
                $results = $db->toArray();
                    $this->setResults($results,'string');
                } else {
                    $this->setResults([],'string');
                }
                return $this->results;
            case 'object':
                $item = ModelItem::mold($this->modelSingle.'Item');
                $item = $this->AddPropertiesToModelItem($item);
                if ($this->gotResults) {
                    $items = $db->toObject($item,$this->primaryKey);
                    $this->setResults($items,'string');
                    $this->add($items);
                } else {
                    // Defaults are added when the "AddPropertiesToModelItem" is called
                    $this->setResults($item,'string');
                    $this->add($item);
                }
                
                return $this->results;
            default:
                return $this->results;
            break;
        }
    }
    
    public function AddPropertiesToModelItem($item) {
        $objectProps = $this->getObjectProperties();
        foreach($objectProps as $property => $value) {
            // The use defaults is already used when adding properties to $this->objectProperties
            $item->$property = $value;
        }
        return $item;
    }

    public function getLastRecord() {
        $this->gotResults = false;
        $db = new Database($this->connection);
        $this->_connection = $db->getConnection();

        $sql = 'SELECT ';
        foreach ($this->columns['name'] as $column) {
            $select[] = $column;
        }
        $sql .= implode(',',$select).' ';
        $sql .=  'FROM `'.$this->table.'`';

        if (isset($this->joins) && count($this->joins) > 0) {
            foreach($this->joins as $join) {
                $sql .= '  '.$join['type'].' JOIN `'.$join['table'].'` ON '.$join['left'].' = '.$join['right'];
            }
        }

        $sql .= ' ORDER BY '.$this->primaryKey.' DESC LIMIT 1';
        $this->sqlQuery = $sql;
        $db->query($sql);

        if ($db->gotResults()) {
            $this->gotResults = true;
            $this->setResults($db->nextItem(),'string');
        } elseif($this->useDefaults) {
            $this->setResults($this->defaults,'string');
        } else {
            $this->setResults([],'string');
        }
        return $this->results;
    }

    public function getRecord($key) {
        $this->gotResults = false;
        $db = new Database($this->connection);
        $this->_connection = $db->getConnection();

        $sql = 'SELECT ';
        foreach ($this->columns['name'] as $column) {
            $select[] = $column;
        }
        $sql .= implode(',',$select).' ';
        $sql .=  'FROM `'.$this->table.'`';

        if (isset($this->joins) && count($this->joins) > 0) {
            foreach($this->joins as $join) {
                $sql .= ' '.strtoupper($join['type']).' JOIN';
                if (strpos($join['table'],' AS ') !== false) {
                    $asses = explode(' AS ',$join['table']);
                    $sql .= ' `'.$asses[0].'` AS `'.$asses[1].'` ON ';
                } else {
                    $sql .= ' `'.str_replace('.','`.`',$join['table']).'` ON ';
                }
                $sql .= '`'.str_replace('.','`.`',$join['left']).'` = `'.str_replace('.','`.`',$join['right']).'`';

                if (strpos($join['table'],' AS ') !== false) {
                    $join['table'] = $asses[1];
                }

                if (isset($filter) && count($filter) > 0 && is_array($filter)) {
                    foreach($filter as $curFilter) {
                        if (isset($curFilter['joinId']) && $key == $curFilter['joinId']) {
                            $sql .= ' AND {JOIN'.$key.'}';
                        }
                    }
                }
            }
        }

        $sql .= ' WHERE `'.$this->table.'`.`'.$this->primaryKey.'` = ';
        if (is_numeric($key)) {
            $sql.= $key;
        } elseif (is_bool($key)) {
            if ($key == true) {
                $sql.= 1;
            } else {
                $sql.= 0;
            }
        } else {
            $sql .= "'".$db->escape_value($key)."'";
        }
        $this->sqlQuery = $sql;
        $db->query($sql);

        $ModelItem = ModelItem::mold($this->modelSingle.'Item');
        $ModelItem = $this->AddPropertiesToModelItem($ModelItem);
        if ($db->gotResults()) {
            $this->gotResults = true;
            $item = $db->toObject($ModelItem,$this->primaryKey);
            $this->setResults($item,'string');
            $this->add($item);
        } else {
            // Defaults are added when the "AddPropertiesToModelItem" is called
            $this->setResults($ModelItem,'string');
            $this->add($ModelItem);
        }
        $this->isLoaded(true);
        return $this->results;
    }

    public function getNumRows() {
        $db = new Database($this->connection);
        $this->_connection = $db->getConnection();

        $sql = 'SELECT
                    count(*) AS count
                FROM `'.$this->table.'`';
        $this->sqlQuery = $sql;
        $db->query($sql);
        $count = $db->nextItem();
        return $count['count'];
    }

    public function getTotals() {
        $newRows = [];
        $i = 0;
        $totals = [];
        foreach($this->results as $row) {
            $newRows[$i] = $row;
            foreach($row as $key=>$value) {
                if (is_numeric($value)) {
                    if (!isset($totals[$key])) {
                        $totals[$key] = 0;
                    }
                    $totals[$key] += $value;
                    $newRows[$i][$key.'_columnTotal'] = $totals[$key];
                }
            }
            $i++;
        }
        unset($this->results);
        $this->setResults($newRows);
        return $this;
    }

    public function getAvarages() {
        $newRows = [];
        $i = 0;
        $totals = [];
        foreach($this->results as $row) {
            $newRows[$i] = $row;
            foreach($row as $key=>$value) {
                if (is_numeric($value)) {
                    if (!isset($totals[$key])) {
                        $totals[$key] = 0;
                    }
                    $totals[$key] += $value;
                    $newRows[$i][$key.'_columnAvarage'] = $totals[$key] / ($i+1);
                }
            }
            $i++;
        }
        unset($this->results);
        $this->setResults($newRows);
        return $this;
    }

    public function transpose($columns = [],$key = null) {
        if (!isset($key)) {
            if (!is_array($this->primaryKey)) {
                $key = $this->primaryKey;
            } else {
                $this->setError('The <strong>key</strong> can not be an array');
                return false;
            }
        }

        if (!$this->gotResults) {
            $this->setNotify('info','No results were found to transpose');
            return false;
        }

        if (current($this->results)->{$key} === null) {
            $this->setError('The key ('.$key.') you are using to transpose does not exist');
            return false;
        }

        foreach($columns as $column) {
            if(is_array($key) && array_key_exists($key,$column)) {
                $this->setError('Column: <strong>'.$column.'</strong> does not exist in results');
                return false;
            }
        }

        $newResults = [];
        $tempData = [];
        $i = 0;
        foreach($this->results as $result) {
            if (!isset($tempData[$result->{$key}])) {
                $tempData[$result->{$key}] = new \stdClass();
            }
            foreach($columns as $column) {
                if (!empty($result->{$column})) {
                    $tempData[$result->{$key}]->{$column}[] = $result->{$column};
                }
                //unset($result->{$column});
            }
            $i++;
        }

        foreach($this->results as $result) {
            $newResults[$result->{$key}] = $result;
            if ($tempData[$result->{$key}] !== null) {
                $newResults[$result->{$key}]->{'transposed'} = $tempData[$result->{$key}];
            }
        }
        $this->setResults($newResults);
        return $this;
    }

    public function saveModelHistory($queryType,$history) {
        $selectRows    = '';
        $lastInsertId  = '';
        $insertHistory = '';
        $updateHistory = '';
        $deleteHistory = '';

        switch($queryType) {
            case 'Select':
                $selectRows = count($this->results);
                $lastInsertId = null;
            break;
            case 'Insert':
                $insertHistory = $history;
                $lastInsertId = $this->lastInsertId;
            break;
            case 'Update':
                $updateHistory = $history;
                $lastInsertId = null;
            break;
            case 'Delete':
                $deleteHistory = $history;
                $lastInsertId = null;
            break;
        }

        $gotResults = '0';
        if ($this->gotResults) {
            $gotResults = '1';
        }

        $foundDouble = '0';
        if ($this->foundDouble) {
            $foundDouble = '1';
        }
        $db = new Database();

        $controller = 'undefined';
        if (isset($_SERVER['CALLED_CONTROLLER'])) {
            $controller = $_SERVER['CALLED_CONTROLLER'];
        }

        if (isset($_SERVER['CALLED_METHOD'])) {
            $function = $_SERVER['CALLED_METHOD'];
        } else {
            $function = 'undefined';
        }

        $params = 'undefined';
        if (isset($_SERVER['CALLED_PARAMS'])) {
            $params = $_SERVER['CALLED_METHOD'];
        }

        $logSQL = "INSERT INTO
                    `model_history`
                    (
                        `model`,
                        `connection`,
                        `table`,
                        `columns`,
                        `key`,
                        `comb_key`,
                        `got_results`,
                        `joins`,
                        `found_double`,
                        `model_xml`,
                        `sql_query`,
                        `log_query`,
                        `data_set_filter`,
                        `data_set_order`,
                        `data_set_group`,
                        `data_set_return`,
                        `data_set_limit`,
                        `data_set_columns`,
                        `data_set_concat`,
                        `data_set_column_count`,
                        `data_set`,
                        `data_set_alias`,
                        `data_set_description`,
                        `order`,
                        `limit`,
                        `group`,
                        `query_type`,
                        `selected_rows`,
                        `updated_rows`,
                        `deleted_rows`,
                        `inserted_rows`,
                        `last_insert_id`,
                        `controller`,
                        `method`,
                        `params`,
                        `user_id`
                    )
                    VALUES
                    (
                        '".$db->escape_value($this->modelAlias)."',
                        '".$db->escape_value($this->connection)."',
                        '".$db->escape_value($this->table)."',
                        '".$db->escape_value(json_encode($this->columns))."',
                        '".$db->escape_value(json_encode($this->primaryKey))."',
                        '".$db->escape_value(json_encode($this->combKey))."',
                        '".$gotResults."',
                        '".$db->escape_value(json_encode($this->joins))."',
                        '".$foundDouble."',
                        '".$db->escape_value($this->xml->asXML())."',
                        '".$db->escape_value($this->sqlQuery)."',
                        '".$db->escape_value($this->logQuery)."',
                        '".$db->escape_value(json_encode($this->dataSetFilter))."',
                        '".$db->escape_value(json_encode($this->dataSetOrder))."',
                        '".$db->escape_value(json_encode($this->dataSetGroup))."',
                        '".$db->escape_value($this->dataSetReturn)."',
                        '".$db->escape_value(json_encode($this->dataSetLimit))."',
                        '".$db->escape_value(json_encode($this->dataSetColumns))."',
                        '".$db->escape_value(json_encode($this->dataSetConcat))."',
                        '".$db->escape_value($this->dataSetColumnCount)."',
                        '".$db->escape_value($this->dataSet)."',
                        '".$db->escape_value($this->dataSetAlias)."',
                        '".$db->escape_value($this->dataSetDescription)."',
                        '".$db->escape_value(json_encode($this->order))."',
                        '".$db->escape_value(json_encode($this->limit))."',
                        '".$db->escape_value(json_encode($this->group))."',
                        '".$db->escape_value($queryType)."',
                        '".$db->escape_value(json_encode($selectRows))."',
                        '".$db->escape_value(json_encode($updateHistory))."',
                        '".$db->escape_value(json_encode($deleteHistory))."',
                        '".$db->escape_value(json_encode($insertHistory))."',
                        '".$lastInsertId."',
                        '".$db->escape_value($controller)."',
                        '".$db->escape_value($function)."',
                        '".$db->escape_value($params)."',
                        '".$this->session('user.id')."'
                    )";
        foreach($this->expressionParams as $key => $value) {
            $logSQL = str_replace('${'.$key.'}',$value,$logSQL);
        }
        $db->query($logSQL);
    }
}
