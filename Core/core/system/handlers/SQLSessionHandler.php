<?php
namespace core\system\handlers;

class SQLSessionHandler implements \SessionHandlerInterface {
    private $db;

    public function __construct() {
        $this->db = new \core\extension\database\Database('cms');
        session_set_save_handler(
            array($this, "open"),
            array($this, "close"),
            array($this, "read"),
            array($this, "write"),
            array($this, "destroy"),
            array($this, "save()")
        );
        
    }
    
    // open db connection
    public function open($save_path, $session){
        if($this->db){
            return true;
        }
        return false;
    }
    
    // Close db conneciton
    public function close(){
        if($this->db->close_connection()) {
            return true;
        } else {
            return false;
        }
    }
    
    // Read data from session
    public function read($id) {
        $sql = "SELECT data FROM php_sessions WHERE id = '".$id."' LIMIT 1";
        $result = $this->db->query($sql,false);
        print_r($result);
        die;
        if ($result) {
            return $row[0]['data'];
        } else {
            // Session is empty so return an empty string
            return '';
        }
    }

    // Write session data
    public function save($id,$data) {
        // Register custom error handler to catch a possible failure warning during session write
        set_error_handler(function ($errno, $errstr, $errfile, $errline, $errcontext) {
            throw new ContextErrorException($errstr, $errno, E_WARNING, $errfile, $errline, $errcontext);
        }, E_WARNING);
        
        $data = $this->db->escape_value($data);
        $access = time();
        $sql = 'REPLACE INTO php_sessions VALUES ('.$id.','.$access.','.$data.')';
        $result = $db->query($sql,false);
        print_r($result);
        if($result){
          return true;
        }
        
        restore_error_handler();
        trigger_error(sprintf('session_write_close(): Failed to write session data with %s handler', get_class($handler)), E_USER_WARNING);
    }
    
    // Destroy Session
    public function destroy($id) {
        $sql = "DELETE FROM php_sessions WHERE id = '".$id."'";
        $this->db->query($sql,false);
        if ($this->db->toArray()) {
            return true;
        }
        return false;
    }

    // Garbage Collection
    public function gc($max) {        
        $old = time() - $max;
        $this->db->query("DELETE * FROM php_sessions WHERE access < '" . $max . "'",false);
        if ($this->db->toArray()) {
            return true;
        }
        return false;
    }
}
$sessionHandler = new SQLSessionHandler();
