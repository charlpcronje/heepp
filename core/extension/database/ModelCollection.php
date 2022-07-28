<?php
namespace core\extension\database;
use core\mold\php\ModelItem;

trait ModelCollection {
    public $modified      = [];
    public $inserted      = [];
    public $collection    = [];
    private $hidden       = [];
    private $currentIndex;
    private $keys         = [];
    private $isLoaded     = false;
    private $lastCommand;
    private $deleted      = [];
    
    public function __debugInfo() {
        /** @noinspection MagicMethodsValidityInspection */
        return $this->debugInfo();
    }
    
    private function debugInfo() {
        switch($this->lastCommand) {
            case 'save':
                if (isset($this->modified[$this->currentIndex]) && $this->modified[$this->currentIndex]->updatedRows == 0) {
                   return [
                        'result'        => 'failed',
                        'reason'        => 'Nothing on the object you attempted to save has changed',
                        'action'        => 'save',
                        'rowsUpdated'   => $this->modified[$this->currentIndex]->updatedRows,
                        'modified'      => $this->modified[$this->currentIndex],
                        'query'         => $this->sqlQuery
                    ];
                }

                $result = [
                    'result'        => 'success',
                    'action'        => 'save',
                    'key'           => $this->primaryKey,
                    'keyValue'      => $this->currentIndex,
                    'rowsUpdated'   => $this->modified[$this->currentIndex]->updatedRows,
                    'modified'      => $this->modified[$this->currentIndex],
                    'query'         => $this->sqlQuery
                ];

                return $result;
            break;
            case 'saveAll':
                return [
                    'status'   => 'success',
                    'action'   => 'saveAll',
                    'modified' => $this->modified,
                    'query'    => $this->sqlQuery
                ];
            break;
            case 'setProperty':
            case 'modifyItem':
                if (count($this->modified) > 0) {
                    return [
                        'action'   => 'modifyItem',
                        'modified' => $this->modified[$this->currentIndex],
                        'current'  => $this->current()
                    ];
                }
                return (array)$this->current();
            break;
            case 'unload':
                return [
                    'action'     => 'unload',
                    'collection' => $this->collection
                ];
            break;
            case 'loadDataSet':
            case 'setExpressionParam':                
                if ($this->length() > 0 && $this->length() == 1) {
                    return (array)$this->collection[key($this->collection)];
                }
                if ($this->length() > 1) {
                    return $this->collection;
                }
                return $this->collection;
            case 'get':
                if ($this->length() == 0) {
                    return [];
                }
                if ($this->length() == 1) {
                    return $this->current();
                }
                if ($this->length() > 1) {
                    return $this->collection;
                }
            break;
            case 'getAll':
                return $this->collection;
            break;
            case 'find':
            case 'add':
                return (array)$this->current();
            break;
            //case 'setProperty':
            //    return [
            //        'action'   => 'setProperty',
            //        'property' => $this->modified[$this->currentIndex],
            //        'current'  => $this->current()
            //    ];
            //break;
            case 'addProperty':
                return [
                    'action'   => 'addProperty',
                    'current'  => $this->current()
                ];
            break;
            case 'runDataSet':
                if ($this->length() > 1) {
                    return $this->current();
                }
                return (array)$this->collection;
            break;
            case 'checkLoaded':
                return [
                    'action' => 'loaded',
                    'suggestion' => 'Try using one of the following methods: get, getAll, current, next, rewind or count'
                ];
            break;
            case 'remove':
                return [
                    'action'   => 'remove',
                    'modified' => $this->modified[$this->currentIndex],
                    'query'    => $this->sqlQuery
                ];
            break;
            case 'restoreSuccess':
                return [
                    'action'    => 'undelete',
                    'item'      => $this->current()
                ];
            break;
            case 'restoreFailed':
                return [
                    'action' => 'restoreFailed',
                    'reason' => 'The record was no longer available to undelete'
                ];
            break;
            //case 'add':
            case 'insert':
                return [
                    'action'   => 'insert',
                    'inserted' => $this->inserted
                ];
            break;
            default:
                if ($this->length() > 1) {
                    return $this->collection;
                }
                if ($this->length() == 1) {
                    return $this->current();
                }
                if (empty($this->dataSet)) {
                    return [
                        'action' => 'notLoaded',
                        'reason' => 'No dataSet specified'
                    ];
                }
            break;
        }
    }
    
