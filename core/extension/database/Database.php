<?php
namespace core\extension\database;

class Database {
    public $last_query;
    public $_magic_quotes_active;
    public $_real_escape_string_exists;
    public $result_set;
    public $lastInsertId;
    private $_connection;
    private $mysqlI;
    private $keyValueSet = [];
            
    public function __construct($conn = null,$openConnection = true) {
        $connection = (new \Connection($conn))->getConnection();
        
        if (!isset($connection)) {
            new \Exception('The connection for MySQL has neen set');
        } elseif(is_array($connection)) {
            $connection = (object)$connection;
        }
        
        if (!isset($connection->host)) {
            new \Exception("Database 'host' Not Defined");
        }
        
        if (!isset($connection->username)) {
            new \Exception("Database 'username' Not Defined");
        }
        
        if (!isset($connection->password)) {
            new \Exception("Database 'password' Not Defined");
        }

        if (isset($connection->database)) {
            $this->openConnection(
                $connection->host,
                $connection->username,
                $connection->password,
                $connection->database
                //$connection->auto_increment_increment
            );
        } else {
            new \Exception("Database 'dbname' Not Defined");
        }

        $this->_magic_quotes_active = get_magic_quotes_gpc();
        $this->_real_escape_string_exists = function_exists("mysqli_real_escape_string");
    }
    
    public function getConnection() {
        return $this->_connection;
    }
    
    public function selectDatabase($db) {
        mysqli_select_db($db, $this->_connection);
    }
    
    public function bind($key,$value) {
        $this->keyValueSet[$key] = $value;
    }
    
    public function gotResults() {
        return mysqli_num_rows($this->result_set) > 0;
    }
    
    public function getLink() {
        return $this->mysqlI;
    }

    public function openConnection($db_host = null, $db_user = null, $db_pass = null, $db_name = null, $db_increment_increment = null) {
        $this->mysqlI = new \mysqli($db_host,$db_user,$db_pass,$db_name);
        $this->mysqlI->query("SET character_set_results=utf8");
        if (isset($db_increment_increment)) {
            $this->mysqlI->query("SET @@auto_increment_increment = ".$db_increment_increment.";");
        }
        mb_language('uni'); 
        mb_internal_encoding('UTF-8');
        $this->mysqlI->query("set names 'utf8'");
        
        if(strlen($this->mysqlI->error) > 0) {
            die('Database connection failed: ' . $this->mysqlI->error);
        }
        $db_select = $this->mysqlI->select_db($db_name);

        if(!$db_select) {
            new \Exception('Database selection failed: ' . mysqli_error($this->mysqlI));
        }
    }

    public function close_connection() {
        if(isset($this->_connection)) {
            mysqli_close($this->_connection);
            unset($this->_connection);
        }
    }

    public function query($sql,$logQuery = null) {
        if (!isset($logQuery)) {
            $logQuery = (int)env('log.sql');
        }
        $this->last_query = $sql;
        $this->result_set = $this->mysqlI->query($sql);
        $this->_confirm_query($this->result_set,$sql);
        $this->lastInsertId = $this->mysqlI->insert_id;
        if ($logQuery) {
            $this->logQuery();
        }
        return $this->mysqlI;
    }
    
    private function logQuery() {
        //if (sessionSet('user.id') && strpos(strtolower($this->last_query),'select') === false &&
        if (strpos(strtolower($this->last_query),'`log`') === false) {
            $sql = "INSERT INTO sql_log
                    (
                        `sql_query`,
                        `user_id`
                    )
                    VALUES
                    (
                        '".$this->escape_value($this->last_query)."',
                        '".$this->escape_value(session('user.id'))."'
                    )";
            $result = $this->mysqlI->query($sql);
            $this->_confirm_query($result,$sql);
        }
    }

    public function escape_value($value) {
        if ($this->_real_escape_string_exists) { // PHP v4.3.0 or higher
            // undo any magic quote effects so mysqli_real_escape_string can do the work
            if ($this->_magic_quotes_active) { 
                $value = stripslashes($value);
            }
            if (is_array($value)) {
                $value = (string)current($value);
            }
            $value = $this->mysqlI->real_escape_string($value);
        } else { 
            // before PHP v4.3.0
            // if magic quotes aren't already on then add slashes manually
            if (!$this->_magic_quotes_active) { 
                $value = addslashes($value);
            }
            // if magic quotes are active, then the slashes already exist
        }
        return $value;
    }

    public function nextItem() {
        return $this->result_set->fetch_assoc();
    }
    
    public function fetch_array() {
        return $this->result_set->fetch_array();
    }

    public function num_rows() {
        return $this->result_set->num_rows();
    }

    public function insert_id() {
        return $this->lastInsertId;
    }

    public function affected_rows() {
        return $this->mysqlI->affected_rows;
    }

    private function _confirm_query($result,$sql) {
        if(!$result) {
            throw new \Exception("Database query failed: " . $this->mysqlI->error. 'In Query: '.$sql);
            // uncomment below line when you want to debug your last query
            // $output .= "<br /><br />Last SQL Query: " . $this->last_query;
            //die($output);
        }
    }
    
    public function toJSON() {
        $json = $this->toArray();
        return json_encode($json);
    }
    
    public function toArray() {
        $values = array();
        while ($row = $this->nextItem()) {
            foreach ($row AS $key => $value) {
                $row[$key] = $value;
            }
            $values[] = $row;
        }
        return $values;
    }
    
    public function fetch_all() {
        return $this->result_set->fetch_all(MYSQLI_ASSOC);
    }
    
    public function toObject($dataObj,$primaryKey = null) {
        if (!is_object($dataObj)) {
            $dataObj = new \stdClass();
        }  
        // Fetch all rows from MysqlI Resource
        $rows = $this->fetch_all();
        
        $objects = [];
        foreach($rows as $rowKey => $rowData) {
            $object = clone $dataObj;
            if(isset($rowData->id)) {
                $key = $rowData->id;
            } else {
                $key = $rowKey;
            }
            foreach($rowData as $propKey => $propValue) {
                $object->$propKey = $propValue;
            }
            if (isset($object->$primaryKey) && !empty($object->$primaryKey)) {
                $key = $object->$primaryKey;
            }
            $objects[$key] = $object;
        }
        return $objects;
        
        // If $object is an instance of \stdClass the objects rows will be added to stdClass objects and returned;
        if ($object instanceof \stdClass) {

        } else {
            $objects = (object)[];
            // Add to Model Collection
            foreach($rows as $row) {
                $object->add($row,$object->primaryKey);
            }
            return $object->collection;
        }        
    }
}
