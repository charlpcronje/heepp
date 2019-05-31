<?php
class Connection {
    private $connection = [];

    public function __construct($connection = null) {
        switch($connection) {
            case 'console':
                $this->connection = $this->console();
            break;
            default:
                $this->connection = $this->console();
            break;
        }
    }

    public function getConnection($connection = null) {
        return $this->connection;
    }

    public function getConnections() {
        $connections[] = (object)[
            'name'  =>'console',
            'alias' =>'console / Console'
        ];
        return $connections;
    }

    private function console() {
        return (object)[
            'host'     => '127.0.0.1',
            'username' => 'root',
            'password' => '',
            'database' => 'console'
        ];

    }
}