    private function createUpdateLog($result) {
        $this->lastCommand = 'insert';
        $ModelCollection = Model::mold('updateLog');
        
        $result = (object)$result;
        $hash                 = md5(json_encode($result).date('YmdHis'));
        $model                = ModelItem::mold('updateLog');
        $model->update_hash   = $hash;
        $model->table         = $this->table;
        $model->key           = $result->key;
        $model->key_value     = $result->keyValue;
        $model->query         = $result->query;
        $model->affected_rows = @$result->modified->updatedRows;
        $model->updated_by    = json_encode($this->session('user'));
        $model->api_key       = API_KEY;
        $model->created_at    = CURRENT_TIMESTAMP;
        $model->updated_at    = CURRENT_TIMESTAMP;
        
        $modelClone = clone $model;
        
        unset($result->modified->updatedRows);
        foreach((array)$result->modified as $modified) {            
            $modelClone->field = key($modified);
            if (isset($modified->from)) {
                $modelClone->from = $modified->from;
            }
            
            if (isset($modified->to)) {
                $modelClone->to = $modified->to;
            }
            $ModelCollection->add($modelClone);
        }
    }
    
    public function save() {
        $this->lastCommand = 'save';
        if (isset($this->collection[$this->currentIndex]->{$this->primaryKey}) && !empty($this->collection[$this->currentIndex]->{$this->primaryKey})) {
            if (isset($this->modified[$this->currentIndex])) {
                $updateArray = [];
                foreach($this->modified[$this->currentIndex] as $property => $propObj) {
                    $updateArray[$property] = $propObj->to;
                }
                if (count($updateArray) == 0) {
                    $this->modified[$this->currentIndex]->updatedRows = 0;

                    return $this;
                }
                $updateArray[$this->primaryKey] = $this->currentIndex;

                $this->update($updateArray);
                $this->modified[$this->currentIndex]->updatedRows = $this->results;

                return $this;
            }
        } elseif(!isset($this->collection[$this->currentIndex]->{$this->primaryKey}) || empty($this->collection[$this->currentIndex]->{$this->primaryKey})) {
            $this->lastCommand = 'insert';
            foreach((array)$this->collection[$this->currentIndex] as $key => $value) {
                if (!empty($value)) {
                    $insertArray[$key] = $value;
                }
            }

            $this->insert($insertArray);
            $this->inserted = new \stdClass();
            $this->inserted->{$this->primaryKey} = $this->getLastInsertId();
            foreach($insertArray as $key => $value) {
                $this->inserted->{$key} = $value;
            }
            $newItem = $this->AddPropertiesToModelItem(ModelItem::mold($this->model));
            $newItem->{$this->primaryKey} = $this->getLastInsertId();
            foreach($insertArray as $key => $value) {
                $newItem->{$key} = $value;
            }
            $this->add($newItem);
            return $this;
        }
    }
    
    public function saveAll() {
        $this->lastCommand = 'saveAll';
        //$currentIndex = $this->currentIndex;
        if (count($this->modified) > 0) {
            foreach($this->modified as $modified) {
                $this->currentIndex = $modified->{$this->primaryKey};
                $this->save();
            }
        }
        //$currentIndex = $this->currentIndex;
    }
    
    public function find($key) {
        /* Does not unload, a new find will jsy add to the collection and move 
         * the collection cursor to the new item's position
         * $this->unload();
         */
        $this->lastCommand = 'find';
        $record = $this->getRecord($key);
        if (is_array($record) && count($record) == 1) {
            $record = $record[key($record)];
        } else {
            if (!is_object($record) && !is_array($record)) {
                $record = (object)$record;
            }
        }
        
        if ($this->getGotResults()) {
            $this->add($record,$record->{$this->primaryKey});
            if(!isset($this->currentIndex)) {
                $this->currentIndex = $record->{$this->primaryKey};
            }
        }
        return $this;
    }
    
    private function modifyItem($key,$property,$value) {
        $this->lastCommand = 'modifyItem';
        if (property_exists((object)$this->collection[$key],$property)) {
            if (!isset($this->modified[$key])) {
                $this->modified[$key] = new \stdClass();
            }
            if ($this->collection[$key]->{$property} != $value) {
                $this->modified[$key]->{$property} = new \stdClass();
                if (isset($this->modified[$key]->{$property}->from)) {
                    $this->modified[$key]->{$property}->to = $value;
                } else {
                    $this->modified[$key]->{$property}->from = $this->collection[$key]->{$property};
                    $this->modified[$key]->{$property}->to = $value;
                }
            }
        }
    }
    
