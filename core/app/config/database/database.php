<?php
class Connection {
    private $connection = [];

    public function __construct($connection = null) {
        switch($connection) {
            case 'heepp':
                $this->connection = $this->heepp();
                break;
            default:
                $this->connection = $this->heepp();
                break;
        }
    }

    public function getConnection($connection = null) {
        return $this->connection;
    }

    public function getConnections() {
        $connections[] = (object)[
            'name'  =>'heepp',
            'alias' =>'Heepp / Console'];
        return $connections;
    }

    private function heepp() {
       return (object)[
            'host'     => 'localhost',
            'username' => 'heepp',
            'password' => 'Pl@tinum1',
            'database' => 'heepp'
        ];
    }
}