    public function addProperty($property,$value) {
        if (!isset($this->collection[$this->currentIndex])) {
            $this->collection[$this->currentIndex] = new \stdClass();
        }
        $this->lastCommand = 'addProperty';
        $this->collection[$this->currentIndex]->{$property} = $value;
        return $this;
    }
    
    public function set($property,$value = null) {
        $this->setProperty($property, $value);
        return $this;
    }
    
    public function setProperty($property,$value) {
        $this->lastCommand = 'setProperty';
        if (!isset($this->collection[$this->currentIndex])) {
            $this->collection[$this->currentIndex] = new \stdClass();
        }
        $this->modifyItem($this->currentIndex,$property,$value);
        $this->collection[$this->currentIndex]->{$property} = $value;
        return $this;
    }
    
    // ----- ITERATOR IMPLEMENTATIONS
    private function addToCollection($item,$key = null) {
        if ($key === null){
            $this->collection[] = $item;
            if(!isset($this->currentIndex)) {
                $this->currentIndex = key(end($this->collection));
            }
        } else {
            // key was specified, check if key exists
            if (isset($this->collection[$key])) {
                new \Exception('Trying to add duplicate key: "'.$key.'" to "'.$this->model.'" collection');
            } else {
                $this->collection[$key] = $item;
            }
        }
    }
    
    public function keys() {
        return array_keys($this->collection);
    }
    
    public function add($item, $key = null) {
        $this->lastCommand = 'add';
        if (isset($key)) {
            $this->addToCollection($item,$key);
        } else {
            if (is_array($item) && count($item) > 1) {
                foreach($item as $objkey => $object) {
                    if (isset($object->{$this->primaryKey})) {
                        $this->addToCollection($object,$object->{$this->primaryKey});
                    } else {
                        $this->addToCollection($object,$objkey);
                    }
                }
            } elseif (is_array($item) && count($item) == 1) {
                $objkey = key($item);
                $object = $item[$objkey];
                if (isset($object->{$this->primaryKey})) {
                    $this->addToCollection($object,$object->{$this->primaryKey});
                } else {
                    $this->addToCollection($object,$objkey);
                }
            } elseif(is_object($item)) {
                $object = $item;
                if (isset($object->{$this->primaryKey})) {
                    $this->addToCollection($object,$object->{$this->primaryKey});
                } elseif(isset($objkey)) {
                    $this->addToCollection($object);
                }
            }
        }
        $this->updateKeys();
        if(!isset($this->currentIndex)) {
            $this->currentIndex = $key;
        }
        $this->isLoaded(true);
        return $this;
    }
    
    private function checkLookups() {
        if (isset($this->lookups) && count($this->lookups) > 0) {
            foreach($this->lookups as $key => $value) {
                
            }
        }
    }
    
    private function isLoaded($isLoaded = false) {
        $this->isLoaded = $isLoaded;
        if ($this->isLoaded) {
            $this->checkLookups();
        }
    }
    
    public function checkLoaded() {
        $this->lastCommand = 'checkLoaded';
        if (!empty($this->dataSet) && !$this->isLoaded) {
            $this->lastCommand = 'runDataSet';
            $this->runDataSet();
            $this->isLoaded(true);
            return $this;
        }
        if (empty($this->dataSet) && !$this->isLoaded) {
            $this->lastCommand = 'runDataSet';
            $this->runDataSet();
            $this->isLoaded(true);
            return $this;
        }
        return $this;
    }
    
    public function get($key = null) {
        $this->lastCommand = 'get';
        $this->checkLoaded();
        if (!isset($key)) {
            if ($this->length() == 1) {
                return $this;
            }
            return $this->getAll();
        }
        if (isset($this->collection) && isset($this->collection[$key])) {
            return $this->collection[$key];
        }
        return $this->collection = new \ArrayIterator();
    }
    
    public function getAll() {
        $this->checkLoaded();
        $this->updateKeys();
        $this->lastCommand = 'getAll';
        return $this->collection;
    }
    
    public function length() {
        $this->checkLoaded();
        return count($this->collection);
    }
    
    public function clear() {
        $this->collection = [];
        $this->modified = [];
        $this->updateKeys();
    }
    
    public function exists($key){
        $this->checkLoaded();
        return isset($this->collection[$key]);
    }
    
    public function deleteItem($key,$removed = false) {
        $this->delete($key);
        $this->deleted[$key] = (object)[
            'recordsDeleted' => $this->results,
            'modified'       => $this->modified[$key],
            'deletedItem'    => $this->collection[$key]      
        ];
        if (!$removed) {
            $this->remove($key,true);
        }
        unset($this->collection[$key]);
    }
    
    public function remove($key,$deleted = false){
        $this->lastCommand = 'remove';
        $this->checkLoaded();
        if (isset($this->collection[$key])) {
            if ($deleted) {
                foreach($this->collection[$key] as $property) {
                    $this->modifyItem($key,$property,$value = null);
                }
                $this->modified[$key]->recordsDeleted = $this->results;
                $this->updateKeys();
            }
            if (!$deleted) {
                $this->deleteItem($key,true);
            }
        }
    }
    
    public function restore($key) {
        $this->lastCommand = 'restore';
        if (isset($this->deleted[$key])) {
            $this->insert($this->deleted[$key]->deletedItem);
            if ($this->getLastInsertId() == $key) {
                $this->add($this->deleted[$key]->deletedItem,$key);
                unset($this->deleted[$key]);
                $this->currentIndex = $key;
                $this->lastCommand = 'restoreSuccess';
            }
        } else {
            $this->lastCommand = 'restoreFailed';
        }
    }
    
    private function updateKeys() {
        $this->keys = $this->keys();
    }
    
    public function current() {
        $this->checkLoaded();
        if (!isset($this->keys)) {
            $this->keys();
        }
        return $this->get($this->key());
    }
    
    public function unload(){
        $this->lastCommand = 'unload';
        $this->clear();
        $this->isLoaded(false);
    }
    
    public function key() {
        return current($this->keys);
    }
    
    public function prev() {
        $currentKey = current($this->keys);
        // Check if im not already on the first key
        if ($currentKey == current(array_keys($this->keys))) {
            $this->currentIndex = $currentKey;
        } else {
            $this->currentIndex = prev($this->keys);
        }
        return $this;
    }
    
    public function next() {
        $currentKey = current($this->keys);
        if ($currentKey == end($this->keys)) {
            $this->currentIndex = $currentKey;
        } else {
            $this->currentIndex = next($this->keys);
        }
        return $this;
    }
    
    public function findNearestIndex($currentIndex, $canStay = true) {
        // if my current key is valid ill stay where I am if $canStay is true
        if ($this->valid($currentIndex) && $canStay) {
            return $currentIndex;
        }

        // Check if im at the end of the array and go back
        if ($currentIndex == end($this->keys)) {
            $this->prev();

        // Check if im at the start of the array and go forward
        } elseif ($currentIndex == current(array_keys($this->keys))) {
            $this->next();
        }

        // There are no other places to go
        if ($currentIndex == $this->currentIndex) {
            return null;
        }
        return $this->currentIndex;
    }
    
    public function hide($key) {
        if (!$this->valid($key)) {
            return $this;
        }

        if (!isset($this->collection[$key])) {
            return $this;
        }
        $this->hidden[$key] = $this->collection[$key];
        unset($this->collection[$key]);
        $this->updateKeys();
        if ($this->currentIndex == $key) {
            //$this->findNearestIndex($key);
        }
        return $this;
    }
    
    public function valid($key = null) {
        if (isset($key)) {
            return isset($this->keys[$key]);
        } 
        return isset($this->keys[$this->currentIndex]);
    }
    
    public function reverse() {
        $this->collection = array_reverse($this->collection);
        $this->rewind();
    }
    
    public function rewind() {
        $this->currentIndex = current(array_keys($this->keys));
        
        //= reset($this->keys);
        return $this;
    }
    
    public function count() {
        return count($this->collection);
    }
    
    // ------- MAGIC METHODS
    public function __set($property, $value = null) {
        if (isset($this->collection[$this->currentIndex]) && property_exists($this->collection[$this->currentIndex],$property)) {
            if ($this->collection[$this->currentIndex]->{$property} !== $value) {
                $this->setProperty($property,$value);
            }
        } else {
            $this->addProperty($property,$value);
        }
        return $this;
    }

    public function __isset($property = null) {
        if (isset($this->$property)) {
            return true;
        }
        return false;
    }

    public function __get($property) {
        if (isset($this->collection[$this->currentIndex]->{$property})) {
            return $this->collection[$this->currentIndex]->{$property};
        }
        return null;
    }
}
